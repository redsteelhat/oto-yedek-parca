<?php

namespace App\Services;

use App\Models\ShippingCompany;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuratShippingService
{
    /**
     * Get shipping cost from Sürat Kargo API
     */
    public static function getShippingCost(ShippingCompany $company, Order $order)
    {
        // Sürat Kargo API implementation
        // This is a placeholder - actual implementation depends on Sürat Kargo API documentation
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $company->api_key,
                'Content-Type' => 'application/json',
            ])->post($company->api_url . '/calculate-shipping', [
                'origin' => $company->api_config['from_city'] ?? 'İstanbul',
                'destination' => $order->shipping_city,
                'weight' => self::calculateWeight($order),
                'desi' => self::calculateDesi($order),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['shipping_cost'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Sürat Kargo API Error', [
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
                'order_no' => $order->order_number,
                'receiver' => [
                    'name' => $order->shipping_name,
                    'mobile' => $order->shipping_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'town' => $order->shipping_district,
                ],
                'weight' => self::calculateWeight($order),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'tracking_number' => $data['tracking_no'] ?? null,
                    'label_url' => $data['label'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Sürat Kargo Label Creation Error', [
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
            Log::error('Sürat Kargo Track Error', [
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
     * Calculate order desi (volume)
     */
    private static function calculateDesi(Order $order)
    {
        $desi = 0;
        foreach ($order->items as $item) {
            $desi += $item->quantity * 1;
        }
        return $desi;
    }
}

