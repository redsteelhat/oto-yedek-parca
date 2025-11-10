<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarYear extends Model
{
    use HasFactory;

    protected $table = 'cars_years';

    protected $fillable = [
        'model_id',
        'year',
        'motor_type',
        'engine_code',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
    ];

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_car_compatibility', 'car_year_id', 'product_id');
    }
}
