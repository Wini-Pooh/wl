@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-check-circle"></i> Подписка успешно оформлена!
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h5 class="text-success mb-3">Добро пожаловать в {{ $subscription->subscriptionPlan->name }}!</h5>
                    
                    <div class="alert alert-light">
                        <div class="row text-start">
                            <div class="col-sm-6">
                                <strong>Тариф:</strong><br>
                                {{ $subscription->subscriptionPlan->name }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Период:</strong><br>
                                {{ $subscription->billing_period === 'yearly' ? 'Годовая' : 'Месячная' }}
                            </div>
                            <div class="col-sm-6 mt-2">
                                <strong>Действует до:</strong><br>
                                {{ $subscription->expires_at->format('d.m.Y') }}
                            </div>
                            <div class="col-sm-6 mt-2">
                                <strong>Сумма:</strong><br>
                                {{ number_format($subscription->paid_amount, 0, ',', ' ') }} ₽
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Что вам теперь доступно:</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> {{ $subscription->subscriptionPlan->max_active_projects }} активных проектов</li>
                            <li><i class="bi bi-check text-success"></i> {{ $subscription->subscriptionPlan->project_storage_limit_mb }} МБ на каждый проект</li>
                            <li><i class="bi bi-check text-success"></i> {{ $subscription->subscriptionPlan->max_employees }} сотрудников</li>
                            <li><i class="bi bi-check text-success"></i> Все функции вашего тарифа</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house"></i> Перейти в личный кабинет
                        </a>
                        <a href="{{ route('subscriptions.manage') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-gear"></i> Управление подпиской
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
