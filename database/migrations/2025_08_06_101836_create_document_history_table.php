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
        Schema::create('document_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // created, updated, sent, viewed, signed, archived, etc.
            $table->text('description')->nullable(); // Описание действия
            $table->json('changes')->nullable(); // Изменения (old/new values)
            $table->string('ip_address')->nullable(); // IP адрес пользователя
            $table->string('user_agent')->nullable(); // User agent браузера
            $table->json('additional_data')->nullable(); // Дополнительные данные
            $table->timestamps();
            
            // Индексы для оптимизации запросов
            $table->index('document_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_history');
    }
};
