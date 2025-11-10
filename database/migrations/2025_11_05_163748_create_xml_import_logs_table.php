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
        Schema::create('xml_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->integer('total_items')->default(0);
            $table->integer('imported_items')->default(0);
            $table->integer('updated_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->text('error_message')->nullable();
            $table->text('log_details')->nullable(); // JSON formatında detaylı log
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['supplier_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xml_import_logs');
    }
};
