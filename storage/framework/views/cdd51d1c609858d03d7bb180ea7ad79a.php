<!-- График работ и этапы -->
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<style>
/* Дополнительные стили для улучшения UI */
.nav-tabs {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 0;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    background: none;
    color: #6c757d;
    padding: 12px 20px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: rgba(0, 123, 255, 0.5);
    color: #007bff;
    background: rgba(0, 123, 255, 0.05);
}

.nav-tabs .nav-link.active {
    background: none;
    border-bottom-color: #007bff;
    color: #007bff;
    font-weight: 600;
}

.tab-content {
    background: #fff;
    min-height: 400px;
}

.timeline-item {
    transition: all 0.3s ease;
    border-left: 4px solid #dee2e6;
    padding-left: 20px;
    margin-left: 10px;
    position: relative;
    margin-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 20px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #dee2e6;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item[data-stage-id] {
    border-left-color: #007bff;
}

.timeline-item[data-stage-id]::before {
    background: #007bff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-item.completed {
    border-left-color: #198754;
    opacity: 0.8;
    order: 999; /* Перемещаем завершенные в конец */
}

.timeline-item.completed::before {
    background: #198754;
    box-shadow: 0 0 0 2px #198754;
}

.timeline-item.completed .card {
    background-color: #f8f9fa;
    border-color: #198754;
}

.timeline-item.completed .card-title {
    color: #6c757d;
    text-decoration: line-through;
}

.timeline-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    border-left-color: #0056b3;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.card .card-body {
    position: relative;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: 0.375rem;
}

.loading-spinner {
    width: 3rem;
    height: 3rem;
}

/* Кнопка завершения */
.btn-complete {
    background-color: #198754;
    border-color: #198754;
    color: white;
    transition: all 0.3s ease;
}

.btn-complete:hover {
    background-color: #157347;
    border-color: #146c43;
    color: white;
    transform: scale(1.05);
}

.btn-complete:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
    cursor: not-allowed;
}

/* Кнопка завершения для событий (меньшего размера) */
.btn-complete.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-complete.btn-sm:hover {
    transform: scale(1.1);
}

/* Улучшенные карточки событий */
.card.border-start {
    border-left-width: 4px !important;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

/* Стили для завершенных событий */
.card.completed {
    background-color: #f8f9fa;
    opacity: 0.8;
}

.card.completed .card-title {
    color: #6c757d;
}

/* Стили для пустых состояний */
.text-center.py-5 {
    color: #6c757d;
}

/* Статистика */
.border-end {
    border-right: 1px solid #dee2e6 !important;
}

/* Контейнеры для сортировки */
.stages-container, .events-container {
    display: flex;
    flex-direction: column;
}

.stages-container .timeline-item.completed {
    order: 999;
}

/* Адаптивность */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 8px 15px;
        font-size: 0.9rem;
    }
    
    .timeline-item {
        margin-left: 5px;
        padding-left: 15px;
    }
    
    .timeline-item::before {
        left: -6px;
        width: 10px;
        height: 10px;
    }
    
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        margin-bottom: 15px;
        padding-bottom: 15px;
    }
}

@media (max-width: 576px) {
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    
    .badge {
        font-size: 0.7em;
    }
}
</style>

<div class="row">
    <div class="col-12">
        <!-- Статистика -->
        <div class=" mb-4 flex ">
            <div class="col-md-3 border-end text-center">
                <h4 class="text-primary" id="stagesCount">0</h4>
                <small class="text-muted">Всего этапов</small>
            </div>
            <div class="col-md-3 border-end text-center">
                <h4 class="text-success" id="completedStagesCount">0</h4>
                <small class="text-muted">Завершено</small>
            </div>
            <div class="col-md-3 border-end text-center">
                <h4 class="text-warning" id="eventsCount">0</h4>
                <small class="text-muted">События</small>
            </div>
            <div class="col-md-3 text-center">
                <h4 class="text-info" id="progressPercent">0%</h4>
                <small class="text-muted">Прогресс</small>
            </div>
        </div>

        <!-- Вкладки -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="scheduleSubTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="stages-tab" data-bs-toggle="tab" data-bs-target="#stages-pane" type="button" role="tab">
                            <i class="bi bi-list-check me-2"></i>Этапы
                            <span class="badge bg-primary ms-2" id="stagesBadge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events-pane" type="button" role="tab">
                            <i class="bi bi-calendar-event me-2"></i>События
                            <span class="badge bg-info ms-2" id="eventsBadge">0</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="scheduleSubTabsContent">
                    <!-- Вкладка Этапы -->
                    <div class="tab-pane fade show active" id="stages-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Этапы проекта</h6>
                            <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                            <button class="btn btn-primary btn-sm" type="button" onclick="openAddStageModal()">
                                <i class="bi bi-plus-circle me-1"></i>Добавить этап
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Индикатор загрузки для этапов -->
                        <div id="stagesLoader" class="d-none">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                                <div class="mt-2">Загрузка этапов...</div>
                            </div>
                        </div>
                        
                        <!-- Контейнер для этапов -->
                        <div id="stagesContainer" class="stages-container">
                            <!-- Этапы будут загружаться через AJAX -->
                        </div>
                        
                        <!-- Пустое состояние для этапов -->
                        <div class="text-center py-5 d-none" id="emptyStagesState">
                            <i class="bi bi-list-check text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">Этапы не добавлены</h4>
                            <p class="text-muted">Добавьте первый этап для управления ходом проекта</p>
                            <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                            <button class="btn btn-primary" onclick="openAddStageModal()">
                                <i class="bi bi-plus-circle me-1"></i>Добавить первый этап
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Вкладка События -->
                    <div class="tab-pane fade" id="events-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">События проекта</h6>
                            <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                            <button class="btn btn-info btn-sm" type="button" onclick="openAddEventModal()">
                                <i class="bi bi-plus-circle me-1"></i>Добавить событие
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Индикатор загрузки для событий -->
                        <div id="eventsLoader" class="d-none">
                            <div class="text-center py-5">
                                <div class="spinner-border text-info" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                                <div class="mt-2">Загрузка событий...</div>
                            </div>
                        </div>
                        
                        <!-- Контейнер для событий -->
                        <div id="eventsContainer" class="events-container">
                            <!-- События будут загружаться через AJAX -->
                        </div>
                        
                        <!-- Пустое состояние для событий -->
                        <div class="text-center py-5 d-none" id="emptyEventsState">
                            <i class="bi bi-calendar-event text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">События не добавлены</h4>
                            <p class="text-muted">Добавьте первое событие для планирования проекта</p>
                            <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                            <button class="btn btn-info" onclick="openAddEventModal()">
                                <i class="bi bi-plus-circle me-1"></i>Добавить первое событие
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Определяем projectId глобально
window.projectId = <?php echo e($project->id ?? 'null'); ?>;
// Определяем роль пользователя
window.isClient = <?php echo json_encode(!(App\Helpers\UserRoleHelper::canSeeActionButtons()), 15, 512) ?>;

// Функция показа сообщений (использует глобальную функцию из show.blade.php)
function showMessage(message, type = 'info') {
    if (typeof window.showMessage === 'function') {
        window.showMessage(message, type);
    } else {
        // Fallback если глобальная функция недоступна
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
}

// Глобальные переменные для хранения данных
window.scheduleData = {
    stages: [],
    events: [],
    summary: {}
};

$(document).ready(function() {
    // Инициализация будет происходить из основного файла show.blade.php при переходе на вкладку
    
    // Обработчики переключения подвкладок
    $('#stages-tab').on('shown.bs.tab', function() {
        console.log('Переключение на вкладку этапов');
        loadStagesData();
    });
    
    $('#events-tab').on('shown.bs.tab', function() {
        console.log('Переключение на вкладку событий');
        loadEventsData();
    });
});

// Основная функция инициализации расписания
function initScheduleTab() {
    console.log('=== ИНИЦИАЛИЗАЦИЯ ВКЛАДКИ РАСПИСАНИЯ ===');
    
    // Сначала пытаемся загрузить все данные
    loadScheduleData();
    
    // Инициализируем обработчики
    initScheduleHandlers();
    
    // Если данные уже есть, сразу отображаем активную подвкладку
    setTimeout(function() {
        loadActiveSubTab();
    }, 100);
    
    // Резервный механизм: если через 2 секунды данных все еще нет, 
    // пытаемся загрузить этапы принудительно
    setTimeout(function() {
        if (window.scheduleData.stages.length === 0 && window.scheduleData.events.length === 0) {
            console.log('=== РЕЗЕРВНАЯ ЗАГРУЗКА ===');
            console.log('Данные не загрузились, пытаемся загрузить этапы принудительно...');
            loadStagesData();
        }
    }, 2000);
}

// Загрузка всех данных расписания через AJAX
function loadScheduleData() {
    console.log('=== ЗАГРУЗКА ДАННЫХ РАСПИСАНИЯ ===');
    console.log('Project ID:', window.projectId);
    
    showScheduleLoader();
    
    $.ajax({
        url: `/partner/projects/${window.projectId}/schedule`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        xhrFields: {
            withCredentials: true
        },
        success: function(response) {
            console.log('Ответ от сервера:', response);
            
            if (response.success) {
                window.scheduleData.stages = response.stages || [];
                window.scheduleData.events = response.events || [];
                
                console.log('Загружено этапов:', window.scheduleData.stages.length);
                console.log('Загружено событий:', window.scheduleData.events.length);
                
                // Обновляем статистику
                loadSummaryData();
                
                // Отображаем данные для активной подвкладки
                loadActiveSubTab();
                
                // Обновляем счетчики
                updateScheduleCounts();
            } else {
                console.error('Ошибка в ответе сервера:', response);
                showMessage('Ошибка загрузки данных расписания', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX ошибка загрузки данных расписания:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            showMessage('Ошибка загрузки данных расписания', 'error');
        },
        complete: function() {
            hideScheduleLoader();
        }
    });
}

// Загрузка сводной информации
function loadSummaryData() {
    $.ajax({
        url: `/partner/projects/${window.projectId}/schedule/summary`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                window.scheduleData.summary = response.summary;
                updateStatistics(response.summary);
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка загрузки сводки:', error);
        }
    });
}

// Обновление статистики
function updateStatistics(summary) {
    if (summary.stages) {
        $('#stagesCount').text(summary.stages.total || 0);
        $('#completedStagesCount').text(summary.stages.completed || 0);
        $('#progressPercent').text((summary.stages.progress || 0) + '%');
    }
    
    if (summary.events) {
        $('#eventsCount').text(summary.events.total || 0);
    }
}

// Загрузка только этапов
function loadStagesData() {
    console.log('=== ЗАГРУЗКА ЭТАПОВ ===');
    console.log('Текущее количество этапов:', window.scheduleData.stages.length);
    
    if (window.scheduleData.stages.length === 0) {
        console.log('Этапы пустые, загружаем с сервера...');
        showStagesLoader();
        
        $.ajax({
            url: `/partner/projects/${window.projectId}/stages-partial`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Ответ сервера по этапам:', response);
                
                if (response.success) {
                    window.scheduleData.stages = response.stages || [];
                    console.log('Загружено этапов:', window.scheduleData.stages.length);
                    renderStages(window.scheduleData.stages);
                } else {
                    console.error('Ошибка в ответе сервера по этапам:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX ошибка загрузки этапов:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                showMessage('Ошибка загрузки этапов', 'error');
            },
            complete: function() {
                hideStagesLoader();
            }
        });
    } else {
        console.log('Этапы уже загружены, отображаем...');
        renderStages(window.scheduleData.stages);
    }
}

// Загрузка только событий
function loadEventsData() {
    console.log('=== ЗАГРУЗКА СОБЫТИЙ ===');
    console.log('Текущее количество событий:', window.scheduleData.events.length);
    
    if (window.scheduleData.events.length === 0) {
        console.log('События пустые, загружаем с сервера...');
        showEventsLoader();
        
        $.ajax({
            url: `/partner/projects/${window.projectId}/events-partial`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Ответ сервера по событиям:', response);
                
                if (response.success) {
                    window.scheduleData.events = response.events || [];
                    console.log('Загружено событий:', window.scheduleData.events.length);
                    renderEvents(window.scheduleData.events);
                } else {
                    console.error('Ошибка в ответе сервера по событиям:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX ошибка загрузки событий:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                showMessage('Ошибка загрузки событий', 'error');
            },
            complete: function() {
                hideEventsLoader();
            }
        });
    } else {
        console.log('События уже загружены, отображаем...');
        renderEvents(window.scheduleData.events);
    }
}

// Отображение этапов
function renderStages(stages) {
    console.log('=== ОТОБРАЖЕНИЕ ЭТАПОВ ===');
    console.log('Получено этапов для отображения:', stages ? stages.length : 0);
    console.log('Данные этапов:', stages);
    
    const container = $('#stagesContainer');
    const emptyState = $('#emptyStagesState');
    const loader = $('#stagesLoader');
    
    // Скрываем индикатор загрузки
    loader.addClass('d-none');
    
    // Показываем контейнер
    container.removeClass('d-none');
    
    container.empty();
    
    if (!stages || stages.length === 0) {
        console.log('Этапы отсутствуют, показываем пустое состояние');
        emptyState.removeClass('d-none');
        container.addClass('d-none');
        return;
    }
    
    console.log('Скрываем пустое состояние и отображаем этапы');
    emptyState.addClass('d-none');
    container.removeClass('d-none');
    
    // Сортируем этапы: незавершенные сначала, завершенные в конце
    const sortedStages = stages.sort((a, b) => {
        if (a.status === 'completed' && b.status !== 'completed') return 1;
        if (a.status !== 'completed' && b.status === 'completed') return -1;
        return (a.order || 0) - (b.order || 0);
    });
    
    sortedStages.forEach((stage, index) => {
        console.log(`Создание карточки этапа ${index + 1}:`, stage.title || stage.name);
        const stageCard = createStageCard(stage);
        container.append(stageCard);
    });
    
    // Обновляем счетчик
    $('#stagesBadge').text(stages.length);
    console.log('Этапы успешно отображены');
}

// Создание карточки этапа
function createStageCard(stage) {
    const isCompleted = stage.status === 'completed';
    const statusClass = isCompleted ? 'success' : (stage.status === 'in_progress' ? 'warning' : 'secondary');
    const statusText = getStageStatusText(stage.status);
    
    let datesHtml = '';
    if (stage.planned_start_date) {
        const startDate = new Date(stage.planned_start_date).toLocaleDateString('ru-RU');
        datesHtml += `<small class="text-muted me-3"><i class="bi bi-calendar3"></i> Начало: ${startDate}</small>`;
    }
    if (stage.planned_end_date) {
        const endDate = new Date(stage.planned_end_date).toLocaleDateString('ru-RU');
        datesHtml += `<small class="text-muted"><i class="bi bi-calendar-check"></i> Окончание: ${endDate}</small>`;
    }
    
    const actionsHtml = window.isClient ? '' : (isCompleted ? 
        `<button class="btn btn-outline-primary btn-sm" title="Редактировать" onclick="editStage(${stage.id})">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-outline-danger btn-sm" title="Удалить" onclick="confirmDeleteStage(${stage.id})">
            <i class="bi bi-trash"></i>
        </button>` :
        `<button class="btn btn-complete btn-sm me-1" title="Завершить" onclick="completeStage(${stage.id})">
            <i class="bi bi-check-circle me-1"></i>Завершить
        </button>
        <button class="btn btn-outline-primary btn-sm" title="Редактировать" onclick="editStage(${stage.id})">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-outline-danger btn-sm" title="Удалить" onclick="confirmDeleteStage(${stage.id})">
            <i class="bi bi-trash"></i>
        </button>`);
    
    return `
        <div class="timeline-item card mb-3 ${isCompleted ? 'completed border-success' : ''}" data-stage-id="${stage.id}">
            <div class="card-body ${isCompleted ? 'bg-light' : ''}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-2 ${isCompleted ? 'text-muted' : ''}">
                            ${stage.name}
                            ${isCompleted ? '<i class="bi bi-check-circle-fill text-success ms-2"></i>' : ''}
                        </h6>
                        ${stage.description ? `<p class="card-text text-muted small">${stage.description}</p>` : ''}
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-${statusClass}">${statusText}</span>
                            ${datesHtml}
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        ${actionsHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Получение текста статуса этапа
function getStageStatusText(status) {
    const statusTexts = {
        'not_started': 'Не начато',
        'in_progress': 'В работе',
        'completed': 'Завершено',
        'on_hold': 'Приостановлено'
    };
    return statusTexts[status] || 'Неизвестно';
}

// Отображение событий
function renderEvents(events) {
    console.log('=== ОТОБРАЖЕНИЕ СОБЫТИЙ ===');
    console.log('Получено событий для отображения:', events ? events.length : 0);
    console.log('Данные событий:', events);
    
    const container = $('#eventsContainer');
    const emptyState = $('#emptyEventsState');
    const loader = $('#eventsLoader');
    
    // Скрываем индикатор загрузки
    loader.addClass('d-none');
    
    // Показываем контейнер
    container.removeClass('d-none');
    
    container.empty();
    
    if (!events || events.length === 0) {
        console.log('События отсутствуют, показываем пустое состояние');
        emptyState.removeClass('d-none');
        container.addClass('d-none');
        return;
    }
    
    console.log('Скрываем пустое состояние и отображаем события');
    emptyState.addClass('d-none');
    container.removeClass('d-none');
    
    // Сортируем события: сначала незавершенные по дате (новые сверху), потом завершенные
    const sortedEvents = events.sort((a, b) => {
        // Сначала проверяем статус
        if (a.status === 'completed' && b.status !== 'completed') return 1;
        if (a.status !== 'completed' && b.status === 'completed') return -1;
        
        // Если статусы одинаковые, сортируем по дате
        return new Date(b.event_date) - new Date(a.event_date);
    });
    
    sortedEvents.forEach((event, index) => {
        console.log(`Создание карточки события ${index + 1}:`, event.title);
        const eventCard = createEventCard(event);
        container.append(eventCard);
    });
    
    // Обновляем счетчик
    $('#eventsBadge').text(events.length);
    console.log('События успешно отображены');
}

// Создание карточки события
function createEventCard(event) {
    // Форматируем дату
    let eventDate = '';
    if (event.event_date) {
        const date = new Date(event.event_date);
        eventDate = date.toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        // Добавляем время, если оно указано
        if (event.event_time) {
            eventDate += ' ' + event.event_time;
        }
    }
    
    const typeColors = {
        'meeting': 'primary',
        'deadline': 'danger',
        'delivery': 'warning',
        'inspection': 'info',
        'other': 'secondary'
    };
    
    const typeNames = {
        'meeting': 'Встреча',
        'deadline': 'Дедлайн',
        'delivery': 'Поставка',
        'inspection': 'Проверка',
        'other': 'Другое'
    };
    
    const borderColor = typeColors[event.type] || 'secondary';
    const typeName = typeNames[event.type] || 'Другое';
    
    // Проверяем статус события
    const isCompleted = event.status === 'completed';
    const cardClasses = `card mb-3 border-start border-${borderColor}${isCompleted ? ' completed' : ''}`;
    const titleClasses = `card-title mb-2${isCompleted ? ' text-decoration-line-through text-muted' : ''}`;
    
    // Создаем кнопки с учетом статуса и роли пользователя
    let actionButtons = '';
    if (!window.isClient) {
        if (!isCompleted) {
            actionButtons = `
                <button class="btn btn-complete btn-sm" title="Завершить" onclick="completeEvent(${event.id})">
                    <i class="bi bi-check-circle"></i>Завершить
                </button>
                <button class="btn btn-outline-primary btn-sm" title="Редактировать" onclick="editEvent(${event.id})">
                    <i class="bi bi-pencil"></i>
                </button>
            `;
        } else {
            actionButtons = `
                <span class="badge bg-success me-2">Завершено</span>
            `;
        }
    }
    
    return `
        <div class="${cardClasses}" data-event-id="${event.id}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="${titleClasses}">${event.title}</h6>
                        ${event.description ? `<p class="card-text text-muted small">${event.description}</p>` : ''}
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-${borderColor}">${typeName}</span>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> ${eventDate}
                            </small>
                            ${event.location ? `<small class="text-muted"><i class="bi bi-geo-alt"></i> ${event.location}</small>` : ''}
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        ${actionButtons}
                        ${!window.isClient ? `<button class="btn btn-outline-danger btn-sm" title="Удалить" onclick="confirmDeleteEvent(${event.id})">
                            <i class="bi bi-trash"></i>
                        </button>` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Завершение этапа
function completeStage(stageId) {
    $.ajax({
        url: `/partner/projects/${window.projectId}/stages/${stageId}/complete`,
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Этап успешно завершен', 'success');
                
                // Обновляем данные этапа в массиве
                const stageIndex = window.scheduleData.stages.findIndex(s => s.id === stageId);
                if (stageIndex !== -1) {
                    window.scheduleData.stages[stageIndex].status = 'completed';
                }
                
                // Перерендериваем этапы с обновленным порядком
                renderStages(window.scheduleData.stages);
                
                // Обновляем сводку
                loadSummaryData();
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка завершения этапа:', error);
            showMessage('Ошибка при завершении этапа', 'error');
        }
    });
}

// Завершение события
function completeEvent(eventId) {
    $.ajax({
        url: `/partner/projects/${window.projectId}/events/${eventId}/complete`,
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Событие успешно завершено', 'success');
                
                // Обновляем данные события в массиве
                const eventIndex = window.scheduleData.events.findIndex(e => e.id === eventId);
                if (eventIndex !== -1) {
                    window.scheduleData.events[eventIndex].status = 'completed';
                }
                
                // Перерендериваем события
                renderEvents(window.scheduleData.events);
                
                // Обновляем сводку
                loadSummaryData();
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка завершения события:', error);
            showMessage('Ошибка при завершении события', 'error');
        }
    });
}

// Удаление этапа
function confirmDeleteStage(stageId) {
    deleteStage(stageId);
}

function deleteStage(stageId) {
    $.ajax({
        url: `/partner/projects/${window.projectId}/stages/${stageId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Этап успешно удален', 'success');
                
                // Удаляем этап из массива
                window.scheduleData.stages = window.scheduleData.stages.filter(s => s.id !== stageId);
                
                // Перерендериваем этапы
                renderStages(window.scheduleData.stages);
                
                // Обновляем сводку
                loadSummaryData();
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка удаления этапа:', error);
            showMessage('Ошибка при удалении этапа', 'error');
        }
    });
}

// Удаление события
function confirmDeleteEvent(eventId) {
    deleteEvent(eventId);
}

function deleteEvent(eventId) {
    $.ajax({
        url: `/partner/projects/${window.projectId}/events/${eventId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showMessage('Событие успешно удалено', 'success');
                
                // Удаляем событие из массива
                window.scheduleData.events = window.scheduleData.events.filter(e => e.id !== eventId);
                
                // Перерендериваем события
                renderEvents(window.scheduleData.events);
                
                // Обновляем сводку
                loadSummaryData();
            }
        },
        error: function(xhr, status, error) {
            console.error('Ошибка удаления события:', error);
            showMessage('Ошибка при удалении события', 'error');
        }
    });
}

// Показать индикатор загрузки
function showScheduleLoader() {
    showStagesLoader();
    showEventsLoader();
}

function hideScheduleLoader() {
    hideStagesLoader();
    hideEventsLoader();
}

function showStagesLoader() {
    $('#stagesLoader').removeClass('d-none');
    $('#stagesContainer').addClass('d-none');
    $('#emptyStagesState').addClass('d-none');
}

function hideStagesLoader() {
    $('#stagesLoader').addClass('d-none');
    $('#stagesContainer').removeClass('d-none');
}

function showEventsLoader() {
    $('#eventsLoader').removeClass('d-none');
    $('#eventsContainer').addClass('d-none');
    $('#emptyEventsState').addClass('d-none');
}

function hideEventsLoader() {
    $('#eventsLoader').addClass('d-none');
    $('#eventsContainer').removeClass('d-none');
}

// Обновление счетчиков
function updateScheduleCounts() {
    const stages = window.scheduleData.stages || [];
    const events = window.scheduleData.events || [];
    
    $('#stagesBadge').text(stages.length);
    $('#eventsBadge').text(events.length);
}

// Загрузка данных для активной подвкладки
function loadActiveSubTab() {
    console.log('=== ЗАГРУЗКА АКТИВНОЙ ПОДВКЛАДКИ ===');
    
    // Определяем активную подвкладку
    const activeTab = document.querySelector('#scheduleSubTabs .nav-link.active');
    console.log('Найдена активная подвкладка:', activeTab);
    
    if (activeTab) {
        const activeTabId = activeTab.getAttribute('id');
        console.log('ID активной подвкладки:', activeTabId);
        
        if (activeTabId === 'stages-tab') {
            console.log('Активная подвкладка: Этапы');
            // Проверяем, есть ли данные этапов, если нет - загружаем
            if (window.scheduleData.stages.length === 0) {
                console.log('Данные этапов пустые, загружаем...');
                loadStagesData();
            } else {
                console.log('Данные этапов уже есть, отображаем...');
                renderStages(window.scheduleData.stages);
            }
        } else if (activeTabId === 'events-tab') {
            console.log('Активная подвкладка: События');
            // Проверяем, есть ли данные событий, если нет - загружаем
            if (window.scheduleData.events.length === 0) {
                console.log('Данные событий пустые, загружаем...');
                loadEventsData();
            } else {
                console.log('Данные событий уже есть, отображаем...');
                renderEvents(window.scheduleData.events);
            }
        }
    } else {
        console.log('Активная подвкладка не найдена, используем этапы по умолчанию');
        // Если активная вкладка не найдена, загружаем этапы по умолчанию
        if (window.scheduleData.stages.length === 0) {
            console.log('Данные этапов пустые, загружаем...');
            loadStagesData();
        } else {
            console.log('Данные этапов уже есть, отображаем...');
            renderStages(window.scheduleData.stages);
        }
    }
}

// Инициализация обработчиков событий
function initScheduleHandlers() {
    // Обработчики будут добавлены в основном файле show.blade.php
}

// Функции для модальных окон (используют modalManager)
function openAddStageModal() {
    console.log('Открытие модального окна для добавления этапа');
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        window.modalManager.loadModal('stage-add');
    } else {
        console.warn('modalManager не инициализирован, используем прямое открытие модального окна');
        openStageModalDirect();
    }
}

function openAddEventModal() {
    console.log('Открытие модального окна для добавления события');
    if (window.modalManager && typeof window.modalManager.loadModal === 'function') {
        window.modalManager.loadModal('event-add');
    } else {
        console.warn('modalManager не инициализирован, используем прямое открытие модального окна');
        openEventModalDirect();
    }
}

// Прямые функции открытия модальных окон (резерв на случай отсутствия modalManager)
function openStageModalDirect() {
    if (!window.projectId) {
        showMessage('Project ID не найден', 'error');
        return;
    }
    
    $.get(`/partner/projects/${window.projectId}/modals/stage-add`)
        .done(function(data) {
            if (data.html) {
                $('#modalContainer').html(data.html);
                const modal = new bootstrap.Modal(document.getElementById('addStageModal'));
                modal.show();
            }
        })
        .fail(function() {
            showMessage('Ошибка загрузки модального окна', 'error');
        });
}

function openEventModalDirect() {
    if (!window.projectId) {
        showMessage('Project ID не найден', 'error');
        return;
    }
    
    $.get(`/partner/projects/${window.projectId}/modals/event-add`)
        .done(function(data) {
            if (data.html) {
                $('#modalContainer').html(data.html);
                const modal = new bootstrap.Modal(document.getElementById('addEventModal'));
                modal.show();
            }
        })
        .fail(function() {
            showMessage('Ошибка загрузки модального окна', 'error');
        });
}

function editStage(stageId) {
    // Эта функция будет определена в основном файле с модальными окнами
    if (typeof window.modalManager === 'object' && typeof window.modalManager.loadModal === 'function') {
        window.modalManager.loadModal('stage-edit', { stageId: stageId });
    } else {
        console.warn('modalManager не инициализирован, не удается открыть модальное окно редактирования этапа');
    }
}

function editEvent(eventId) {
    // Эта функция будет определена в основном файле с модальными окнами  
    if (typeof window.modalManager === 'object' && typeof window.modalManager.loadModal === 'function') {
        window.modalManager.loadModal('event-edit', { eventId: eventId });
    } else {
        console.warn('modalManager не инициализирован, не удается открыть модальное окно редактирования события');
    }
}

// Функция обновления данных текущей вкладки
function refreshCurrentTabData() {
    loadScheduleData();
}

// Экспорт функций для использования в других местах
window.loadScheduleData = loadScheduleData;
window.loadActiveSubTab = loadActiveSubTab;
window.refreshCurrentTabData = refreshCurrentTabData;
window.completeStage = completeStage;
window.completeEvent = completeEvent;
window.deleteStage = deleteStage;
window.deleteEvent = deleteEvent;
// Экспорт функций модальных окон
window.openAddStageModal = openAddStageModal;
window.openAddEventModal = openAddEventModal;
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/schedule.blade.php ENDPATH**/ ?>