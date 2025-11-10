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
        Schema::create('shipping_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // yurtici, aras, mng, surat
            $table->string('api_type')->nullable(); // yurtici_api, aras_api, mng_api, surat_api, manual
            $table->text('api_url')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('api_username')->nullable();
            $table->string('api_password')->nullable();
            $table->json('api_config')->nullable(); // Ek API ayarları
            $table->decimal('base_price', 10, 2)->default(0); // Temel kargo ücreti
            $table->decimal('price_per_kg', 10, 2)->default(0); // Kilo başı ücret
            $table->decimal('price_per_cm3', 10, 2)->default(0); // Desi başı ücret
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // Ücretsiz kargo limiti
            $table->integer('estimated_days')->default(3); // Tahmini teslimat süresi
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_companies');
    }
};
