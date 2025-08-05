<!-- Модальное окно для этапов -->
<div class="modal fade" id="stageModal" tabindex="-1" aria-labelledby="stageModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stageModalLabel">
                    <i class="bi bi-list-check me-2"></i>Добавить этап
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stageForm">
                    @csrf
                    <input type="hidden" name="project_id" id="stageProjectId" value="{{ $project->id }}">
                    <input type="hidden" name="stage_id" id="stageId" value="">
                    
                    <div class="row">
                        <!-- Основная информация этапа -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-info-circle me-1"></i>Информация об этапе
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="stageName" class="form-label">Название этапа <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="stageName" name="name" 
                                                       placeholder="Например: Подготовительные работы" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="stageDescription" class="form-label">Описание</label>
                                        <textarea class="form-control" id="stageDescription" name="description" rows="3" 
                                                  placeholder="Подробное описание этапа..."></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="stagePlannedStartDate" class="form-label">Запланированная дата начала</label>
                                            <input type="date" class="form-control" id="stagePlannedStartDate" name="planned_start_date">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="stagePlannedEndDate" class="form-label">Запланированная дата окончания</label>
                                            <input type="date" class="form-control" id="stagePlannedEndDate" name="planned_end_date">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="stageActualStartDate" class="form-label">Фактическая дата начала</label>
                                            <input type="date" class="form-control" id="stageActualStartDate" name="actual_start_date">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="stageActualEndDate" class="form-label">Фактическая дата окончания</label>
                                            <input type="date" class="form-control" id="stageActualEndDate" name="actual_end_date">
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
                                        <label for="stageStatus" class="form-label">Статус</label>
                                        <select class="form-select" id="stageStatus" name="status">
                                            <option value="not_started">Не начат</option>
                                            <option value="in_progress">В процессе</option>
                                            <option value="completed">Завершен</option>
                                            <option value="on_hold">Приостановлен</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="stageProgress" class="form-label">Прогресс (%)</label>
                                        <input type="text" class="form-control progress-mask" id="stageProgress" name="progress" 
                                               value="0" placeholder="0"
                                               data-mask="percentage">
                                    </div>

                                    <div class="mb-3">
                                        <label for="stageOrder" class="form-label">Порядок</label>
                                        <input type="number" class="form-control" id="stageOrder" name="order" 
                                               min="1" value="1">
                                    </div>

                                    <div class="mb-3">
                                        <label for="stageDurationDays" class="form-label">Длительность (дни)</label>
                                        <input type="number" class="form-control" id="stageDurationDays" name="duration_days" 
                                               min="1" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="saveStageBtn">
                    <i class="bi bi-check-lg me-1"></i>Сохранить этап
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Предотвращаем повторную инициализацию
if (!window.stageModalInitialized) {
    window.stageModalInitialized = true;
    
    $(document).ready(function() {
        console.log('🔧 Начинаем инициализацию модального окна этапов...');
        
        // Устанавливаем projectId
        const projectId = window.projectId || {{ $project->id ?? 'null' }};
        console.log('🆔 Project ID:', projectId);
        
        if (projectId) {
            $('#stageProjectId').val(projectId);
        }
        
        // Проверяем наличие элементов
        const saveBtn = $('#saveStageBtn');
        console.log('🔘 Кнопка сохранения найдена:', saveBtn.length > 0);
        
        if (saveBtn.length === 0) {
            console.error('❌ Кнопка сохранения этапа не найдена в DOM!');
            return;
        }
        
        initStageModalHandlers();
        console.log('✅ Модальное окно этапов инициализировано');
    });
}

function initStageModalHandlers() {
    console.log('📝 Инициализация обработчиков модального окна этапов...');
    
    // Проверяем, не были ли уже установлены обработчики
    if ($('#saveStageBtn').data('handlers-initialized')) {
        console.log('⚠️ Обработчики уже инициализированы, пропускаем');
        return;
    }
    
    // Убираем все старые обработчики для предотвращения дублирования
    $('#saveStageBtn').off('click.stageModal');
    $('#stagePlannedStartDate, #stagePlannedEndDate, #stageActualStartDate, #stageActualEndDate').off('change.stageModal blur.stageModal');
    
    // Проверяем кнопку еще раз
    const saveBtn = $('#saveStageBtn');
    console.log('🔘 Проверка кнопки в initStageModalHandlers:', saveBtn.length);
    
    if (saveBtn.length === 0) {
        console.error('❌ Кнопка сохранения этапа не найдена');
        return;
    }
    
    // Убираем класс saving, если он остался от предыдущих вызовов
    saveBtn.removeClass('saving').prop('disabled', false);
    saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
    
    // Обработчик сохранения с защитой от дублирования
    saveBtn.on('click.stageModal', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Проверяем, не выполняется ли уже сохранение
        if ($(this).prop('disabled') || $(this).hasClass('saving') || window.stageSaving) {
            console.log('⚠️ Сохранение уже выполняется, пропускаем клик');
            return false;
        }
        
        console.log('🔘 Клик по кнопке сохранения этапа - обработчик сработал!');
        saveStage();
        return false;
    });
    
    // Валидация дат с namespace для всех полей дат
    $('#stagePlannedStartDate, #stagePlannedEndDate, #stageActualStartDate, #stageActualEndDate').on('change.stageModal blur.stageModal', validateStageDates);
    
    // Отмечаем, что обработчики установлены
    saveBtn.data('handlers-initialized', true);
    
    console.log('✅ Обработчики модального окна этапов установлены');
}

function validateStageDates() {
    const startDate = $('#stagePlannedStartDate').val();
    const endDate = $('#stagePlannedEndDate').val();
    const actualStartDate = $('#stageActualStartDate').val();
    const actualEndDate = $('#stageActualEndDate').val();
    
    let isValid = true;
    
    // Убираем предыдущие ошибки
    $('#stagePlannedStartDate, #stagePlannedEndDate, #stageActualStartDate, #stageActualEndDate').removeClass('is-invalid');
    
    // Проверяем запланированные даты
    if (startDate && endDate && startDate > endDate) {
        $('#stagePlannedEndDate').addClass('is-invalid');
        console.warn('⚠️ Запланированная дата окончания не может быть раньше даты начала');
        isValid = false;
    }
    
    // Проверяем фактические даты
    if (actualStartDate && actualEndDate && actualStartDate > actualEndDate) {
        $('#stageActualEndDate').addClass('is-invalid');
        console.warn('⚠️ Фактическая дата окончания не может быть раньше даты начала');
        isValid = false;
    }
    
    // Проверяем формат дат
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    
    if (startDate && !dateRegex.test(startDate)) {
        $('#stagePlannedStartDate').addClass('is-invalid');
        console.warn('⚠️ Неверный формат запланированной даты начала');
        isValid = false;
    }
    
    if (endDate && !dateRegex.test(endDate)) {
        $('#stagePlannedEndDate').addClass('is-invalid');
        console.warn('⚠️ Неверный формат запланированной даты окончания');
        isValid = false;
    }
    
    if (actualStartDate && !dateRegex.test(actualStartDate)) {
        $('#stageActualStartDate').addClass('is-invalid');
        console.warn('⚠️ Неверный формат фактической даты начала');
        isValid = false;
    }
    
    if (actualEndDate && !dateRegex.test(actualEndDate)) {
        $('#stageActualEndDate').addClass('is-invalid');
        console.warn('⚠️ Неверный формат фактической даты окончания');
        isValid = false;
    }
    
    return isValid;
}

function saveStage() {
    console.log('💾 Сохранение этапа... Вызов функции начинается');
    
    // Глобальная защита от дублирования
    if (window.stageSaving) {
        console.log('⚠️ Глобальная защита: сохранение уже выполняется');
        return;
    }
    
    // Проверяем, не выполняется ли уже сохранение
    const saveBtn = $('#saveStageBtn');
    if (saveBtn.prop('disabled') || saveBtn.hasClass('saving')) {
        console.log('⚠️ Сохранение уже выполняется, отменяем повторный вызов');
        return;
    }
    
    // Устанавливаем глобальный флаг
    window.stageSaving = true;
    
    // Устанавливаем флаг, что сохранение началось
    saveBtn.addClass('saving').prop('disabled', true);
    saveBtn.html('<i class="bi bi-hourglass-split me-1"></i>Сохранение...');
    
    const projectId = $('#stageProjectId').val();
    const stageId = $('#stageId').val();
    
    console.log('📊 Данные для сохранения:', {
        projectId: projectId,
        stageId: stageId,
        modalManager: !!window.modalManager
    });
    
    if (!projectId) {
        console.error('❌ Project ID не найден');
        alert('Ошибка: ID проекта не найден');
        
        // Сбрасываем состояние
        window.stageSaving = false;
        saveBtn.removeClass('saving').prop('disabled', false);
        saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
        return;
    }
    
    // Валидация обязательных полей
    const stageName = $('#stageName').val().trim();
    console.log('📝 Название этапа:', stageName);
    
    if (!stageName) {
        $('#stageName').addClass('is-invalid');
        console.error('❌ Название этапа не заполнено');
        alert('Введите название этапа');
        
        // Сбрасываем состояние
        window.stageSaving = false;
        saveBtn.removeClass('saving').prop('disabled', false);
        saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
        return;
    } else {
        $('#stageName').removeClass('is-invalid');
    }
    
    // Валидация дат
    if (!validateStageDates()) {
        alert('Пожалуйста, исправьте ошибки в датах');
        
        // Сбрасываем состояние
        window.stageSaving = false;
        saveBtn.removeClass('saving').prop('disabled', false);
        saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
        return;
    }
    
    // Функция для валидации и получения даты
    function getValidDate(selector) {
        const value = $(selector).val();
        if (!value || value.trim() === '') {
            return null;
        }
        
        // Проверяем формат даты (Y-m-d)
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateRegex.test(value)) {
            console.warn(`⚠️ Неверный формат даты в ${selector}: ${value}`);
            return null;
        }
        
        // Проверяем, что дата валидна
        const date = new Date(value + 'T00:00:00');
        if (isNaN(date.getTime())) {
            console.warn(`⚠️ Невалидная дата в ${selector}: ${value}`);
            return null;
        }
        
        return value;
    }
    
    // Собираем данные формы
    const formData = {
        project_id: projectId,
        name: stageName,
        description: $('#stageDescription').val() || '',
        status: $('#stageStatus').val(),
        progress: $('#stageProgress').val() || 0,
        order: $('#stageOrder').val() || 1,
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    
    // Добавляем даты только если они заполнены и валидны
    const plannedStartDate = getValidDate('#stagePlannedStartDate');
    if (plannedStartDate !== null) {
        formData.planned_start_date = plannedStartDate;
    }
    
    const plannedEndDate = getValidDate('#stagePlannedEndDate');
    if (plannedEndDate !== null) {
        formData.planned_end_date = plannedEndDate;
    }
    
    const actualStartDate = getValidDate('#stageActualStartDate');
    if (actualStartDate !== null) {
        formData.actual_start_date = actualStartDate;
    }
    
    const actualEndDate = getValidDate('#stageActualEndDate');
    if (actualEndDate !== null) {
        formData.actual_end_date = actualEndDate;
    }
    
    const durationDays = $('#stageDurationDays').val();
    if (durationDays && durationDays.trim() !== '' && !isNaN(parseInt(durationDays))) {
        formData.duration_days = parseInt(durationDays);
    }
    
    console.log('📦 Данные формы:', formData);
    
    // Определяем URL и метод
    const isEdit = stageId && stageId !== '';
    const url = isEdit 
        ? `/partner/projects/${projectId}/stages/${stageId}`
        : `/partner/projects/${projectId}/stages`;
    const method = isEdit ? 'PUT' : 'POST';
    
    console.log('🌐 AJAX запрос:', { url, method, isEdit });
    
    $.ajax({
        url: url,
        method: method,
        data: formData,
        beforeSend: function() {
            console.log('🚀 Отправка AJAX запроса...');
        },
        success: function(response) {
            console.log('✅ Этап успешно сохранен:', response);
            
              // Закрываем модальное окно
            $('#stageModal').modal('hide');
            
            // Перезагружаем список этапов на странице
            if (typeof window.reloadStages === 'function') {
                window.reloadStages();
            } else {
                console.log('⚠️ Функция reloadStages не найдена, перезагружаем страницу');
                location.reload();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Ошибка сохранения этапа:', { xhr, status, error });
            console.error('Response text:', xhr.responseText);
            
            let errorMessage = 'Ошибка сохранения этапа';
            
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Показываем ошибки валидации для каждого поля
                if (xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let validationErrors = [];
                    
                    // Подсвечиваем поля с ошибками
                    Object.keys(errors).forEach(field => {
                        const fieldErrors = errors[field];
                        if (Array.isArray(fieldErrors)) {
                            fieldErrors.forEach(error => {
                                validationErrors.push(`${field}: ${error}`);
                                
                                // Подсвечиваем поле с ошибкой
                                let fieldSelector = '';
                                switch(field) {
                                    case 'name':
                                        fieldSelector = '#stageName';
                                        break;
                                    case 'description':
                                        fieldSelector = '#stageDescription';
                                        break;
                                    case 'planned_start_date':
                                        fieldSelector = '#stagePlannedStartDate';
                                        break;
                                    case 'planned_end_date':
                                        fieldSelector = '#stagePlannedEndDate';
                                        break;
                                    case 'actual_start_date':
                                        fieldSelector = '#stageActualStartDate';
                                        break;
                                    case 'actual_end_date':
                                        fieldSelector = '#stageActualEndDate';
                                        break;
                                    case 'status':
                                        fieldSelector = '#stageStatus';
                                        break;
                                    case 'progress':
                                        fieldSelector = '#stageProgress';
                                        break;
                                    case 'order':
                                        fieldSelector = '#stageOrder';
                                        break;
                                    case 'duration_days':
                                        fieldSelector = '#stageDurationDays';
                                        break;
                                }
                                
                                if (fieldSelector) {
                                    $(fieldSelector).addClass('is-invalid');
                                }
                            });
                        }
                    });
                    
                    if (validationErrors.length > 0) {
                        errorMessage += '\n\nОшибки валидации:\n' + validationErrors.join('\n');
                    }
                }
            } else if (xhr.responseText) {
                errorMessage = 'Ошибка сервера: ' + xhr.status;
            }
            
            alert(errorMessage);
        },
        complete: function() {
            console.log('🏁 AJAX запрос завершен');
            
            // Сбрасываем глобальный флаг
            window.stageSaving = false;
            
            // Включаем кнопку сохранения обратно и убираем флаг saving
            saveBtn.removeClass('saving').prop('disabled', false);
            saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
        }
    });
}

// Функция для редактирования этапа
window.editStage = function(stageId) {
    console.log('✏️ Редактирование этапа ID:', stageId);
    
    const projectId = window.projectId || $('#stageProjectId').val();
    
    if (!projectId) {
        console.error('❌ Project ID не найден');
        alert('Ошибка: ID проекта не найден');
        return;
    }
    
    // Меняем заголовок модального окна
    $('#stageModalLabel').html('<i class="bi bi-pencil me-2"></i>Редактировать этап');
    
    // Устанавливаем ID этапа
    $('#stageId').val(stageId);
    
    // Загружаем данные этапа
    $.ajax({
        url: `/partner/projects/${projectId}/stages/${stageId}`,
        method: 'GET',
        success: function(response) {
            console.log('📝 Данные этапа загружены:', response);
            
            const stage = response.stage;
            
            // Заполняем форму
            $('#stageName').val(stage.name);
            $('#stageDescription').val(stage.description);
            $('#stagePlannedStartDate').val(stage.planned_start_date);
            $('#stagePlannedEndDate').val(stage.planned_end_date);
            $('#stageActualStartDate').val(stage.actual_start_date);
            $('#stageActualEndDate').val(stage.actual_end_date);
            $('#stageStatus').val(stage.status);
            $('#stageProgress').val(stage.progress);
            $('#stageOrder').val(stage.order);
            $('#stageDurationDays').val(stage.duration_days);
            
            // Открываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('stageModal'));
            modal.show();
        },
        error: function(xhr, status, error) {
            console.error('❌ Ошибка загрузки данных этапа:', error);
            alert('Ошибка загрузки данных этапа');
        }
    });
};

// Функция для тестирования модального окна
window.testStageModal = function() {
    console.log('🧪 Тестирование модального окна этапов...');
    
    // Сбрасываем глобальный флаг
    window.stageSaving = false;
    
    // Сбрасываем форму
    $('#stageForm')[0].reset();
    $('#stageId').val('');
    $('#stageModalLabel').html('<i class="bi bi-list-check me-2"></i>Добавить этап');
    
    // Убираем все классы валидации
    $('#stageForm .is-invalid').removeClass('is-invalid');
    
    // Устанавливаем значения по умолчанию
    $('#stageStatus').val('not_started');
    $('#stageProgress').val('0');
    $('#stageOrder').val('1');
    $('#stageDurationDays').val('1');
    
    // Сбрасываем состояние кнопки
    const saveBtn = $('#saveStageBtn');
    saveBtn.removeClass('saving').prop('disabled', false);
    saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(document.getElementById('stageModal'));
    modal.show();
    
    console.log('✅ Модальное окно открыто для тестирования');
};

// Функция для открытия нового этапа
window.openNewStageModal = function() {
    console.log('🆕 Открытие модального окна для нового этапа...');
    
    // Сбрасываем глобальный флаг
    window.stageSaving = false;
    
    // Сбрасываем форму
    $('#stageForm')[0].reset();
    $('#stageId').val('');
    $('#stageModalLabel').html('<i class="bi bi-list-check me-2"></i>Добавить этап');
    
    // Устанавливаем значения по умолчанию
    $('#stageStatus').val('not_started');
    $('#stageProgress').val('0');
    $('#stageOrder').val('1');
    $('#stageDurationDays').val('1');
    
    // Убираем все классы валидации
    $('#stageForm .is-invalid').removeClass('is-invalid');
    
    // Сбрасываем состояние кнопки
    const saveBtn = $('#saveStageBtn');
    saveBtn.removeClass('saving').prop('disabled', false);
    saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(document.getElementById('stageModal'));
    modal.show();
    
    console.log('✅ Модальное окно для нового этапа открыто');
};

// Функция для сброса формы при закрытии модального окна
$('#stageModal').off('hidden.bs.modal.stageModal').on('hidden.bs.modal.stageModal', function() {
    console.log('🔄 Сброс формы этапа при закрытии модального окна');
    
    // Сбрасываем глобальный флаг
    window.stageSaving = false;
    
    // Сбрасываем форму
    $('#stageForm')[0].reset();
    $('#stageId').val('');
    
    // Возвращаем заголовок
    $('#stageModalLabel').html('<i class="bi bi-list-check me-2"></i>Добавить этап');
    
    // Убираем все классы валидации
    $('#stageForm .is-invalid').removeClass('is-invalid');
    
    // Включаем кнопку сохранения и убираем все флаги
    const saveBtn = $('#saveStageBtn');
    saveBtn.removeClass('saving').prop('disabled', false);
    saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
    
    // Устанавливаем значения по умолчанию
    $('#stageStatus').val('not_started');
    $('#stageProgress').val('0');
    $('#stageOrder').val('1');
    $('#stageDurationDays').val('1');
});
</script>
