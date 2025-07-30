@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>Тест ролей пользователя</h2>
            
            @if(Auth::check())
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Информация о пользователе: {{ Auth::user()->name ?? 'Неизвестно' }}</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Методы проверки ролей:</h6>
                                <ul>
                                    <li>Auth::check(): {{ Auth::check() ? 'true' : 'false' }}</li>
                                    <li>Auth::user()->isAdmin(): {{ Auth::user()->isAdmin() ? 'true' : 'false' }}</li>
                                    <li>Auth::user()->isPartner(): {{ Auth::user()->isPartner() ? 'true' : 'false' }}</li>
                                    <li>Auth::user()->isEmployee(): {{ Auth::user()->isEmployee() ? 'true' : 'false' }}</li>
                                    <li>Auth::user()->isForeman(): {{ Auth::user()->isForeman() ? 'true' : 'false' }}</li>
                                    <li>Auth::user()->isEstimator(): {{ Auth::user()->isEstimator() ? 'true' : 'false' }}</li>
                                    <li>Auth::user()->isClient(): {{ Auth::user()->isClient() ? 'true' : 'false' }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Логика кнопок:</h6>
                                <ul>
                                    <li>Старая логика (Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isEmployee()): 
                                        {{ (Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isEmployee()) ? 'ПОКАЗАТЬ' : 'СКРЫТЬ' }}
                                    </li>
                                    <li>Новая логика (!Auth::user()->isClient()): 
                                        {{ !Auth::user()->isClient() ? 'ПОКАЗАТЬ' : 'СКРЫТЬ' }}
                                    </li>
                                </ul>
                                
                                <h6>Видимость кнопок:</h6>
                                @if(!Auth::user()->isClient())
                                    <button class="btn btn-success">✅ Кнопка ВИДНА (новая логика)</button>
                                @else
                                    <button class="btn btn-danger">❌ Кнопка СКРЫТА (новая логика)</button>
                                @endif
                                
                                <br><br>
                                
                                @if(Auth::user()->isAdmin() || Auth::user()->isPartner() || Auth::user()->isEmployee())
                                    <button class="btn btn-success">✅ Кнопка ВИДНА (старая логика)</button>
                                @else
                                    <button class="btn btn-danger">❌ Кнопка СКРЫТА (старая логика)</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h6>Все роли пользователя:</h6>
                        <ul>
                            @foreach(Auth::user()->roles as $role)
                                <li>{{ $role->name }} ({{ $role->display_name ?? 'No display name' }})</li>
                            @endforeach
                        </ul>
                        
                        <h6>Основная роль:</h6>
                        <p>{{ Auth::user()->defaultRole->name ?? 'Не установлена' }}</p>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    Пользователь не авторизован
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
