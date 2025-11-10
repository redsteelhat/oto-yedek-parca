<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_car_compatibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('car_year_id')->constrained('cars_years')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['product_id', 'car_year_id']);
            $table->index(['product_id']);
            $table->index(['car_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_car_compatibility');
    }
};
