<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Сначала создаем роли
        $this->call(RoleSeeder::class);
        
        // Получаем ID ролей
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $clientRole = \App\Models\Role::where('name', 'client')->first();
        $partnerRole = \App\Models\Role::where('name', 'partner')->first();
        
        // Создаем тестового администратора
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '+7 (999) 111-11-11',
            'password' => bcrypt('password'),
            'default_role_id' => $adminRole->id,
        ]);
        $admin->assignRole($adminRole);
        
        // Создаем тестового клиента
        $client = \App\Models\User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'phone' => '+7 (999) 222-22-22',
            'password' => bcrypt('password'),
            'default_role_id' => $clientRole->id,
        ]);
        $client->assignRole($clientRole);
        
        // Создаем тестового партнера
        $partner = \App\Models\User::factory()->create([
            'name' => 'Partner User',
            'email' => 'partner@example.com',
            'phone' => '+7 (999) 333-33-33',
            'password' => bcrypt('password'),
            'default_role_id' => $partnerRole->id,
        ]);
        $partner->assignRole($partnerRole);
        
        // Дополнительные пользователи
        // \App\Models\User::factory(10)->create();
    }
}
