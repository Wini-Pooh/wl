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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Название тарифа
            $table->string('slug')->unique(); // Слаг для URL
            $table->text('description')->nullable(); // Описание тарифа
            
            // Лимиты и ограничения
            $table->integer('max_active_projects')->default(3); // Максимальное количество активных объектов
            $table->integer('project_storage_limit_mb')->default(300); // Лимит хранилища на объект в МБ
            $table->integer('max_estimate_templates')->default(3); // Максимальное количество шаблонов смет
            $table->integer('max_employees')->default(10); // Максимальное количество сотрудников
            $table->integer('max_right_hand_employees')->default(1); // Максимальное количество сотрудников "правая рука"
            
            // Доступ к функционалу
            $table->boolean('access_estimates')->default(true); // Доступ к сметам
            $table->boolean('access_documents')->default(true); // Доступ к документам
            $table->boolean('access_projects')->default(true); // Доступ к объектам
            $table->boolean('access_analytics')->default(true); // Доступ к аналитике
            $table->boolean('access_employees')->default(true); // Доступ к сотрудникам
            $table->boolean('access_online_training')->default(true); // Доступ к онлайн обучению
            
            // Цена и активность
            $table->decimal('monthly_price', 10, 2); // Цена за месяц
            $table->decimal('yearly_price', 10, 2); // Цена за год
            $table->decimal('yearly_discount_percent', 5, 2)->default(0); // Скидка за годовую подписку в %
            $table->boolean('is_active')->default(true); // Активен ли тариф
            $table->integer('sort_order')->default(0); // Порядок сортировки
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
