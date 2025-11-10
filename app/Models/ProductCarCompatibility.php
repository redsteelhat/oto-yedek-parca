<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCarCompatibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'car_year_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function carYear()
    {
        return $this->belongsTo(CarYear::class);
    }
}
