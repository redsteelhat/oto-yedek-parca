<?php

namespace App\Services;

use App\Models\ShippingCompany;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MngShippingService
{
    /**
     * Get shipping cost from MNG Kargo API
     */
    public static function getShippingCost(ShippingCompany $company, Order $order)
    {
        // MNG Kargo API implementation
        // This is a placeholder - actual implementation depends on MNG Kargo API documentation
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $company->api_key,
                'Content-Type' => 'application/json',
            ])->post($company->api_url . '/price-calculation', [
                'from' => $company->api_config['from_city'] ?? 'İstanbul',
                'to' => $order->shipping_city,
                'weight' => self::calculateWeight($order),
                'volume' => self::calculateVolume($order),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['price'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('MNG Kargo API Error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create shipping label and get tracking number
     */
    public static function createLabel(ShippingCompany $company, Order $order)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $company->api_key,
                'Content-Type' => 'application/json',
            ])->post($company->api_url . '/create-shipment', [
                'order_id' => $order->order_number,
                'recipient' => [
                    'name' => $order->shipping_name,
                    'phone' => $order->shipping_phone,
                    'address' => [
                        'line1' => $order->shipping_address,
                        'city' => $order->shipping_city,
                        'district' => $order->shipping_district,
                    ],
                ],
                'weight' => self::calculateWeight($order),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'tracking_number' => $data['tracking_code'] ?? null,
                    'label_url' => $data['label'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('MNG Kargo Label Creation Error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Track shipping status
     */
    public static function track(ShippingCompany $company, $trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $company->api_key,
            ])->get($company->api_url . '/shipment-status/' . $trackingNumber);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('MNG Kargo Track Error', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Calculate order weight
     */
    private static function calculateWeight(Order $order)
    {
        $weight = 0;
        foreach ($order->items as $item) {
            $weight += $item->quantity * 1;
        }
        return $weight;
    }

    /**
     * Calculate order volume
     */
    private static function calculateVolume(Order $order)
    {
        $volume = 0;
        foreach ($order->items as $item) {
            $volume += $item->quantity * 1; // Assuming 1 m³ per product
        }
        return $volume;
    }
}

