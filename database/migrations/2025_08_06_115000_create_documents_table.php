<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы документов
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Заголовок документа
            $table->text('description')->nullable(); // Описание документа
            $table->longText('content')->nullable(); // Содержание документа
            $table->string('document_type'); // Тип документа (contract, invoice, report, etc.)
            $table->string('category')->nullable(); // Категория документа
            
            // Связи
            $table->foreignId('template_id')->nullable()->constrained('document_templates')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Получатель документа
            $table->string('recipient_type')->nullable(); // user, client, external
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_email')->nullable(); // Для внешних получателей
            $table->string('recipient_name')->nullable(); // Для внешних получателей
            
            // Статусы и состояние
            $table->string('status')->default('draft'); // draft, sent, received, viewed, completed, archived
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->boolean('is_internal')->default(true); // Внутренний документ или внешний
            
            // Файловые данные
            $table->string('file_path')->nullable(); // Путь к основному файлу документа
            $table->string('original_filename')->nullable(); // Оригинальное имя файла
            $table->bigInteger('file_size')->nullable(); // Размер файла в байтах
            $table->string('mime_type')->nullable(); // MIME тип файла
            $table->string('file_hash')->nullable(); // Хеш файла
            
            // Подписи и безопасность
            $table->boolean('signature_required')->default(false);
            $table->string('signature_status')->default('not_required'); // not_required, pending, signed, rejected
            $table->json('signature_data')->nullable(); // Данные о подписях
            $table->text('digital_signature')->nullable(); // Цифровая подпись
            $table->text('signature_certificate')->nullable(); // Сертификат подписи
            
            // Версионирование
            $table->integer('version')->default(1); // Версия документа
            $table->foreignId('parent_id')->nullable()->constrained('documents')->onDelete('cascade'); // Родительский документ для версий
            $table->boolean('is_current_version')->default(true); // Текущая версия
            
            // Временные метки
            $table->timestamp('sent_at')->nullable(); // Дата отправки
            $table->timestamp('received_at')->nullable(); // Дата получения
            $table->timestamp('viewed_at')->nullable(); // Дата просмотра
            $table->timestamp('signed_at')->nullable(); // Дата подписания
            $table->timestamp('expires_at')->nullable(); // Дата истечения
            $table->timestamp('archived_at')->nullable(); // Дата архивирования
            
            // Дополнительные данные
            $table->json('metadata')->nullable(); // Дополнительные метаданные
            $table->json('template_variables')->nullable(); // Переменные шаблона
            $table->text('notes')->nullable(); // Заметки
            $table->decimal('amount', 12, 2)->nullable(); // Сумма (для финансовых документов)
            $table->string('currency', 3)->default('RUB'); // Валюта
            
            $table->timestamps();
            $table->softDeletes(); // Мягкое удаление
            
            // Индексы
            $table->index(['document_type', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['recipient_type', 'recipient_id']);
            $table->index(['project_id', 'status']);
            $table->index('signature_status');
            $table->index('priority');
            $table->index(['version', 'parent_id']);
            $table->index('is_current_version');
            $table->index('expires_at');
            $table->index('sent_at');
            $table->index('category');
            $table->index('file_hash');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
