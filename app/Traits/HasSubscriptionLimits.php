<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

trait HasSubscriptionLimits
{
    /**
     * Проверить лимит ресурса и вернуть редирект с ошибкой если лимит исчерпан
     */
    protected function checkResourceLimitOrFail(string $resource, string $redirectRoute = null): ?RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || !$user->checkResourceLimit($resource)) {
            $resourceNames = [
                'active_projects' => 'активных проектов',
                'employees' => 'сотрудников',
                'right_hand_employees' => 'правых рук',
                'estimate_templates' => 'шаблонов смет',
            ];
            
            $resourceName = $resourceNames[$resource] ?? $resource;
            $redirectRoute = $redirectRoute ?? 'subscriptions.index';
            
            return redirect()->route($redirectRoute)
                ->with('warning', "Достигнут лимит {$resourceName} для вашего тарифного плана. Обновите подписку для увеличения лимитов.");
        }
        
        return null;
    }
    
    /**
     * Проверить доступ к функции и вернуть редирект с ошибкой если доступ запрещен
     */
    protected function checkFeatureAccessOrFail(string $feature, string $redirectRoute = null): ?RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasFeatureAccess($feature)) {
            $featureNames = [
                'estimates' => 'смет',
                'documents' => 'документов',
                'projects' => 'проектов',
                'analytics' => 'аналитики',
                'employees' => 'управления сотрудниками',
                'online_training' => 'онлайн обучения',
            ];
            
            $featureName = $featureNames[$feature] ?? $feature;
            $redirectRoute = $redirectRoute ?? 'subscriptions.index';
            
            return redirect()->route($redirectRoute)
                ->with('warning', "Доступ к разделу {$featureName} не включен в ваш тарифный план. Обновите подписку для получения доступа.");
        }
        
        return null;
    }
    
    /**
     * Получить информацию об использовании ресурсов для текущего пользователя
     */
    protected function getResourceUsageInfo(): array
    {
        $user = Auth::user();
        
        if (!$user || !$user->activeSubscription) {
            return [];
        }
        
        $subscription = $user->activeSubscription;
        $plan = $subscription->subscriptionPlan;
        
        return [
            'active_projects' => [
                'current' => $subscription->current_active_projects ?? 0,
                'limit' => $plan->max_active_projects,
                'percentage' => $user->getResourceUsage('active_projects'),
                'name' => 'Активные проекты',
            ],
            'employees' => [
                'current' => $subscription->current_employees ?? 0,
                'limit' => $plan->max_employees,
                'percentage' => $user->getResourceUsage('employees'),
                'name' => 'Сотрудники',
            ],
            'right_hand_employees' => [
                'current' => $subscription->current_right_hand_employees ?? 0,
                'limit' => $plan->max_right_hand_employees,
                'percentage' => $user->getResourceUsage('right_hand_employees'),
                'name' => 'Правые руки',
            ],
            'estimate_templates' => [
                'current' => $subscription->current_estimate_templates ?? 0,
                'limit' => $plan->max_estimate_templates ?? 0,
                'percentage' => $user->getResourceUsage('estimate_templates'),
                'name' => 'Шаблоны смет',
            ],
        ];
    }
    
    /**
     * Увеличить использование ресурса после успешного создания
     */
    protected function incrementResourceUsage(string $resource): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->activeSubscription) {
            return false;
        }
        
        return $user->activeSubscription->incrementResource($resource);
    }
    
    /**
     * Уменьшить использование ресурса после удаления
     */
    protected function decrementResourceUsage(string $resource): void
    {
        $user = Auth::user();
        
        if ($user && $user->activeSubscription) {
            $user->activeSubscription->decrementResource($resource);
        }
    }
    
    /**
     * Проверить, близок ли пользователь к лимиту ресурса (80% или более)
     */
    protected function isNearResourceLimit(string $resource): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return true;
        }
        
        $usage = $user->getResourceUsage($resource);
        
        return $usage >= 80;
    }
    
    /**
     * Получить сообщение-предупреждение о близости к лимиту
     */
    protected function getResourceLimitWarning(string $resource): ?string
    {
        if (!$this->isNearResourceLimit($resource)) {
            return null;
        }
        
        $user = Auth::user();
        $usage = $user->getResourceUsage($resource);
        
        $resourceNames = [
            'active_projects' => 'активных проектов',
            'employees' => 'сотрудников',
            'right_hand_employees' => 'правых рук',
            'estimate_templates' => 'шаблонов смет',
        ];
        
        $resourceName = $resourceNames[$resource] ?? $resource;
        
        if ($usage >= 100) {
            return "Достигнут лимит {$resourceName}. Обновите тарифный план для увеличения лимитов.";
        }
        
        return "Использовано {$usage}% лимита {$resourceName}. Рассмотрите возможность обновления тарифного плана.";
    }
}
