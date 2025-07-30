<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Очищаем таблицу ролей перед заполнением
        DB::table('roles')->truncate();
        
        // Создаем роли
        Role::create([
            'name' => 'admin',
            'description' => 'Администратор с полным доступом'
        ]);
        
        Role::create([
            'name' => 'client',
            'description' => 'Клиент (пользователь по умолчанию)'
        ]);
        
        Role::create([
            'name' => 'partner',
            'description' => 'Партнер сервиса'
        ]);
    }
}
