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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name')->nullable(); // Misafir yorumları için
            $table->string('email')->nullable(); // Misafir yorumları için
            $table->integer('rating')->default(5); // 1-5 arası yıldız puanı
            $table->string('title')->nullable(); // Yorum başlığı
            $table->text('comment'); // Yorum içeriği
            $table->boolean('is_approved')->default(false); // Admin onayı
            $table->boolean('is_verified_purchase')->default(false); // Doğrulanmış satın alma
            $table->timestamps();
            
            $table->index(['product_id', 'is_approved']);
            $table->index(['user_id']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};

