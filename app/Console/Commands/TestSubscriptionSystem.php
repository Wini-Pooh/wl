<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StorageLimitService;
use Illuminate\Console\Command;

class TestSubscriptionSystem extends Command
{
    protected $signature = 'subscription:test';
    protected $description = 'Тестирование системы подписок';

    public function handle()
    {
        $this->info('=== Тестирование системы тарифных планов ===');
        
        // Найдем первого партнера
        $partner = User::whereHas('roles', function($q) {
            $q->where('name', 'partner');
        })->first();
        
        if (!$partner) {
            $this->error('Партнер не найден!');
            return;
        }
        
        $this->info("Тестируем партнера: {$partner->name} (ID: {$partner->id})");
        
        // Проверяем подписку
        $subscription = $partner->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            $this->error('У партнера нет активной подписки!');
            return;
        }
        
        $this->info("Активная подписка: {$subscription->subscriptionPlan->name}");
        $this->info("Лимиты плана:");
        $this->line("- Проекты: {$subscription->subscriptionPlan->max_active_projects}");
        $this->line("- Сотрудники: {$subscription->subscriptionPlan->max_employees}");
        $this->line("- Хранилище: {$subscription->subscriptionPlan->project_storage_limit_mb} МБ");
        
        // Проверяем использование ресурсов
        $this->info("\nТекущее использование:");
        $this->line("- Активные проекты: {$subscription->current_active_projects}");
        $this->line("- Активные сотрудники: {$subscription->current_employees}");
        
        // Проверяем хранилище
        $storageService = app(StorageLimitService::class);
        $storageInfo = $storageService->getUserStorageInfo($partner);
        
        $this->info("\nИспользование хранилища:");
        $this->line("- Использовано: {$storageInfo['current_formatted']}");
        $this->line("- Лимит: {$storageInfo['limit_formatted']}");
        $this->line("- Процент: {$storageInfo['percentage']}%");
        $this->line("- Доступно: {$storageInfo['available_formatted']}");
        
        // Проверяем предупреждения
        $warning = $storageService->checkStorageLimitWarning($partner);
        if ($warning) {
            $this->warn("Предупреждение: {$warning}");
        }
        
        $this->info("\n=== Тест завершен ===");
    }
}
