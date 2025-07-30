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
        Schema::create('project_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Наименование транспортировки
            $table->decimal('amount', 12, 2)->default(0); // Сумма
            $table->decimal('paid_amount', 12, 2)->default(0); // Оплаченная сумма
            $table->date('payment_date')->nullable(); // Дата оплаты
            $table->text('description')->nullable(); // Описание
            $table->timestamps();
            
            // Индексы
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_transports');
    }
};
