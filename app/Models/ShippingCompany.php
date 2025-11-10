<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'api_type',
        'api_url',
        'api_key',
        'api_secret',
        'api_username',
        'api_password',
        'api_config',
        'base_price',
        'price_per_kg',
        'price_per_cm3',
        'free_shipping_threshold',
        'estimated_days',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'api_config' => 'array',
        'base_price' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'price_per_cm3' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'estimated_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Calculate shipping cost
     */
    public function calculateShippingCost($weight = 0, $volume = 0, $subtotal = 0)
    {
        // Free shipping threshold check
        if ($this->free_shipping_threshold && $subtotal >= $this->free_shipping_threshold) {
            return 0;
        }

        $cost = $this->base_price;

        // Add weight-based cost
        if ($weight > 0 && $this->price_per_kg > 0) {
            $cost += $weight * $this->price_per_kg;
        }

        // Add volume-based cost
        if ($volume > 0 && $this->price_per_cm3 > 0) {
            $cost += $volume * $this->price_per_cm3;
        }

        return max(0, $cost);
    }

    /**
     * Get available shipping companies
     */
    public static function getActive()
    {
        return self::where('is_active', true)->orderBy('sort_order')->get();
    }
}
