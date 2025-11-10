<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierXmlMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XmlMappingController extends Controller
{
    /**
     * Show XML mapping UI for supplier
     */
    public function index(Supplier $supplier)
    {
        $supplier->load('xmlMappings');
        
        // Get sample XML structure
        $sampleXml = null;
        $xmlFields = [];
        
        if ($supplier->xml_url) {
            try {
                $response = Http::timeout(30)->get($supplier->xml_url);
                if ($response->successful()) {
                    $xmlContent = $response->body();
                    $xml = simplexml_load_string($xmlContent);
                    
                    if ($xml) {
                        // Get first item as sample
                        $items = $xml->xpath('//product') ?: $xml->xpath('//item') ?: [$xml];
                        if (count($items) > 0) {
                            $sampleXml = $items[0];
                            
                            // Extract all fields from sample
                            $xmlFields = $this->extractXmlFields($sampleXml);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('XML Sample Fetch Error', [
                    'supplier_id' => $supplier->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Get local fields (product fields)
        $localFields = [
            'sku' => 'SKU',
            'oem_code' => 'OEM Kodu',
            'name' => 'Ürün Adı',
            'description' => 'Açıklama',
            'short_description' => 'Kısa Açıklama',
            'price' => 'Fiyat',
            'sale_price' => 'İndirimli Fiyat',
            'stock' => 'Stok',
            'min_stock_level' => 'Minimum Stok',
            'tax_rate' => 'KDV Oranı',
            'manufacturer' => 'Üretici',
            'part_type' => 'Parça Tipi',
            'category' => 'Kategori',
            'image' => 'Görsel URL',
        ];

        return view('admin.xml-mapping.index', compact('supplier', 'xmlFields', 'localFields', 'sampleXml'));
    }

    /**
     * Store or update XML mappings
     */
    public function store(Request $request, Supplier $supplier)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.xml_field' => 'required|string|max:255',
            'mappings.*.local_field' => 'required|string|max:255',
            'mappings.*.transform_rule' => 'nullable|string|max:255',
            'mappings.*.is_required' => 'boolean',
            'mappings.*.sort_order' => 'nullable|integer',
        ]);

        // Delete existing mappings
        $supplier->xmlMappings()->delete();

        // Create new mappings
        foreach ($request->mappings as $index => $mapping) {
            SupplierXmlMapping::create([
                'supplier_id' => $supplier->id,
                'xml_field' => $mapping['xml_field'],
                'local_field' => $mapping['local_field'],
                'transform_rule' => $mapping['transform_rule'] ?? null,
                'is_required' => isset($mapping['is_required']) && $mapping['is_required'],
                'sort_order' => $mapping['sort_order'] ?? $index,
            ]);
        }

        return redirect()->route('admin.xml-mapping.index', $supplier)
            ->with('success', 'XML mapping başarıyla kaydedildi.');
    }

    /**
     * Test XML mapping
     */
    public function test(Request $request, Supplier $supplier)
    {
        $request->validate([
            'xml_url' => 'required|url',
            'mappings' => 'required|array',
        ]);

        try {
            $response = Http::timeout(30)->get($request->xml_url);
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'XML yüklenemedi: HTTP ' . $response->status(),
                ], 400);
            }

            $xmlContent = $response->body();
            $xml = simplexml_load_string($xmlContent);

            if (!$xml) {
                return response()->json([
                    'success' => false,
                    'message' => 'XML parse edilemedi.',
                ], 400);
            }

            // Get first item
            $items = $xml->xpath('//product') ?: $xml->xpath('//item') ?: [$xml];
            if (count($items) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'XML içinde ürün bulunamadı.',
                ], 400);
            }

            $sampleItem = $items[0];
            $mappedData = [];

            foreach ($request->mappings as $mapping) {
                $xmlField = $mapping['xml_field'];
                $localField = $mapping['local_field'];
                
                // Try to get value from XML
                $value = null;
                try {
                    $xpathResult = $sampleItem->xpath($xmlField);
                    if ($xpathResult && count($xpathResult) > 0) {
                        $value = (string) $xpathResult[0];
                    } else {
                        $value = (string) ($sampleItem->{$xmlField} ?? $sampleItem[$xmlField] ?? null);
                    }
                } catch (\Exception $e) {
                    $value = null;
                }

                // Apply transform
                if ($value && isset($mapping['transform_rule'])) {
                    $value = $this->applyTransform($value, $mapping['transform_rule']);
                }

                $mappedData[$localField] = $value;
            }

            return response()->json([
                'success' => true,
                'data' => $mappedData,
                'message' => 'Mapping test başarılı.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract all fields from XML element
     */
    private function extractXmlFields($xmlElement, $prefix = '')
    {
        $fields = [];

        // Attributes
        foreach ($xmlElement->attributes() as $key => $value) {
            $fields[] = ($prefix ? $prefix . '/' : '') . '@' . $key;
        }

        // Children
        foreach ($xmlElement->children() as $key => $child) {
            $fieldName = ($prefix ? $prefix . '/' : '') . $key;
            
            if (count($child->children()) > 0) {
                // Recursive for nested elements
                $fields = array_merge($fields, $this->extractXmlFields($child, $fieldName));
            } else {
                $fields[] = $fieldName;
            }
        }

        // Direct value
        if ((string) $xmlElement) {
            $fields[] = $prefix ?: 'value';
        }

        return array_unique($fields);
    }

    /**
     * Apply transform rule
     */
    private function applyTransform($value, $rule)
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
}
