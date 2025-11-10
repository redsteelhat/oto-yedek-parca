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
        Schema::create('cars_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('cars_models')->onDelete('cascade');
            $table->year('year');
            $table->string('motor_type')->nullable(); // 1.2 TSI, 1.6 TDI vb.
            $table->string('engine_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['model_id', 'year']);
            $table->unique(['model_id', 'year', 'motor_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars_years');
    }
};
