<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'domain',
        'email',
        'phone',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'status',
        'subscription_plan',
        'subscription_expires_at',
        'max_products',
        'max_users',
        'settings',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'settings' => 'array',
        'max_products' => 'integer',
        'max_users' => 'integer',
    ];

    /**
     * Get the users for the tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the categories for the tenant.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the products for the tenant.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders for the tenant.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the addresses for the tenant.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the coupons for the tenant.
     */
    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get the campaigns for the tenant.
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the suppliers for the tenant.
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * Get the shipping companies for the tenant.
     */
    public function shippingCompanies()
    {
        return $this->hasMany(ShippingCompany::class);
    }

    /**
     * Get the pages for the tenant.
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get the settings for the tenant.
     */
    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Get the chat rooms for the tenant.
     */
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    /**
     * Get the product reviews for the tenant.
     */
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Check if tenant is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant is suspended.
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if subscription is active.
     */
    public function hasActiveSubscription()
    {
        if ($this->subscription_plan === 'free') {
            return true;
        }

        if (!$this->subscription_expires_at) {
            return false;
        }

        return $this->subscription_expires_at->isFuture();
    }

    /**
     * Check if tenant can create more products.
     */
    public function canCreateProduct()
    {
        if (!$this->max_products) {
            return true; // No limit
        }

        return $this->products()->count() < $this->max_products;
    }

    /**
     * Check if tenant can create more users.
     */
    public function canCreateUser()
    {
        if (!$this->max_users) {
            return true; // No limit
        }

        return $this->users()->count() < $this->max_users;
    }

    /**
     * Get setting value.
     */
    public function getSetting($key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    /**
     * Set setting value.
     */
    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Scope to get active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get suspended tenants.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }
}



