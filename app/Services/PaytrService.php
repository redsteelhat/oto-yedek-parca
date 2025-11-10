<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaytrService
{
    /**
     * Process payment with PayTR
     */
    public static function processPayment(Order $order, $paymentData)
    {
        $merchantId = Setting::getValue('payment_paytr_merchant_id');
        $merchantKey = Setting::getValue('payment_paytr_merchant_key');
        $merchantSalt = Setting::getValue('payment_paytr_merchant_salt');

        if (!$merchantId || !$merchantKey || !$merchantSalt) {
            throw new \Exception('PayTR API bilgileri eksik.');
        }

        // Prepare payment data
        $merchantOid = $order->order_number;
        $email = $order->user->email;
        $paymentAmount = (int)($order->total * 100); // Convert to kuruş (cents)
        $currency = 'TL';
        $testMode = Setting::getValue('payment_paytr_test_mode', false);

        // Create hash
        $hashStr = $merchantId . $merchantOid . $email . $paymentAmount . $currency . $testMode;
        $hash = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

        $paymentData = [
            'merchant_id' => $merchantId,
            'merchant_oid' => $merchantOid,
            'email' => $email,
            'payment_amount' => $paymentAmount,
            'currency' => $currency,
            'test_mode' => $testMode ? '1' : '0',
            'user_name' => $order->shipping_name,
            'user_address' => $order->shipping_address,
            'user_phone' => $order->shipping_phone,
            'user_basket' => base64_encode(json_encode(self::prepareBasketItems($order))),
            'user_ip' => request()->ip(),
            'hash' => $hash,
            'callback_url' => route('payment.paytr.callback'),
            'fail_url' => route('payment.fail'),
            'success_url' => route('payment.success', $order),
        ];

        try {
            // Make API request to PayTR
            $response = Http::asForm()->post('https://www.paytr.com/odeme/api/get-token', $paymentData);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === 'success') {
                // Return iframe token for payment
                return [
                    'success' => true,
                    'token' => $result['token'],
                    'redirect' => route('payment.paytr.form', ['token' => $result['token']]),
                ];
            } else {
                $errorMessage = $result['reason'] ?? 'Ödeme işlemi başarısız.';
                
                Log::error('PayTR Payment Error', [
                    'order_id' => $order->id,
                    'response' => $result,
                ]);

                return [
                    'success' => false,
                    'message' => $errorMessage,
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayTR Payment Exception', [
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
     * Prepare basket items for PayTR
     */
    private static function prepareBasketItems(Order $order)
    {
        $items = [];
        
        foreach ($order->items as $item) {
            $items[] = [
                $item->product_name,
                number_format($item->price, 2, '.', ''),
                $item->quantity,
            ];
        }

        // Add shipping cost as item
        if ($order->shipping_cost > 0) {
            $items[] = [
                'Kargo',
                number_format($order->shipping_cost, 2, '.', ''),
                1,
            ];
        }

        return $items;
    }

    /**
     * Handle PayTR callback
     */
    public static function handleCallback($callbackData)
    {
        $merchantKey = Setting::getValue('payment_paytr_merchant_key');
        $merchantSalt = Setting::getValue('payment_paytr_merchant_salt');

        $merchantOid = $callbackData['merchant_oid'] ?? null;
        $status = $callbackData['status'] ?? null;
        $totalAmount = $callbackData['total_amount'] ?? null;
        $hash = $callbackData['hash'] ?? null;

        // Verify hash
        $hashStr = $merchantOid . $merchantSalt . $status . $totalAmount;
        $calculatedHash = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

        if ($hash !== $calculatedHash) {
            Log::error('PayTR Callback Hash Mismatch', [
                'merchant_oid' => $merchantOid,
            ]);
            return false;
        }

        $order = Order::where('order_number', $merchantOid)->first();

        if (!$order) {
            return false;
        }

        if ($status === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'payment_transaction_id' => $callbackData['payment_id'] ?? null,
            ]);

            return true;
        }

        return false;
    }
}

