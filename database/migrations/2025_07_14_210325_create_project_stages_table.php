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
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Название этапа
            $table->text('description')->nullable(); // Описание этапа
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started');
            $table->date('planned_start_date')->nullable(); // Планируемая дата начала
            $table->date('planned_end_date')->nullable(); // Планируемая дата окончания
            $table->date('actual_start_date')->nullable(); // Фактическая дата начала
            $table->date('actual_end_date')->nullable(); // Фактическая дата окончания
            $table->integer('duration_days')->nullable(); // Планируемая продолжительность в днях
            $table->integer('order')->default(0); // Порядок этапа
            $table->decimal('progress', 5, 2)->default(0); // Прогресс в процентах (0-100)
            $table->timestamps();
            
            // Индексы
            $table->index('project_id');
            $table->index('status');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stages');
    }
};
