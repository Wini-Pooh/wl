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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('name'); // Имя файла в системе
            $table->string('original_name'); // Оригинальное имя файла
            $table->string('file_path'); // Путь к файлу в storage
            $table->bigInteger('file_size'); // Размер файла в байтах
            $table->string('mime_type'); // MIME-тип файла
            $table->string('document_type')->default('contract')->index(); // Тип документа
            $table->enum('importance', ['normal', 'high', 'urgent'])->default('normal'); // Важность
            $table->string('category')->nullable()->index(); // Категория документа
            $table->string('version')->default('1.0'); // Версия документа
            $table->date('document_date')->nullable(); // Дата документа
            $table->string('author')->nullable(); // Автор документа
            $table->boolean('is_signed')->default(false)->index(); // Подписан ли документ
            $table->text('description')->nullable(); // Описание документа
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Кто загрузил
            $table->boolean('requires_signature')->default(false); // Требует подписи
            $table->string('signature_status')->default('unsigned'); // Статус подписи
            $table->integer('required_signatures_count')->default(0); // Требуемое количество подписей
            $table->integer('received_signatures_count')->default(0); // Полученное количество подписей
            $table->timestamp('signature_deadline')->nullable(); // Срок подписания
            $table->json('signature_settings')->nullable(); // Настройки подписи
            $table->boolean('is_template_generated')->default(false); // Создан из шаблона
            $table->string('template_type')->nullable(); // Тип шаблона
            $table->json('template_variables')->nullable(); // Переменные шаблона
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index('project_id');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
