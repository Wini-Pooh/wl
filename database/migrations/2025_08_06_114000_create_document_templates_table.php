<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы шаблонов документов
     */
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название шаблона
            $table->text('description')->nullable(); // Описание шаблона
            $table->string('document_type'); // Тип документа (contract, invoice, report, etc.)
            $table->string('category')->nullable(); // Категория шаблона
            $table->longText('content'); // Содержимое шаблона (HTML)
            $table->json('variables')->nullable(); // Переменные шаблона
            $table->json('default_values')->nullable(); // Значения по умолчанию
            $table->boolean('is_active')->default(true); // Активен ли шаблон
            $table->boolean('is_system')->default(false); // Системный шаблон
            $table->string('file_format')->default('html'); // html, pdf, docx, etc.
            $table->json('formatting_options')->nullable(); // Опции форматирования
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('usage_count')->default(0); // Количество использований
            $table->timestamps();
            
            // Индексы
            $table->index('document_type');
            $table->index('category');
            $table->index('is_active');
            $table->index('is_system');
            $table->index('created_by');
            $table->index('file_format');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
