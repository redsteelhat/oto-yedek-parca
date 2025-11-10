<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\CarYear;
use Illuminate\Database\Seeder;

class CarDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Toyota' => [
                'models' => [
                    'Corolla' => [
                        ['year' => 2020, 'motor_type' => '1.6', 'engine_code' => '1ZR-FE'],
                        ['year' => 2019, 'motor_type' => '1.6', 'engine_code' => '1ZR-FE'],
                        ['year' => 2018, 'motor_type' => '1.6', 'engine_code' => '1ZR-FE'],
                        ['year' => 2021, 'motor_type' => '1.8', 'engine_code' => '2ZR-FE'],
                        ['year' => 2022, 'motor_type' => '1.8', 'engine_code' => '2ZR-FE'],
                    ],
                    'Camry' => [
                        ['year' => 2020, 'motor_type' => '2.5', 'engine_code' => '2AR-FE'],
                        ['year' => 2021, 'motor_type' => '2.5', 'engine_code' => '2AR-FE'],
                        ['year' => 2022, 'motor_type' => '2.5', 'engine_code' => '2AR-FE'],
                    ],
                    'RAV4' => [
                        ['year' => 2020, 'motor_type' => '2.0', 'engine_code' => '3ZR-FAE'],
                        ['year' => 2021, 'motor_type' => '2.0', 'engine_code' => '3ZR-FAE'],
                        ['year' => 2022, 'motor_type' => '2.5', 'engine_code' => 'A25A-FKS'],
                    ],
                ],
            ],
            'Volkswagen' => [
                'models' => [
                    'Golf' => [
                        ['year' => 2020, 'motor_type' => '1.4 TSI', 'engine_code' => 'EA211'],
                        ['year' => 2021, 'motor_type' => '1.4 TSI', 'engine_code' => 'EA211'],
                        ['year' => 2022, 'motor_type' => '1.5 TSI', 'engine_code' => 'EA211'],
                    ],
                    'Passat' => [
                        ['year' => 2020, 'motor_type' => '2.0 TDI', 'engine_code' => 'EA288'],
                        ['year' => 2021, 'motor_type' => '2.0 TDI', 'engine_code' => 'EA288'],
                        ['year' => 2022, 'motor_type' => '2.0 TSI', 'engine_code' => 'EA888'],
                    ],
                    'Tiguan' => [
                        ['year' => 2020, 'motor_type' => '2.0 TSI', 'engine_code' => 'EA888'],
                        ['year' => 2021, 'motor_type' => '2.0 TSI', 'engine_code' => 'EA888'],
                        ['year' => 2022, 'motor_type' => '2.0 TSI', 'engine_code' => 'EA888'],
                    ],
                ],
            ],
            'Ford' => [
                'models' => [
                    'Focus' => [
                        ['year' => 2020, 'motor_type' => '1.5 EcoBoost', 'engine_code' => 'CAF'],
                        ['year' => 2021, 'motor_type' => '1.5 EcoBoost', 'engine_code' => 'CAF'],
                        ['year' => 2022, 'motor_type' => '1.5 EcoBoost', 'engine_code' => 'CAF'],
                    ],
                    'Mondeo' => [
                        ['year' => 2020, 'motor_type' => '2.0 EcoBoost', 'engine_code' => 'CAF'],
                        ['year' => 2021, 'motor_type' => '2.0 EcoBoost', 'engine_code' => 'CAF'],
                    ],
                    'Kuga' => [
                        ['year' => 2020, 'motor_type' => '2.0 EcoBoost', 'engine_code' => 'CAF'],
                        ['year' => 2021, 'motor_type' => '2.0 EcoBoost', 'engine_code' => 'CAF'],
                        ['year' => 2022, 'motor_type' => '2.5 Hybrid', 'engine_code' => 'CAF'],
                    ],
                ],
            ],
            'Renault' => [
                'models' => [
                    'Clio' => [
                        ['year' => 2020, 'motor_type' => '1.0 TCe', 'engine_code' => 'H4Bt'],
                        ['year' => 2021, 'motor_type' => '1.0 TCe', 'engine_code' => 'H4Bt'],
                        ['year' => 2022, 'motor_type' => '1.0 TCe', 'engine_code' => 'H4Bt'],
                    ],
                    'Megane' => [
                        ['year' => 2020, 'motor_type' => '1.5 dCi', 'engine_code' => 'K9K'],
                        ['year' => 2021, 'motor_type' => '1.5 dCi', 'engine_code' => 'K9K'],
                        ['year' => 2022, 'motor_type' => '1.3 TCe', 'engine_code' => 'H5Ht'],
                    ],
                    'Captur' => [
                        ['year' => 2020, 'motor_type' => '1.3 TCe', 'engine_code' => 'H5Ht'],
                        ['year' => 2021, 'motor_type' => '1.3 TCe', 'engine_code' => 'H5Ht'],
                        ['year' => 2022, 'motor_type' => '1.3 TCe', 'engine_code' => 'H5Ht'],
                    ],
                ],
            ],
            'Fiat' => [
                'models' => [
                    'Egea' => [
                        ['year' => 2020, 'motor_type' => '1.4 Fire', 'engine_code' => '350A2000'],
                        ['year' => 2021, 'motor_type' => '1.4 Fire', 'engine_code' => '350A2000'],
                        ['year' => 2022, 'motor_type' => '1.4 Fire', 'engine_code' => '350A2000'],
                    ],
                    'Tipo' => [
                        ['year' => 2020, 'motor_type' => '1.4 T-Jet', 'engine_code' => '350A2000'],
                        ['year' => 2021, 'motor_type' => '1.4 T-Jet', 'engine_code' => '350A2000'],
                        ['year' => 2022, 'motor_type' => '1.4 T-Jet', 'engine_code' => '350A2000'],
                    ],
                ],
            ],
        ];

        foreach ($brands as $brandName => $brandData) {
            $brandSlug = \Illuminate\Support\Str::slug($brandName);
            $brand = CarBrand::firstOrCreate(
                ['slug' => $brandSlug],
                [
                    'name' => $brandName,
                    'slug' => $brandSlug,
                    'is_active' => true,
                    'sort_order' => CarBrand::max('sort_order') + 1,
                ]
            );

            foreach ($brandData['models'] as $modelName => $years) {
                $modelSlug = \Illuminate\Support\Str::slug($modelName);
                $model = CarModel::firstOrCreate(
                    ['slug' => $modelSlug, 'brand_id' => $brand->id],
                    [
                        'brand_id' => $brand->id,
                        'name' => $modelName,
                        'slug' => $modelSlug,
                        'is_active' => true,
                        'sort_order' => CarModel::where('brand_id', $brand->id)->max('sort_order') + 1 ?? 1,
                    ]
                );

                foreach ($years as $yearData) {
                    CarYear::firstOrCreate(
                        [
                            'model_id' => $model->id,
                            'year' => $yearData['year'],
                            'motor_type' => $yearData['motor_type'],
                            'engine_code' => $yearData['engine_code'],
                        ],
                        [
                            'model_id' => $model->id,
                            'year' => $yearData['year'],
                            'motor_type' => $yearData['motor_type'],
                            'engine_code' => $yearData['engine_code'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        $this->command->info('Araç veritabanı başarıyla oluşturuldu!');
        $this->command->info('Marka sayısı: ' . CarBrand::count());
        $this->command->info('Model sayısı: ' . CarModel::count());
        $this->command->info('Yıl sayısı: ' . CarYear::count());
    }
}

