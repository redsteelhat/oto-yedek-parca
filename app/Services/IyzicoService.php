<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IyzicoService
{
    /**
     * Process payment with Iyzico
     */
    public static function processPayment(Order $order, $paymentData)
    {
        $apiKey = Setting::getValue('payment_iyzico_api_key');
        $secretKey = Setting::getValue('payment_iyzico_secret_key');
        $baseUrl = Setting::getValue('payment_iyzico_base_url', 'https://api.iyzipay.com');

        if (!$apiKey || !$secretKey) {
            throw new \Exception('İyzico API bilgileri eksik.');
        }

        // Create payment request
        $paymentRequest = [
            'locale' => 'tr',
            'conversationId' => $order->order_number,
            'price' => number_format($order->total, 2, '.', ''),
            'paidPrice' => number_format($order->total, 2, '.', ''),
            'currency' => 'TRY',
            'installment' => 1,
            'basketId' => $order->id,
            'paymentChannel' => 'WEB',
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => route('payment.iyzico.callback'),
            'buyer' => [
                'id' => $order->user_id,
                'name' => $order->shipping_name,
                'surname' => '',
                'gsmNumber' => $order->shipping_phone,
                'email' => $order->user->email,
                'identityNumber' => '',
                'lastLoginDate' => now()->format('Y-m-d H:i:s'),
                'registrationDate' => $order->user->created_at->format('Y-m-d H:i:s'),
                'registrationAddress' => $order->shipping_address,
                'ip' => request()->ip(),
                'city' => $order->shipping_city,
                'country' => 'Turkey',
                'zipCode' => $order->shipping_postal_code ?? '',
            ],
            'shippingAddress' => [
                'contactName' => $order->shipping_name,
                'city' => $order->shipping_city,
                'country' => 'Turkey',
                'address' => $order->shipping_address,
                'zipCode' => $order->shipping_postal_code ?? '',
            ],
            'billingAddress' => [
                'contactName' => $order->billing_name ?? $order->shipping_name,
                'city' => $order->billing_city ?? $order->shipping_city,
                'country' => 'Turkey',
                'address' => $order->billing_address ?? $order->shipping_address,
                'zipCode' => $order->billing_postal_code ?? $order->shipping_postal_code ?? '',
            ],
            'basketItems' => self::prepareBasketItems($order),
        ];

        try {
            // Make API request to Iyzico
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':' . $secretKey),
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/payment/auth', $paymentRequest);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === 'success') {
                // Payment successful
                $order->update([
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $result['paymentId'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message' => 'Ödeme başarıyla tamamlandı.',
                    'redirect' => route('checkout.confirm', $order),
                ];
            } else {
                // Payment failed
                $errorMessage = $result['errorMessage'] ?? 'Ödeme işlemi başarısız.';
                
                Log::error('Iyzico Payment Error', [
                    'order_id' => $order->id,
                    'response' => $result,
                ]);

                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Iyzico Payment Exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ödeme işlemi sırasında bir hata oluştu.',
            ];
        }
    }

    /**
     * Prepare basket items for Iyzico
     */
    private static function prepareBasketItems(Order $order)
    {
        $items = [];
        
        foreach ($order->items as $item) {
            $categoryName = 'Genel';
            if ($item->product && $item->product->category) {
                $categoryName = $item->product->category->name;
            }
            
            $items[] = [
                'id' => $item->product_id,
                'name' => $item->product_name,
                'category1' => $categoryName,
                'itemType' => 'PHYSICAL',
                'price' => number_format($item->price, 2, '.', ''),
            ];
        }

        // Add shipping cost as item
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'name' => 'Kargo',
                'category1' => 'Kargo',
                'itemType' => 'PHYSICAL',
                'price' => number_format($order->shipping_cost, 2, '.', ''),
            ];
        }

        return $items;
    }

    /**
     * Handle Iyzico callback
     */
    public static function handleCallback($callbackData)
    {
        $conversationId = $callbackData['conversationId'] ?? null;
        
        if (!$conversationId) {
            return false;
        }

        $order = Order::where('order_number', $conversationId)->first();

        if (!$order) {
            return false;
        }

        if (isset($callbackData['status']) && $callbackData['status'] === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'payment_transaction_id' => $callbackData['paymentId'] ?? null,
            ]);

            return true;
        }

        return false;
    }
}

