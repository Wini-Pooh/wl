

<?php $__env->startSection('page-content'); ?>
    <?php echo $__env->make('partner.projects.tabs.schedule', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <!-- Модальные окна для страницы расписания -->
    <?php echo $__env->make('partner.projects.tabs.modals.stage-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partner.projects.tabs.modals.event-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
    <!-- Дополнительные стили для страницы графика -->
    <link rel="stylesheet" href="<?php echo e(asset('css/mobile-filters.css')); ?>">
    <style>
        /* Специфичные стили для страницы графика */
        .timeline-container {
            position: relative;
            padding: 1rem 0;
        }
        
        .stage-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        
        .stage-card:hover {
            border-left-color: #0056b3;
            box-shadow: 0 4px 12px rgba(0,123,255,0.15);
        }
        
        .stage-card.completed {
            border-left-color: #28a745;
            background-color: #f8fff9;
        }
        
        .stage-progress {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .stage-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #007bff, #0056b3);
            transition: width 0.3s ease;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
    <script>
        $(document).ready(function() {
            console.log('=== СТРАНИЦА РАСПИСАНИЯ ПРОЕКТА ===');
            
            // Инициализация через ProjectManager
            if (window.projectManager) {
                window.projectManager.initPage('schedule', function() {
                    console.log('✅ Страница расписания инициализирована через ProjectManager');
                    loadScheduleData();
                    initScheduleHandlers();
                });
            } else {
                console.warn('⚠️ ProjectManager не найден, используем прямую инициализацию');
                initSchedulePage();
            }
        });
        
        function initSchedulePage() {
            console.log('📅 Инициализация страницы расписания...');
            
            // Загружаем расписание и этапы
            loadScheduleData();
            
            // Инициализируем обработчики
            initScheduleHandlers();
        }
        
        function loadScheduleData() {
            const projectId = window.projectId;
            if (!projectId) return;
            
            console.log('📊 Загружаем данные расписания...');
            
            // Загружаем этапы проекта
            $.ajax({
                url: `/partner/projects/${projectId}/stages-partial`,
                method: 'GET',
                success: function(response) {
                    console.log('✅ Этапы проекта загружены');
                    if (response.stages && response.stages.length > 0) {
                        displayStages(response.stages);
                    } else {
                        displayEmptyStages();
                    }
                },
                error: function(xhr) {
                    console.warn('⚠️ Ошибка загрузки этапов:', xhr);
                }
            });
            
            // Загружаем события расписания
            $.ajax({
                url: `/partner/projects/${projectId}/events-partial`,
                method: 'GET',
                success: function(response) {
                    console.log('✅ События расписания загружены');
                    if (response.events && response.events.length > 0) {
                        displayEvents(response.events);
                    } else {
                        displayEmptyEvents();
                    }
                },
                error: function(xhr) {
                    console.warn('⚠️ Ошибка загрузки событий:', xhr);
                }
            });
        }
        
        function displayStages(stages) {
            const container = $('#stagesContainer');
            if (!container.length) return;
            
            container.empty();
            
            stages.forEach(stage => {
                console.log('📋 Этап:', stage); // Отладочная информация
                const progress = stage.progress || 0;
                const statusClass = stage.status === 'completed' ? 'completed' : 
                                   (stage.status === 'in_progress' ? 'in-progress' : 'pending');
                
                const stageHtml = `
                    <div class="card mb-3 stage-card ${statusClass}" data-stage-id="${stage.id}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="card-title mb-1">${stage.name}</h6>
                                    <p class="card-text text-muted small mb-2">${stage.description || ''}</p>
                                    <div class="stage-progress mb-2">
                                        <div class="stage-progress-bar" style="width: ${progress}%"></div>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar me-1"></i>
                                        ${formatDateRange(stage.start_date, stage.end_date)}
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="badge ${getStatusBadgeClass(stage.status)} mb-2">${getStatusText(stage.status)}</div>
                                    <div class="btn-group d-block" role="group">
                                        <button class="btn btn-sm btn-outline-primary edit-stage-btn" 
                                                data-stage-id="${stage.id}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        ${stage.status !== 'completed' ? `
                                            <button class="btn btn-sm btn-outline-success complete-stage-btn" 
                                                    data-stage-id="${stage.id}">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        ` : ''}
                                        <button class="btn btn-sm btn-outline-danger delete-stage-btn" 
                                                data-stage-id="${stage.id}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.append(stageHtml);
            });
        }
        
        function displayEmptyStages() {
            const container = $('#stagesContainer');
            if (!container.length) return;
            
            container.html(`
                <div class="text-center text-muted py-5">
                    <i class="bi bi-list-task display-4"></i>
                    <p class="mt-3">Этапов проекта пока нет</p>
                    <button class="btn btn-primary" data-modal-type="stage">
                        <i class="bi bi-plus me-1"></i>
                        Добавить первый этап
                    </button>
                </div>
            `);
        }
        
        function displayEvents(events) {
            const container = $('#eventsContainer');
            if (!container.length) return;
            
            container.empty();
            
            // Группируем события по датам
            const groupedEvents = groupEventsByDate(events);
            
            Object.keys(groupedEvents).forEach(date => {
                const dateHtml = `
                    <div class="events-date-group mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-calendar-event me-2"></i>
                            ${formatScheduleDate(date)}
                        </h6>
                        <div class="events-list">
                            ${groupedEvents[date].map(event => createEventCard(event)).join('')}
                        </div>
                    </div>
                `;
                container.append(dateHtml);
            });
        }
        
        function displayEmptyEvents() {
            const container = $('#eventsContainer');
            if (!container.length) return;
            
            container.html(`
                <div class="text-center text-muted py-5">
                    <i class="bi bi-calendar-x display-4"></i>
                    <p class="mt-3">Событий в расписании пока нет</p>
                    <button class="btn btn-primary" data-modal-type="event">
                        <i class="bi bi-plus me-1"></i>
                        Добавить первое событие
                    </button>
                </div>
            `);
        }
        
        function createEventCard(event) {
            const startTime = event.start_date ? formatTime(event.start_date) : 'Не указано';
            const endTime = event.end_date ? formatTime(event.end_date) : '';
            const timeRange = endTime ? `${startTime} - ${endTime}` : startTime;
            
            return `
                <div class="card mb-2 event-card" data-event-id="${event.id}">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title mb-1">${event.title}</h6>
                                <p class="card-text text-muted small mb-1">${event.description || ''}</p>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    ${timeRange}
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary edit-event-btn" 
                                            data-event-id="${event.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-event-btn" 
                                            data-event-id="${event.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function initScheduleHandlers() {
            console.log('🎯 Инициализация обработчиков расписания...');
            
            // Обработчики для модальных окон
            $(document).off('click', '[data-modal-type="stage"]').on('click', '[data-modal-type="stage"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🔘 Клик по кнопке добавления этапа');
                
                // Сбрасываем форму этапа и открываем модальное окно
                if (typeof window.openNewStageModal === 'function') {
                    window.openNewStageModal();
                } else if (typeof window.testStageModal === 'function') {
                    window.testStageModal();
                } else {
                    // Fallback - открываем модальное окно напрямую
                    console.log('⚠️ Функции openNewStageModal и testStageModal не найдены');
                    const modal = new bootstrap.Modal(document.getElementById('stageModal'));
                    modal.show();
                }
                
                return false;
            });
            
            $(document).off('click', '[data-modal-type="event"]').on('click', '[data-modal-type="event"]', function(e) {
                e.preventDefault();
                console.log('🔘 Клик по кнопке добавления события');
                
                // Сбрасываем форму события и открываем модальное окно
                if (typeof window.testEventModal === 'function') {
                    window.testEventModal();
                } else {
                    // Fallback - открываем модальное окно напрямую
                    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                }
            });
            
            // Обработчики для редактирования/удаления этапов
            $(document).off('click', '.edit-stage-btn').on('click', '.edit-stage-btn', function(e) {
                e.preventDefault();
                const stageId = $(this).data('stage-id');
                editStageFromSchedule(stageId);
            });
            
            $(document).off('click', '.complete-stage-btn').on('click', '.complete-stage-btn', function(e) {
                e.preventDefault();
                const stageId = $(this).data('stage-id');
                console.log('🔘 Кнопка завершения этапа нажата, ID этапа:', stageId);
                console.log('🔘 Элемент кнопки:', this);
                console.log('🔘 data-stage-id:', $(this).attr('data-stage-id'));
                
                if (!stageId) {
                    console.error('❌ ID этапа не найден в data-stage-id');
                    alert('Ошибка: ID этапа не найден');
                    return;
                }
                
                completeStage(stageId);
            });
            
            $(document).off('click', '.delete-stage-btn').on('click', '.delete-stage-btn', function(e) {
                e.preventDefault();
                const stageId = $(this).data('stage-id');
                deleteStage(stageId);
            });
            
            // Обработчики для редактирования/удаления событий
            $(document).off('click', '.edit-event-btn').on('click', '.edit-event-btn', function(e) {
                e.preventDefault();
                const eventId = $(this).data('event-id');
                editEventFromSchedule(eventId);
            });
            
            $(document).off('click', '.delete-event-btn').on('click', '.delete-event-btn', function(e) {
                e.preventDefault();
                const eventId = $(this).data('event-id');
                deleteEvent(eventId);
            });
        }
        
        function editStageFromSchedule(stageId) {
            console.log('✏️ Редактирование этапа:', stageId);
            
            // Вызываем функцию из stage-modal.blade.php
            if (typeof window.editStage === 'function') {
                window.editStage(stageId);
            } else {
                console.error('❌ Функция editStage не найдена');
                alert('Ошибка: функция редактирования этапа не найдена');
            }
        }
        
        function completeStage(stageId) {
            console.log('✅ Завершение этапа:', stageId);
            console.log('🆔 window.projectId:', window.projectId);
            
            if (!window.projectId) {
                console.error('❌ window.projectId не определен');
                alert('Ошибка: ID проекта не найден');
                return;
            }
            
            // Проверим CSRF токен
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            console.log('🔐 CSRF Token:', csrfToken ? 'найден' : 'НЕ НАЙДЕН');
            
            if (!csrfToken) {
                console.error('❌ CSRF токен не найден');
                alert('Ошибка: CSRF токен не найден');
                return;
            }
            
          
            
            const url = `/partner/projects/${window.projectId}/stages/${stageId}/complete`;
            console.log('🌐 AJAX URL:', url);
            
            $.ajax({
                url: url,
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    console.log('✅ Успешный ответ:', response);
                   
                    loadScheduleData();
                },
                error: function(xhr) {
                    console.error('❌ Ошибка завершения этапа:', xhr);
                    console.error('❌ Статус:', xhr.status);
                    console.error('❌ Ответ:', xhr.responseText);
                    alert('Ошибка завершения этапа: ' + xhr.status + ' - ' + xhr.responseText);
                }
            });
        }
        
        function deleteStage(stageId) {
            console.log('🗑️ Удаление этапа:', stageId);
            
            if (!confirm('Удалить этот этап?')) {
                return;
            }
            
            $.ajax({
                url: `/partner/projects/${window.projectId}/stages/${stageId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Этап удален');
                    loadScheduleData();
                },
                error: function(xhr) {
                    console.error('❌ Ошибка удаления этапа:', xhr);
                    alert('Ошибка удаления этапа');
                }
            });
        }
        
        function editEventFromSchedule(eventId) {
            console.log('✏️ Редактирование события:', eventId);
            
            // Вызываем функцию из event-modal.blade.php
            if (typeof window.editEvent === 'function') {
                window.editEvent(eventId);
            } else {
                console.error('❌ Функция editEvent не найдена');
                alert('Ошибка: функция редактирования события не найдена');
            }
        }
        
        function deleteEvent(eventId) {
            console.log('🗑️ Удаление события:', eventId);
            
            if (!confirm('Удалить это событие?')) {
                return;
            }
            
            $.ajax({
                url: `/partner/projects/${window.projectId}/events/${eventId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Событие удалено');
                    loadScheduleData();
                },
                error: function(xhr) {
                    console.error('❌ Ошибка удаления события:', xhr);
                    alert('Ошибка удаления события');
                }
            });
        }
        
        // Вспомогательные функции
        function groupEventsByDate(events) {
            const grouped = {};
            
            events.forEach(event => {
                const date = event.start_date ? event.start_date.split(' ')[0] : new Date().toISOString().split('T')[0];
                if (!grouped[date]) {
                    grouped[date] = [];
                }
                grouped[date].push(event);
            });
            
            // Сортируем по датам
            const sortedGrouped = {};
            Object.keys(grouped).sort().forEach(key => {
                sortedGrouped[key] = grouped[key];
            });
            
            return sortedGrouped;
        }
        
        function formatDateRange(startDate, endDate) {
            if (!startDate && !endDate) return 'Даты не указаны';
            
            if (startDate && !endDate) {
                return `с ${formatDate(startDate)}`;
            }
            
            if (!startDate && endDate) {
                return `до ${formatDate(endDate)}`;
            }
            
            return `${formatDate(startDate)} - ${formatDate(endDate)}`;
        }
        
        function formatScheduleDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('ru-RU', {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }).format(date);
        }
        
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(date);
        }
        
        function formatTime(dateTimeString) {
            if (!dateTimeString) return '';
            const date = new Date(dateTimeString);
            return new Intl.DateTimeFormat('ru-RU', {
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        }
        
        function getStatusText(status) {
            switch(status) {
                case 'completed': return 'Завершен';
                case 'in_progress': return 'В работе';
                case 'on_hold': return 'Приостановлен';
                case 'not_started':
                default: return 'Не начат';
            }
        }
        
        function getStatusBadgeClass(status) {
            switch(status) {
                case 'completed': return 'bg-success';
                case 'in_progress': return 'bg-warning';
                case 'on_hold': return 'bg-danger';
                case 'not_started':
                default: return 'bg-secondary';
            }
        }
        
        // Функция для обновления расписания после изменений
        window.reloadSchedule = function() {
            console.log('🔄 Обновление расписания...');
            loadScheduleData();
        };
        
        // Функция для обновления этапов (для совместимости с stage-modal.blade.php)
        window.reloadStages = function() {
            console.log('🔄 Обновление этапов...');
            loadScheduleData();
        };
        
        // Функция для обновления событий (для совместимости с event-modal.blade.php)
        window.reloadEvents = function() {
            console.log('🔄 Обновление событий...');
            loadScheduleData();
        };
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partner.projects.layouts.project-base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/pages/schedule.blade.php ENDPATH**/ ?>