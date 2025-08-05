<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Project;
use App\Models\Employee;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;

class TestEmployeeInheritanceComplete extends Command
{
    protected $signature = 'test:employee-inheritance-complete {userId}';
    protected $description = 'Полное тестирование наследования подписки сотрудником';

    public function handle()
    {
        $userId = $this->argument('userId');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден");
            return;
        }

        $this->info("=== ПОЛНОЕ ТЕСТИРОВАНИЕ НАСЛЕДОВАНИЯ ПОДПИСКИ ===");
        $this->info("Пользователь: {$user->name} (ID: {$user->id})");
        
        // 1. Проверяем является ли пользователь сотрудником
        $this->info("\n1. ПРОВЕРКА СТАТУСА СОТРУДНИКА:");
        if ($user->isEmployee()) {
            $this->info("✓ Пользователь является сотрудником");
            $partner = $user->getPartner();
            if ($partner) {
                $this->info("✓ Партнер найден: {$partner->name} (ID: {$partner->id})");
            } else {
                $this->error("✗ Партнер не найден!");
                return;
            }
        } else {
            $this->error("✗ Пользователь НЕ является сотрудником");
            return;
        }

        // 2. Проверяем подписку партнера
        $this->info("\n2. ПРОВЕРКА ПОДПИСКИ ПАРТНЕРА:");
        $partnerSubscription = $partner->activeSubscription();
        if ($partnerSubscription) {
            $plan = $partnerSubscription->subscriptionPlan;
            $this->info("✓ У партнера есть активная подписка: {$plan->name}");
            $this->info("  - Проекты: {$plan->max_active_projects}");
            $this->info("  - Сотрудники: {$plan->max_employees}");
            $this->info("  - Хранилище: {$plan->project_storage_limit_mb} MB");
        } else {
            $this->error("✗ У партнера НЕТ активной подписки!");
            return;
        }

        // 3. Проверяем наследование подписки сотрудником
        $this->info("\n3. ПРОВЕРКА НАСЛЕДОВАНИЯ ПОДПИСКИ:");
        $employeeSubscription = $user->activeSubscription();
        if ($employeeSubscription) {
            $this->info("✓ Сотрудник имеет доступ к подписке: {$employeeSubscription->subscriptionPlan->name}");
            
            // Проверяем что это та же подписка
            if ($employeeSubscription->id === $partnerSubscription->id) {
                $this->info("✓ Сотрудник использует подписку партнера (правильное наследование)");
            } else {
                $this->warning("⚠ Сотрудник использует собственную подписку, а не партнера");
            }
        } else {
            $this->error("✗ Сотрудник НЕ имеет доступа к подписке!");
            return;
        }

        // 4. Тестируем лимиты через trait
        $this->info("\n4. ПРОВЕРКА ЛИМИТОВ ЧЕРЕЗ TRAIT:");
        try {
            // Проверяем лимит проектов
            $projectsCount = $user->projects()->count();
            $maxProjects = $employeeSubscription->subscriptionPlan->max_active_projects;
            $this->info("Проекты: {$projectsCount}/{$maxProjects}");
            
            if ($projectsCount < $maxProjects) {
                $this->info("✓ Можно создать новый проект");
            } else {
                $this->warning("⚠ Достигнут лимит проектов");
            }

            // Проверяем лимит сотрудников (для партнера)
            $employeesCount = $partner->employees()->count();
            $maxEmployees = $employeeSubscription->subscriptionPlan->max_employees;
            $this->info("Сотрудники у партнера: {$employeesCount}/{$maxEmployees}");
            
        } catch (\Exception $e) {
            $this->error("Ошибка при проверке лимитов: " . $e->getMessage());
        }

        // 5. Тестируем методы из HasSubscriptionLimits
        $this->info("\n5. ТЕСТИРОВАНИЕ МЕТОДОВ TRAIT:");
        try {
            // Проверяем warning для ресурсов
            $warning = $user->getResourceLimitWarning('projects');
            if ($warning) {
                $this->warning("Warning для проектов: {$warning}");
            } else {
                $this->info("✓ Нет предупреждений для проектов");
            }

        } catch (\Exception $e) {
            $this->error("Ошибка при тестировании trait: " . $e->getMessage());
        }

        // 6. Симуляция создания проекта
        $this->info("\n6. СИМУЛЯЦИЯ СОЗДАНИЯ ПРОЕКТА:");
        try {
            $newProject = new Project([
                'name' => 'Тестовый проект ' . now()->format('H:i:s'),
                'description' => 'Проект для тестирования лимитов',
                'user_id' => $user->id,
                'status' => 'active'
            ]);
            
            // Проверяем можно ли создать
            $currentCount = $user->projects()->count();
            $limit = $employeeSubscription->subscriptionPlan->max_active_projects;
            
            if ($currentCount < $limit) {
                $this->info("✓ Проект можно создать ({$currentCount}/{$limit})");
                
                // Только симуляция, не сохраняем
                $this->info("  Создан проект: {$newProject->name}");
            } else {
                $this->error("✗ Нельзя создать проект - достигнут лимит ({$currentCount}/{$limit})");
            }
            
        } catch (\Exception $e) {
            $this->error("Ошибка при симуляции создания проекта: " . $e->getMessage());
        }

        $this->info("\n=== ТЕСТИРОВАНИЕ ЗАВЕРШЕНО ===");
    }
}
