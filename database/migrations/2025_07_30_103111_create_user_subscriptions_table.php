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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID пользователя
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade'); // ID тарифного плана
            
            // Даты подписки
            $table->datetime('started_at'); // Дата начала подписки
            $table->datetime('expires_at'); // Дата окончания подписки
            $table->datetime('last_payment_at')->nullable(); // Дата последнего платежа
            $table->datetime('next_payment_at')->nullable(); // Дата следующего платежа
            
            // Статус подписки
            $table->enum('status', ['active', 'expired', 'cancelled', 'suspended'])->default('active');
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly'); // Период оплаты
            
            // Финансовые данные
            $table->decimal('paid_amount', 10, 2); // Сумма оплаты
            $table->string('payment_method')->nullable(); // Способ оплаты
            $table->string('transaction_id')->nullable(); // ID транзакции
            
            // Использование ресурсов (кэш для быстрых проверок)
            $table->integer('current_active_projects')->default(0); // Текущее количество активных проектов
            $table->integer('current_employees')->default(0); // Текущее количество сотрудников
            $table->integer('current_right_hand_employees')->default(0); // Текущее количество "правых рук"
            $table->integer('current_estimate_templates')->default(0); // Текущее количество шаблонов смет
            
            // Служебные поля
            $table->boolean('auto_renewal')->default(true); // Автопродление
            $table->text('notes')->nullable(); // Заметки
            
            $table->timestamps();
            
            // Индексы
            $table->index(['user_id', 'status']);
            $table->index(['expires_at', 'status']);
            $table->index('next_payment_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
