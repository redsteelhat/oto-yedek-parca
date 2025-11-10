<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CarBrand extends Model
{
    use HasFactory;

    protected $table = 'cars_brands';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function models()
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }

    public function activeModels()
    {
        return $this->hasMany(CarModel::class, 'brand_id')->where('is_active', true);
    }
}
