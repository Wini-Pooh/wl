@if(Auth::check() && Auth::user()->activeSubscription)
    @php
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription->subscriptionPlan;
        
        $warnings = [];
        
        // Проверяем лимиты ресурсов
        $resources = [
            'active_projects' => 'активных проектов',
            'employees' => 'сотрудников', 
            'right_hand_employees' => 'правых рук',
            'estimate_templates' => 'шаблонов смет'
        ];
        
        foreach ($resources as $resource => $name) {
            $usage = $user->getResourceUsage($resource);
            if ($usage >= 90) {
                $warnings[] = [
                    'type' => 'danger',
                    'message' => "Достигнут лимит {$name}! Обновите тарифный план.",
                    'resource' => $resource
                ];
            } elseif ($usage >= 80) {
                $warnings[] = [
                    'type' => 'warning', 
                    'message' => "Использовано {$usage}% лимита {$name}. Рассмотрите обновление тарифа.",
                    'resource' => $resource
                ];
            }
        }
        
        // Проверяем срок действия подписки
        $daysUntilExpiry = $subscription->expires_at->diffInDays(now());
        if ($daysUntilExpiry <= 3) {
            $warnings[] = [
                'type' => 'danger',
                'message' => "Подписка истекает через {$daysUntilExpiry} дней! Продлите подписку.",
                'resource' => 'subscription'
            ];
        } elseif ($daysUntilExpiry <= 7) {
            $warnings[] = [
                'type' => 'warning',
                'message' => "Подписка истекает через {$daysUntilExpiry} дней.",
                'resource' => 'subscription'
            ];
        }
    @endphp

    @if(count($warnings) > 0)
        <div class="subscription-warnings mb-3">
            @foreach($warnings as $warning)
                <div class="alert alert-{{ $warning['type'] }} alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            @if($warning['type'] === 'danger')
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            @else
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                            @endif
                            {{ $warning['message'] }}
                        </div>
                        <div class="ms-3">
                            @if($warning['resource'] === 'subscription')
                                <a href="{{ route('subscriptions.manage') }}" class="btn btn-sm btn-outline-{{ $warning['type'] === 'danger' ? 'light' : 'dark' }}">
                                    Управление
                                </a>
                            @else
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-outline-{{ $warning['type'] === 'danger' ? 'light' : 'dark' }}">
                                    Обновить план
                                </a>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endforeach
        </div>
    @endif
@endif

<style>
.subscription-warnings .alert {
    border-left: 4px solid;
    border-radius: 0.375rem;
    margin-bottom: 0.75rem;
}

.subscription-warnings .alert-warning {
    border-left-color: #ffc107;
}

.subscription-warnings .alert-danger {
    border-left-color: #dc3545;
}

.subscription-warnings .btn {
    font-size: 0.875rem;
    padding: 0.25rem 0.75rem;
}
</style>
