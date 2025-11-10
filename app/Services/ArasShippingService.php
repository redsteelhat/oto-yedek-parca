<?php

namespace App\Services;

use App\Models\ShippingCompany;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArasShippingService
{
    /**
     * Get shipping cost from Aras Kargo API
     */
    public static function getShippingCost(ShippingCompany $company, Order $order)
    {
        // Aras Kargo API implementation
        // This is a placeholder - actual implementation depends on Aras Kargo API documentation
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $company->api_key,
                'Content-Type' => 'application/json',
            ])->post($company->api_url . '/calculate', [
                'origin_city' => $company->api_config['from_city'] ?? 'İstanbul',
                'destination_city' => $order->shipping_city,
                'weight' => self::calculateWeight($order),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['amount'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Aras Kargo API Error', [
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
            ])->post($company->api_url . '/shipment', [
                'reference' => $order->order_number,
                'receiver' => [
                    'name' => $order->shipping_name,
                    'phone' => $order->shipping_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'district' => $order->shipping_district,
                ],
                'weight' => self::calculateWeight($order),
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
            Log::error('Aras Kargo Label Creation Error', [
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
            ])->get($company->api_url . '/tracking/' . $trackingNumber);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Aras Kargo Track Error', [
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
}

