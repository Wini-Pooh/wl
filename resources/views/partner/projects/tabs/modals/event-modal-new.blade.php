<!-- Модальное окно для добавления/редактирования события -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">
                    <i class="bi bi-calendar-event me-2"></i>Добавить событие
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm" action="{{ route('partner.projects.events.store', $project->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="eventTitle" class="form-label">Название события <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eventTitle" name="title" required placeholder="Например: Встреча с заказчиком">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Описание события</label>
                        <textarea class="form-control" id="eventDescription" name="description" rows="3" placeholder="Подробное описание события"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventDate" class="form-label">Дата события <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="eventDate" name="event_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eventTime" class="form-label">Время события</label>
                            <input type="time" class="form-control" id="eventTime" name="event_time">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="eventType" class="form-label">Тип события</label>
                            <select class="form-select" id="eventType" name="type">
                                <option value="meeting">Встреча</option>
                                <option value="delivery">Доставка</option>
                                <option value="inspection">Проверка</option>
                                <option value="milestone">Веха</option>
                                <option value="other">Другое</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="eventStatus" class="form-label">Статус</label>
                            <select class="form-select" id="eventStatus" name="status">
                                <option value="planned">Запланировано</option>
                                <option value="completed">Завершено</option>
                                <option value="cancelled">Отменено</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="eventLocation" class="form-label">Место проведения</label>
                        <input type="text" class="form-control" id="eventLocation" name="location" placeholder="Адрес или описание места">
                    </div>
                    
                    <div class="mb-3">
                        <label for="eventNotes" class="form-label">Заметки</label>
                        <textarea class="form-control" id="eventNotes" name="notes" rows="3" placeholder="Дополнительные заметки"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" form="addEventForm" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i>Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик отправки формы
    const form = document.getElementById('addEventForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Закрываем модальное окно
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
                    if (modal) modal.hide();
                    
                    // Показываем сообщение об успехе
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Событие успешно добавлено', 'success');
                    }
                    
                    // Обновляем данные на странице
                    if (typeof loadScheduleData === 'function') {
                        loadScheduleData();
                    }
                } else {
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Ошибка при добавлении события', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                if (typeof showMessage === 'function') {
                    showMessage('Произошла ошибка при добавлении события', 'error');
                }
            });
        });
    }
});
</script>
