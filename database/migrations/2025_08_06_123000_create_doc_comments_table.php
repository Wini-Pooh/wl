<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы комментариев к документам
     */
    public function up(): void
    {
        Schema::create('doc_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('docs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('doc_comments')->onDelete('cascade');
            $table->text('content'); // Содержание комментария
            $table->boolean('is_internal')->default(true); // Внутренний комментарий или для клиента
            $table->string('status')->default('active'); // active, hidden, deleted
            $table->json('mentions')->nullable(); // Упоминания пользователей
            $table->timestamps();
            
            // Индексы
            $table->index('doc_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('is_internal');
            $table->index('created_at');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_comments');
    }
};
