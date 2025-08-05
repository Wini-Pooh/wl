@extends('layouts.app')

@section('title', 'Система тарифных планов')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Система тарифных планов</h1>
                <span class="badge bg-success fs-6">Активна</span>
            </div>

            <div class="alert alert-info" role="alert">
                <h5 class="alert-heading">
                    <i class="bi bi-info-circle"></i>
                    Система тарифных планов успешно интегрирована!
                </h5>
                <p class="mb-0">
                    Система ограничивает пользователей по количеству проектов, сотрудников и объему хранилища 
                    в зависимости от выбранного тарифного плана.
                </p>
            </div>

            <div class="row">
                <!-- Доступные тарифы -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-star"></i>
                                Доступные тарифные планы
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Trial план -->
                                <div class="col-md-4 mb-3">
                                    <div class="card border-secondary h-100">
                                        <div class="card-header bg-secondary text-white text-center">
                                            <h6 class="mb-0">Trial</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <h4>0 ₽</h4>
                                                <small class="text-muted">в месяц</small>
                                            </div>
                                            <ul class="list-unstyled">
                                                <li><i class="bi bi-check text-success"></i> 3 проекта</li>
                                                <li><i class="bi bi-check text-success"></i> 2 сотрудника</li>
                                                <li><i class="bi bi-check text-success"></i> 300 МБ хранилища</li>
                                                <li><i class="bi bi-check text-success"></i> Базовые функции</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Successful план -->
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary h-100">
                                        <div class="card-header bg-primary text-white text-center">
                                            <h6 class="mb-0">Successful</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <h4>999 ₽</h4>
                                                <small class="text-muted">в месяц</small>
                                            </div>
                                            <ul class="list-unstyled">
                                                <li><i class="bi bi-check text-success"></i> 7 проектов</li>
                                                <li><i class="bi bi-check text-success"></i> 5 сотрудников</li>
                                                <li><i class="bi bi-check text-success"></i> 600 МБ хранилища</li>
                                                <li><i class="bi bi-check text-success"></i> Расширенные функции</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Master план -->
                                <div class="col-md-4 mb-3">
                                    <div class="card border-warning h-100">
                                        <div class="card-header bg-warning text-dark text-center">
                                            <h6 class="mb-0">Master</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <h4>1999 ₽</h4>
                                                <small class="text-muted">в месяц</small>
                                            </div>
                                            <ul class="list-unstyled">
                                                <li><i class="bi bi-check text-success"></i> 15 проектов</li>
                                                <li><i class="bi bi-check text-success"></i> 10 сотрудников</li>
                                                <li><i class="bi bi-check text-success"></i> 1500 МБ хранилища</li>
                                                <li><i class="bi bi-check text-success"></i> Все функции</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Интегрированные компоненты -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-gear"></i>
                                Интегрированные компоненты
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        <i class="bi bi-database"></i>
                                        Модели данных
                                    </span>
                                    <span class="badge bg-success rounded-pill">✓</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        <i class="bi bi-shield"></i>
                                        Middleware защиты
                                    </span>
                                    <span class="badge bg-success rounded-pill">✓</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        <i class="bi bi-controller"></i>
                                        Контроллеры
                                    </span>
                                    <span class="badge bg-success rounded-pill">✓</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        <i class="bi bi-eye"></i>
                                        UI компоненты
                                    </span>
                                    <span class="badge bg-success rounded-pill">✓</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        <i class="bi bi-hdd"></i>
                                        Контроль хранилища
                                    </span>
                                    <span class="badge bg-success rounded-pill">✓</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>
                                        <i class="bi bi-terminal"></i>
                                        Artisan команды
                                    </span>
                                    <span class="badge bg-success rounded-pill">✓</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Функциональность -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check"></i>
                                Реализованная функциональность
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Ограничения по ресурсам</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle text-success"></i> Лимит на количество проектов</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Лимит на количество сотрудников</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Лимит на объем хранилища</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Автоматическая проверка лимитов</li>
                                    </ul>

                                    <h6 class="text-primary mt-4">Middleware и защита</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle text-success"></i> CheckSubscriptionLimits</li>
                                        <li><i class="bi bi-check-circle text-success"></i> CheckFeatureAccess</li>
                                        <li><i class="bi bi-check-circle text-success"></i> CheckFileUploadLimits</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Автоматическое применение к маршрутам</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">Интеграция с контроллерами</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle text-success"></i> ProjectController - проверка лимитов проектов</li>
                                        <li><i class="bi bi-check-circle text-success"></i> EmployeeController - проверка лимитов сотрудников</li>
                                        <li><i class="bi bi-check-circle text-success"></i> StorageLimitService - контроль хранилища</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Трейт HasSubscriptionLimits</li>
                                    </ul>

                                    <h6 class="text-primary mt-4">UI и уведомления</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle text-success"></i> Компонент отображения лимитов</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Компонент статистики хранилища</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Предупреждения о приближении к лимитам</li>
                                        <li><i class="bi bi-check-circle text-success"></i> Страница управления подпиской</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Команды -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-terminal"></i>
                                Доступные команды
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Создание тарифных планов:</h6>
                                    <code>php artisan subscription:setup-plans</code>
                                    <p class="text-muted small mt-1">Создает базовые тарифные планы (Trial, Successful, Master)</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Тестирование системы:</h6>
                                    <code>php artisan subscription:test</code>
                                    <p class="text-muted small mt-1">Тестирует работу системы подписок (требует подключение к БД)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
