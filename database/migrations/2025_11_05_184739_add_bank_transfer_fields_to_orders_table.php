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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('bank_transfer_receipt')->nullable()->after('payment_transaction_id');
            $table->timestamp('bank_transfer_receipt_uploaded_at')->nullable()->after('bank_transfer_receipt');
            $table->text('bank_transfer_notes')->nullable()->after('bank_transfer_receipt_uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['bank_transfer_receipt', 'bank_transfer_receipt_uploaded_at', 'bank_transfer_notes']);
        });
    }
};
