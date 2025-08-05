@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Управление подпиской</h4>
                </div>
                <div class="card-body">
                    @if($subscription)
                        <!-- Информация о текущей подписке -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $subscription->subscriptionPlan->name }}</h5>
                                        <p class="card-text">
                                            <strong>Период:</strong> {{ $subscription->billing_period === 'yearly' ? 'Годовая' : 'Месячная' }}<br>
                                            <strong>Статус:</strong> 
                                            @if($subscription->status === 'active')
                                                <span class="badge bg-success">Активна</span>
                                            @elseif($subscription->status === 'cancelled')
                                                <span class="badge bg-warning">Отменена</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                            @endif
                                        </p>
                                        <p class="card-text">
                                            <strong>Действует до:</strong> {{ $subscription->expires_at->format('d.m.Y H:i') }}<br>
                                            @if($subscription->isExpiringSoon())
                                                <small class="text-warning">
                                                    <i class="bi bi-exclamation-triangle"></i> 
                                                    Подписка истекает через {{ $subscription->expires_at->diffInDays(now()) }} дней
                                                </small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Использование ресурсов</h5>
                                        @foreach($resourceUsage as $resource => $usage)
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between">
                                                    <span>
                                                        @if($resource === 'active_projects')
                                                            Активные проекты
                                                        @elseif($resource === 'employees')
                                                            Сотрудники
                                                        @elseif($resource === 'right_hand_employees')
                                                            Правые руки
                                                        @elseif($resource === 'estimate_templates')
                                                            Шаблоны смет
                                                        @endif
                                                    </span>
                                                    <span>{{ $usage['current'] }} / {{ $usage['limit'] }}</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar 
                                                        @if($usage['percentage'] > 90) bg-danger
                                                        @elseif($usage['percentage'] > 75) bg-warning  
                                                        @else bg-success @endif" 
                                                        role="progressbar" 
                                                        style="width: {{ min(100, $usage['percentage']) }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Действия с подпиской -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Управление подпиской</h5>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-up-circle"></i> Изменить план
                                    </a>
                                    
                                    @if($subscription->status === 'active')
                                        <form method="POST" action="{{ route('subscriptions.cancel') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning" 
                                                onclick="return confirm('Вы уверены, что хотите отменить подписку? Доступ сохранится до окончания оплаченного периода.')">
                                                <i class="bi bi-x-circle"></i> Отменить подписку
                                            </button>
                                        </form>
                                    @elseif($subscription->status === 'cancelled')
                                        <form method="POST" action="{{ route('subscriptions.resume') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success">
                                                <i class="bi bi-play-circle"></i> Возобновить подписку
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Информация об автопродлении -->
                        @if($subscription->auto_renewal && $subscription->next_payment_at)
                            <div class="alert alert-info mt-4">
                                <h6><i class="bi bi-arrow-repeat"></i> Автопродление включено</h6>
                                <p class="mb-0">
                                    Следующий платеж: {{ $subscription->next_payment_at->format('d.m.Y') }}<br>
                                    Сумма: {{ number_format($subscription->subscriptionPlan->getPriceForPeriod($subscription->billing_period), 0, ',', ' ') }} ₽
                                </p>
                            </div>
                        @endif

                        <!-- История платежей -->
                        <div class="mt-4">
                            <h5>История платежей</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>Сумма</th>
                                            <th>Период</th>
                                            <th>Статус</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $subscription->last_payment_at ? $subscription->last_payment_at->format('d.m.Y') : '-' }}</td>
                                            <td>{{ number_format($subscription->paid_amount, 0, ',', ' ') }} ₽</td>
                                            <td>{{ $subscription->billing_period === 'yearly' ? 'Годовая' : 'Месячная' }}</td>
                                            <td><span class="badge bg-success">Оплачено</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            У вас нет активной подписки. 
                            <a href="{{ route('subscriptions.index') }}" class="alert-link">Выберите тарифный план</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
