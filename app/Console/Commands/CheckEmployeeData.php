<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;

class CheckEmployeeData extends Command
{
    protected $signature = 'check:employee-data';
    protected $description = 'Проверка данных сотрудников и их связей';

    public function handle()
    {
        $this->info('=== ПРОВЕРКА ДАННЫХ СОТРУДНИКОВ ===');
        
        // Получаем всех сотрудников
        $employees = Employee::with(['user', 'partner'])->get();
        
        $this->info("Всего записей сотрудников: " . $employees->count());
        
        foreach ($employees as $employee) {
            $this->line("\n--- Сотрудник #{$employee->id} ---");
            $this->line("User ID: {$employee->user_id}");
            $this->line("User Name: {$employee->user->name}");
            $this->line("Partner ID: {$employee->partner_id}");
            $this->line("Partner Name: {$employee->partner->name}");
            $this->line("Role: {$employee->role}");
            
            // Проверяем роли в базе данных
            $user = $employee->user;
            $roles = $user->roles()->pluck('name')->toArray();
            $this->line("Roles in DB: " . implode(', ', $roles));
            
            // Проверяем метод isEmployee() для этого пользователя
            $isEmployee = $user->isEmployee();
            $this->line("isEmployee(): " . ($isEmployee ? 'true' : 'false'));
            
            // Проверяем employeeProfile
            $profile = $user->employeeProfile;
            $this->line("employeeProfile exists: " . ($profile ? 'true' : 'false'));
            
            // Проверяем getPartner()
            $partner = $user->getPartner();
            $this->line("getPartner() result: " . ($partner ? $partner->name : 'null'));
            
            // Проверяем activeSubscription для сотрудника
            $subscription = $user->activeSubscription();
            if ($subscription) {
                $sub = $subscription->first();
                if ($sub) {
                    $this->line("Employee subscription: {$sub->subscriptionPlan->name}");
                } else {
                    $this->line("Employee subscription: null");
                }
            } else {
                $this->line("Employee subscription: null (no relation)");
            }
        }
        
        $this->info("\n=== ПРОВЕРКА ЗАВЕРШЕНА ===");
    }
}
