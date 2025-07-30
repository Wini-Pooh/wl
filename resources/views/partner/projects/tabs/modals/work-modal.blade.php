<!-- Модальное окно для добавления/редактирования работы -->
@php
    $isEdit = isset($params['work']);
    $work = $params['work'] ?? null;
    $modalTitle = $isEdit ? 'Редактировать работу' : 'Добавить работу';
    $formAction = $isEdit 
        ? route('partner.projects.works.update', [$project->id, $work['id']]) 
        : route('partner.projects.works.store', $project->id);
@endphp

<div class="modal fade" id="addWorkModal" tabindex="-1" aria-labelledby="addWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWorkModalLabel">
                    <i class="bi bi-hammer me-2"></i>{{ $modalTitle }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="addWorkForm" action="{{ $formAction }}" method="POST">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="workName" class="form-label">Название работы <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="workName" name="name" 
                                   value="{{ $work['name'] ?? '' }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="workType" class="form-label">Тип работы</label>
                            <select class="form-select" id="workType" name="type">
                                <option value="basic" {{ ($work['type'] ?? 'basic') == 'basic' ? 'selected' : '' }}>Основная</option>
                                <option value="additional" {{ ($work['type'] ?? '') == 'additional' ? 'selected' : '' }}>Дополнительная</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="workUnit" class="form-label">Единица измерения</label>
                            <select class="form-select" id="workUnit" name="unit">
                                <option value="шт" {{ ($work['unit'] ?? 'шт') == 'шт' ? 'selected' : '' }}>шт</option>
                                <option value="м" {{ ($work['unit'] ?? '') == 'м' ? 'selected' : '' }}>м</option>
                                <option value="м²" {{ ($work['unit'] ?? '') == 'м²' ? 'selected' : '' }}>м²</option>
                                <option value="м³" {{ ($work['unit'] ?? '') == 'м³' ? 'selected' : '' }}>м³</option>
                                <option value="кг" {{ ($work['unit'] ?? '') == 'кг' ? 'selected' : '' }}>кг</option>
                                <option value="час" {{ ($work['unit'] ?? '') == 'час' ? 'selected' : '' }}>час</option>
                                <option value="день" {{ ($work['unit'] ?? '') == 'день' ? 'selected' : '' }}>день</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="workQuantity" class="form-label">Количество <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="workQuantity" name="quantity" 
                                   step="0.001" min="0" value="{{ $work['quantity'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="workPrice" class="form-label">Цена за единицу <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="workPrice" name="price" 
                                       step="0.01" min="0" value="{{ $work['price'] ?? '' }}" required>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="workAmount" class="form-label">Общая стоимость</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="workAmount" name="amount" 
                                       step="0.01" value="{{ isset($work) ? ($work['quantity'] * $work['price']) : '' }}" readonly>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="workPaidAmount" class="form-label">Оплаченная сумма</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="workPaidAmount" name="paid_amount" 
                                       step="0.01" min="0" value="{{ $work['paid_amount'] ?? '0' }}">
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="workPaymentDate" class="form-label">Дата оплаты</label>
                            <input type="date" class="form-control" id="workPaymentDate" name="payment_date"
                                   value="{{ $work['payment_date'] ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="workDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="workDescription" name="description" 
                                  rows="3">{{ $work['description'] ?? '' }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" form="addWorkForm" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Автоматический расчет общей стоимости
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('workQuantity');
    const priceInput = document.getElementById('workPrice');
    const amountInput = document.getElementById('workAmount');
    
    function calculateAmount() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const amount = quantity * price;
        amountInput.value = amount.toFixed(2);
    }
    
    if (quantityInput && priceInput && amountInput) {
        quantityInput.addEventListener('input', calculateAmount);
        priceInput.addEventListener('input', calculateAmount);
    }
    
    // Обработчик отправки формы
    const form = document.getElementById('addWorkForm');
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addWorkModal'));
                    if (modal) modal.hide();
                    
                    // Показываем сообщение об успехе
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Работа успешно добавлена', 'success');
                    }
                    
                    // Обновляем данные на странице
                    if (typeof loadFinanceData === 'function') {
                        loadFinanceData();
                    }
                } else {
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Ошибка при добавлении работы', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                if (typeof showMessage === 'function') {
                    showMessage('Произошла ошибка при добавлении работы', 'error');
                }
            });
        });
    }
});
</script>
