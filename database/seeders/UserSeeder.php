<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcı
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'phone' => '05551234567',
                'user_type' => 'admin',
                'is_verified' => true,
            ]
        );

        // Dealer kullanıcı
        User::firstOrCreate(
            ['email' => 'dealer@example.com'],
            [
                'name' => 'Bayi Kullanıcı',
                'password' => Hash::make('password'),
                'phone' => '05551234568',
                'company_name' => 'Örnek Otomotiv Bayi',
                'tax_number' => '1234567890',
                'user_type' => 'dealer',
                'is_verified' => true,
            ]
        );

        // Normal müşteriler
        $customers = [
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet@example.com',
                'password' => Hash::make('password'),
                'phone' => '05551234569',
                'user_type' => 'customer',
                'is_verified' => true,
            ],
            [
                'name' => 'Mehmet Demir',
                'email' => 'mehmet@example.com',
                'password' => Hash::make('password'),
                'phone' => '05551234570',
                'user_type' => 'customer',
                'is_verified' => true,
            ],
            [
                'name' => 'Ayşe Kaya',
                'email' => 'ayse@example.com',
                'password' => Hash::make('password'),
                'phone' => '05551234571',
                'user_type' => 'customer',
                'is_verified' => true,
            ],
            [
                'name' => 'Fatma Şahin',
                'email' => 'fatma@example.com',
                'password' => Hash::make('password'),
                'phone' => '05551234572',
                'user_type' => 'customer',
                'is_verified' => true,
            ],
            [
                'name' => 'Ali Öztürk',
                'email' => 'ali@example.com',
                'password' => Hash::make('password'),
                'phone' => '05551234573',
                'user_type' => 'customer',
                'is_verified' => true,
            ],
        ];

        foreach ($customers as $customer) {
            User::firstOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }

        $this->command->info('Kullanıcılar başarıyla oluşturuldu!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Dealer: dealer@example.com / password');
        $this->command->info('Müşteriler: ahmet@example.com, mehmet@example.com, ayse@example.com, fatma@example.com, ali@example.com / password');
    }
}

