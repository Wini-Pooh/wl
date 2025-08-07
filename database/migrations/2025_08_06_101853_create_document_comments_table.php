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
        Schema::create('document_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('document_comments')->onDelete('cascade');
            $table->text('content'); // Содержание комментария
            $table->boolean('is_internal')->default(true); // Внутренний комментарий или для клиента
            $table->string('status')->default('active'); // active, hidden, deleted
            $table->json('mentions')->nullable(); // Упоминания пользователей (@username)
            $table->json('attachments')->nullable(); // Вложения к комментарию
            $table->timestamps();
            
            // Индексы для оптимизации запросов
            $table->index('document_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('is_internal');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_comments');
    }
};
