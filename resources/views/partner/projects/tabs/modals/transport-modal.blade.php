<!-- Модальное окно для добавления/редактирования транспорта -->
@php
    $isEdit = isset($params['transport']);
    $transport = $params['transport'] ?? null;
    $modalTitle = $isEdit ? 'Редактировать транспорт' : 'Добавить транспорт';
    $formAction = $isEdit 
        ? route('partner.projects.transports.update', [$project->id, $transport['id']]) 
        : route('partner.projects.transports.store', $project->id);
@endphp

<div class="modal fade" id="addTransportModal" tabindex="-1" aria-labelledby="addTransportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTransportModalLabel">
                    <i class="bi bi-truck me-2"></i>{{ $modalTitle }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="addTransportForm" action="{{ $formAction }}" method="POST">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="transportName" class="form-label">Название транспорта <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="transportName" name="name" 
                                   value="{{ $transport['name'] ?? '' }}" required placeholder="Например: Доставка материалов">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="transportUnit" class="form-label">Единица измерения</label>
                            <select class="form-select" id="transportUnit" name="unit">
                                <option value="шт" {{ ($transport['unit'] ?? 'шт') == 'шт' ? 'selected' : '' }}>шт</option>
                                <option value="км" {{ ($transport['unit'] ?? '') == 'км' ? 'selected' : '' }}>км</option>
                                <option value="рейс" {{ ($transport['unit'] ?? '') == 'рейс' ? 'selected' : '' }}>рейс</option>
                                <option value="час" {{ ($transport['unit'] ?? '') == 'час' ? 'selected' : '' }}>час</option>
                                <option value="день" {{ ($transport['unit'] ?? '') == 'день' ? 'selected' : '' }}>день</option>
                                <option value="т" {{ ($transport['unit'] ?? '') == 'т' ? 'selected' : '' }}>т</option>
                                <option value="м³" {{ ($transport['unit'] ?? '') == 'м³' ? 'selected' : '' }}>м³</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transportQuantity" class="form-label">Количество <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="transportQuantity" name="quantity" 
                                   step="0.01" min="0" value="{{ $transport['quantity'] ?? '1' }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="transportPrice" class="form-label">Цена за единицу <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="transportPrice" name="price" 
                                       step="0.01" min="0" value="{{ $transport['price'] ?? '' }}" required>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transportAmount" class="form-label">Общая стоимость</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="transportAmount" name="amount" 
                                       step="0.01" value="{{ isset($transport) ? ($transport['quantity'] * $transport['price']) : '' }}" readonly>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="transportPaidAmount" class="form-label">Оплаченная сумма</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="transportPaidAmount" name="paid_amount" 
                                       step="0.01" min="0" value="{{ $transport['paid_amount'] ?? '0' }}">
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transportPaymentDate" class="form-label">Дата оплаты</label>
                            <input type="date" class="form-control" id="transportPaymentDate" name="payment_date"
                                   value="{{ $transport['payment_date'] ?? '' }}">
                            <input type="date" class="form-control" id="transportPaymentDate" name="payment_date">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="transportDescription" class="form-label">Описание</label>
                            <textarea class="form-control" id="transportDescription" name="description" rows="3" placeholder="Дополнительные детали транспорта"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" form="addTransportForm" class="btn btn-warning">
                    <i class="bi bi-check-lg me-1"></i>Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Автоматический расчет общей стоимости
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('transportQuantity');
    const priceInput = document.getElementById('transportPrice');
    const amountInput = document.getElementById('transportAmount');
    
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
    const form = document.getElementById('addTransportForm');
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addTransportModal'));
                    if (modal) modal.hide();
                    
                    // Показываем сообщение об успехе
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Транспорт успешно добавлен', 'success');
                    }
                    
                    // Обновляем данные на странице
                    if (typeof loadFinanceData === 'function') {
                        loadFinanceData();
                    }
                } else {
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Ошибка при добавлении транспорта', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                if (typeof showMessage === 'function') {
                    showMessage('Произошла ошибка при добавлении транспорта', 'error');
                }
            });
        });
    }
});
</script>
