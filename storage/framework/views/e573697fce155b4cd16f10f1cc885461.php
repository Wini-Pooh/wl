<!-- Модальное окно для событий -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">
                    <i class="bi bi-calendar-event me-2"></i>Добавить событие
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="project_id" id="eventProjectId" value="<?php echo e($project->id); ?>">
                    <input type="hidden" name="event_id" id="eventId" value="">
                    
                    <div class="row">
                        <!-- Основная информация события -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-info-circle me-1"></i>Информация о событии
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="eventTitle" class="form-label">Название события <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="eventTitle" name="title" 
                                                       placeholder="Например: Встреча с заказчиком" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="eventDescription" class="form-label">Описание</label>
                                        <textarea class="form-control" id="eventDescription" name="description" rows="3" 
                                                  placeholder="Подробное описание события..."></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="eventDate" class="form-label">Дата события <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="eventDate" name="event_date" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="eventTime" class="form-label">Время</label>
                                            <input type="time" class="form-control" id="eventTime" name="event_time">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Дополнительные параметры -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-gear me-1"></i>Параметры
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="eventType" class="form-label">Тип события</label>
                                        <select class="form-select" id="eventType" name="type">
                                            <option value="meeting">Встреча</option>
                                            <option value="delivery">Доставка</option>
                                            <option value="inspection">Проверка</option>
                                            <option value="milestone">Веха</option>
                                            <option value="other">Другое</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="eventStatus" class="form-label">Статус</label>
                                        <select class="form-select" id="eventStatus" name="status">
                                            <option value="planned">Запланировано</option>
                                            <option value="completed">Завершено</option>
                                            <option value="cancelled">Отменено</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-people me-1"></i>Участники и контакты
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="eventLocation" class="form-label">Место проведения</label>
                                                <input type="text" class="form-control" id="eventLocation" name="location" 
                                                       placeholder="Адрес или место проведения">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="eventContact" class="form-label">Контактное лицо</label>
                                                <input type="text" class="form-control" id="eventContact" name="contact" 
                                                       placeholder="ФИО контактного лица">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="eventNotes" class="form-label">Заметки</label>
                                        <textarea class="form-control" id="eventNotes" name="notes" rows="3" 
                                                  placeholder="Дополнительные заметки о событии..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-info" id="saveEventBtn">
                    <i class="bi bi-check-lg me-1"></i>Сохранить событие
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Оптимизированная инициализация через ProjectManager
$(document).ready(function() {
    if (window.projectManager) {
        // Используем унифицированную систему инициализации модалов
        window.projectManager.initModal('eventModal', 'event', function() {
            console.log('✅ Модал событий инициализирован через ProjectManager');
            
            // Устанавливаем projectId
            const projectId = window.projectId || <?php echo e($project->id ?? 'null'); ?>;
            if (projectId) {
                $('#eventProjectId').val(projectId);
            }
            
            // Инициализируем обработчики
            initEventModalHandlers();
        });
    } else {
        console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
        // Fallback инициализация
        const projectId = window.projectId || <?php echo e($project->id ?? 'null'); ?>;
        if (projectId) {
            $('#eventProjectId').val(projectId);
        }
        
        initEventModalHandlers();
    }
});

function initEventModalHandlers() {
    console.log('📅 Инициализация обработчиков модального окна событий...');
    
    const saveBtn = document.getElementById('saveEventBtn');
    
    // Обработчик сохранения
    saveBtn.addEventListener('click', function() {
        saveEvent();
    });
    
    // Автоустановка текущей даты при открытии модального окна
    if (!$('#eventDate').val()) {
        const today = new Date().toISOString().split('T')[0];
        $('#eventDate').val(today);
    }
}

function saveEvent() {
    console.log('💾 Сохранение события...');
    
    const projectId = $('#eventProjectId').val();
    const eventId = $('#eventId').val();
    
    if (!projectId) {
        console.error('❌ Project ID не найден');
        if (window.modalManager) {
            window.modalManager.showErrorToast('Ошибка: ID проекта не найден');
        }
        return;
    }
    
    // Валидация обязательных полей
    if (!$('#eventTitle').val().trim()) {
        $('#eventTitle').addClass('is-invalid');
        if (window.modalManager) {
            window.modalManager.showErrorToast('Введите название события');
        }
        return;
    } else {
        $('#eventTitle').removeClass('is-invalid');
    }
    
    if (!$('#eventDate').val()) {
        $('#eventDate').addClass('is-invalid');
        if (window.modalManager) {
            window.modalManager.showErrorToast('Выберите дату события');
        }
        return;
    } else {
        $('#eventDate').removeClass('is-invalid');
    }
    
    // Собираем данные формы
    const formData = {
        project_id: projectId,
        title: $('#eventTitle').val(),
        description: $('#eventDescription').val(),
        event_date: $('#eventDate').val(),
        event_time: $('#eventTime').val() || null,
        type: $('#eventType').val(),
        status: $('#eventStatus').val(),
        priority: $('#eventPriority').val(),
        location: $('#eventLocation').val(),
        contact: $('#eventContact').val(),
        is_reminder: $('#eventIsReminder').is(':checked'),
        notes: $('#eventNotes').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    // Определяем URL и метод
    const isEdit = eventId && eventId !== '';
    const url = isEdit 
        ? `/partner/projects/${projectId}/events/${eventId}`
        : `/partner/projects/${projectId}/events`;
    const method = isEdit ? 'PUT' : 'POST';
    
    // Отключаем кнопку сохранения
    const saveBtn = $('#saveEventBtn');
    saveBtn.prop('disabled', true);
    saveBtn.html('<i class="bi bi-hourglass-split me-1"></i>Сохранение...');
    
    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function(response) {
            console.log('✅ Событие успешно сохранено:', response);
            
            if (window.modalManager) {
                window.modalManager.closeActiveModal();
                window.modalManager.showSuccessToast(
                    isEdit ? 'Событие успешно обновлено!' : 'Событие успешно добавлено!'
                );
            }
            
            // Перезагружаем список событий на странице
            if (typeof window.reloadEvents === 'function') {
                window.reloadEvents();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Ошибка сохранения события:', error);
            
            saveBtn.prop('disabled', false);
            saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить событие');
            
            let errorMessage = 'Ошибка сохранения события';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            if (window.modalManager) {
                window.modalManager.showErrorToast(errorMessage);
            }
        }
    });
}

// Функция для редактирования события
window.editEvent = function(eventId) {
    console.log('✏️ Редактирование события ID:', eventId);
    
    const projectId = window.projectId || $('#eventProjectId').val();
    
    if (!projectId) {
        console.error('❌ Project ID не найден');
        return;
    }
    
    // Меняем заголовок модального окна
    $('#eventModalLabel').html('<i class="bi bi-pencil me-2"></i>Редактировать событие');
    
    // Устанавливаем ID события
    $('#eventId').val(eventId);
    
    // Загружаем данные события
    $.ajax({
        url: `/partner/projects/${projectId}/events/${eventId}`,
        method: 'GET',
        success: function(event) {
            console.log('📝 Данные события загружены:', event);
            
            // Заполняем форму
            $('#eventTitle').val(event.title);
            $('#eventDescription').val(event.description);
            $('#eventDate').val(event.event_date);
            $('#eventTime').val(event.event_time);
            $('#eventType').val(event.type);
            $('#eventStatus').val(event.status);
            $('#eventPriority').val(event.priority);
            $('#eventLocation').val(event.location);
            $('#eventContact').val(event.contact);
            $('#eventIsReminder').prop('checked', event.is_reminder);
            $('#eventNotes').val(event.notes);
            
            // Открываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        },
        error: function(xhr, status, error) {
            console.error('❌ Ошибка загрузки данных события:', error);
            
            if (window.modalManager) {
                window.modalManager.showErrorToast('Ошибка загрузки данных события');
            }
        }
    });
};

// Функция для тестирования модального окна событий
window.testEventModal = function() {
    console.log('🧪 Тестирование модального окна событий...');
    
    // Сбрасываем форму
    $('#eventForm')[0].reset();
    $('#eventId').val('');
    $('#eventModalLabel').html('<i class="bi bi-calendar-event me-2"></i>Добавить событие');
    
    // Устанавливаем текущую дату
    const today = new Date().toISOString().split('T')[0];
    $('#eventDate').val(today);
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
    
    console.log('✅ Модальное окно событий открыто для тестирования');
};

// Функция для сброса формы при открытии модального окна для нового события
$('#eventModal').on('hidden.bs.modal', function() {
    console.log('🔄 Сброс формы события');
    
    // Сбрасываем форму
    $('#eventForm')[0].reset();
    $('#eventId').val('');
    
    // Устанавливаем текущую дату
    const today = new Date().toISOString().split('T')[0];
    $('#eventDate').val(today);
    
    // Возвращаем заголовок
    $('#eventModalLabel').html('<i class="bi bi-calendar-event me-2"></i>Добавить событие');
    
    // Убираем классы валидации
    $('.is-invalid').removeClass('is-invalid');
    
    // Включаем кнопку сохранения
    const saveBtn = $('#saveEventBtn');
    saveBtn.prop('disabled', false);
    saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить событие');
});
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/modals/event-modal.blade.php ENDPATH**/ ?>