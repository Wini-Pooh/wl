<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Тестовый',
                'slug' => 'test',
                'description' => 'Тестовый тарифный план для новых пользователей',
                'max_active_projects' => 1,
                'project_storage_limit_mb' => 100,
                'max_estimate_templates' => 1,
                'max_employees' => 1,
                'max_right_hand_employees' => 0,
                'access_estimates' => true,
                'access_documents' => true,
                'access_projects' => true,
                'access_analytics' => false,
                'access_employees' => false,
                'access_online_training' => false,
                'monthly_price' => 0.00,
                'yearly_price' => 0.00,
                'yearly_discount_percent' => 0.00,
                'is_active' => true,
                'sort_order' => 0,
            ],
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

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
