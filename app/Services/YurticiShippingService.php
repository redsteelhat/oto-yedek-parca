<?php

namespace App\Services;

use App\Models\ShippingCompany;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YurticiShippingService
{
    /**
     * Get shipping cost from Yurtiçi Kargo API
     */
    public static function getShippingCost(ShippingCompany $company, Order $order)
    {
        // Yurtiçi Kargo API implementation
        // This is a placeholder - actual implementation depends on Yurtiçi Kargo API documentation
        
        try {
            // Example API call structure
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $company->api_key,
                'Content-Type' => 'application/json',
            ])->post($company->api_url . '/calculate-price', [
                'from_city' => $company->api_config['from_city'] ?? 'İstanbul',
                'to_city' => $order->shipping_city,
                'weight' => self::calculateWeight($order),
                'desi' => self::calculateDesi($order),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['price'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Yurtiçi Kargo API Error', [
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
            ])->post($company->api_url . '/create-label', [
                'order_number' => $order->order_number,
                'receiver_name' => $order->shipping_name,
                'receiver_phone' => $order->shipping_phone,
                'receiver_address' => $order->shipping_address,
                'receiver_city' => $order->shipping_city,
                'receiver_district' => $order->shipping_district,
                'weight' => self::calculateWeight($order),
                'desi' => self::calculateDesi($order),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'tracking_number' => $data['tracking_number'] ?? null,
                    'label_url' => $data['label_url'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Yurtiçi Kargo Label Creation Error', [
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
            ])->get($company->api_url . '/track/' . $trackingNumber);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Yurtiçi Kargo Track Error', [
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
            $weight += $item->quantity * 1; // Assuming 1kg per product
        }
        return $weight;
    }

    /**
     * Calculate order desi (volume)
     */
    private static function calculateDesi(Order $order)
    {
        // Simple calculation: estimate based on quantity
        $desi = 0;
        foreach ($order->items as $item) {
            $desi += $item->quantity * 1; // Assuming 1 desi per product
        }
        return $desi;
    }
}

