@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>Тест кнопок для проекта #{{ $project->id }}</h1>
    
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Диагностика пользователя и ролей</h5>
        </div>
        <div class="card-body">
            @auth
            <p><strong>Пользователь:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
            <p><strong>ID пользователя:</strong> {{ Auth::user()->id }}</p>
            <p><strong>Роли:</strong> 
                @if(Auth::user()->roles->count() > 0)
                    {{ Auth::user()->roles->pluck('name')->join(', ') }}
                @else
                    Нет ролей
                @endif
            </p>
            <p><strong>Роль по умолчанию:</strong> 
                @if(Auth::user()->defaultRole)
                    {{ Auth::user()->defaultRole->name }}
                @else
                    Не установлена
                @endif
            </p>
            @else
            <p>Пользователь не авторизован</p>
            @endauth
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Проверка методов UserRoleHelper</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Метод</th>
                        <th>Результат</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>canManageProjects()</td>
                        <td>
                            @if(\App\Helpers\UserRoleHelper::canManageProjects())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                        <td>Может создавать/редактировать проекты</td>
                    </tr>
                    <tr>
                        <td>canSeeActionButtons()</td>
                        <td>
                            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                        <td>Может видеть кнопки действий</td>
                    </tr>
                    <tr>
                        <td>canAccessProjects()</td>
                        <td>
                            @if(\App\Helpers\UserRoleHelper::canAccessProjects())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                        <td>Может работать с проектами</td>
                    </tr>
                    <tr>
                        <td>canManageEmployees()</td>
                        <td>
                            @if(\App\Helpers\UserRoleHelper::canManageEmployees())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                        <td>Может управлять сотрудниками</td>
                    </tr>
                    <tr>
                        <td>getUserRoleDisplay()</td>
                        <td><span class="badge bg-info">{{ \App\Helpers\UserRoleHelper::getUserRoleDisplay() }}</span></td>
                        <td>Отображаемая роль</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Тест методов User</h5>
        </div>
        <div class="card-body">
            @auth
            <table class="table">
                <thead>
                    <tr>
                        <th>Метод</th>
                        <th>Результат</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>isAdmin()</td>
                        <td>
                            @if(Auth::user()->isAdmin())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>isPartner()</td>
                        <td>
                            @if(Auth::user()->isPartner())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>isEmployee()</td>
                        <td>
                            @if(Auth::user()->isEmployee())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>isClient()</td>
                        <td>
                            @if(Auth::user()->isClient())
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>hasRole('admin')</td>
                        <td>
                            @if(Auth::user()->hasRole('admin'))
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>hasRole('partner')</td>
                        <td>
                            @if(Auth::user()->hasRole('partner'))
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>hasRole('employee')</td>
                        <td>
                            @if(Auth::user()->hasRole('employee'))
                                <span class="badge bg-success">true</span>
                            @else
                                <span class="badge bg-danger">false</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            @else
            <p>Пользователь не авторизован</p>
            @endauth
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Проверка отображения кнопок</h5>
        </div>
        <div class="card-body">
            <h6>Кнопка "Редактировать" (как в show.blade.php)</h6>
            @if(\App\Helpers\UserRoleHelper::canManageProjects())
                <a href="{{ route('partner.projects.edit', $project) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Редактировать проект
                </a>
                <div class="alert alert-success mt-2">✅ Кнопка отображается</div>
            @else
                <div class="alert alert-warning">⚠️ Кнопка скрыта</div>
            @endif
            
            <hr>
            
            <h6>Кнопки действий финансов</h6>
            @if(\App\Helpers\UserRoleHelper::canManageProjects())
                <button class="btn btn-primary btn-sm me-2">
                    <i class="bi bi-plus-circle me-1"></i>Добавить работу
                </button>
                <button class="btn btn-success btn-sm me-2">
                    <i class="bi bi-plus-circle me-1"></i>Добавить материал
                </button>
                <button class="btn btn-warning btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Добавить транспорт
                </button>
                <div class="alert alert-success mt-2">✅ Кнопки финансов отображаются</div>
            @else
                <div class="alert alert-warning">⚠️ Кнопки финансов скрыты</div>
            @endif
            
            <hr>
            
            <h6>Кнопки расписания</h6>
            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                <button class="btn btn-primary btn-sm me-2">
                    <i class="bi bi-plus-circle me-1"></i>Добавить этап
                </button>
                <button class="btn btn-info btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Добавить событие
                </button>
                <div class="alert alert-success mt-2">✅ Кнопки расписания отображаются</div>
            @else
                <div class="alert alert-warning">⚠️ Кнопки расписания скрыты</div>
            @endif
            
            <hr>
            
            <h6>Кнопки вкладок загрузки файлов</h6>
            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                <button class="btn btn-primary btn-sm me-2" data-modal-type="photo">
                    <i class="bi bi-plus-lg me-2"></i>Загрузить фото
                </button>
                <button class="btn btn-primary btn-sm me-2" data-modal-type="design">
                    <i class="bi bi-plus-lg me-2"></i>Загрузить дизайн
                </button>
                <button class="btn btn-primary btn-sm me-2" data-modal-type="scheme">
                    <i class="bi bi-plus-lg me-2"></i>Загрузить схему
                </button>
                <button class="btn btn-primary btn-sm" data-modal-type="document">
                    <i class="bi bi-plus-lg me-2"></i>Загрузить документ
                </button>
                <div class="alert alert-success mt-2">✅ Кнопки загрузки файлов отображаются</div>
            @else
                <div class="alert alert-warning">⚠️ Кнопки загрузки файлов скрыты</div>
            @endif
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('partner.projects.show', $project) }}" class="btn btn-primary">
            Перейти к реальной странице проекта
        </a>
        <a href="{{ route('partner.projects.index') }}" class="btn btn-secondary">
            К списку проектов
        </a>
    </div>
</div>
@endsection
