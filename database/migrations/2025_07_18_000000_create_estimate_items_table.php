<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('estimate_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->onDelete('cascade');
            $table->string('section')->nullable(); // Раздел сметы
            $table->string('name'); // Наименование позиции
            $table->text('description')->nullable(); // Описание
            $table->string('unit')->default('шт'); // Единица измерения
            $table->decimal('quantity', 10, 3)->default(1); // Количество
            $table->decimal('price', 12, 2)->default(0); // Цена за единицу
            $table->decimal('total_amount', 12, 2)->default(0); // Общая сумма
            $table->integer('order')->default(0); // Порядок сортировки
            $table->string('type')->default('main'); // Тип позиции: main, additional, material
            
            // Поля для учета платежей
            $table->boolean('is_paid')->default(false); // Полностью оплачено
            $table->decimal('paid_amount', 12, 2)->default(0); // Оплаченная сумма
            $table->date('paid_date')->nullable(); // Дата оплаты
            $table->text('payment_notes')->nullable(); // Примечания к оплате
            
            $table->timestamps();
            
            // Индексы
            $table->index('estimate_id');
            $table->index('section');
            $table->index('type');
            $table->index('is_paid');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_items');
    }
};
