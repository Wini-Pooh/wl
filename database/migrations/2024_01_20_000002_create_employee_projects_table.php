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
        Schema::create('employee_projects', function (Blueprint $table) {
            $table->id();
            
            // Связи
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // Дополнительная информация о роли сотрудника в проекте
            $table->string('role_in_project')->nullable()
                  ->comment('Роль сотрудника в конкретном проекте');
            $table->text('responsibilities')->nullable()
                  ->comment('Обязанности сотрудника в данном проекте');
            
            // Временные рамки участия в проекте
            $table->date('start_date')->nullable()
                  ->comment('Дата начала работы над проектом');
            $table->date('end_date')->nullable()
                  ->comment('Дата окончания работы над проектом');
            
            // Статус участия
            $table->enum('status', ['active', 'completed', 'suspended'])
                  ->default('active')
                  ->comment('Статус участия: активен, завершен, приостановлен');
            
            $table->timestamps();
            
            // Индексы и ограничения
            $table->unique(['employee_id', 'project_id'], 'employee_project_unique');
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_projects');
    }
};
