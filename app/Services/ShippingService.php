<?php

namespace App\Services;

use App\Models\ShippingCompany;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Calculate shipping cost for an order
     */
    public static function calculateShippingCost(ShippingCompany $company, $weight = 0, $volume = 0, $subtotal = 0)
    {
        // Use company's calculateShippingCost method
        return $company->calculateShippingCost($weight, $volume, $subtotal);
    }

    /**
     * Get shipping cost from API if available
     */
    public static function getShippingCostFromAPI(ShippingCompany $company, Order $order)
    {
        try {
            switch ($company->api_type) {
                case 'yurtici':
                    return YurticiShippingService::getShippingCost($company, $order);
                case 'aras':
                    return ArasShippingService::getShippingCost($company, $order);
                case 'mng':
                    return MngShippingService::getShippingCost($company, $order);
                case 'surat':
                    return SuratShippingService::getShippingCost($company, $order);
                default:
                    // Fallback to manual calculation
                    return self::calculateShippingCost($company, self::calculateOrderWeight($order), 0, $order->subtotal);
            }
        } catch (\Exception $e) {
            Log::error('Shipping API Error', [
                'company' => $company->name,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback to manual calculation
            return self::calculateShippingCost($company, self::calculateOrderWeight($order), 0, $order->subtotal);
        }
    }

    /**
     * Create shipping label and get tracking number
     */
    public static function createShippingLabel(ShippingCompany $company, Order $order)
    {
        try {
            switch ($company->api_type) {
                case 'yurtici':
                    return YurticiShippingService::createLabel($company, $order);
                case 'aras':
                    return ArasShippingService::createLabel($company, $order);
                case 'mng':
                    return MngShippingService::createLabel($company, $order);
                case 'surat':
                    return SuratShippingService::createLabel($company, $order);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error('Shipping Label Creation Error', [
                'company' => $company->name,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Track shipping status
     */
    public static function trackShipping(ShippingCompany $company, $trackingNumber)
    {
        try {
            switch ($company->api_type) {
                case 'yurtici':
                    return YurticiShippingService::track($company, $trackingNumber);
                case 'aras':
                    return ArasShippingService::track($company, $trackingNumber);
                case 'mng':
                    return MngShippingService::track($company, $trackingNumber);
                case 'surat':
                    return SuratShippingService::track($company, $trackingNumber);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error('Shipping Track Error', [
                'company' => $company->name,
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Calculate order weight
     */
    private static function calculateOrderWeight(Order $order)
    {
        $weight = 0;
        foreach ($order->items as $item) {
            // Assuming 1kg per product (you can add weight field to products later)
            $weight += $item->quantity * 1;
        }
        return $weight;
    }
}

