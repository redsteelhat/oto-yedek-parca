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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name')->nullable(); // Misafir kullanıcılar için
            $table->string('email')->nullable(); // Misafir kullanıcılar için
            $table->string('phone')->nullable(); // Misafir kullanıcılar için
            $table->string('subject')->nullable(); // Konu
            $table->enum('status', ['open', 'closed', 'pending'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Admin kullanıcı
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_count_user')->default(0); // Kullanıcı için okunmamış mesaj sayısı
            $table->integer('unread_count_admin')->default(0); // Admin için okunmamış mesaj sayısı
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};

