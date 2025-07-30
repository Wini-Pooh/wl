@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>Тест видимости кнопок действий</h2>
            
            @if(Auth::check())
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Информация о пользователе: {{ Auth::user()->name ?? 'Неизвестно' }}</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Методы проверки ролей:</h6>
                                <ul>
                                    <li>isAdmin(): {{ Auth::user()->isAdmin() ? 'true' : 'false' }}</li>
                                    <li>isPartner(): {{ Auth::user()->isPartner() ? 'true' : 'false' }}</li>
                                    <li>isEmployee(): {{ Auth::user()->isEmployee() ? 'true' : 'false' }}</li>
                                    <li>isForeman(): {{ Auth::user()->isForeman() ? 'true' : 'false' }}</li>
                                    <li>isEstimator(): {{ Auth::user()->isEstimator() ? 'true' : 'false' }}</li>
                                    <li>isClient(): {{ Auth::user()->isClient() ? 'true' : 'false' }}</li>
                                </ul>
                                
                                <h6>Основная роль:</h6>
                                <p><strong>{{ Auth::user()->defaultRole->name ?? 'Не установлена' }}</strong></p>
                                
                                <h6>Дополнительные роли:</h6>
                                <ul>
                                    @foreach(Auth::user()->roles as $role)
                                        <li>{{ $role->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Логика кнопок (Helper методы):</h6>
                                <ul>
                                    <li>canManageProjects(): 
                                        {{ \App\Helpers\UserRoleHelper::canManageProjects() ? 'true' : 'false' }}
                                    </li>
                                    <li>canSeeActionButtons(): 
                                        {{ \App\Helpers\UserRoleHelper::canSeeActionButtons() ? 'true' : 'false' }}
                                    </li>
                                    <li>canAccessProjects(): 
                                        {{ \App\Helpers\UserRoleHelper::canAccessProjects() ? 'true' : 'false' }}
                                    </li>
                                </ul>
                                
                                <h6>Видимость кнопок проектов:</h6>
                                @if(\App\Helpers\UserRoleHelper::canManageProjects())
                                    <button class="btn btn-success mb-2">✅ Кнопка "Редактировать" ВИДНА</button>
                                @else
                                    <button class="btn btn-danger mb-2">❌ Кнопка "Редактировать" СКРЫТА</button>
                                @endif
                                
                                <br>
                                
                                @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                                    <button class="btn btn-success">✅ Кнопки действий ВИДНЫ</button>
                                @else
                                    <button class="btn btn-danger">❌ Кнопки действий СКРЫТЫ</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h6>Тест финансовых кнопок (как в finance.blade.php):</h6>
                        
                        @php
                            $isClient = Auth::check() && Auth::user()->isClient();
                        @endphp
                        
                        <p>PHP переменная $isClient: <strong>{{ $isClient ? 'true' : 'false' }}</strong></p>
                        
                        @if(!$isClient)
                            <button class="btn btn-primary me-2">✅ Добавить работу (ВИДНА)</button>
                            <button class="btn btn-success me-2">✅ Добавить материал (ВИДНА)</button>
                            <button class="btn btn-warning">✅ Добавить транспорт (ВИДНА)</button>
                        @else
                            <p class="text-muted">❌ Кнопки добавления скрыты для клиентов</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-warning">Пользователь не авторизован</div>
            @endif
        </div>
    </div>
</div>
@endsection
