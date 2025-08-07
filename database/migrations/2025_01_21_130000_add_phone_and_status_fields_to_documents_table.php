<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавляем поля для отправки по телефону и улучшенной логики
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Поля для отправки по номеру телефона
            $table->string('recipient_phone')->nullable()->after('recipient_id')->comment('Номер телефона получателя');
            $table->string('recipient_name')->nullable()->after('recipient_phone')->comment('Имя получателя');
            $table->string('recipient_email')->nullable()->after('recipient_name')->comment('Email получателя');
            
            // Дополнительные поля для работы с документами
            $table->boolean('can_be_deleted')->default(true)->after('signature_status')->comment('Можно ли удалить документ');
            $table->text('rejection_reason')->nullable()->after('can_be_deleted')->comment('Причина отказа от подписи');
            $table->timestamp('rejected_at')->nullable()->after('rejection_reason')->comment('Дата отказа');
            $table->json('delivery_status')->nullable()->after('rejected_at')->comment('Статус доставки уведомлений');
            
            // Поля для версионирования
            $table->integer('version')->default(1)->after('delivery_status')->comment('Версия документа');
            $table->unsignedBigInteger('parent_document_id')->nullable()->after('version')->comment('ID родительского документа');
            
            // Индексы
            $table->index('recipient_phone');
            $table->index('can_be_deleted');
            $table->index('parent_document_id');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['recipient_phone']);
            $table->dropIndex(['can_be_deleted']);
            $table->dropIndex(['parent_document_id']);
            
            $table->dropColumn([
                'recipient_phone',
                'recipient_name', 
                'recipient_email',
                'can_be_deleted',
                'rejection_reason',
                'rejected_at',
                'delivery_status',
                'version',
                'parent_document_id'
            ]);
        });
    }
};
