<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Обновляем таблицу signature_requests для улучшенной работы
     */
    public function up(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            // Добавляем поля для отправки по телефону
            $table->string('recipient_phone')->nullable()->after('recipient_id')->comment('Номер телефона получателя');
            $table->string('recipient_name')->nullable()->after('recipient_phone')->comment('Имя получателя');
            
            // Улучшаем статусы
            $table->string('status')->default('pending')->change(); // pending, signed, rejected, expired, cancelled
            
            // Добавляем причину отказа
            $table->text('rejection_reason')->nullable()->after('status')->comment('Причина отказа от подписи');
            $table->timestamp('rejected_at')->nullable()->after('rejection_reason')->comment('Дата отказа');
            
            // Поля для уведомлений
            $table->json('notification_settings')->nullable()->after('rejected_at')->comment('Настройки уведомлений');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('notification_settings')->comment('Последнее напоминание');
            
            // Индексы
            $table->index('recipient_phone');
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropIndex(['recipient_phone']);
            
            $table->dropColumn([
                'recipient_phone',
                'recipient_name',
                'rejection_reason',
                'rejected_at',
                'notification_settings',
                'last_reminder_sent_at'
            ]);
        });
    }
};
