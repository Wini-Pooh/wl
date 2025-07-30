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
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('document_type');
            $table->foreignId('template_id')->nullable()->constrained('document_templates')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->string('recipient_type')->nullable(); // user, client, external
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('draft');
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('original_name')->nullable();
            $table->boolean('is_template')->default(false);
            $table->json('template_data')->nullable();
            $table->boolean('signature_required')->default(false);
            $table->string('signature_status')->default('not_required');
            $table->json('signature_data')->nullable();
            $table->text('digital_signature')->nullable();
            $table->text('signature_certificate')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Индексы
            $table->index(['document_type', 'status']);
            $table->index(['sender_id', 'status']);
            $table->index(['recipient_type', 'recipient_id']);
            $table->index('signature_status');
            $table->index('is_template');
            $table->index('expires_at');
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
