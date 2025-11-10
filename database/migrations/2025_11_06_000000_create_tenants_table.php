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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tenant adı
            $table->string('slug')->unique(); // URL slug
            $table->string('subdomain')->unique(); // Subdomain (örn: tenant1)
            $table->string('domain')->nullable()->unique(); // Custom domain (örn: tenant1.com)
            $table->string('email')->nullable(); // Tenant iletişim emaili
            $table->string('phone')->nullable(); // Tenant telefon
            $table->string('logo')->nullable(); // Tenant logosu
            $table->string('favicon')->nullable(); // Tenant favicon
            $table->string('primary_color')->default('#3B82F6'); // Ana renk
            $table->string('secondary_color')->default('#1E40AF'); // İkincil renk
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active');
            $table->enum('subscription_plan', ['free', 'basic', 'premium', 'enterprise'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable(); // Abonelik bitiş tarihi
            $table->integer('max_products')->nullable(); // Maksimum ürün sayısı
            $table->integer('max_users')->nullable(); // Maksimum kullanıcı sayısı
            $table->json('settings')->nullable(); // Tenant-specific ayarlar (JSON)
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('subdomain');
            $table->index('status');
            $table->index('subscription_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};



