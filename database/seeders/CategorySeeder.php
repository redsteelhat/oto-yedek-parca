<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Ana Kategoriler
            [
                'name' => 'Motor Parçaları',
                'slug' => 'motor-parcalari',
                'description' => 'Motor ile ilgili tüm yedek parçalar',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Motor Parçaları - Yedek Parça',
                'meta_description' => 'Motor parçaları için geniş ürün yelpazesi',
                'children' => [
                    [
                        'name' => 'Piston ve Segmanlar',
                        'slug' => 'piston-segmanlar',
                        'description' => 'Piston ve segman parçaları',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Krank ve Biyel',
                        'slug' => 'krank-biyel',
                        'description' => 'Krank ve biyel parçaları',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Subap ve Subap Takımı',
                        'slug' => 'subap-takimi',
                        'description' => 'Subap ve subap takımı parçaları',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Filtreler',
                'slug' => 'filtreler',
                'description' => 'Araç filtreleri',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Araç Filtreleri - Yedek Parça',
                'meta_description' => 'Motor yağ filtresi, hava filtresi, yakıt filtresi ve daha fazlası',
                'children' => [
                    [
                        'name' => 'Motor Yağ Filtresi',
                        'slug' => 'motor-yag-filtresi',
                        'description' => 'Motor yağ filtreleri',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Hava Filtresi',
                        'slug' => 'hava-filtresi',
                        'description' => 'Hava filtreleri',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Yakıt Filtresi',
                        'slug' => 'yakit-filtresi',
                        'description' => 'Yakıt filtreleri',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Kabin Filtresi',
                        'slug' => 'kabin-filtresi',
                        'description' => 'Kabin hava filtreleri',
                        'is_active' => true,
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'name' => 'Fren Sistemi',
                'slug' => 'fren-sistemi',
                'description' => 'Fren sistemi parçaları',
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Fren Sistemi Parçaları - Yedek Parça',
                'meta_description' => 'Fren balata, fren disk, fren kaliperi ve daha fazlası',
                'children' => [
                    [
                        'name' => 'Fren Balata',
                        'slug' => 'fren-balata',
                        'description' => 'Fren balataları',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Fren Disk',
                        'slug' => 'fren-disk',
                        'description' => 'Fren diskleri',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Fren Kaliperi',
                        'slug' => 'fren-kaliperi',
                        'description' => 'Fren kaliperleri',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Süspansiyon',
                'slug' => 'suspansiyon',
                'description' => 'Süspansiyon sistemi parçaları',
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Süspansiyon Parçaları - Yedek Parça',
                'meta_description' => 'Amortisör, yay, rot başı ve daha fazlası',
                'children' => [
                    [
                        'name' => 'Amortisör',
                        'slug' => 'amortisor',
                        'description' => 'Amortisörler',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Yay',
                        'slug' => 'yay',
                        'description' => 'Süspansiyon yayları',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Rot Başı',
                        'slug' => 'rot-basi',
                        'description' => 'Rot başları',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Elektrik ve Elektronik',
                'slug' => 'elektrik-elektronik',
                'description' => 'Elektrik ve elektronik parçaları',
                'is_active' => true,
                'sort_order' => 5,
                'meta_title' => 'Elektrik Elektronik Parçaları - Yedek Parça',
                'meta_description' => 'Alternatör, marş motoru, sensörler ve daha fazlası',
                'children' => [
                    [
                        'name' => 'Alternatör',
                        'slug' => 'alternator',
                        'description' => 'Alternatörler',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Marş Motoru',
                        'slug' => 'mars-motoru',
                        'description' => 'Marş motorları',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Sensörler',
                        'slug' => 'sensorler',
                        'description' => 'Araç sensörleri',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Aydınlatma',
                'slug' => 'aydinlatma',
                'description' => 'Aydınlatma parçaları',
                'is_active' => true,
                'sort_order' => 6,
                'meta_title' => 'Aydınlatma Parçaları - Yedek Parça',
                'meta_description' => 'Far, stop lambası, sinyal lambası ve daha fazlası',
            ],
            [
                'name' => 'Kaporta',
                'slug' => 'kaporta',
                'description' => 'Kaporta parçaları',
                'is_active' => true,
                'sort_order' => 7,
                'meta_title' => 'Kaporta Parçaları - Yedek Parça',
                'meta_description' => 'Kapı, kaput, çamurluk ve daha fazlası',
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parentCategory = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            foreach ($children as $childData) {
                $childData['parent_id'] = $parentCategory->id;
                Category::firstOrCreate(
                    ['slug' => $childData['slug'], 'parent_id' => $parentCategory->id],
                    $childData
                );
            }
        }

        $this->command->info('Kategoriler başarıyla oluşturuldu!');
    }
}

