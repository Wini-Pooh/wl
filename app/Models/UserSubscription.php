<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'started_at',
        'expires_at',
        'last_payment_at',
        'next_payment_at',
        'status',
        'billing_period',
        'paid_amount',
        'payment_method',
        'transaction_id',
        'current_active_projects',
        'current_employees',
        'current_right_hand_employees',
        'current_estimate_templates',
        'auto_renewal',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_payment_at' => 'datetime',
        'paid_amount' => 'decimal:2',
        'auto_renewal' => 'boolean',
    ];

    /**
     * Пользователь
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Тарифный план
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Проверить, активна ли подписка
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               $this->expires_at > now();
    }

    /**
     * Проверить, истекает ли подписка скоро
     */
    public function isExpiringSoon($days = 7)
    {
        return $this->expires_at <= now()->addDays($days);
    }

    /**
     * Продлить подписку
     */
    public function renew($period = null)
    {
        if (!$period) {
            $period = $this->billing_period;
        }

        $plan = $this->subscriptionPlan;
        
        // Рассчитываем новую дату окончания
        if ($period === 'yearly') {
            $this->expires_at = $this->expires_at->addYear();
        } else {
            $this->expires_at = $this->expires_at->addMonth();
        }

        // Обновляем статус
        $this->status = 'active';
        $this->last_payment_at = now();
        
        // Рассчитываем следующий платеж
        if ($this->auto_renewal) {
            $this->next_payment_at = $period === 'yearly' 
                ? $this->expires_at->copy()->subDays(7)
                : $this->expires_at->copy()->subDays(3);
        }

        $this->save();
    }

    /**
     * Отменить подписку
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->auto_renewal = false;
        $this->next_payment_at = null;
        $this->saveQuietly();
    }

    /**
     * Приостановить подписку
     */
    public function suspend()
    {
        $this->status = 'suspended';
        $this->save();
    }

    /**
     * Проверить лимит ресурса
     */
    public function checkResourceLimit($resource)
    {
        $plan = $this->subscriptionPlan;
        $currentField = 'current_' . $resource;
        $limitField = 'max_' . $resource;

        $currentCount = $this->$currentField ?? 0;
        $limit = $plan->$limitField ?? 0;

        return $currentCount < $limit;
    }

    /**
     * Увеличить использование ресурса
     */
    public function incrementResource($resource)
    {
        $currentField = 'current_' . $resource;
        
        if ($this->checkResourceLimit($resource)) {
            $this->increment($currentField);
            return true;
        }
        
        return false;
    }

    /**
     * Уменьшить использование ресурса
     */
    public function decrementResource($resource)
    {
        $currentField = 'current_' . $resource;
        $current = $this->$currentField ?? 0;
        
        if ($current > 0) {
            $this->decrement($currentField);
        }
    }

    /**
     * Обновить счетчики использования ресурсов
     */
    public function updateResourceCounters()
    {
        $user = $this->user;
        
        // Считаем активные проекты
        $activeProjects = $user->projects()
            ->where('project_status', '!=', 'archived')
            ->count();
        
        // Считаем сотрудников
        $employees = $user->employees()->count();
        
        // Считаем "правых рук"
        $rightHandEmployees = $user->employees()
            ->whereHas('user.roles', function($query) {
                $query->where('name', 'employee_right_hand');
            })
            ->count();
        
        // Считаем шаблоны смет
        $estimateTemplates = EstimateTemplate::where('created_by', $user->id)->count();
        
        $this->update([
            'current_active_projects' => $activeProjects,
            'current_employees' => $employees,
            'current_right_hand_employees' => $rightHandEmployees,
            'current_estimate_templates' => $estimateTemplates,
        ]);
    }

    /**
     * Scope для активных подписок
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope для истекающих подписок
     */
    public function scopeExpiring($query, $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '<=', now()->addDays($days))
                    ->where('expires_at', '>', now());
    }
}
