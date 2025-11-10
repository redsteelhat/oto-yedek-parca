<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Order;

class PaymentService
{
    /**
     * Get enabled payment methods
     */
    public static function getEnabledMethods()
    {
        $methods = [];

        if (Setting::getValue('payment_iyzico_enabled', false)) {
            $methods['credit_card'] = [
                'name' => 'Kredi Kartı (İyzico)',
                'gateway' => 'iyzico',
            ];
        }

        if (Setting::getValue('payment_paytr_enabled', false)) {
            $methods['credit_card'] = [
                'name' => 'Kredi Kartı (PayTR)',
                'gateway' => 'paytr',
            ];
        }

        if (Setting::getValue('payment_bank_transfer_enabled', false)) {
            $methods['bank_transfer'] = [
                'name' => 'Havale/EFT',
                'gateway' => 'bank_transfer',
            ];
        }

        if (Setting::getValue('payment_cash_on_delivery_enabled', false)) {
            $methods['cash_on_delivery'] = [
                'name' => 'Kapıda Ödeme',
                'gateway' => 'cash_on_delivery',
            ];
        }

        // Default methods if no settings
        if (empty($methods)) {
            $methods = [
                'credit_card' => [
                    'name' => 'Kredi Kartı',
                    'gateway' => 'iyzico',
                ],
                'bank_transfer' => [
                    'name' => 'Havale/EFT',
                    'gateway' => 'bank_transfer',
                ],
                'cash_on_delivery' => [
                    'name' => 'Kapıda Ödeme',
                    'gateway' => 'cash_on_delivery',
                ],
            ];
        }

        return $methods;
    }

    /**
     * Process payment based on method
     */
    public static function processPayment(Order $order, $paymentMethod, $paymentData = [])
    {
        switch ($paymentMethod) {
            case 'credit_card':
                return self::processCreditCard($order, $paymentData);
            case 'bank_transfer':
                return self::processBankTransfer($order);
            case 'cash_on_delivery':
                return self::processCashOnDelivery($order);
            default:
                throw new \Exception('Geçersiz ödeme yöntemi.');
        }
    }

    /**
     * Process credit card payment
     */
    private static function processCreditCard(Order $order, $paymentData)
    {
        // Check which gateway is enabled
        $iyzicoEnabled = Setting::getValue('payment_iyzico_enabled', false);
        $paytrEnabled = Setting::getValue('payment_paytr_enabled', false);

        if ($iyzicoEnabled) {
            return IyzicoService::processPayment($order, $paymentData);
        } elseif ($paytrEnabled) {
            return PaytrService::processPayment($order, $paymentData);
        }

        throw new \Exception('Kredi kartı ödeme gateway\'i aktif değil.');
    }

    /**
     * Process bank transfer
     */
    private static function processBankTransfer(Order $order)
    {
        // Bank transfer requires admin approval
        $order->update([
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        return [
            'success' => true,
            'message' => 'Havale/EFT ödemesi için banka hesap bilgileri gösterildi. Ödeme yapıldıktan sonra dekont yükleyebilirsiniz.',
            'redirect' => route('payment.bank-transfer.show', $order),
        ];
    }

    /**
     * Process cash on delivery
     */
    private static function processCashOnDelivery(Order $order)
    {
        $order->update([
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        return [
            'success' => true,
            'message' => 'Kapıda ödeme siparişi oluşturuldu.',
            'redirect' => route('checkout.confirm', $order),
        ];
    }
}

