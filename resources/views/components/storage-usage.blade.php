@props(['storageInfo' => null])

@php
    $user = auth()->user();
    if (!$storageInfo && $user && method_exists($user, 'activeSubscription')) {
        $subscription = $user->activeSubscription();
        $storageInfo = $subscription ? [
            'current' => 0, // Будет рассчитано позже
            'limit' => $subscription->subscriptionPlan->project_storage_limit_mb,
            'percentage' => 0
        ] : null;
    }
@endphp

@if($storageInfo && $storageInfo['limit'] > 0)
<div class="card border-info mb-3">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">
            <i class="bi bi-hdd"></i>
            Использование хранилища
        </h6>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted">Использовано:</span>
            <strong>{{ $storageInfo['current_formatted'] ?? '0 МБ' }} / {{ $storageInfo['limit_formatted'] ?? $storageInfo['limit'] . ' МБ' }}</strong>
        </div>
        
        @php
            $percentage = $storageInfo['percentage'] ?? 0;
            $progressClass = 'bg-success';
            if ($percentage >= 90) {
                $progressClass = 'bg-danger';
            } elseif ($percentage >= 70) {
                $progressClass = 'bg-warning';
            }
        @endphp
        
        <div class="progress mb-2" style="height: 8px;">
            <div class="progress-bar {{ $progressClass }}" 
                 role="progressbar" 
                 style="width: {{ $percentage }}%"
                 aria-valuenow="{{ $percentage }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
            </div>
        </div>
        
        <div class="row">
            <div class="col-6">
                <small class="text-muted">
                    {{ round($percentage, 1) }}% использовано
                </small>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted">
                    Доступно: {{ $storageInfo['available_formatted'] ?? 'N/A' }}
                </small>
            </div>
        </div>
        
        @if($percentage >= 90)
            <div class="alert alert-danger mt-2 mb-0" role="alert">
                <small>
                    <i class="bi bi-exclamation-triangle"></i>
                    Хранилище почти заполнено! Освободите место или обновите тарифный план.
                </small>
            </div>
        @elseif($percentage >= 80)
            <div class="alert alert-warning mt-2 mb-0" role="alert">
                <small>
                    <i class="bi bi-info-circle"></i>
                    Использовано более 80% хранилища. Рекомендуем освободить место.
                </small>
            </div>
        @endif
    </div>
</div>
@endif
