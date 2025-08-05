<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUserRole extends Command
{
    protected $signature = 'fix:user-role {user-id} {role}';
    protected $description = 'Добавляет роль пользователю';

    public function handle()
    {
        $userId = $this->argument('user-id');
        $role = $this->argument('role');
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден!");
            return;
        }

        $this->info("Добавляем роль '{$role}' пользователю '{$user->name}' (ID: {$userId})");
        
        $user->assignRole($role);
        
        $this->info("✓ Роль успешно добавлена!");
        
        // Проверим результат
        $roles = $user->roles()->pluck('name')->toArray();
        $this->line("Текущие роли пользователя: " . implode(', ', $roles));
    }
}
