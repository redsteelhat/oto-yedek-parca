<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\CarYear;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('oem_code', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $carYears = CarYear::with(['model.brand'])->where('is_active', true)->get();

        return view('admin.products.create', compact('categories', 'suppliers', 'carYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check product limit
        if (!$this->checkProductLimit()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ürün limitine ulaşıldı. Daha fazla ürün eklemek için abonelik planınızı yükseltin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'compatible_cars' => 'nullable|array',
            'compatible_cars.*' => 'exists:cars_years,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['tax_rate'] = $request->tax_rate ?? 20.00;
        
        // Add tenant_id to validated data
        $validated['tenant_id'] = $this->getCurrentTenantId();

        $product = Product::create($validated);

        // Handle images with security validation
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                try {
                    $path = \App\Services\FileUploadService::uploadImage(
                        $image,
                        'products',
                        2048 // 2MB max per image
                    );
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                    ]);
                } catch (\Exception $e) {
                    // Log error but continue with other images
                    \Log::warning('Product image upload failed: ' . $e->getMessage());
                }
            }
        }

        // Handle compatible cars
        if ($request->compatible_cars) {
            $product->compatibleCars()->attach($request->compatible_cars);
        }

        // Clear product cache
        CacheService::clearProductCache();

        return redirect()->route('admin.products.index')
            ->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'images', 'compatibleCars.model.brand']);
        
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['images', 'compatibleCars']);
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $carYears = CarYear::with(['model.brand'])->where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'suppliers', 'carYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'compatible_cars' => 'nullable|array',
            'compatible_cars.*' => 'exists:cars_years,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product->update($validated);

        // Handle new images with security validation
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                try {
                    $path = \App\Services\FileUploadService::uploadImage(
                        $image,
                        'products',
                        2048 // 2MB max per image
                    );
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => $product->images()->max('sort_order') + $index + 1,
                        'is_primary' => false,
                    ]);
                } catch (\Exception $e) {
                    // Log error but continue with other images
                    \Log::warning('Product image upload failed: ' . $e->getMessage());
                }
            }
        }

        // Update compatible cars
        if ($request->has('compatible_cars')) {
            $product->compatibleCars()->sync($request->compatible_cars);
        }

        // Clear product cache
        CacheService::clearProductCache();

        return redirect()->route('admin.products.index')
            ->with('success', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        // Clear product cache
        CacheService::clearProductCache();

        return redirect()->route('admin.products.index')
            ->with('success', 'Ürün başarıyla silindi.');
    }

    /**
     * Bulk actions (activate, deactivate, delete)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        switch ($request->action) {
            case 'activate':
                Product::whereIn('id', $request->product_ids)->update(['status' => 'active']);
                $message = 'Seçili ürünler aktif edildi.';
                break;
            case 'deactivate':
                Product::whereIn('id', $request->product_ids)->update(['status' => 'inactive']);
                $message = 'Seçili ürünler pasif edildi.';
                break;
            case 'delete':
                Product::whereIn('id', $request->product_ids)->delete();
                $message = 'Seçili ürünler silindi.';
                break;
        }

        // Clear product cache after bulk actions
        CacheService::clearProductCache();

        return back()->with('success', $message);
    }

    /**
     * Duplicate product
     */
    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->sku = $product->sku . '-COPY-' . time();
        $newProduct->name = $product->name . ' (Kopya)';
        $newProduct->slug = Str::slug($newProduct->name);
        $newProduct->status = 'draft';
        $newProduct->save();

        // Duplicate images
        foreach ($product->images as $image) {
            $newImage = $image->replicate();
            $newImage->product_id = $newProduct->id;
            $newImage->save();
        }

        // Duplicate compatible cars
        $newProduct->compatibleCars()->attach($product->compatibleCars->pluck('id'));

        return redirect()->route('admin.products.edit', $newProduct)
            ->with('success', 'Ürün başarıyla kopyalandı.');
    }

    /**
     * Export products to CSV
     */
    public function export(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $products = $query->get();

        $filename = 'products_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'SKU',
                'OEM Kodu',
                'Ad',
                'Kategori',
                'Tedarikçi',
                'Fiyat',
                'İndirimli Fiyat',
                'Stok',
                'Min Stok',
                'KDV Oranı',
                'Durum',
                'Açıklama',
            ]);

            // CSV Data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sku,
                    $product->oem_code,
                    $product->name,
                    $product->category->name ?? '',
                    $product->supplier->name ?? '',
                    $product->price,
                    $product->sale_price ?? '',
                    $product->stock,
                    $product->min_stock_level,
                    $product->tax_rate,
                    $product->status,
                    $product->description,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import products from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        // Remove header
        array_shift($data);

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                if (count($row) < 3) continue; // Skip invalid rows

                $sku = $row[0] ?? null;
                if (empty($sku)) continue;

                $product = Product::where('sku', $sku)->first();

                $productData = [
                    'sku' => $sku,
                    'oem_code' => $row[1] ?? null,
                    'name' => $row[2] ?? 'Ürün',
                    'price' => floatval($row[5] ?? 0),
                    'sale_price' => !empty($row[6]) ? floatval($row[6]) : null,
                    'stock' => intval($row[7] ?? 0),
                    'min_stock_level' => intval($row[8] ?? 0),
                    'tax_rate' => floatval($row[9] ?? 20),
                    'status' => $row[10] ?? 'draft',
                    'description' => $row[11] ?? null,
                ];

                // Find category by name
                if (!empty($row[3])) {
                    $category = \App\Models\Category::where('name', $row[3])->first();
                    if ($category) {
                        $productData['category_id'] = $category->id;
                    }
                }

                // Find supplier by name
                if (!empty($row[4])) {
                    $supplier = \App\Models\Supplier::where('name', $row[4])->first();
                    if ($supplier) {
                        $productData['supplier_id'] = $supplier->id;
                    }
                }

                $productData['slug'] = Str::slug($productData['name']);

                if ($product) {
                    $product->update($productData);
                    $updated++;
                } else {
                    // Add tenant_id for new products
                    $productData['tenant_id'] = $this->getCurrentTenantId();
                    Product::create($productData);
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Satır " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        // Clear product cache after import
        CacheService::clearProductCache();

        $message = "İçe aktarılan: {$imported}, Güncellenen: {$updated}";
        if (!empty($errors)) {
            $message .= ", Hata: " . count($errors);
        }

        return back()->with('success', $message)->with('errors', $errors);
    }

    /**
     * Update image sort order
     */
    public function updateImageOrder(Request $request, Product $product)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:product_images,id',
        ]);

        foreach ($request->image_ids as $index => $imageId) {
            $product->images()->where('id', $imageId)->update([
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete product image
     */
    public function deleteImage(Request $request, Product $product, $imageId)
    {
        $image = $product->images()->findOrFail($imageId);
        
        // Delete file
        if (file_exists(storage_path('app/public/' . $image->image_path))) {
            unlink(storage_path('app/public/' . $image->image_path));
        }
        
        $image->delete();

        return back()->with('success', 'Görsel silindi.');
    }
}
