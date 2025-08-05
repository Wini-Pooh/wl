@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Тарифные планы</h4>
                </div>
                <div class="card-body">
                 

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Характеристика</th>
                                    @foreach($plans as $plan)
                                        <th scope="col" class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'bg-primary' : '' }}">
                                            {{ $plan->name }}
                                            @if($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id)
                                                <br><small class="badge bg-light text-dark">Текущий</small>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Стоимость -->
                                <tr>
                                    <td><strong>Стоимость в месяц</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->monthly_price > 0)
                                                <div class="h5 text-primary mb-1">
                                                    {{ number_format($plan->monthly_price, 0, ',', ' ') }} ₽
                                                </div>
                                            @else
                                                <div class="h5 text-success mb-1">
                                                    Бесплатно
                                                </div>
                                            @endif
                                            @if($plan->yearly_price > 0)
                                                <small class="text-muted">
                                                    {{ number_format($plan->yearly_price, 0, ',', ' ') }} ₽/год<br>
                                                    <span class="text-success">скидка {{ $plan->yearly_discount_percent }}%</span>
                                                </small>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Активные проекты -->
                                <tr>
                                    <td><strong>Активных проектов</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            <span class="badge bg-success fs-6">{{ $plan->max_active_projects }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Хранилище -->
                                <tr>
                                    <td><strong>Хранилище на проект</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            <span class="badge bg-info fs-6">{{ $plan->project_storage_limit_mb }} МБ</span>
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Шаблоны смет -->
                                <tr>
                                    <td><strong>Шаблонов смет</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->max_estimate_templates > 0)
                                                <span class="badge bg-warning fs-6">{{ $plan->max_estimate_templates }}</span>
                                            @else
                                                <span class="badge bg-success fs-6">Неограниченно</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Сотрудники -->
                                <tr>
                                    <td><strong>Сотрудников</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            <span class="badge bg-secondary fs-6">{{ $plan->max_employees }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Правые руки -->
                                <tr>
                                    <td><strong>Правых рук</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            <span class="badge bg-secondary fs-6">{{ $plan->max_right_hand_employees }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Доступ к сметам -->
                                <tr>
                                    <td><strong>Доступ к сметам</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->access_estimates)
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Доступ к документам -->
                                <tr>
                                    <td><strong>Доступ к документам</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->access_documents)
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Доступ к проектам -->
                                <tr>
                                    <td><strong>Доступ к проектам</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->access_projects)
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Аналитика -->
                                <tr>
                                    <td><strong>Аналитика</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->access_analytics)
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Управление сотрудниками -->
                                <tr>
                                    <td><strong>Управление сотрудниками</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->access_employees)
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Онлайн обучение -->
                                <tr>
                                    <td><strong>Онлайн обучение</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($plan->access_online_training)
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            @else
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                
                                <!-- Действия -->
                                <tr class="table-light">
                                    <td><strong>Действие</strong></td>
                                    @foreach($plans as $plan)
                                        <td class="text-center {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : '' }}">
                                            @if($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id)
                                                <button class="btn btn-outline-primary btn-sm" disabled>
                                                    Активный план
                                                </button>
                                            @elseif($currentSubscription)
                                                <a href="{{ route('subscriptions.select-period', $plan) }}" class="btn btn-primary btn-sm">
                                                    Изменить план
                                                </a>
                                            @else
                                                <a href="{{ route('subscriptions.select-period', $plan) }}" class="btn btn-primary btn-sm">
                                                    Выбрать план
                                                </a>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-light">
                                <h6><i class="bi bi-info-circle"></i> Дополнительная информация</h6>
                                <ul class="mb-0">
                                    <li>Тестовый план автоматически назначается новым пользователям</li>
                                    <li>Все планы включают полный доступ к базовому функционалу системы</li>
                                    <li>Годовая подписка предоставляет скидку 15%</li>
                                    <li>Возможность смены тарифного плана в любое время</li>
                                    <li>В дальнейшем планируется добавление нового функционала</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
