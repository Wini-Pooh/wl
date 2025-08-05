<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Project;
use App\Models\Employee;
use App\Helpers\SubscriptionHelper;
use App\Services\StorageLimitService;

class TestSubscriptionLimits extends Command
{
    protected $signature = 'subscription:test-limits {--user-id=1}';
    protected $description = 'Тестирование ограничений тарифных планов';

    public function handle()
    {
        $userId = $this->option('user-id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден!");
            return;
        }

        $this->info('=== Детальное тестирование тарифных ограничений ===');
        $this->line("Тестируем пользователя: {$user->name} (ID: {$user->id})");
        
        $subscription = $user->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            $this->error('У пользователя нет активной подписки!');
            return;
        }

        $plan = $subscription->subscriptionPlan;
        $this->info("\nТарифный план: {$plan->name}");
        
        // 1. Тестирование лимитов проектов
        $this->testProjectLimits($user, $plan);
        
        // 2. Тестирование лимитов сотрудников
        $this->testEmployeeLimits($user, $plan);
        
        // 3. Тестирование лимитов хранилища
        $this->testStorageLimits($user, $plan);
        
        // 4. Тестирование доступа к функциям
        $this->testFeatureAccess($user, $plan);
        
        // 5. Тестирование работы для сотрудников партнера
        $this->testEmployeeAccess($user);
        
        $this->info("\n=== Тестирование завершено ===");
    }
    
    private function testProjectLimits($user, $plan)
    {
        $this->info("\n--- Тестирование лимитов проектов ---");
        
        $activeProjects = Project::where('partner_id', $user->id)->count();
        $limit = $plan->max_active_projects;
        
        $this->line("Активных проектов: {$activeProjects} из {$limit}");
        $this->line("Доступно для создания: " . max(0, $limit - $activeProjects));
        
        // Проверяем функцию проверки лимита
        $canCreate = SubscriptionHelper::checkResourceLimit($user, 'active_projects');
        $this->line("Можно создать новый проект: " . ($canCreate ? 'Да' : 'Нет'));
        
        if ($activeProjects >= $limit) {
            $this->warn("⚠️ Достигнут лимит проектов!");
        } else {
            $this->info("✓ Лимит проектов в норме");
        }
    }
    
    private function testEmployeeLimits($user, $plan)
    {
        $this->info("\n--- Тестирование лимитов сотрудников ---");
        
        $employees = Employee::where('partner_id', $user->id)->count();
        $rightHandEmployees = Employee::where('partner_id', $user->id)
            ->where('role', 'right_hand')->count();
        
        $employeeLimit = $plan->max_employees;
        $rightHandLimit = $plan->max_right_hand_employees;
        
        $this->line("Всего сотрудников: {$employees} из {$employeeLimit}");
        $this->line("Правых рук: {$rightHandEmployees} из {$rightHandLimit}");
        
        // Проверяем функции проверки лимитов
        $canCreateEmployee = SubscriptionHelper::checkResourceLimit($user, 'employees');
        $canCreateRightHand = SubscriptionHelper::checkResourceLimit($user, 'right_hand_employees');
        
        $this->line("Можно создать сотрудника: " . ($canCreateEmployee ? 'Да' : 'Нет'));
        $this->line("Можно создать правую руку: " . ($canCreateRightHand ? 'Да' : 'Нет'));
        
        if ($employees >= $employeeLimit) {
            $this->warn("⚠️ Достигнут лимит сотрудников!");
        }
        if ($rightHandEmployees >= $rightHandLimit) {
            $this->warn("⚠️ Достигнут лимит правых рук!");
        }
        if ($employees < $employeeLimit && $rightHandEmployees < $rightHandLimit) {
            $this->info("✓ Лимиты сотрудников в норме");
        }
    }
    
    private function testStorageLimits($user, $plan)
    {
        $this->info("\n--- Тестирование лимитов хранилища ---");
        
        $storageService = app(StorageLimitService::class);
        $storageInfo = $storageService->getUserStorageInfo($user);
        
        $this->line("Использовано: {$storageInfo['current_formatted']}");
        $this->line("Лимит: {$storageInfo['limit_formatted']}");
        $this->line("Процент использования: {$storageInfo['percentage']}%");
        $this->line("Доступно: {$storageInfo['available_formatted']}");
        
        if ($storageInfo['percentage'] >= 90) {
            $this->warn("⚠️ Хранилище почти заполнено!");
        } elseif ($storageInfo['percentage'] >= 100) {
            $this->error("❌ Лимит хранилища превышен!");
        } else {
            $this->info("✓ Лимит хранилища в норме");
        }
    }
    
    private function testFeatureAccess($user, $plan)
    {
        $this->info("\n--- Тестирование доступа к функциям ---");
        
        $features = [
            'estimates' => 'Сметы',
            'documents' => 'Документы', 
            'projects' => 'Проекты',
            'analytics' => 'Аналитика',
            'employees' => 'Сотрудники',
            'online_training' => 'Онлайн обучение'
        ];
        
        foreach ($features as $feature => $name) {
            $hasAccess = SubscriptionHelper::checkFeatureAccess($user, $feature);
            $status = $hasAccess ? '✓' : '❌';
            $this->line("{$status} {$name}: " . ($hasAccess ? 'Доступно' : 'Недоступно'));
        }
    }
    
    private function testEmployeeAccess($user)
    {
        $this->info("\n--- Тестирование доступа для сотрудников партнера ---");
        
        $employees = Employee::where('partner_id', $user->id)->with('user')->get();
        
        if ($employees->isEmpty()) {
            $this->line("У партнера нет сотрудников");
            return;
        }
        
        foreach ($employees as $employee) {
            $this->line("\nСотрудник: {$employee->user->name} (роль: {$employee->role})");
            
            // Проверяем, наследует ли сотрудник ограничения партнера
            $partnerSubscription = $user->activeSubscription;
            
            if ($partnerSubscription) {
                $this->line("  Наследует ограничения партнера: Да");
                $this->line("  Лимит проектов партнера: {$partnerSubscription->subscriptionPlan->max_active_projects}");
                $this->line("  Лимит хранилища партнера: {$partnerSubscription->subscriptionPlan->project_storage_limit_mb} МБ");
                
                // Проверяем доступ к функциям
                $canAccessProjects = SubscriptionHelper::checkFeatureAccess($user, 'projects');
                $canAccessEstimates = SubscriptionHelper::checkFeatureAccess($user, 'estimates');
                
                $this->line("  Может работать с проектами: " . ($canAccessProjects ? 'Да' : 'Нет'));
                $this->line("  Может работать со сметами: " . ($canAccessEstimates ? 'Да' : 'Нет'));
            } else {
                $this->line("  У партнера нет активной подписки");
            }
        }
    }
}
