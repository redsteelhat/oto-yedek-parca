<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            CarDatabaseSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        $this->command->info('Tüm seeder\'lar başarıyla çalıştırıldı!');
    }
}
