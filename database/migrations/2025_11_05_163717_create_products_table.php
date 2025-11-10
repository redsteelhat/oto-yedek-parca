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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('sku')->unique();
            $table->string('oem_code')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->string('manufacturer')->nullable();
            $table->enum('part_type', ['oem', 'aftermarket'])->default('aftermarket');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('views')->default(0);
            $table->integer('sales_count')->default(0);
            $table->timestamps();
            
            $table->index(['category_id', 'status']);
            $table->index(['supplier_id']);
            $table->index(['sku']);
            $table->index(['oem_code']);
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
