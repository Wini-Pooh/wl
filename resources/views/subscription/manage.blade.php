@extends('layouts.app')

@section('title', 'Управление подпиской')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Управление подпиской</h1>
                @if($subscription && $subscription->subscriptionPlan && $subscription->subscriptionPlan->name !== 'Master')
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-up-circle"></i>
                        Улучшить план
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Текущий план -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-star"></i>
                                Текущий тарифный план
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($subscription && $subscription->subscriptionPlan)
                                <h4 class="text-primary">{{ $subscription->subscriptionPlan->name }}</h4>
                                <p class="text-muted">{{ $subscription->subscriptionPlan->description }}</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Статус:</strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : 'warning' }}">
                                            {{ $subscription->status === 'active' ? 'Активна' : 'Неактивна' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Дата окончания:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : 'Бессрочно' }}
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Стоимость:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ $subscription->subscriptionPlan ? $subscription->subscriptionPlan->monthly_price : 0 }} ₽/мес
                                    </div>
                                </div>
                            @else
                                <div class="text-center">
                                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Нет активной подписки</h5>
                                    <p class="text-muted">Выберите тарифный план для начала работы</p>
                                    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
                                        Выбрать план
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Использование ресурсов -->
                <div class="col-md-6 mb-4">
                @if($subscription && $subscription->subscriptionPlan)
                        <x-subscription-limits :subscription="$subscription" />
                    @endif
                </div>
            </div>

            @if($subscription && $subscription->subscriptionPlan)
                <div class="row">
                    <!-- Хранилище -->
                    <div class="col-lg-6 mb-4">
                        <x-storage-usage :storageInfo="$storageInfo" />
                    </div>

                    <!-- Лимиты плана -->
                    <div class="col-lg-6 mb-4">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-list-check"></i>
                                    Ограничения плана "{{ $subscription->subscriptionPlan ? $subscription->subscriptionPlan->name : 'Неизвестно' }}"
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-8">
                                        <i class="bi bi-folder"></i>
                                        Максимальное количество проектов:
                                    </div>
                                    <div class="col-4 text-end">
                                        <strong>{{ $subscription->subscriptionPlan ? $subscription->subscriptionPlan->max_active_projects : 0 }}</strong>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-8">
                                        <i class="bi bi-people"></i>
                                        Максимальное количество сотрудников:
                                    </div>
                                    <div class="col-4 text-end">
                                        <strong>{{ $subscription->subscriptionPlan ? $subscription->subscriptionPlan->max_employees : 0 }}</strong>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-8">
                                        <i class="bi bi-hdd"></i>
                                        Лимит хранилища:
                                    </div>
                                    <div class="col-4 text-end">
                                        <strong>{{ $subscription->subscriptionPlan ? $subscription->subscriptionPlan->project_storage_limit_mb : 0 }} МБ</strong>
                                    </div>
                                </div>

                                @if($subscription->subscriptionPlan && $subscription->subscriptionPlan->features)
                                    <hr>
                                    <h6>Дополнительные возможности:</h6>
                                    <ul class="list-unstyled">
                                        @foreach($subscription->subscriptionPlan->features as $feature)
                                            <li class="mb-1">
                                                <i class="bi bi-check-circle text-success"></i>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- История платежей -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock-history"></i>
                                    История подписки
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <strong>Дата активации:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $subscription->created_at ? $subscription->created_at->format('d.m.Y H:i') : 'Неизвестно' }}
                                    </div>
                                </div>
                                
                                @if($subscription->ends_at)
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Дата окончания:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $subscription->ends_at->format('d.m.Y H:i') }}
                                            @if($subscription->ends_at->isPast())
                                                <span class="badge bg-danger ms-2">Истекла</span>
                                            @elseif($subscription->ends_at->diffInDays() <= 7)
                                                <span class="badge bg-warning ms-2">Истекает скоро</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="row">
                                    <div class="col-sm-4">
                                        <strong>Последнее обновление:</strong>
                                    </div>
                                    <div class="col-sm-8">
                                        {{ $subscription->updated_at ? $subscription->updated_at->format('d.m.Y H:i') : 'Неизвестно' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
