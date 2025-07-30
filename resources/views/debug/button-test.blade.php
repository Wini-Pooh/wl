@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2>Тест отображения кнопок действий</h2>
            
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
                                    <li>!Auth::user()->isClient(): {{ !Auth::user()->isClient() ? 'true' : 'false' }}</li>
                                    <li>Auth::check() && !Auth::user()->isClient(): {{ Auth::check() && !Auth::user()->isClient() ? 'true' : 'false' }}</li>
                                </ul>
                                
                                <h6>Роли в БД:</h6>
                                <ul>
                                    <li>Default Role: {{ Auth::user()->defaultRole->name ?? 'Нет' }}</li>
                                    @foreach(Auth::user()->roles as $role)
                                        <li>Additional Role: {{ $role->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5>Тест кнопок действий</h5>
                        
                        <div class="mb-3">
                            <h6>Кнопка "Загрузить фото" (должна показываться для всех кроме клиентов):</h6>
                            @if(Auth::check() && !Auth::user()->isClient())
                                <button class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-2"></i>Загрузить фото
                                </button>
                                <span class="text-success ms-2">✓ Кнопка видна</span>
                            @else
                                <span class="text-danger">✗ Кнопка скрыта</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <h6>Кнопка "Редактировать проект" (должна показываться для всех кроме клиентов):</h6>
                            @if(Auth::check() && !Auth::user()->isClient())
                                <button class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </button>
                                <span class="text-success ms-2">✓ Кнопка видна</span>
                            @else
                                <span class="text-danger">✗ Кнопка скрыта</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <h6>Кнопки финансов (должны показываться для всех кроме клиентов):</h6>
                            @if(Auth::check() && !Auth::user()->isClient())
                                <button class="btn btn-success btn-sm">
                                    <i class="bi bi-plus"></i> Добавить работу
                                </button>
                                <button class="btn btn-info btn-sm">
                                    <i class="bi bi-plus"></i> Добавить материал
                                </button>
                                <span class="text-success ms-2">✓ Кнопки видны</span>
                            @else
                                <span class="text-danger">✗ Кнопки скрыты</span>
                            @endif
                        </div>

                        <div class="mt-4">
                            <h6>Ожидаемое поведение:</h6>
                            <ul>
                                <li><strong>Партнеры</strong> - должны видеть ВСЕ кнопки действий</li>
                                <li><strong>Сотрудники</strong> - должны видеть ВСЕ кнопки действий</li>
                                <li><strong>Прорабы</strong> - должны видеть ВСЕ кнопки действий</li>
                                <li><strong>Администраторы</strong> - должны видеть ВСЕ кнопки действий</li>
                                <li><strong>Клиенты</strong> - НЕ должны видеть кнопки действий (только просмотр)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">Пользователь не авторизован</div>
            @endif
        </div>
    </div>
</div>
@endsection
