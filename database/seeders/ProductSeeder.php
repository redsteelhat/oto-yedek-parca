<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önce bir tedarikçi oluştur
        $supplier = Supplier::firstOrCreate(
            ['code' => 'SUP001'],
            [
                'name' => 'Örnek Tedarikçi',
                'code' => 'SUP001',
                'is_active' => true,
            ]
        );

        // Kategorileri al
        $categories = Category::whereNull('parent_id')->get();
        $subCategories = Category::whereNotNull('parent_id')->get();

        // Örnek ürünler
        $products = [
            [
                'name' => 'Motor Yağ Filtresi OE Kalite',
                'sku' => 'MYF-001',
                'oem_code' => 'OE-12345',
                'category_id' => $subCategories->where('slug', 'motor-yag-filtresi')->first()?->id ?? $categories->first()->id,
                'description' => 'Yüksek kaliteli motor yağ filtresi. Orijinal ekipman kalitesinde üretilmiştir.',
                'short_description' => 'OE kalite motor yağ filtresi',
                'price' => 150.00,
                'sale_price' => 120.00,
                'stock' => 50,
                'min_stock_level' => 10,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => true,
                'manufacturer' => 'Premium Filtre',
                'part_type' => 'oem',
            ],
            [
                'name' => 'Hava Filtresi Standart',
                'sku' => 'HF-001',
                'oem_code' => 'OE-12346',
                'category_id' => $subCategories->where('slug', 'hava-filtresi')->first()?->id ?? $categories->first()->id,
                'description' => 'Standart hava filtresi. Motor performansını korur.',
                'short_description' => 'Standart hava filtresi',
                'price' => 80.00,
                'sale_price' => null,
                'stock' => 100,
                'min_stock_level' => 20,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => false,
                'manufacturer' => 'Standart Filtre',
                'part_type' => 'aftermarket',
            ],
            [
                'name' => 'Fren Balata Seti Ön',
                'sku' => 'FB-001',
                'oem_code' => 'OE-12347',
                'category_id' => $subCategories->where('slug', 'fren-balata')->first()?->id ?? $categories->first()->id,
                'description' => 'Ön fren balata seti. Yüksek fren performansı sağlar.',
                'short_description' => 'Ön fren balata seti',
                'price' => 450.00,
                'sale_price' => 380.00,
                'stock' => 30,
                'min_stock_level' => 5,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => true,
                'manufacturer' => 'Premium Fren',
                'part_type' => 'oem',
            ],
            [
                'name' => 'Fren Disk Çift',
                'sku' => 'FD-001',
                'oem_code' => 'OE-12348',
                'category_id' => $subCategories->where('slug', 'fren-disk')->first()?->id ?? $categories->first()->id,
                'description' => 'Fren disk çifti. Isıya dayanıklı malzeme.',
                'short_description' => 'Fren disk çifti',
                'price' => 1200.00,
                'sale_price' => 1000.00,
                'stock' => 15,
                'min_stock_level' => 3,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => false,
                'manufacturer' => 'Premium Fren',
                'part_type' => 'aftermarket',
            ],
            [
                'name' => 'Amortisör Ön Sağ',
                'sku' => 'AM-001',
                'oem_code' => 'OE-12349',
                'category_id' => $subCategories->where('slug', 'amortisor')->first()?->id ?? $categories->first()->id,
                'description' => 'Ön sağ amortisör. Konfor ve güvenlik sağlar.',
                'short_description' => 'Ön sağ amortisör',
                'price' => 850.00,
                'sale_price' => null,
                'stock' => 20,
                'min_stock_level' => 4,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => false,
                'manufacturer' => 'Premium Süspansiyon',
                'part_type' => 'oem',
            ],
            [
                'name' => 'Alternatör 120A',
                'sku' => 'ALT-001',
                'oem_code' => 'OE-12350',
                'category_id' => $subCategories->where('slug', 'alternator')->first()?->id ?? $categories->first()->id,
                'description' => '120 amper alternatör. Yüksek performans.',
                'short_description' => '120A alternatör',
                'price' => 2500.00,
                'sale_price' => 2200.00,
                'stock' => 10,
                'min_stock_level' => 2,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => true,
                'manufacturer' => 'Premium Elektrik',
                'part_type' => 'oem',
            ],
            [
                'name' => 'Marş Motoru',
                'sku' => 'MM-001',
                'oem_code' => 'OE-12351',
                'category_id' => $subCategories->where('slug', 'mars-motoru')->first()?->id ?? $categories->first()->id,
                'description' => 'Güçlü marş motoru. Kolay çalıştırma.',
                'short_description' => 'Marş motoru',
                'price' => 1800.00,
                'sale_price' => null,
                'stock' => 12,
                'min_stock_level' => 2,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => false,
                'manufacturer' => 'Premium Elektrik',
                'part_type' => 'aftermarket',
            ],
            [
                'name' => 'Far Lambası Sağ',
                'sku' => 'FL-001',
                'oem_code' => 'OE-12352',
                'category_id' => $categories->where('slug', 'aydinlatma')->first()?->id ?? $categories->first()->id,
                'description' => 'Sağ far lambası. Yüksek parlaklık LED.',
                'short_description' => 'Sağ far lambası',
                'price' => 600.00,
                'sale_price' => 500.00,
                'stock' => 25,
                'min_stock_level' => 5,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => false,
                'manufacturer' => 'Premium Aydınlatma',
                'part_type' => 'oem',
            ],
            [
                'name' => 'Kapı Kolu Sağ Ön',
                'sku' => 'KK-001',
                'oem_code' => 'OE-12353',
                'category_id' => $categories->where('slug', 'kaporta')->first()?->id ?? $categories->first()->id,
                'description' => 'Sağ ön kapı kolu. Orijinal renk.',
                'short_description' => 'Sağ ön kapı kolu',
                'price' => 350.00,
                'sale_price' => null,
                'stock' => 40,
                'min_stock_level' => 8,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => false,
                'manufacturer' => 'Premium Kaporta',
                'part_type' => 'aftermarket',
            ],
            [
                'name' => 'Piston Seti',
                'sku' => 'PS-001',
                'oem_code' => 'OE-12354',
                'category_id' => $subCategories->where('slug', 'piston-segmanlar')->first()?->id ?? $categories->first()->id,
                'description' => 'Motor piston seti. Yüksek dayanıklılık.',
                'short_description' => 'Piston seti',
                'price' => 3500.00,
                'sale_price' => 3000.00,
                'stock' => 8,
                'min_stock_level' => 2,
                'tax_rate' => 20.00,
                'status' => 'active',
                'is_featured' => true,
                'manufacturer' => 'Premium Motor',
                'part_type' => 'oem',
            ],
        ];

        foreach ($products as $productData) {
            $productData['supplier_id'] = $supplier->id;
            $slug = Str::slug($productData['name']);
            $productData['slug'] = $slug;
            
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        $this->command->info('Ürünler başarıyla oluşturuldu!');
        $this->command->info('Toplam ürün sayısı: ' . Product::count());
    }
}

