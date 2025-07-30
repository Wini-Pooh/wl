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
        Schema::create('employee_finances', function (Blueprint $table) {
            $table->id();
            
            // Связь с сотрудником
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Тип финансовой записи
            $table->enum('type', ['salary', 'bonus', 'penalty', 'expense', 'debt'])
                  ->comment('Тип: зарплата, премия, штраф, расход, долг');
            
            // Финансовые данные
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RUB');
            
            // Описание и статус
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])
                  ->default('pending')
                  ->comment('Статус: ожидает, выплачено, просрочено');
            
            // Даты
            $table->date('due_date')->comment('Дата к выплате');
            $table->date('paid_date')->nullable()->comment('Дата фактической выплаты');
            
            // Связь с проектом (если применимо)
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            
            // Индексы
            $table->index(['employee_id', 'type']);
            $table->index(['employee_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_finances');
    }
};
