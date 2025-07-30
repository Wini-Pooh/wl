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
        Schema::create('project_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Название события
            $table->text('description')->nullable(); // Описание события
            $table->enum('type', ['meeting', 'delivery', 'inspection', 'milestone', 'other'])->default('other');
            $table->date('event_date'); // Дата события
            $table->time('event_time')->nullable(); // Время события
            $table->enum('status', ['planned', 'completed', 'cancelled'])->default('planned');
            $table->string('location')->nullable(); // Место проведения
            $table->text('notes')->nullable(); // Заметки
            $table->timestamps();
            
            // Индексы
            $table->index('project_id');
            $table->index('event_date');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_events');
    }
};
