<!-- Модальное окно для основной страницы проекта -->
<div class="modal fade" id="projectMainModal" tabindex="-1" aria-labelledby="projectMainModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectMainModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Быстрое добавление
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Вкладки для быстрого добавления -->
                <ul class="nav nav-tabs" id="quickAddTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="quick-stage-tab" data-bs-toggle="tab" data-bs-target="#quick-stage" type="button" role="tab">
                            <i class="bi bi-list-check me-1"></i>Этап
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="quick-task-tab" data-bs-toggle="tab" data-bs-target="#quick-task" type="button" role="tab">
                            <i class="bi bi-check2-square me-1"></i>Задача
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="quick-note-tab" data-bs-toggle="tab" data-bs-target="#quick-note" type="button" role="tab">
                            <i class="bi bi-sticky me-1"></i>Заметка
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content mt-3" id="quickAddTabContent">
                    <!-- Быстрое добавление этапа -->
                    <div class="tab-pane fade show active" id="quick-stage" role="tabpanel">
                        <form id="quickStageForm">
                            @csrf
                            <input type="hidden" name="project_id" id="quickStageProjectId" value="{{ $project->id }}">
                            
                            <div class="mb-3">
                                <label for="quickStageName" class="form-label">Название этапа <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="quickStageName" name="name" required
                                       placeholder="Введите название этапа">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quickStageStartDate" class="form-label">Дата начала</label>
                                        <input type="date" class="form-control" id="quickStageStartDate" name="start_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quickStageEndDate" class="form-label">Дата окончания</label>
                                        <input type="date" class="form-control" id="quickStageEndDate" name="end_date">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quickStageDescription" class="form-label">Описание</label>
                                <textarea class="form-control" id="quickStageDescription" name="description" rows="3"
                                          placeholder="Описание этапа..."></textarea>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Быстрое добавление задачи -->
                    <div class="tab-pane fade" id="quick-task" role="tabpanel">
                        <form id="quickTaskForm">
                            @csrf
                            <input type="hidden" name="project_id" id="quickTaskProjectId" value="{{ $project->id }}">
                            
                            <div class="mb-3">
                                <label for="quickTaskTitle" class="form-label">Название задачи <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="quickTaskTitle" name="title" required
                                       placeholder="Введите название задачи">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quickTaskPriority" class="form-label">Приоритет</label>
                                        <select class="form-select" id="quickTaskPriority" name="priority">
                                            <option value="low">Низкий</option>
                                            <option value="normal" selected>Обычный</option>
                                            <option value="high">Высокий</option>
                                            <option value="urgent">Срочный</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quickTaskDeadline" class="form-label">Срок выполнения</label>
                                        <input type="date" class="form-control" id="quickTaskDeadline" name="deadline">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quickTaskDescription" class="form-label">Описание</label>
                                <textarea class="form-control" id="quickTaskDescription" name="description" rows="3"
                                          placeholder="Описание задачи..."></textarea>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Быстрое добавление заметки -->
                    <div class="tab-pane fade" id="quick-note" role="tabpanel">
                        <form id="quickNoteForm">
                            @csrf
                            <input type="hidden" name="project_id" id="quickNoteProjectId" value="{{ $project->id }}">
                            
                            <div class="mb-3">
                                <label for="quickNoteTitle" class="form-label">Заголовок заметки <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="quickNoteTitle" name="title" required
                                       placeholder="Введите заголовок заметки">
                            </div>
                            
                            <div class="mb-3">
                                <label for="quickNoteCategory" class="form-label">Категория</label>
                                <select class="form-select" id="quickNoteCategory" name="category">
                                    <option value="general">Общая</option>
                                    <option value="important">Важная</option>
                                    <option value="reminder">Напоминание</option>
                                    <option value="meeting">Встреча</option>
                                    <option value="decision">Решение</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quickNoteContent" class="form-label">Содержание <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="quickNoteContent" name="content" rows="5" required
                                          placeholder="Введите содержание заметки..."></textarea>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="quickNoteIsImportant" name="is_important">
                                <label class="form-check-label" for="quickNoteIsImportant">
                                    Важная заметка
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="saveQuickItemBtn">
                    <i class="bi bi-check-lg me-1"></i>Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Предотвращаем повторную инициализацию
if (!window.projectMainModalInitialized) {
    window.projectMainModalInitialized = true;
    
    $(document).ready(function() {
        // Устанавливаем projectId во все формы
        const projectId = window.projectId || {{ $project->id ?? 'null' }};
        if (projectId) {
            $('#quickStageProjectId').val(projectId);
            $('#quickTaskProjectId').val(projectId);
            $('#quickNoteProjectId').val(projectId);
        }
        
        initProjectMainModalHandlers();
    });
}

function initProjectMainModalHandlers() {
    console.log('🏠 Инициализация обработчиков главного модального окна проекта...');
    
    const saveBtn = document.getElementById('saveQuickItemBtn');
    
    // Обработчик сохранения
    saveBtn.addEventListener('click', function() {
        const activeTab = document.querySelector('#quickAddTabs .nav-link.active');
        const activeTabId = activeTab.getAttribute('data-bs-target');
        
        switch(activeTabId) {
            case '#quick-stage':
                saveQuickStage();
                break;
            case '#quick-task':
                saveQuickTask();
                break;
            case '#quick-note':
                saveQuickNote();
                break;
        }
    });
    
    // Обработчик переключения вкладок - меняем текст кнопки
    $('#quickAddTabs button').on('shown.bs.tab', function(e) {
        const target = $(e.target).data('bs-target');
        const saveBtn = $('#saveQuickItemBtn');
        
        switch(target) {
            case '#quick-stage':
                saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
                break;
            case '#quick-task':
                saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить задачу');
                break;
            case '#quick-note':
                saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить заметку');
                break;
        }
    });
}

function saveQuickStage() {
    console.log('💾 Сохранение быстрого этапа...');
    
    const form = document.getElementById('quickStageForm');
    const formData = new FormData(form);
    const projectId = $('#quickStageProjectId').val();
    
    if (!projectId) {
        console.error('❌ Project ID не найден');
        if (window.modalManager) {
            window.modalManager.showErrorToast('Ошибка: ID проекта не найден');
        }
        return;
    }
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const saveBtn = $('#saveQuickItemBtn');
    saveBtn.prop('disabled', true);
    saveBtn.html('<i class="bi bi-hourglass-split me-1"></i>Сохранение...');
    
    $.ajax({
        url: `/partner/projects/${projectId}/stages`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('✅ Быстрый этап успешно сохранен:', response);
            
            if (window.modalManager) {
                window.modalManager.closeActiveModal();
                window.modalManager.showSuccessToast('Этап успешно добавлен!');
            }
            
            // Перезагружаем данные на всех страницах
            if (typeof window.reloadProjectData === 'function') {
                window.reloadProjectData();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Ошибка сохранения этапа:', error);
            
            let errorMessage = 'Ошибка сохранения этапа';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            if (window.modalManager) {
                window.modalManager.showErrorToast(errorMessage);
            }
        },
        complete: function() {
            saveBtn.prop('disabled', false);
            saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
        }
    });
}

function saveQuickTask() {
    console.log('💾 Сохранение быстрой задачи...');
    
    const form = document.getElementById('quickTaskForm');
    const formData = new FormData(form);
    const projectId = $('#quickTaskProjectId').val();
    
    if (!projectId) {
        console.error('❌ Project ID не найден');
        if (window.modalManager) {
            window.modalManager.showErrorToast('Ошибка: ID проекта не найден');
        }
        return;
    }
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const saveBtn = $('#saveQuickItemBtn');
    saveBtn.prop('disabled', true);
    saveBtn.html('<i class="bi bi-hourglass-split me-1"></i>Сохранение...');
    
    $.ajax({
        url: `/partner/projects/${projectId}/tasks`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('✅ Быстрая задача успешно сохранена:', response);
            
            if (window.modalManager) {
                window.modalManager.closeActiveModal();
                window.modalManager.showSuccessToast('Задача успешно добавлена!');
            }
            
            // Перезагружаем данные на всех страницах
            if (typeof window.reloadProjectData === 'function') {
                window.reloadProjectData();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Ошибка сохранения задачи:', error);
            
            let errorMessage = 'Ошибка сохранения задачи';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            if (window.modalManager) {
                window.modalManager.showErrorToast(errorMessage);
            }
        },
        complete: function() {
            saveBtn.prop('disabled', false);
            saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить задачу');
        }
    });
}

function saveQuickNote() {
    console.log('💾 Сохранение быстрой заметки...');
    
    const form = document.getElementById('quickNoteForm');
    const formData = new FormData(form);
    const projectId = $('#quickNoteProjectId').val();
    
    if (!projectId) {
        console.error('❌ Project ID не найден');
        if (window.modalManager) {
            window.modalManager.showErrorToast('Ошибка: ID проекта не найден');
        }
        return;
    }
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const saveBtn = $('#saveQuickItemBtn');
    saveBtn.prop('disabled', true);
    saveBtn.html('<i class="bi bi-hourglass-split me-1"></i>Сохранение...');
    
    $.ajax({
        url: `/partner/projects/${projectId}/notes`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('✅ Быстрая заметка успешно сохранена:', response);
            
            if (window.modalManager) {
                window.modalManager.closeActiveModal();
                window.modalManager.showSuccessToast('Заметка успешно добавлена!');
            }
            
            // Перезагружаем данные на всех страницах
            if (typeof window.reloadProjectData === 'function') {
                window.reloadProjectData();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Ошибка сохранения заметки:', error);
            
            let errorMessage = 'Ошибка сохранения заметки';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            if (window.modalManager) {
                window.modalManager.showErrorToast(errorMessage);
            }
        },
        complete: function() {
            saveBtn.prop('disabled', false);
            saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить заметку');
        }
    });
}

// Функция для сброса форм при закрытии модального окна
$('#projectMainModal').on('hidden.bs.modal', function() {
    console.log('🔄 Сброс форм главного модального окна');
    
    // Сбрасываем все формы
    $('#quickStageForm')[0].reset();
    $('#quickTaskForm')[0].reset();
    $('#quickNoteForm')[0].reset();
    
    // Возвращаем на первую вкладку
    const firstTab = new bootstrap.Tab(document.querySelector('#quick-stage-tab'));
    firstTab.show();
    
    // Убираем классы валидации
    $('.is-invalid').removeClass('is-invalid');
    
    // Сбрасываем кнопку сохранения
    const saveBtn = $('#saveQuickItemBtn');
    saveBtn.prop('disabled', false);
    saveBtn.html('<i class="bi bi-check-lg me-1"></i>Сохранить этап');
});
</script>
