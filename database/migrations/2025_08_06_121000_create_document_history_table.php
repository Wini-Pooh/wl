<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы истории документов
     */
    public function up(): void
    {
        Schema::create('document_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // created, updated, sent, received, viewed, signed, rejected, archived
            $table->text('description')->nullable(); // Описание действия
            $table->json('changes')->nullable(); // Изменения (old/new values)
            $table->string('ip_address')->nullable(); // IP адрес
            $table->string('user_agent')->nullable(); // User agent
            $table->timestamps();
            
            // Индексы
            $table->index('document_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('document_history');
    }
};
