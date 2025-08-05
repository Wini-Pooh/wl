<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionPlan;

class SetupSubscriptionPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:setup-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Настройка тарифных планов согласно требованиям';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Настройка тарифных планов...');
        
        $plans = [
            [
                'name' => 'Пробный',
                'slug' => 'trial',
                'description' => 'Базовый тарифный план для знакомства с системой',
                'max_active_projects' => 3,
                'project_storage_limit_mb' => 300,
                'max_estimate_templates' => 3,
                'max_employees' => 10,
                'max_right_hand_employees' => 1,
                'access_estimates' => true,
                'access_documents' => true,
                'access_projects' => true,
                'access_analytics' => true,
                'access_employees' => true,
                'access_online_training' => true,
                'monthly_price' => 10500.00,
                'yearly_price' => 107100.00, // 10500 * 12 * 0.85 (скидка 15%)
                'yearly_discount_percent' => 15.00,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Успешный',
                'slug' => 'successful',
                'description' => 'Расширенный тарифный план для растущего бизнеса',
                'max_active_projects' => 7,
                'project_storage_limit_mb' => 600,
                'max_estimate_templates' => 0, // Без ограничений
                'max_employees' => 20,
                'max_right_hand_employees' => 2,
                'access_estimates' => true,
                'access_documents' => true,
                'access_projects' => true,
                'access_analytics' => true,
                'access_employees' => true,
                'access_online_training' => true,
                'monthly_price' => 25000.00,
                'yearly_price' => 255000.00, // 25000 * 12 * 0.85 (скидка 15%)
                'yearly_discount_percent' => 15.00,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Мастер',
                'slug' => 'master',
                'description' => 'Профессиональный тарифный план для крупных компаний',
                'max_active_projects' => 15,
                'project_storage_limit_mb' => 1500,
                'max_estimate_templates' => 0, // Без ограничений
                'max_employees' => 45,
                'max_right_hand_employees' => 5,
                'access_estimates' => true,
                'access_documents' => true,
                'access_projects' => true,
                'access_analytics' => true,
                'access_employees' => true,
                'access_online_training' => true,
                'monthly_price' => 50000.00,
                'yearly_price' => 510000.00, // 50000 * 12 * 0.85 (скидка 15%)
                'yearly_discount_percent' => 15.00,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        $createdPlans = 0;
        $updatedPlans = 0;

        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::where('slug', $planData['slug'])->first();
            
            if ($plan) {
                $plan->update($planData);
                $updatedPlans++;
                $this->line("Обновлен тариф: {$planData['name']}");
            } else {
                SubscriptionPlan::create($planData);
                $createdPlans++;
                $this->line("Создан тариф: {$planData['name']}");
            }
        }

        $this->newLine();
        $this->info("Настройка завершена!");
        $this->info("Создано планов: {$createdPlans}");
        $this->info("Обновлено планов: {$updatedPlans}");
        
        $this->newLine();
        $this->info('Тарифные планы:');
        $this->table(
            ['Название', 'Проекты', 'Хранилище (МБ)', 'Сотрудники', 'Цена/мес'],
            SubscriptionPlan::active()->ordered()->get()->map(function ($plan) {
                return [
                    $plan->name,
                    $plan->max_active_projects,
                    $plan->project_storage_limit_mb,
                    $plan->max_employees,
                    number_format($plan->monthly_price, 0, ',', ' ') . ' ₽'
                ];
            })
        );
        
        return 0;
    }
}
