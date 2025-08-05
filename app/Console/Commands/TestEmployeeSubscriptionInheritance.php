<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;

class TestEmployeeSubscriptionInheritance extends Command
{
    protected $signature = 'subscription:test-employee-inheritance {--employee-id=}';
    protected $description = 'Тестирование наследования подписки сотрудниками';

    public function handle()
    {
        $employeeId = $this->option('employee-id');
        
        if (!$employeeId) {
            // Найдем первого сотрудника
            $employee = Employee::with(['user', 'partner'])->first();
            if (!$employee) {
                $this->error('Сотрудники не найдены!');
                return;
            }
            $employeeUser = $employee->user;
        } else {
            $employeeUser = User::find($employeeId);
            if (!$employeeUser) {
                $this->error("Пользователь с ID {$employeeId} не найден!");
                return;
            }
        }
        
        $this->info('=== Тестирование наследования подписки сотрудником ===');
        $this->line("Тестируем пользователя: {$employeeUser->name} (ID: {$employeeUser->id})");
        
        // Проверяем, является ли пользователь сотрудником
        $isEmployee = $employeeUser->isEmployee() || $employeeUser->isEstimator() || $employeeUser->isForeman();
        $this->line("Является сотрудником: " . ($isEmployee ? 'Да' : 'Нет'));
        
        if (!$isEmployee) {
            $this->warn('Пользователь не является сотрудником, тестирование может быть неточным.');
        }
        
        // Получаем партнера
        $partner = $employeeUser->partner;
        if ($partner) {
            $this->line("Партнер: {$partner->name} (ID: {$partner->id})");
            
            // Проверяем подписку партнера
            $partnerSubscription = $partner->activeSubscription()->with('subscriptionPlan')->first();
            if ($partnerSubscription) {
                $this->line("Подписка партнера: {$partnerSubscription->subscriptionPlan->name}");
                $this->line("Статус подписки партнера: {$partnerSubscription->status}");
            } else {
                $this->warn("У партнера нет активной подписки!");
            }
        } else {
            $this->error("Партнер не найден!");
            return;
        }
        
        // Проверяем наследование подписки
        $inheritedSubscription = $employeeUser->activeSubscription()->with('subscriptionPlan')->first();
        
        if ($inheritedSubscription) {
            $this->info("\n✅ Наследование работает!");
            $this->line("Унаследованная подписка: {$inheritedSubscription->subscriptionPlan->name}");
            $this->line("Лимиты:");
            $this->line("  - Проекты: {$inheritedSubscription->subscriptionPlan->max_active_projects}");
            $this->line("  - Сотрудники: {$inheritedSubscription->subscriptionPlan->max_employees}");
            $this->line("  - Хранилище: {$inheritedSubscription->subscriptionPlan->project_storage_limit_mb} МБ");
            
            // Проверяем, что это та же подписка, что и у партнера
            if ($partnerSubscription && $inheritedSubscription->id === $partnerSubscription->id) {
                $this->info("✅ Сотрудник использует подписку партнера");
            } else {
                $this->warn("⚠️ Сотрудник использует не ту же подписку, что и партнер");
            }
        } else {
            $this->error("❌ Наследование не работает! Сотрудник не получил подписку партнера.");
        }
        
        $this->info("\n=== Тестирование завершено ===");
    }
}
