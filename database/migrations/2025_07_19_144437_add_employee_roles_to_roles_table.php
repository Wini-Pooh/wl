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
        // Добавляем роли для сотрудников, если их еще нет
        $roles = [
            ['name' => 'employee', 'description' => 'Сотрудник - доступ к проектам партнера'],
            ['name' => 'estimator', 'description' => 'Сметчик - доступ к проектам и сметам партнера'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем роли сотрудников
        \App\Models\Role::whereIn('name', ['employee', 'estimator'])->delete();
    }
};
