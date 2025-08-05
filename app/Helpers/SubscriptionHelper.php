<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Project;
use App\Services\StorageLimitService;

class SubscriptionHelper
{
    /**
     * Проверить лимит ресурса
     */
    public static function checkResourceLimit(User $user, string $resource): bool
    {
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return false;
        }
        
        return $subscription->checkResourceLimit($resource);
    }
    
    /**
     * Проверить доступ к функции
     */
    public static function checkFeatureAccess(User $user, string $feature): bool
    {
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return false;
        }
        
        return $subscription->subscriptionPlan->hasAccess($feature);
    }
    
    /**
     * Получить процент использования ресурса
     */
    public static function getResourceUsage(User $user, string $resource): float
    {
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return 100;
        }
        
        $plan = $subscription->subscriptionPlan;
        $currentField = 'current_' . $resource;
        $limitField = 'max_' . $resource;
        
        $current = $subscription->$currentField ?? 0;
        $limit = $plan->$limitField ?? 1;
        
        return $limit > 0 ? ($current / $limit) * 100 : 0;
    }
    
    /**
     * Увеличить использование ресурса
     */
    public static function incrementResource(User $user, string $resource): bool
    {
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return false;
        }
        
        return $subscription->incrementResource($resource);
    }
    
    /**
     * Уменьшить использование ресурса
     */
    public static function decrementResource(User $user, string $resource): void
    {
        $subscription = $user->activeSubscription;
        
        if ($subscription) {
            $subscription->decrementResource($resource);
        }
    }
    
    /**
     * Получить информацию об использовании ресурсов
     */
    public static function getResourceUsageInfo(User $user): array
    {
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return [];
        }
        
        $plan = $subscription->subscriptionPlan;
        
        return [
            'active_projects' => [
                'current' => $subscription->current_active_projects ?? 0,
                'limit' => $plan->max_active_projects,
                'percentage' => self::getResourceUsage($user, 'active_projects'),
                'can_create' => self::checkResourceLimit($user, 'active_projects'),
            ],
            'employees' => [
                'current' => $subscription->current_employees ?? 0,
                'limit' => $plan->max_employees,
                'percentage' => self::getResourceUsage($user, 'employees'),
                'can_create' => self::checkResourceLimit($user, 'employees'),
            ],
            'right_hand_employees' => [
                'current' => $subscription->current_right_hand_employees ?? 0,
                'limit' => $plan->max_right_hand_employees,
                'percentage' => self::getResourceUsage($user, 'right_hand_employees'),
                'can_create' => self::checkResourceLimit($user, 'right_hand_employees'),
            ],
            'estimate_templates' => [
                'current' => $subscription->current_estimate_templates ?? 0,
                'limit' => $plan->max_estimate_templates,
                'percentage' => self::getResourceUsage($user, 'estimate_templates'),
                'can_create' => self::checkResourceLimit($user, 'estimate_templates'),
            ],
        ];
    }
    
    /**
     * Проверить лимит хранилища для проекта
     */
    public static function checkProjectStorageLimit(User $user, int $projectId = null, int $fileSizeBytes = 0): bool
    {
        $storageService = app(StorageLimitService::class);
        
        if ($fileSizeBytes > 0 && $projectId) {
            $project = $user->projects()->find($projectId);
            if (!$project) {
                return false;
            }
            
            return $storageService->canUploadFile($project, $fileSizeBytes);
        }
        
        return $storageService->checkProjectStorageLimit($user, $projectId);
    }
    
    /**
     * Обновить счетчики использования ресурсов
     */
    public static function updateResourceCounters(User $user): void
    {
        $subscription = $user->activeSubscription;
        
        if ($subscription) {
            $subscription->updateResourceCounters();
        }
    }
    
    /**
     * Получить названия ресурсов на русском языке
     */
    public static function getResourceNames(): array
    {
        return [
            'active_projects' => 'активных проектов',
            'employees' => 'сотрудников',
            'right_hand_employees' => 'правых рук',
            'estimate_templates' => 'шаблонов смет',
        ];
    }
    
    /**
     * Получить названия функций на русском языке
     */
    public static function getFeatureNames(): array
    {
        return [
            'estimates' => 'смет',
            'documents' => 'документов',
            'projects' => 'проектов',
            'analytics' => 'аналитики',
            'employees' => 'управления сотрудниками',
            'online_training' => 'онлайн обучения',
        ];
    }
}
