@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-speedometer2 me-2"></i>
            @if($user->isEstimator())
                Панель сметчика
            @elseif($user->isForeman())
                Панель прораба
            @else
                Панель сотрудника
            @endif
        </h2>
        <div class="text-muted">
            <i class="bi bi-person-check me-1"></i>
            {{ $user->name }}
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Всего проектов</h6>
                            <h3 class="mb-0">{{ $stats['total_projects'] }}</h3>
                            <small class="opacity-75">Доступно в системе</small>
                        </div>
                        <i class="bi bi-folder fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success  h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Активных проектов</h6>
                            <h3 class="mb-0">{{ $stats['active_projects'] }}</h3>
                            <small class="opacity-75">В работе</small>
                        </div>
                        <i class="bi bi-play-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info  h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Всего смет</h6>
                            <h3 class="mb-0">{{ $stats['total_estimates'] }}</h3>
                            <small class="opacity-75">В системе</small>
                        </div>
                        <i class="bi bi-calculator fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning  h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Смет в работе</h6>
                            <h3 class="mb-0">{{ $stats['pending_estimates'] }}</h3>
                            <small class="opacity-75">Требуют внимания</small>
                        </div>
                        <i class="bi bi-clock fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Последние проекты -->
        @if(!$user->isEstimator())
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-folder me-2"></i>
                        Последние проекты
                    </h5>
                    <a href="{{ route('employee.projects.index') }}" class="btn btn-sm btn-outline-primary">
                        Все проекты
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($projects->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($projects as $project)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $project->name }}</h6>
                                        <p class="mb-1 text-muted small">{{ $project->client_name }}</p>
                                        <small class="text-muted">{{ $project->created_at->format('d.m.Y') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge 
                                            @if($project->project_status === 'in_progress') bg-success
                                            @elseif($project->project_status === 'completed') bg-primary
                                            @else bg-secondary
                                            @endif">
                                            {{ $project->status_label }}
                                        </span>
                                        <div class="mt-1">
                                            <a href="{{ route('employee.projects.show', $project) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Открыть
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-folder-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Нет доступных проектов</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Последние сметы -->
        <div class="@if($user->isEstimator()) col-12 @else col-lg-6 @endif mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator me-2"></i>
                        Последние сметы
                    </h5>
                    <a href="{{ route('employee.estimates.index') }}" class="btn btn-sm btn-outline-primary">
                        Все сметы
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($estimates->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($estimates as $estimate)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $estimate->name }}</h6>
                                        <p class="mb-1 text-muted small">{{ $estimate->project->name }}</p>
                                        <small class="text-muted">{{ $estimate->created_at->format('d.m.Y') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge 
                                            @if($estimate->status === 'draft') bg-warning
                                            @elseif($estimate->status === 'approved') bg-success
                                            @else bg-secondary
                                            @endif">
                                            {{ $estimate->status_label }}
                                        </span>
                                        <div class="mt-1">
                                            <a href="{{ route('employee.estimates.show', $estimate) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Открыть
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calculator-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Нет доступных смет</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
