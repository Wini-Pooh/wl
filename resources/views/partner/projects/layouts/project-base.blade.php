@extends('layouts.app')

@section('content')
<div class="container-fluid fade-in">
    <!-- Заголовок страницы с мобильной адаптацией -->
    <div class="row mb-4" style="">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div class="mb-2 mb-md-0 flex-grow-1">
                    <h1 class="h4 h-md-3 mb-1 gradient-text">
                        <i class="bi bi-building me-2"></i>
                        <span class="d-md-inline">Проект #{{ $project->id }} - {{ $project->client_full_name }}</span>
                    </h1>
                </div>
                <div class="col-12 flex">
                    @if(\App\Helpers\UserRoleHelper::canManageProjects())
                    <a href="{{ route('partner.projects.edit', $project) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil"></i> 
                        <span class="d-none d-lg-inline">Редактировать</span>
                        <span class="d-lg-none d-none d-md-inline">Ред.</span>
                    </a>
                    @endif
                    <a href="{{ route('partner.projects.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> 
                        <span class="d-none d-lg-inline">Назад к списку</span>
                        <span class="d-lg-none d-none d-md-inline">Назад</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Система уведомлений -->
    <div id="alert-container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Ошибки валидации:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div id="error-alert" class="alert alert-danger d-none" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span id="error-message"></span>
        </div>
        
        <div id="success-alert" class="alert alert-success d-none" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span id="success-message"></span>
        </div>
    </div>
    
    <!-- Навигация по разделам проекта -->
    <div class="card shadow-blue mb-4" id="project-navigation-card">
        <div class="card-header p-0">
            <!-- Мобильная навигация -->
            <ul class="nav nav-tabs-mobile d-md-none" id="project-navigation-mobile" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.show') || request()->routeIs('partner.projects.main') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.main', $project) }}">
                        <i class="bi bi-info-circle me-1"></i>Инфо
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.finance') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.finance', $project) }}">
                        <i class="bi bi-cash-coin me-1"></i>Финансы
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.schedule') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.schedule', $project) }}">
                        <i class="bi bi-calendar3 me-1"></i>График
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.photos') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.photos', $project) }}">
                        <i class="bi bi-camera me-1"></i>Фото
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.design') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.design', $project) }}">
                        <i class="bi bi-palette me-1"></i>Дизайн
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.schemes') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.schemes', $project) }}">
                        <i class="bi bi-diagram-3 me-1"></i>Схемы
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.documents') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.documents', $project) }}">
                        <i class="bi bi-file-text me-1"></i>Док-ты
                    </a>
                </li>
            </ul>
            
            <!-- Десктопная навигация -->
            <ul class="nav nav-tabs d-none d-md-flex" id="project-navigation" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.show') || request()->routeIs('partner.projects.main') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.main', $project) }}">
                        <i class="bi bi-info-circle me-1"></i>
                        <span class="d-none d-md-inline">Основное</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.finance') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.finance', $project) }}">
                        <i class="bi bi-cash-coin me-1"></i>
                        <span class="d-none d-md-inline">Финансы</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.schedule') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.schedule', $project) }}">
                        <i class="bi bi-calendar3 me-1"></i>
                        <span class="d-none d-md-inline">График</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.photos') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.photos', $project) }}">
                        <i class="bi bi-camera me-1"></i>
                        <span class="d-none d-md-inline">Фото</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.design') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.design', $project) }}">
                        <i class="bi bi-palette me-1"></i>
                        <span class="d-none d-md-inline">Дизайн</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.schemes') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.schemes', $project) }}">
                        <i class="bi bi-diagram-3 me-1"></i>
                        <span class="d-none d-md-inline">Схемы</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('partner.projects.documents') ? 'active' : '' }}" 
                       href="{{ route('partner.projects.documents', $project) }}">
                        <i class="bi bi-file-text me-1"></i>
                        <span class="d-none d-md-inline">Документы</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Контент конкретной страницы -->
    @yield('page-content')
</div>

<!-- Подключение системы модальных окон -->
@include('partner.projects.tabs.modals.init-modals')

<!-- Подключение всех статических модальных окон -->
@include('partner.projects.tabs.modals.photo-modal')
@include('partner.projects.tabs.modals.scheme-modal')
@include('partner.projects.tabs.modals.design-modal')
@include('partner.projects.tabs.modals.document-modal')

@push('styles')
<link href="{{ asset('css/project-navigation.css') }}" rel="stylesheet">
<style>
/* Дополнительные стили для фотографий */
.photo-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #dee2e6;
}

.photo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.photo-preview {
    position: relative;
    overflow: hidden;
}

.photo-preview img {
    transition: transform 0.3s ease;
}

.photo-card:hover .photo-preview img {
    transform: scale(1.05);
}

.photo-badges {
    z-index: 2;
}

.photo-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 3;
}

.photo-card:hover .photo-actions {
    opacity: 1;
}

.photo-actions .btn {
    backdrop-filter: blur(4px);
    background-color: rgba(255, 255, 255, 0.9);
}

/* Стили для кастомных полей в модальном окне */
.input-group .btn-outline-secondary {
    border-left: 0;
}

.input-group .form-select:focus + .btn-outline-secondary {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Мобильная адаптация для фотографий */
@media (max-width: 768px) {
    .photo-actions {
        opacity: 1;
    }
    
    .photo-card {
        margin-bottom: 1rem;
    }
    
    .input-group {
        flex-wrap: wrap;
    }
    
    .input-group .btn-outline-secondary {
        margin-top: 0.5rem;
        width: 100%;
        border-left: 1px solid #ced4da;
    }
}
</style>
@endpush

@push('scripts')
<!-- Новая система инициализации -->
<script src="{{ asset('js/project-initialization-manager.js') }}"></script>
<script src="{{ asset('js/finance-unified-manager.js') }}"></script>
<script src="{{ asset('js/input-masks.js') }}"></script>

<!-- Инициализация фильтров для страниц проектов -->
<script src="{{ asset('js/project-filters-init.js') }}"></script>

<script>
// Устанавливаем projectId для новой системы
window.projectId = {{ $project->id }};

// Дополнительная мета-информация для инициализации
$('head').append('<meta name="project-id" content="{{ $project->id }}">');

console.log('🏗️ Project Base Layout загружен для проекта #{{ $project->id }}');
</script>
@endpush
@endsection
