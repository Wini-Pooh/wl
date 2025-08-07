<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы вложений документов
     */
    public function up(): void
    {
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('filename'); // Имя файла в системе
            $table->string('original_name')->nullable(); // Оригинальное имя файла
            $table->string('path'); // Путь к файлу
            $table->bigInteger('size')->default(0); // Размер файла в байтах
            $table->string('mime_type'); // MIME тип файла
            $table->string('file_hash')->nullable(); // Хеш файла для проверки целостности
            $table->json('metadata')->nullable(); // Дополнительные метаданные
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Индексы
            $table->index('document_id');
            $table->index('mime_type');
            $table->index('uploaded_by');
            $table->index('file_hash');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('document_attachments');
    }
};
