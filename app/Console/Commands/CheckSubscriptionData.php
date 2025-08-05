<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

class CheckSubscriptionData extends Command
{
    protected $signature = 'subscription:check-data';
    protected $description = 'Проверка данных тарифных планов и подписок';

    public function handle()
    {
        $this->info('=== Проверка данных системы тарифных планов ===');
        
        // Проверяем тарифные планы
        $this->info("\n--- Тарифные планы ---");
        $plans = SubscriptionPlan::all();
        foreach ($plans as $plan) {
            $this->line("ID: {$plan->id}, Название: {$plan->name}");
            $this->line("  Проекты: {$plan->max_active_projects}");
            $this->line("  Сотрудники: {$plan->max_employees}");
            $this->line("  Хранилище: {$plan->project_storage_limit_mb} МБ");
            $this->line("  Цена: " . ($plan->monthly_price ?? 0) . " ₽");
            $this->line("  Активен: " . ($plan->is_active ? 'Да' : 'Нет'));
            $this->line("---");
        }
        
        // Проверяем пользователя и его подписку
        $this->info("\n--- Подписки пользователей ---");
        $user = User::find(1);
        if ($user) {
            $this->line("Пользователь: {$user->name} (ID: {$user->id})");
            
            $subscription = $user->activeSubscription()->with('subscriptionPlan')->first();
            if ($subscription) {
                $this->line("Подписка ID: {$subscription->id}");
                $this->line("План ID: {$subscription->subscription_plan_id}");
                $this->line("Статус: {$subscription->status}");
                $this->line("Создана: {$subscription->created_at}");
                $this->line("Истекает: " . ($subscription->expires_at ?? 'Бессрочно'));
                
                if ($subscription->subscriptionPlan) {
                    $plan = $subscription->subscriptionPlan;
                    $this->line("Связанный план: {$plan->name}");
                    $this->line("  Проекты: {$plan->max_active_projects}");
                    $this->line("  Сотрудники: {$plan->max_employees}"); 
                    $this->line("  Хранилище: {$plan->project_storage_limit_mb} МБ");
                } else {
                    $this->error("Связь с планом не найдена!");
                }
            } else {
                $this->error("Активная подписка не найдена!");
            }
        } else {
            $this->error("Пользователь с ID=1 не найден!");
        }
        
        $this->info("\n=== Проверка завершена ===");
    }
}
