<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'max_active_projects',
        'project_storage_limit_mb',
        'max_estimate_templates',
        'max_employees',
        'max_right_hand_employees',
        'access_estimates',
        'access_documents',
        'access_projects',
        'access_analytics',
        'access_employees',
        'access_online_training',
        'monthly_price',
        'yearly_price',
        'yearly_discount_percent',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'access_estimates' => 'boolean',
        'access_documents' => 'boolean',
        'access_projects' => 'boolean',
        'access_analytics' => 'boolean',
        'access_employees' => 'boolean',
        'access_online_training' => 'boolean',
        'is_active' => 'boolean',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'yearly_discount_percent' => 'decimal:2',
    ];

    /**
     * Подписки пользователей по этому тарифу
     */
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Активные подписки пользователей
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    /**
     * Получить цену с учетом периода
     */
    public function getPriceForPeriod($period = 'monthly')
    {
        return $period === 'yearly' ? $this->yearly_price : $this->monthly_price;
    }

    /**
     * Получить экономию при годовой подписке
     */
    public function getYearlySavings()
    {
        $monthlyTotal = $this->monthly_price * 12;
        return $monthlyTotal - $this->yearly_price;
    }

    /**
     * Проверить доступность функции
     */
    public function hasAccess($feature)
    {
        $accessField = 'access_' . $feature;
        return $this->$accessField ?? false;
    }

    /**
     * Проверить лимит ресурса
     */
    public function checkLimit($resource, $currentCount)
    {
        $limitField = 'max_' . $resource;
        $limit = $this->$limitField ?? 0;
        
        return $currentCount < $limit;
    }

    /**
     * Получить процент использования ресурса
     */
    public function getUsagePercentage($resource, $currentCount)
    {
        $limitField = 'max_' . $resource;
        $limit = $this->$limitField ?? 1;
        
        return ($currentCount / $limit) * 100;
    }

    /**
     * Scope для активных тарифов
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для сортировки по порядку
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('monthly_price');
    }
}
