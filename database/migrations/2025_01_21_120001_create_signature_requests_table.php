<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы запросов на подпись
     */
    public function up(): void
    {
        Schema::create('signature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('project_documents')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Кто отправил
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade'); // Кому отправлено
            $table->string('status')->default('pending'); // pending, completed, cancelled, expired
            $table->string('signature_type')->default('simple'); // simple, enhanced, qualified
            $table->text('message')->nullable(); // Сообщение для подписанта
            $table->timestamp('requested_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('permissions')->nullable(); // Права доступа
            $table->timestamps();
            
            // Индексы
            $table->index(['recipient_id', 'status']);
            $table->index('expires_at');
            $table->index('requested_at');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_requests');
    }
};
