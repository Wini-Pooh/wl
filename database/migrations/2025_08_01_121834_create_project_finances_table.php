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
        Schema::create('project_finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense'])->comment('Тип: доходы или расходы');
            $table->string('category')->comment('Категория: employee, materials, contractor, supplier, other');
            $table->string('title')->comment('Название записи');
            $table->decimal('amount', 12, 2)->comment('Сумма');
            $table->date('operation_date')->comment('Дата операции');
            $table->text('description')->nullable()->comment('Описание');
            $table->enum('status', ['planned', 'paid', 'overdue'])->default('planned')->comment('Статус');
            $table->string('contractor')->nullable()->comment('Подрядчик/исполнитель');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card'])->nullable()->comment('Способ оплаты');
            $table->boolean('is_planned')->default(false)->comment('Запланированная операция');
            $table->text('notes')->nullable()->comment('Заметки');
            $table->timestamps();

            // Индексы
            $table->index(['project_id', 'type']);
            $table->index(['project_id', 'status']);
            $table->index(['operation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_finances');
    }
};
