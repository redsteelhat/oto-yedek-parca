<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'type',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'start_date',
        'end_date',
        'is_active',
        'applicable_products',
        'applicable_categories',
        'sort_order',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
    ];

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        return $now->gte($this->start_date) && $now->lte($this->end_date);
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isActive()) {
            return 0;
        }

        if ($this->min_purchase_amount && $amount < $this->min_purchase_amount) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }

        return min($this->discount_value, $amount);
    }
}
