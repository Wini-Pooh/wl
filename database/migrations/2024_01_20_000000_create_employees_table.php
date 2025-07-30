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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            
            // Связь с партнером
            $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
            
            // Персональные данные
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            
            // Роль сотрудника
            $table->enum('role', ['foreman', 'subcontractor', 'estimator'])
                  ->comment('Роль: прораб, субподрядчик, сметчик');
            
            // Статус сотрудника
            $table->enum('status', ['active', 'inactive', 'fired'])
                  ->default('active')
                  ->comment('Статус: активен, неактивен, уволен');
            
            // Дополнительная информация
            $table->text('description')->nullable()
                  ->comment('Обязанности перед партнером');
            $table->text('notes')->nullable()
                  ->comment('Дополнительные заметки');
            
            $table->timestamps();
            
            // Индексы
            $table->index(['partner_id', 'role']);
            $table->index(['partner_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
