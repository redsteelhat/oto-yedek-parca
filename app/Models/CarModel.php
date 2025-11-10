<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CarModel extends Model
{
    use HasFactory;

    protected $table = 'cars_models';

    protected $fillable = [
        'brand_id',
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

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(CarBrand::class, 'brand_id');
    }

    public function years()
    {
        return $this->hasMany(CarYear::class, 'model_id');
    }

    public function activeYears()
    {
        return $this->hasMany(CarYear::class, 'model_id')->where('is_active', true);
    }
}
