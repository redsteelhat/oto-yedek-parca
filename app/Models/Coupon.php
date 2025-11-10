<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
        'applicable_categories',
        'applicable_products',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'used_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isActive()) {
            return 0;
        }

        if ($this->min_purchase_amount && $amount < $this->min_purchase_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $amount);
    }

    public function canBeUsedByUser($userId = null)
    {
        if (!$this->isActive()) {
            return false;
        }

        if (!$userId) {
            return true; // Guest users can use if active
        }

        $userUsage = \App\Models\Order::where('user_id', $userId)
            ->where('coupon_id', $this->id)
            ->count();

        return $userUsage < $this->usage_limit_per_user;
    }

    public function getRemainingUsageForUser($userId = null)
    {
        if (!$userId) {
            return $this->usage_limit_per_user;
        }

        $userUsage = \App\Models\Order::where('user_id', $userId)
            ->where('coupon_id', $this->id)
            ->count();

        return max(0, $this->usage_limit_per_user - $userUsage);
    }
}
