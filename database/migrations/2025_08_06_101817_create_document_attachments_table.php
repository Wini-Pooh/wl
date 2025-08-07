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
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('filename'); // Имя файла в системе
            $table->string('original_name'); // Оригинальное имя файла
            $table->string('path'); // Путь к файлу
            $table->bigInteger('size')->default(0); // Размер файла в байтах
            $table->string('mime_type'); // MIME тип файла
            $table->string('file_hash')->nullable(); // Хеш файла для проверки целостности
            $table->json('metadata')->nullable(); // Дополнительные метаданные (размеры изображения, длительность видео и т.д.)
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Индексы для оптимизации запросов
            $table->index('document_id');
            $table->index('mime_type');
            $table->index('uploaded_by');
            $table->index('file_hash');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_attachments');
    }
};
