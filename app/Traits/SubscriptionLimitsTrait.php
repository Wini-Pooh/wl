<?php

namespace App\Traits;

use App\Services\StorageLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait SubscriptionLimitsTrait
{
    /**
     * Проверить лимит ресурса перед созданием
     */
    protected function checkResourceLimit(string $resource): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->checkResourceLimit($resource);
    }
    
    /**
     * Проверить доступ к функции
     */
    protected function checkFeatureAccess(string $feature): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasFeatureAccess($feature);
    }
    
    /**
     * Увеличить использование ресурса
     */
    protected function incrementResource(string $resource): bool
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return false;
        }
        
        return $subscription->incrementResource($resource);
    }
    
    /**
     * Уменьшить использование ресурса
     */
    protected function decrementResource(string $resource): void
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        
        if ($subscription) {
            $subscription->decrementResource($resource);
        }
    }
    
    /**
     * Проверить лимит хранилища для проекта
     */
    protected function checkProjectStorageLimit(int $projectId, int $fileSizeBytes = 0): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        $storageService = app(StorageLimitService::class);
        
        if ($fileSizeBytes > 0) {
            $project = $user->projects()->find($projectId);
            if (!$project) {
                return false;
            }
            
            return $storageService->canUploadFile($project, $fileSizeBytes);
        }
        
        return $storageService->checkProjectStorageLimit($user, $projectId);
    }
    
    /**
     * Получить информацию об использовании ресурсов
     */
    protected function getResourceUsageInfo(): array
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return [];
        }
        
        $plan = $subscription->subscriptionPlan;
        
        return [
            'active_projects' => [
                'current' => $subscription->current_active_projects ?? 0,
                'limit' => $plan->max_active_projects,
                'percentage' => $user->getResourceUsage('active_projects'),
                'can_create' => $user->checkResourceLimit('active_projects'),
            ],
            'employees' => [
                'current' => $subscription->current_employees ?? 0,
                'limit' => $plan->max_employees,
                'percentage' => $user->getResourceUsage('employees'),
                'can_create' => $user->checkResourceLimit('employees'),
            ],
            'right_hand_employees' => [
                'current' => $subscription->current_right_hand_employees ?? 0,
                'limit' => $plan->max_right_hand_employees,
                'percentage' => $user->getResourceUsage('right_hand_employees'),
                'can_create' => $user->checkResourceLimit('right_hand_employees'),
            ],
            'estimate_templates' => [
                'current' => $subscription->current_estimate_templates ?? 0,
                'limit' => $plan->max_estimate_templates,
                'percentage' => $user->getResourceUsage('estimate_templates'),
                'can_create' => $user->checkResourceLimit('estimate_templates'),
            ],
        ];
    }
    
    /**
     * Отправить предупреждение о достижении лимита
     */
    protected function sendLimitWarning(string $resource): void
    {
        $resourceNames = [
            'active_projects' => 'активных проектов',
            'employees' => 'сотрудников',
            'right_hand_employees' => 'правых рук',
            'estimate_templates' => 'шаблонов смет',
        ];
        
        $resourceName = $resourceNames[$resource] ?? $resource;
        
        session()->flash('warning', 
            "Достигнут лимит {$resourceName} для вашего тарифного плана. " .
            '<a href="' . route('subscriptions.index') . '" class="alert-link">Обновите подписку</a> ' .
            'для увеличения лимитов.'
        );
    }
    
    /**
     * Обновить счетчики использования ресурсов
     */
    protected function updateResourceCounters(): void
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        
        if ($subscription) {
            $subscription->updateResourceCounters();
        }
    }
}
