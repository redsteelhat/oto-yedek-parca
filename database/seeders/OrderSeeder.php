<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('user_type', 'customer')->get();
        $products = Product::where('status', 'active')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Müşteri veya ürün bulunamadı. Önce UserSeeder ve ProductSeeder çalıştırın.');
            return;
        }

        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $paymentMethods = ['credit_card', 'bank_transfer', 'cash_on_delivery'];

        // Her müşteri için 1-3 sipariş oluştur
        foreach ($customers as $customer) {
            $orderCount = rand(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                // Müşteri için adres oluştur (eğer yoksa)
                $address = Address::where('user_id', $customer->id)->first();
                
                if (!$address) {
                    $address = Address::create([
                        'user_id' => $customer->id,
                        'title' => 'Ev',
                        'first_name' => explode(' ', $customer->name)[0],
                        'last_name' => explode(' ', $customer->name)[1] ?? '',
                        'phone' => $customer->phone ?? '05551234567',
                        'city' => 'İstanbul',
                        'district' => 'Kadıköy',
                        'address' => 'Örnek Mahalle, Örnek Sokak No:1',
                        'postal_code' => '34000',
                        'is_default' => true,
                    ]);
                }

                // Sipariş oluştur
                $selectedProducts = $products->random(rand(1, 4));
                $subtotal = 0;
                $items = [];

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $price = $product->sale_price ?? $product->price;
                    $itemTotal = $price * $quantity;
                    $taxAmount = ($itemTotal * $product->tax_rate) / 100;

                    $subtotal += $itemTotal;
                    $items[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                        'tax' => $taxAmount,
                    ];
                }

                $taxAmount = array_sum(array_column($items, 'tax'));
                $shippingCost = rand(50, 150);
                $discountAmount = rand(0, 50);
                $total = $subtotal + $taxAmount + $shippingCost - $discountAmount;

                $orderStatus = $orderStatuses[array_rand($orderStatuses)];
                $paymentStatus = $orderStatus === 'delivered' ? 'paid' : ($orderStatus === 'pending' ? 'pending' : $paymentStatuses[array_rand($paymentStatuses)]);
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // Benzersiz order number oluştur
                do {
                    $orderNumber = 'ORD-' . strtoupper(Str::random(8));
                } while (Order::where('order_number', $orderNumber)->exists());

                $order = Order::firstOrCreate(
                    ['order_number' => $orderNumber],
                    [
                        'user_id' => $customer->id,
                        'order_number' => $orderNumber,
                        'status' => $orderStatus,
                        'payment_status' => $paymentStatus,
                        'payment_method' => $paymentMethod,
                        'subtotal' => $subtotal,
                        'tax_amount' => $taxAmount,
                        'shipping_cost' => $shippingCost,
                        'discount_amount' => $discountAmount,
                        'total' => $total,
                        'shipping_name' => $address->first_name . ' ' . $address->last_name,
                        'shipping_phone' => $address->phone,
                        'shipping_city' => $address->city,
                        'shipping_district' => $address->district,
                        'shipping_address' => $address->address,
                        'shipping_postal_code' => $address->postal_code,
                        'billing_name' => $address->first_name . ' ' . $address->last_name,
                        'billing_phone' => $address->phone,
                        'billing_city' => $address->city,
                        'billing_district' => $address->district,
                        'billing_address' => $address->address,
                        'billing_postal_code' => $address->postal_code,
                        'cargo_company' => $orderStatus === 'shipped' || $orderStatus === 'delivered' ? 'Yurtiçi Kargo' : null,
                        'tracking_number' => $orderStatus === 'shipped' || $orderStatus === 'delivered' ? 'YT' . strtoupper(Str::random(10)) : null,
                        'created_at' => now()->subDays(rand(1, 30)),
                    ]
                );

                // Sipariş kalemlerini sadece yeni sipariş için oluştur
                if ($order->wasRecentlyCreated) {

                    // Sipariş kalemlerini oluştur
                    foreach ($items as $item) {
                        OrderItem::firstOrCreate(
                            [
                                'order_id' => $order->id,
                                'product_id' => $item['product']->id,
                            ],
                            [
                                'order_id' => $order->id,
                                'product_id' => $item['product']->id,
                                'product_name' => $item['product']->name,
                                'product_sku' => $item['product']->sku,
                                'price' => $item['price'],
                                'tax_rate' => $item['product']->tax_rate,
                                'quantity' => $item['quantity'],
                                'subtotal' => $item['total'],
                                'tax_amount' => $item['tax'],
                                'total' => $item['total'] + $item['tax'],
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('Siparişler başarıyla oluşturuldu!');
        $this->command->info('Toplam sipariş sayısı: ' . Order::count());
        $this->command->info('Toplam sipariş kalemi sayısı: ' . OrderItem::count());
    }
}

