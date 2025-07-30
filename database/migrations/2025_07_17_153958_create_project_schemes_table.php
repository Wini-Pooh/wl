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
        Schema::create('project_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Название файла в системе
            $table->string('original_name'); // Оригинальное название файла
            $table->string('file_path'); // Путь к файлу в storage
            $table->bigInteger('file_size'); // Размер файла в байтах
            $table->string('mime_type'); // MIME-тип файла
            $table->string('scheme_type')->default('technical'); // Тип схемы
            $table->string('room')->nullable(); // Помещение
            $table->string('system')->nullable(); // Система (electrical, plumbing, etc.)
            $table->string('scale')->nullable(); // Масштаб
            $table->string('revision')->default('1.0'); // Версия/ревизия
            $table->string('engineer')->nullable(); // Инженер
            $table->string('software')->nullable(); // ПО для создания
            $table->text('description')->nullable(); // Описание
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index('project_id');
            $table->index('scheme_type');
            $table->index('room');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_schemes');
    }
};
