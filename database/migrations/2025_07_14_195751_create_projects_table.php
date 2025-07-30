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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // Обязательные поля
            $table->string('client_first_name');
            $table->string('client_last_name');
            $table->string('client_phone');
            $table->string('object_type');
            $table->string('work_type');
            $table->string('project_status');
            
            // Паспортные данные
            $table->string('passport_series')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_issued_by')->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->string('passport_department_code')->nullable();
            
            // Личные данные
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('client_email')->nullable();
            
            // Адрес прописки
            $table->string('registration_postal_code')->nullable();
            $table->string('registration_city')->nullable();
            $table->string('registration_street')->nullable();
            $table->string('registration_house')->nullable();
            $table->string('registration_apartment')->nullable();
            
            // Характеристики объекта
            $table->string('apartment_number')->nullable();
            $table->string('object_city')->nullable();
            $table->string('object_street')->nullable();
            $table->string('object_house')->nullable();
            $table->string('object_entrance')->nullable();
            $table->decimal('object_area', 8, 2)->nullable();
            $table->string('camera_link')->nullable();
            
            // Финансовые показатели
            $table->decimal('work_cost', 12, 2)->default(0);
            $table->decimal('materials_cost', 12, 2)->default(0);
            $table->decimal('additional_work_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            
            // Временные рамки
            $table->date('contract_date')->nullable();
            $table->date('work_start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->string('contract_number')->nullable();
            
            // Связи
            $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Индексы
            $table->index('client_phone');
            $table->index('partner_id');
            $table->index('project_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
