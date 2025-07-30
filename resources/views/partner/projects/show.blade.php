@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Заголовок страницы -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h1 class="h3 mb-0">Проект #{{ $project->id }} - {{ $project->client_full_name }}</h1>
                  
                </div>
                <div class="btn-group flex-wrap">
                    @if(\App\Helpers\UserRoleHelper::canManageProjects())
                    <a href="{{ route('partner.projects.edit', $project) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Редактировать</span>
                    </a>
                    @endif
                    <a href="{{ route('partner.projects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Назад к списку</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Система уведомлений -->
    <div id="alert-container">
        <div id="error-alert" class="alert alert-danger d-none" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span id="error-message"></span>
        </div>
        
        <div id="success-alert" class="alert alert-success d-none" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span id="success-message"></span>
        </div>
    </div>
    
    <!-- Основная система вкладок -->
    <div class="card" id="project-tabs-card">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" id="project-main-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main-content" 
                            type="button" role="tab" aria-controls="main-content" aria-selected="true">
                        <i class="bi bi-info-circle me-2"></i><span class="d-none d-sm-inline">Основное</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance-content" 
                            type="button" role="tab" aria-controls="finance-content" aria-selected="false">
                        <i class="bi bi-cash-coin me-2"></i><span class="d-none d-sm-inline">Финансы</span>
                 
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule-content" 
                            type="button" role="tab" aria-controls="schedule-content" aria-selected="false">
                        <i class="bi bi-calendar3 me-2"></i><span class="d-none d-sm-inline">График</span>
                     
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos-content" 
                            type="button" role="tab" aria-controls="photos-content" aria-selected="false">
                        <i class="bi bi-camera me-2"></i><span class="d-none d-sm-inline">Фото</span>
                  
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="design-tab" data-bs-toggle="tab" data-bs-target="#design-content" 
                            type="button" role="tab" aria-controls="design-content" aria-selected="false">
                        <i class="bi bi-palette me-2"></i><span class="d-none d-sm-inline">Дизайн</span>
                      
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="schemes-tab" data-bs-toggle="tab" data-bs-target="#schemes-content" 
                            type="button" role="tab" aria-controls="schemes-content" aria-selected="false">
                        <i class="bi bi-diagram-3 me-2"></i><span class="d-none d-sm-inline">Схемы</span>
                  
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-content" 
                            type="button" role="tab" aria-controls="documents-content" aria-selected="false">
                        <i class="bi bi-file-text me-2"></i><span class="d-none d-sm-inline">Документы</span>
                     
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <div class="tab-content" id="project-tabs-content">
                <!-- Основная информация -->
                <div class="tab-pane fade show active" id="main-content" role="tabpanel" aria-labelledby="main-tab" tabindex="0">
                    <div class="tab-loading-spinner d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка данных...</div>
                        </div>
                    </div>
                    <div class="tab-content-wrapper">
                        @include('partner.projects.tabs.main')
                    </div>
                </div>

                <!-- Финансы -->
                <div class="tab-pane fade" id="finance-content" role="tabpanel" aria-labelledby="finance-tab" tabindex="0">
                    <div class="tab-loading-spinner d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка финансовых данных...</div>
                        </div>
                    </div>
                    <div class="tab-content-wrapper">
                        @include('partner.projects.tabs.finance')
                    </div>
                </div>

                <!-- График -->
                <div class="tab-pane fade" id="schedule-content" role="tabpanel" aria-labelledby="schedule-tab" tabindex="0">
                    <div class="tab-loading-spinner d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка расписания...</div>
                        </div>
                    </div>
                    <div class="tab-content-wrapper">
                        @include('partner.projects.tabs.schedule')
                    </div>
                </div>

                <!-- Фото -->
                <div class="tab-pane fade" id="photos-content" role="tabpanel" aria-labelledby="photos-tab" tabindex="0">
                    <div class="tab-loading-spinner d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка фотографий...</div>
                        </div>
                    </div>
                    <div class="tab-content-wrapper">
                        @include('partner.projects.tabs.photos')
                    </div>
                </div>

                <!-- Дизайн -->
                <div class="tab-pane fade" id="design-content" role="tabpanel" aria-labelledby="design-tab" tabindex="0">
                    <div class="tab-loading-spinner d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка дизайна...</div>
                        </div>
                    </div>
                    <div class="tab-content-wrapper">
                        @include('partner.projects.tabs.design')
                    </div>
                </div>

                <!-- Схемы -->
                <div class="tab-pane fade" id="schemes-content" role="tabpanel" aria-labelledby="schemes-tab" tabindex="0">
                    <div class="tab-loading-spinner d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка схем...</div>
                        </div>
                    </div>
                    <div class="tab-content-wrapper">
                        @include('partner.projects.tabs.schemes')
                    </div>
                </div>

                <!-- Документы -->
                <div class="tab-pane fade" id="documents-content" role="tabpanel" aria-labelledby="documents-tab" tabindex="0">
                    <div class="tab-loading-spinner d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка документов...</div>
                        </div>
                    </div>
                    <div class="tab-content-wrapper">
                        @include('partner.projects.tabs.documents')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальные окна -->
@include('partner.projects.tabs.modal')

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Общие стили для всех вкладок */
.tab-loading-spinner {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    z-index: 1000;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    color: #6c757d;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    color: #495057;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.badge {
    font-size: 0.75em;
    font-weight: 500;
}

/* Стили для drag & drop */
.drag-over {
    border: 2px dashed #007bff;
    background-color: rgba(0, 123, 255, 0.1);
}

/* Стили для карточек файлов */
.file-card {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.file-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.file-card:hover .file-overlay {
    opacity: 1;
}

.file-actions {
    display: flex;
    gap: 0.5rem;
}

.file-actions .btn {
    padding: 0.375rem 0.5rem;
    border: none;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.file-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 2;
}

/* Дополнительные стили для разных типов файлов */
.photo-card, .design-card, .scheme-card, .document-card {
    @extend .file-card;
}

.photo-preview, .design-preview, .scheme-preview, .document-preview {
    width: 100%;
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.photo-preview img, .design-preview img, .scheme-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-icon {
    font-size: 3rem;
    color: #6c757d;
}

/* Адаптивность */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .file-card {
        height: auto;
    }
    
    .file-preview {
        height: 150px;
    }
}
</style>
@endpush

@push('scripts')
<script src="/js/notifications.js"></script>
<script src="/js/tab-filters-fix.js"></script>
<script src="/js/file-manager.js"></script>
<script src="/js/photo-manager-fixed.js"></script>
<script src="/js/design-manager-fixed.js"></script>
<script src="/js/document-manager-fixed.js"></script>

<script>
// Глобальные переменные
window.projectId = {{ $project->id }};

// Инициализация после загрузки DOM
$(document).ready(function() {
    // Настройка CSRF токена для AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    console.log('Инициализация проекта #' + window.projectId);
    
    // Преждевременная инициализация финансовых функций
    // (актуально если пользователь сразу кликает на кнопки)
    setTimeout(function() {
        console.log('Принудительная проверка финансовых функций через 500ms...');
        
        // Проверяем, инициализированы ли финансовые функции
        if (typeof window.openAddWorkModal === 'undefined' || 
            window.openAddWorkModal.toString().indexOf('Инициализация вкладки Finance') !== -1) {
            console.log('Финансовые функции еще не инициализированы, выполняем принудительную инициализацию...');
            initFinanceTab();
        } else {
            console.log('Финансовые функции уже инициализированы:', typeof window.openAddWorkModal);
        }
    }, 500);
    
    // Дополнительная проверка через 1 секунду
    setTimeout(function() {
        console.log('Дополнительная проверка финансовых функций через 1s...');
        console.log('openAddWorkModal:', typeof window.openAddWorkModal);
        console.log('openAddMaterialModal:', typeof window.openAddMaterialModal);
        console.log('openAddTransportModal:', typeof window.openAddTransportModal);
        console.log('initFinanceHandlers:', typeof window.initFinanceHandlers);
        
        if (typeof window.openAddWorkModal === 'undefined') {
            console.log('Финансовые функции все еще не инициализированы, последняя попытка...');
            initFinanceTab();
        }
    }, 1000);
    
    // Инициализация Bootstrap компонентов
    initBootstrapComponents();
    
    // Инициализация системы вкладок
    initTabSystem();
    
    // Инициализация обработчиков форм
    initFormHandlers();
    
    // Инициализация drag & drop для всех типов файлов
    initAllDragAndDrop();
});

// Инициализация Bootstrap компонентов
function initBootstrapComponents() {
    // Инициализация тултипов
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Инициализация модальных окон
    const modalElements = document.querySelectorAll('.modal');
    modalElements.forEach(function(modalEl) {
        new bootstrap.Modal(modalEl, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        
        // Добавляем обработчик для очистки backdrop при закрытии
        modalEl.addEventListener('hidden.bs.modal', function() {
            // Убираем все backdrop элементы
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Убираем классы modal-open с body
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    });
}

// Инициализация системы вкладок
function initTabSystem() {
    console.log('Инициализация системы вкладок');
    
    // Обработчики Bootstrap вкладок
    const triggerTabList = [].slice.call(document.querySelectorAll('#project-main-tabs button[data-bs-toggle="tab"]'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('shown.bs.tab', function (event) {
            const tabId = this.getAttribute('data-bs-target') || this.getAttribute('href');
            const tabName = tabId.replace('#', '').replace('-content', '');
            
            console.log('Вкладка активирована:', tabName);
            
            // Сохраняем состояние вкладки
            saveTabState(tabName);
            
            // Инициализируем содержимое вкладки
            setTimeout(function() {
                initTabContent(tabName);
            }, 100);
        });
    });
    
    // Восстанавливаем состояние вкладок
    restoreTabState();
}

// Сохранение состояния вкладки
function saveTabState(tabName) {
    const projectId = window.projectId;
    const storageKey = `project_${projectId}_active_tab`;
    localStorage.setItem(storageKey, tabName);
    console.log('Сохранено состояние вкладки:', tabName);
}

// Восстановление состояния вкладки
function restoreTabState() {
    const projectId = window.projectId;
    const storageKey = `project_${projectId}_active_tab`;
    const activeTab = localStorage.getItem(storageKey);
    
    if (activeTab && activeTab !== 'main') {
        console.log('Восстанавливаем состояние вкладки:', activeTab);
        
        const tabButton = document.querySelector(`#${activeTab}-tab`);
        if (tabButton) {
            const tab = new bootstrap.Tab(tabButton);
            tab.show();
        }
    }
}

// Инициализация содержимого вкладки
function initTabContent(tabName) {
    console.log('Инициализация содержимого вкладки:', tabName);
    
    switch(tabName) {
        case 'finance':
            initFinanceTab();
            break;
        case 'schedule':
            initScheduleTab();
            break;
        case 'photos':
            initPhotosTab();
            break;
        case 'design':
            initDesignTab();
            break;
        case 'schemes':
            initSchemesTab();
            break;
        case 'documents':
            initDocumentsTab();
            break;
        case 'main':
            initMainTab();
            break;
        default:
            console.log('Неизвестная вкладка:', tabName);
    }
}

// Инициализация основной вкладки
function initMainTab() {
    console.log('Инициализация основной вкладки');
    // Здесь можно добавить специфичную логику для основной вкладки
}

// Инициализация финансовой вкладки
function initFinanceTab() {
    console.log('Инициализация финансовой вкладки');
    
    // Вызываем функцию инициализации содержимого финансовой вкладки, если она доступна
    if (typeof window.initFinanceTabContent === 'function') {
        console.log('Вызов initFinanceTabContent...');
        window.initFinanceTabContent();
    } else {
        console.warn('initFinanceTabContent не найдена, попробуем позже...');
        // Попробуем через небольшую задержку
        setTimeout(function() {
            if (typeof window.initFinanceTabContent === 'function') {
                console.log('Вызов initFinanceTabContent с задержкой...');
                window.initFinanceTabContent();
            } else {
                console.error('initFinanceTabContent по-прежнему недоступна');
            }
        }, 500);
    }
}

// Инициализация вкладки расписания
function initScheduleTab() {
    console.log('=== ИНИЦИАЛИЗАЦИЯ ВКЛАДКИ РАСПИСАНИЯ ===');
    console.log('Проверяем доступность функций...');
    console.log('typeof loadScheduleData:', typeof loadScheduleData);
    console.log('typeof initScheduleHandlers:', typeof initScheduleHandlers);
    
    // Вызов функции загрузки данных расписания если существует
    if (typeof loadScheduleData === 'function') {
        console.log('Вызываем loadScheduleData...');
        loadScheduleData();
    } else {
        console.error('loadScheduleData не найдена!');
        // Попробуем через небольшую задержку
        setTimeout(function() {
            if (typeof loadScheduleData === 'function') {
                console.log('Вызываем loadScheduleData с задержкой...');
                loadScheduleData();
            } else {
                console.error('loadScheduleData по-прежнему недоступна');
            }
        }, 500);
    }
    
    // Вызов функции инициализации обработчиков расписания если существует
    if (typeof initScheduleHandlers === 'function') {
        console.log('Вызываем initScheduleHandlers...');
        initScheduleHandlers();
    } else {
        console.warn('initScheduleHandlers не найдена');
    }
}

// Инициализация вкладки фотографий
function initPhotosTab() {
    console.log('Инициализация вкладки фотографий');
    
    // ПРИОРИТЕТ: специализированные менеджеры во вкладках
    // Если PhotoManager уже инициализирован в photos.blade.php, не дублируем
    if (typeof window.PhotoManager !== 'undefined' && window.PhotoManager.initialized) {
        console.log('PhotoManager уже инициализирован в photos.blade.php');
        return;
    }
    
    // Сначала пробуем найти и использовать специализированный менеджер
    if (typeof window.PhotoManager !== 'undefined' && window.PhotoManager.init) {
        console.log('Используем PhotoManager из show.blade.php');
        window.PhotoManager.init();
    } else if (typeof window.PhotoApp !== 'undefined' && window.PhotoApp.loadPhotos) {
        console.log('Используем PhotoApp');
        window.PhotoApp.loadPhotos();
    } else {
        console.log('Используем стандартный FileManager для фотографий');
        if (window.fileManager) {
            window.fileManager.loadFiles('photos');
        } else {
            console.error('FileManager не найден');
        }
    }
}

// Инициализация вкладки дизайна
function initDesignTab() {
    console.log('Инициализация вкладки дизайна');
    
    // ПРИОРИТЕТ: специализированные менеджеры во вкладках
    if (typeof window.DesignManager !== 'undefined' && window.DesignManager.initialized) {
        console.log('DesignManager уже инициализирован в design.blade.php');
        return;
    }
    
    // Сначала пробуем найти и использовать специализированный менеджер
    if (typeof window.DesignManager !== 'undefined' && window.DesignManager.init) {
        console.log('Используем DesignManager из show.blade.php');
        window.DesignManager.init();
    } else {
        console.log('Используем стандартный FileManager для дизайна');
        if (window.fileManager) {
            window.fileManager.loadFiles('design');
        } else {
            console.error('FileManager не найден');
        }
    }
}

// Инициализация вкладки схем
function initSchemesTab() {
    console.log('Инициализация вкладки схем');
    
    // ПРИОРИТЕТ: специализированные менеджеры во вкладках
    if (typeof window.SchemeManager !== 'undefined' && window.SchemeManager.initialized) {
        console.log('SchemeManager уже инициализирован в schemes.blade.php');
        return;
    }
    
    // Сначала пробуем найти и использовать специализированный менеджер
    if (typeof window.SchemeManager !== 'undefined' && window.SchemeManager.init) {
        console.log('Используем SchemeManager из show.blade.php');
        window.SchemeManager.init();
    } else {
        console.log('Используем стандартный FileManager для схем');
        if (window.fileManager) {
            window.fileManager.loadFiles('schemes');
        } else {
            console.error('FileManager не найден');
        }
    }
}

// Инициализация вкладки документов
function initDocumentsTab() {
    console.log('Инициализация вкладки документов');
    
    // Используем исправленный DocumentManagerFixed
    if (typeof window.DocumentManagerFixed !== 'undefined') {
        console.log('Используем DocumentManagerFixed');
        window.DocumentManagerFixed.init();
    } else if (typeof window.DocumentManager !== 'undefined') {
        console.log('Используем DocumentManager из documents.blade.php');
        window.DocumentManager.init();
    } else {
        console.log('Используем стандартный FileManager для документов');
        if (window.fileManager) {
            window.fileManager.loadFiles('documents');
        } else {
            console.error('Ни один менеджер документов не найден');
        }
    }
}

// Инициализация drag & drop для всех типов файлов
function initAllDragAndDrop() {
    console.log('Инициализация drag & drop для всех типов файлов');
    // Drag & drop будет инициализирован в каждом менеджере отдельно
}

// Инициализация обработчиков форм
function initFormHandlers() {
    // Обработчики форм работ
    $(document).off('submit', '#addWorkForm').on('submit', '#addWorkForm', function(e) {
        e.preventDefault();
        handleFormSubmit(this, 'Работа добавлена');
    });
    
    $(document).off('submit', '#editWorkForm').on('submit', '#editWorkForm', function(e) {
        e.preventDefault();
        handleFormSubmit(this, 'Работа обновлена');
    });
    
    // Обработчики форм материалов удалены - они обрабатываются в finance.blade.php
    
    // Обработчики форм этапов
    $(document).off('submit', '#addStageForm').on('submit', '#addStageForm', function(e) {
        e.preventDefault();
        handleFormSubmit(this, 'Этап добавлен');
    });
    
    $(document).off('submit', '#editStageForm').on('submit', '#editStageForm', function(e) {
        e.preventDefault();
        handleFormSubmit(this, 'Этап обновлен');
    });
    
  
}

// Обработка отправки формы
function handleFormSubmit(form, successMessage) {
    const formData = new FormData(form);
    const action = form.action;
    const method = form.method;
    
    fetch(action, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(successMessage, 'success');
            
            // Закрываем модальное окно
            const modal = $(form).closest('.modal');
            if (modal.length) {
                const bsModal = bootstrap.Modal.getInstance(modal[0]);
                if (bsModal) {
                    bsModal.hide();
                }
            }
            
            // Сбрасываем форму
            form.reset();
            
            // Обновляем данные соответствующей вкладки
            setTimeout(() => {
                refreshCurrentTabData();
            }, 500);
        } else {
            showMessage(data.message || 'Ошибка обработки формы', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ошибка отправки формы', 'error');
    });
}

// Обновление данных текущей вкладки
function refreshCurrentTabData() {
    const activeTab = document.querySelector('#project-main-tabs .nav-link.active');
    if (activeTab) {
        const tabId = activeTab.getAttribute('data-bs-target') || activeTab.getAttribute('href');
        const tabName = tabId.replace('#', '').replace('-content', '');
        
        initTabContent(tabName);
    }
}

// Показать сообщение
function showMessage(message, type = 'info') {
    const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
    
    const toast = $(`
        <div class="toast align-items-center text-white ${bgClass} border-0 position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    $('body').append(toast);
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Глобальные функции для совместимости
window.showMessage = showMessage;
window.refreshCurrentTabData = refreshCurrentTabData;

// Функция для генерации PDF отчета по финансам
window.generateFinancePDF = function() {
    console.log('Начинаем генерацию PDF для проекта ID:', window.projectId);
    
    if (!window.projectId) {
        console.error('Ошибка: projectId не определен');
        showMessage('Ошибка: ID проекта не найден', 'danger');
        return;
    }
    
    // Сначала получаем данные о проекте
    $.ajax({
        url: `/partner/projects/${window.projectId}/finance/summary`,
        method: 'GET',
        success: function(summaryData) {
            console.log('Успешно получены данные summary:', summaryData);
            
            // Получаем данные о работах
            $.ajax({
                url: `/partner/projects/${window.projectId}/finance/works-partial`,
                method: 'GET',
                success: function(worksData) {
                    console.log('Успешно получены данные о работах:', worksData);
                    
                    // Получаем данные о материалах
                    $.ajax({
                        url: `/partner/projects/${window.projectId}/finance/materials-partial`,
                        method: 'GET',
                        success: function(materialsData) {
                            console.log('Успешно получены данные о материалах:', materialsData);
                            
                            // Получаем данные о транспорте
                            $.ajax({
                                url: `/partner/projects/${window.projectId}/finance/transports-partial`,
                                method: 'GET',
                                success: function(transportsData) {
                                    console.log('Успешно получены данные о транспорте:', transportsData);
                                    
                                    // Собираем все данные
                                    const pdfData = {
                                        project: summaryData.project || { id: window.projectId },
                                        summary: summaryData.summary || {},
                                        works: worksData.works || [],
                                        materials: materialsData.materials || [],
                                        transports: transportsData.transports || []
                                    };
                                    
                                    console.log('Подготовлены данные для PDF:', pdfData);
                                    
                                    // Генерируем PDF
                                    $.ajax({
                                        url: `/partner/projects/${window.projectId}/finance/generate-pdf`,
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                            'Content-Type': 'application/json'
                                        },
                                        data: JSON.stringify(pdfData),
                                        success: function(response) {
                                            console.log('Успешный ответ на generate-pdf:', response);
                                            
                                            if (response.success && response.pdf_url) {
                                                console.log('URL для скачивания PDF:', response.pdf_url);
                                                // Открываем PDF в новом окне
                                                window.open(response.pdf_url, '_blank');
                                                // Также добавляем прямую ссылку для скачивания в случае проблем с автоматическим открытием
                                                const directUrl = `/partner/projects/${window.projectId}/finance-pdf`;
                                                console.log('Прямая ссылка для скачивания PDF:', directUrl);
                                                showMessage('PDF отчет успешно сгенерирован. <a href="' + directUrl + '" target="_blank">Скачать PDF</a>', 'success');
                                            } else {
                                                console.error('Ошибка в ответе generate-pdf:', response);
                                                showMessage('Ошибка при генерации PDF: ' + (response.message || 'Неизвестная ошибка'), 'danger');
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Ошибка при создании PDF:', {xhr: xhr, status: status, error: error});
                                            console.error('Ответ сервера:', xhr.responseText);
                                            
                                            // Если ответ сервера содержит JSON объект с сообщением об ошибке
                                            let errorMessage = error;
                                            try {
                                                const response = JSON.parse(xhr.responseText);
                                                if (response && response.message) {
                                                    errorMessage = response.message;
                                                }
                                            } catch (e) {
                                                // Если не можем разобрать JSON, оставляем исходное сообщение
                                            }
                                            
                                            // Предлагаем прямую ссылку как альтернативу
                                            const directUrl = `/partner/projects/${window.projectId}/finance-pdf`;
                                            showMessage('Ошибка при создании PDF отчета: ' + errorMessage + 
                                                      '<br><small>Попробуйте <a href="' + directUrl + '" target="_blank">скачать PDF напрямую</a></small>', 'danger');
                                        }
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('Ошибка при загрузке данных о транспорте:', {xhr: xhr, status: status, error: error});
                                    showMessage('Ошибка при загрузке данных о транспорте: ' + error, 'danger');
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Ошибка при загрузке данных о материалах:', {xhr: xhr, status: status, error: error});
                            showMessage('Ошибка при загрузке данных о материалах: ' + error, 'danger');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка при загрузке данных о работах:', {xhr: xhr, status: status, error: error});
                    showMessage('Ошибка при загрузке данных о работах: ' + error, 'danger');
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке сводки финансов:', {xhr: xhr, status: status, error: error});
            showMessage('Ошибка при загрузке сводки финансов: ' + error, 'danger');
        }
    });
};

// Заглушки удалены - все функции теперь определены в finance.blade.php
</script>
@endpush

<!-- Контейнер для модальных окон -->
<div id="modalContainer"></div>

<!-- Подключение системы модальных окон -->
@include('partner.projects.tabs.modals.init-modals')

<!-- Модальные окна для финансов -->
