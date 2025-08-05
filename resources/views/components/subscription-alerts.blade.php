@if(Auth::check() && Auth::user()->activeSubscription)
    @php
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription->subscriptionPlan;
        
        $resourceUsage = [
            'active_projects' => [
                'current' => $subscription->current_active_projects ?? 0,
                'limit' => $plan->max_active_projects,
                'percentage' => $plan->max_active_projects > 0 ? (($subscription->current_active_projects ?? 0) / $plan->max_active_projects) * 100 : 0,
            ],
            'employees' => [
                'current' => $subscription->current_employees ?? 0,
                'limit' => $plan->max_employees,
                'percentage' => $plan->max_employees > 0 ? (($subscription->current_employees ?? 0) / $plan->max_employees) * 100 : 0,
            ],
            'right_hand_employees' => [
                'current' => $subscription->current_right_hand_employees ?? 0,
                'limit' => $plan->max_right_hand_employees,
                'percentage' => $plan->max_right_hand_employees > 0 ? (($subscription->current_right_hand_employees ?? 0) / $plan->max_right_hand_employees) * 100 : 0,
            ],
        ];
        
        $nearLimitResources = [];
        $atLimitResources = [];
        
        foreach ($resourceUsage as $resource => $usage) {
            if ($usage['percentage'] >= 100) {
                $atLimitResources[] = $resource;
            } elseif ($usage['percentage'] >= 80) {
                $nearLimitResources[] = $resource;
            }
        }
        
        $resourceNames = [
            'active_projects' => 'активных проектов',
            'employees' => 'сотрудников',
            'right_hand_employees' => 'правых рук',
            'estimate_templates' => 'шаблонов смет',
        ];
    @endphp

    @if(count($atLimitResources) > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                Лимиты исчерпаны!
            </h6>
            <p class="mb-2">
                Достигнуты лимиты по следующим ресурсам:
            </p>
            <ul class="mb-2">
                @foreach($atLimitResources as $resource)
                    <li>{{ $resourceNames[$resource] ?? $resource }}</li>
                @endforeach
            </ul>
            <hr>
            <p class="mb-0">
                <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-arrow-up-circle"></i> Обновить тариф
                </a>
                <a href="{{ route('subscriptions.manage') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-gear"></i> Управление подпиской
                </a>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(count($nearLimitResources) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
             <h6 class="alert-heading">
                <i class="bi bi-exclamation-triangle"></i> 
                Приближение к лимитам
                 @foreach($nearLimitResources as $resource)
                    @php $usage = $resourceUsage[$resource] @endphp
                   
                        {{ $resourceNames[$resource] ?? $resource }}: 
                        {{ $usage['current'] }}/{{ $usage['limit'] }} 
                        ({{ round($usage['percentage']) }}%)
                   
                @endforeach
            </h6>
         
         
         
               
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         
            
          
        </div>
    @endif

    @if(Auth::user()->activeSubscription->isExpiringSoon())
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="bi bi-calendar-x"></i> 
                Подписка истекает
            </h6>
            <p class="mb-2">
                Ваша подписка "{{ Auth::user()->activeSubscription->subscriptionPlan->name }}" 
                истекает {{ Auth::user()->activeSubscription->expires_at->format('d.m.Y') }}.
            </p>
            <hr>
            <p class="mb-0">
                <a href="{{ route('subscriptions.manage') }}" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-arrow-repeat"></i> Продлить подписку
                </a>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@elseif(Auth::check())
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6 class="alert-heading">
            <i class="bi bi-exclamation-triangle"></i> 
            Нет активной подписки
        </h6>
        <p class="mb-2">
            Для использования всех функций системы необходимо оформить подписку.
        </p>
        <hr>
        <p class="mb-0">
            <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-credit-card"></i> Выбрать тариф
            </a>
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
