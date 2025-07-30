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
        Schema::create('project_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('filename');  // Имя файла (UUID + расширение)
            $table->string('original_name');  // Оригинальное имя файла
            $table->string('path');  // Путь к файлу (projects/{project_id}/photos/{filename})
            $table->string('category')->nullable();  // Категория фотографии
            $table->text('comment')->nullable();  // Комментарий к фотографии
            $table->date('photo_date')->nullable();  // Дата фотографирования
            $table->unsignedBigInteger('file_size')->nullable();  // Размер файла
            $table->string('mime_type')->nullable();  // MIME-тип файла
            $table->string('file_hash')->nullable();  // MD5-хеш содержимого файла
            $table->timestamps();
            
            // Индексы для ускорения запросов
            $table->index('project_id');
            $table->index('category');
            $table->index('file_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_photos');
    }
};
