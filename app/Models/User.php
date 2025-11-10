<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'tax_number',
        'user_type',
        'is_verified',
        'notes',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlist', 'user_id', 'product_id')
            ->withTimestamps();
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isDealer()
    {
        return $this->user_type === 'dealer';
    }

    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin()
    {
        return $this->user_type === 'admin' && $this->tenant_id === null;
    }

    /**
     * Get the tenant that owns the user.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Check if user has purchased a product.
     */
    public function hasPurchasedProduct($productId)
    {
        return $this->orders()
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->whereIn('status', ['delivered', 'shipped'])
            ->exists();
    }

    /**
     * Get the chat rooms for the user.
     */
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    /**
     * Get the chat messages sent by the user.
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the chat rooms assigned to the admin.
     */
    public function assignedChatRooms()
    {
        return $this->hasMany(ChatRoom::class, 'assigned_to');
    }
}
