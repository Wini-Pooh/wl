<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Добавляем роли сотрудника, прораба и сметчика если их еще нет
        $roles = [
            [
                'name' => 'employee',
                'display_name' => 'Сотрудник',
                'description' => 'Сотрудник партнера с доступом к проектам и сметам'
            ],
            [
                'name' => 'foreman',
                'display_name' => 'Прораб',
                'description' => 'Прораб с полным доступом к объектам и сметам, как у управляющего'
            ],
            [
                'name' => 'estimator', 
                'display_name' => 'Сметчик',
                'description' => 'Сметчик с доступом только к сметам'
            ]
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description']
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем добавленные роли
        Role::whereIn('name', ['employee', 'foreman', 'estimator'])->delete();
    }
};
