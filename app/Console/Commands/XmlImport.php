<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\XmlImportLog;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class XmlImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:import {supplier_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from supplier XML files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $supplierId = $this->argument('supplier_id');

        if ($supplierId) {
            $suppliers = Supplier::where('id', $supplierId)->where('is_active', true)->get();
        } else {
            $suppliers = Supplier::where('is_active', true)->get();
        }

        if ($suppliers->isEmpty()) {
            $this->error('Aktif tedarikçi bulunamadı.');
            return 1;
        }

        foreach ($suppliers as $supplier) {
            $this->info("Tedarikçi: {$supplier->name} - İçe aktarma başlatılıyor...");
            $this->importSupplier($supplier);
        }

        return 0;
    }

    protected function importSupplier(Supplier $supplier)
    {
        if (empty($supplier->xml_url)) {
            $this->warn("Tedarikçi {$supplier->name} için XML URL tanımlı değil.");
            return;
        }

        $log = XmlImportLog::create([
            'supplier_id' => $supplier->id,
            'status' => 'processing',
            'started_at' => now(),
            'total_items' => 0,
            'imported_items' => 0,
            'updated_items' => 0,
            'failed_items' => 0,
        ]);

        try {
            // Fetch XML
            $response = Http::timeout(120)->get($supplier->xml_url);
            
            if (!$response->successful()) {
                throw new \Exception("XML yüklenemedi: HTTP {$response->status()}");
            }

            $xmlContent = $response->body();
            
            // Parse XML
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent);
            
            if ($xml === false) {
                $errors = libxml_get_errors();
                throw new \Exception("XML parse hatası: " . implode(', ', array_map(fn($e) => $e->message, $errors)));
            }

            // Get mappings
            $mappings = $supplier->xmlMappings()->get()->keyBy('xml_field');
            
            // Determine XML structure (assuming standard structure)
            $items = $xml->xpath('//product') ?: $xml->xpath('//item') ?: [$xml];
            
            $log->update(['total_items' => count($items)]);
            
            $this->info("Toplam {$log->total_items} ürün bulundu.");

            $imported = 0;
            $updated = 0;
            $failed = 0;
            $errors = [];

            foreach ($items as $index => $item) {
                try {
                    $data = $this->mapXmlData($item, $mappings);
                    
                    if (empty($data['sku'])) {
                        $failed++;
                        $errors[] = "Ürün #{$index}: SKU bulunamadı";
                        continue;
                    }

                    // Auto-match category if not set
                    if (empty($data['category_id']) && !empty($data['category'])) {
                        $category = $this->matchCategory($data['category']);
                        if ($category) {
                            $data['category_id'] = $category->id;
                        }
                    }

                    // Set supplier
                    $data['supplier_id'] = $supplier->id;

                    $product = Product::where('sku', $data['sku'])->first();

                    if ($product) {
                        // Update existing product
                        $product->update($data);
                        $updated++;
                        $this->line("Güncellendi: {$data['sku']}");
                    } else {
                        // Create new product
                        $product = Product::create($data);
                        $imported++;
                        $this->line("İçe aktarıldı: {$data['sku']}");
                    }

                    // Import images if provided
                    if (isset($data['image']) || isset($data['images'])) {
                        $this->importImages($product, $data['image'] ?? $data['images'] ?? []);
                    }

                    // Update stock if provided
                    if (isset($data['stock'])) {
                        $product->update(['stock' => $data['stock']]);
                    }

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Ürün #{$index}: " . $e->getMessage();
                    $this->error("Hata: " . $e->getMessage());
                    Log::error('XML Import Product Error', [
                        'supplier_id' => $supplier->id,
                        'index' => $index,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            $log->update([
                'status' => $failed > 0 ? 'partial' : 'success',
                'imported_items' => $imported,
                'updated_items' => $updated,
                'failed_items' => $failed,
                'error_message' => !empty($errors) ? implode("\n", array_slice($errors, 0, 10)) : null,
                'log_details' => [
                    'errors' => array_slice($errors, 0, 50),
                    'total_processed' => $imported + $updated + $failed,
                    'success_rate' => $log->total_items > 0 ? round((($imported + $updated) / $log->total_items) * 100, 2) : 0,
                ],
                'completed_at' => now(),
            ]);

            $supplier->update(['last_import_at' => now()]);

            $this->info("Tamamlandı! İçe aktarılan: {$imported}, Güncellenen: {$updated}, Hata: {$failed}");

        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $this->error("Hata: " . $e->getMessage());
        }
    }

    protected function mapXmlData($xmlItem, $mappings)
    {
        $data = [];

        foreach ($mappings as $xmlField => $mapping) {
            $value = (string) $xmlItem->xpath($xmlField)[0] ?? 
                     (string) $xmlItem->{$xmlField} ?? 
                     (string) $xmlItem[$xmlField] ?? null;

            if ($value !== null) {
                // Apply transform rules
                if ($mapping->transform_rule) {
                    $value = $this->applyTransform($value, $mapping->transform_rule);
                }

                $data[$mapping->local_field] = $value;
            }
        }

        // Ensure required fields have defaults
        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }

        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        if (empty($data['stock'])) {
            $data['stock'] = 0;
        }

        if (empty($data['tax_rate'])) {
            $data['tax_rate'] = 20.00;
        }

        return $data;
    }

    protected function applyTransform($value, $rule)
    {
        switch ($rule) {
            case 'trim':
                return trim($value);
            case 'uppercase':
                return strtoupper($value);
            case 'lowercase':
                return strtolower($value);
            case 'float':
                return (float) $value;
            case 'int':
                return (int) $value;
            case 'slug':
                return \Illuminate\Support\Str::slug($value);
            default:
                return $value;
        }
    }

    /**
     * Match category by name
     */
    protected function matchCategory($categoryName)
    {
        if (empty($categoryName)) {
            return null;
        }

        // Try exact match
        $category = Category::where('name', $categoryName)->first();
        if ($category) {
            return $category;
        }

        // Try slug match
        $slug = \Illuminate\Support\Str::slug($categoryName);
        $category = Category::where('slug', $slug)->first();
        if ($category) {
            return $category;
        }

        // Try partial match
        $category = Category::where('name', 'like', '%' . $categoryName . '%')->first();
        if ($category) {
            return $category;
        }

        return null;
    }

    /**
     * Import product images
     */
    protected function importImages(Product $product, $imageUrls)
    {
        if (empty($imageUrls)) {
            return;
        }

        // Handle single image string or array
        if (is_string($imageUrls)) {
            $imageUrls = [$imageUrls];
        } elseif (!is_array($imageUrls)) {
            return;
        }

        $sortOrder = 0;
        foreach ($imageUrls as $imageUrl) {
            if (empty($imageUrl)) {
                continue;
            }

            try {
                // Download image
                $response = Http::timeout(30)->get($imageUrl);
                if (!$response->successful()) {
                    Log::warning('Image Download Failed', [
                        'product_id' => $product->id,
                        'url' => $imageUrl,
                    ]);
                    continue;
                }

                // Get image extension
                $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                if (empty($extension)) {
                    $extension = 'jpg'; // Default extension
                }

                // Generate filename
                $filename = 'products/' . $product->id . '/' . uniqid() . '.' . $extension;
                
                // Store image
                Storage::disk('public')->put($filename, $response->body());

                // Check if primary image exists
                $isPrimary = $product->images()->where('is_primary', true)->count() === 0;

                // Create product image record
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $filename,
                    'sort_order' => $sortOrder,
                    'is_primary' => $isPrimary,
                ]);

                $sortOrder++;
            } catch (\Exception $e) {
                Log::error('Image Import Error', [
                    'product_id' => $product->id,
                    'url' => $imageUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
