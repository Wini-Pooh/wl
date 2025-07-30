<!-- Модальное окно для добавления/редактирования этапа -->
<div class="modal fade" id="addStageModal" tabindex="-1" aria-labelledby="addStageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStageModalLabel">
                    <i class="bi bi-list-task me-2"></i>Добавить этап
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="addStageForm" action="{{ route('partner.projects.stages.store', $project->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="stageName" class="form-label">Название этапа <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="stageName" name="name" required placeholder="Например: Подготовительные работы">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="stageDescription" class="form-label">Описание этапа</label>
                        <textarea class="form-control" id="stageDescription" name="description" rows="3" placeholder="Подробное описание того, что включает в себя этап"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stagePlannedStartDate" class="form-label">Планируемая дата начала</label>
                            <input type="date" class="form-control" id="stagePlannedStartDate" name="planned_start_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stagePlannedEndDate" class="form-label">Планируемая дата окончания</label>
                            <input type="date" class="form-control" id="stagePlannedEndDate" name="planned_end_date">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stageStatus" class="form-label">Статус</label>
                            <select class="form-select" id="stageStatus" name="status">
                                <option value="not_started">Не начато</option>
                                <option value="in_progress">В работе</option>
                                <option value="completed">Завершено</option>
                                <option value="on_hold">Приостановлено</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stageOrder" class="form-label">Порядковый номер</label>
                            <input type="number" class="form-control" id="stageOrder" name="order" min="0" placeholder="Оставьте пустым для автоматической нумерации">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" form="addStageForm" class="btn btn-info">
                    <i class="bi bi-check-lg me-1"></i>Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initStageFormHandlers();
});

function initStageFormHandlers() {
    console.log('Инициализация обработчиков модального окна этапов');
    
    const plannedStartDateInput = document.getElementById('stagePlannedStartDate');
    const plannedEndDateInput = document.getElementById('stagePlannedEndDate');
    const statusSelect = document.getElementById('stageStatus');
    
    // Автоматический расчет длительности этапа при изменении дат
    function updateDatesLogic() {
        if (plannedStartDateInput && plannedEndDateInput) {
            // Просто логируем изменения дат для информации
            console.log('Даты этапа изменены:', {
                start: plannedStartDateInput.value,
                end: plannedEndDateInput.value
            });
        }
    }
    
    if (plannedStartDateInput && plannedEndDateInput) {
        plannedStartDateInput.addEventListener('change', updateDatesLogic);
        plannedEndDateInput.addEventListener('change', updateDatesLogic);
    }
    
    // Обработчик отправки формы через делегирование событий
    $(document).off('submit', '#addStageForm').on('submit', '#addStageForm', function(e) {
        console.log('Stage form submitted via delegation');
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        
        // Отключаем кнопку отправки
        const submitBtn = $(form).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Сохранение...');
        
        // Логируем данные формы для отладки
        console.log('Stage form data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        console.log('Отправка этапа по URL:', form.action);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Stage response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Stage response:', data);
            
            // Восстанавливаем кнопку
            submitBtn.prop('disabled', false).html(originalText);
            
            if (data.success) {
                // Закрываем модальное окно через modalManager
                if (window.modalManager && window.modalManager.activeModal) {
                    window.modalManager.activeModal.hide();
                } else {
                    // Fallback метод
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addStageModal'));
                    if (modal) modal.hide();
                }
                
                // Показываем сообщение об успехе
                if (typeof showMessage === 'function') {
                    showMessage(data.message || 'Этап успешно добавлен', 'success');
                }
                
                // Обновляем данные на странице
                if (typeof loadScheduleData === 'function') {
                    loadScheduleData();
                } else if (typeof refreshCurrentTabData === 'function') {
                    refreshCurrentTabData();
                }
                
                // Очищаем форму
                form.reset();
            } else {
                console.error('Stage creation failed:', data);
                if (typeof showMessage === 'function') {
                    const errorMessage = data.message || 'Ошибка при добавлении этапа';
                    showMessage(errorMessage, 'error');
                }
                
                // Если есть детальные ошибки валидации, выводим их
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                    const errors = Object.values(data.errors).flat();
                    if (errors.length > 0 && typeof showMessage === 'function') {
                        showMessage(errors.join(', '), 'error');
                    }
                }
            }
        })
        .catch(error => {
            console.error('Ошибка AJAX:', error);
            
            // Восстанавливаем кнопку
            submitBtn.prop('disabled', false).html(originalText);
            
            if (typeof showMessage === 'function') {
                showMessage('Произошла ошибка при добавлении этапа', 'error');
            }
        });
    });
}

// Экспортируем функцию для возможного повторного использования
window.initStageFormHandlers = initStageFormHandlers;
</script>
