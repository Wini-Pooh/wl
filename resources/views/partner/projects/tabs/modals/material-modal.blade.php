<!-- Модальное окно для добавления/редактирования материала -->
@php
    $isEdit = isset($params['material']);
    $material = $params['material'] ?? null;
    $modalTitle = $isEdit ? 'Редактировать материал' : 'Добавить материал';
    $formAction = $isEdit 
        ? route('partner.projects.materials.update', [$project->id, $material['id']]) 
        : route('partner.projects.materials.store', $project->id);
@endphp

<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">
                    <i class="bi bi-box me-2"></i>{{ $modalTitle }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="addMaterialForm" action="{{ $formAction }}" method="POST">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="materialName" class="form-label">Название материала <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="materialName" name="name" 
                                   value="{{ $material['name'] ?? '' }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="materialType" class="form-label">Тип материала</label>
                            <select class="form-select" id="materialType" name="type">
                                <option value="basic" {{ ($material['type'] ?? 'basic') == 'basic' ? 'selected' : '' }}>Основной</option>
                                <option value="additional" {{ ($material['type'] ?? '') == 'additional' ? 'selected' : '' }}>Дополнительный</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="materialUnit" class="form-label">Единица измерения</label>
                            <select class="form-select" id="materialUnit" name="unit">
                                <option value="шт" {{ ($material['unit'] ?? 'шт') == 'шт' ? 'selected' : '' }}>шт</option>
                                <option value="м" {{ ($material['unit'] ?? '') == 'м' ? 'selected' : '' }}>м</option>
                                <option value="м²" {{ ($material['unit'] ?? '') == 'м²' ? 'selected' : '' }}>м²</option>
                                <option value="м³" {{ ($material['unit'] ?? '') == 'м³' ? 'selected' : '' }}>м³</option>
                                <option value="кг" {{ ($material['unit'] ?? '') == 'кг' ? 'selected' : '' }}>кг</option>
                                <option value="л" {{ ($material['unit'] ?? '') == 'л' ? 'selected' : '' }}>л</option>
                                <option value="упак" {{ ($material['unit'] ?? '') == 'упак' ? 'selected' : '' }}>упак</option>
                                <option value="рулон" {{ ($material['unit'] ?? '') == 'рулон' ? 'selected' : '' }}>рулон</option>
                                <option value="мешок" {{ ($material['unit'] ?? '') == 'мешок' ? 'selected' : '' }}>мешок</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="materialQuantity" class="form-label">Количество <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="materialQuantity" name="quantity" 
                                   step="0.001" min="0" value="{{ $material['quantity'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="materialUnitPrice" class="form-label">Цена за единицу <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="materialUnitPrice" name="unit_price" 
                                       step="0.01" min="0" value="{{ $material['unit_price'] ?? $material['price'] ?? '' }}" required>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="materialAmount" class="form-label">Общая стоимость</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="materialAmount" name="amount" 
                                       step="0.01" value="{{ isset($material) ? ($material['quantity'] * ($material['unit_price'] ?? $material['price'] ?? 0)) : '' }}" readonly>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="materialPaidAmount" class="form-label">Оплаченная сумма</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="materialPaidAmount" name="paid_amount" 
                                       step="0.01" min="0" value="{{ $material['paid_amount'] ?? '0' }}">
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="materialPaymentDate" class="form-label">Дата оплаты</label>
                            <input type="date" class="form-control" id="materialPaymentDate" name="payment_date"
                                   value="{{ $material['payment_date'] ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="materialDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="materialDescription" name="description" 
                                  rows="3">{{ $material['description'] ?? '' }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" form="addMaterialForm" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Автоматический расчет общей стоимости
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('materialQuantity');
    const unitPriceInput = document.getElementById('materialUnitPrice');
    const amountInput = document.getElementById('materialAmount');
    
    function calculateAmount() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const amount = quantity * unitPrice;
        amountInput.value = amount.toFixed(2);
    }
    
    if (quantityInput && unitPriceInput && amountInput) {
        quantityInput.addEventListener('input', calculateAmount);
        unitPriceInput.addEventListener('input', calculateAmount);
    }
    
    // Обработчик отправки формы
    const form = document.getElementById('addMaterialForm');
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'));
                    if (modal) modal.hide();
                    
                    // Показываем сообщение об успехе
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Материал успешно добавлен', 'success');
                    }
                    
                    // Обновляем данные на странице
                    if (typeof loadFinanceData === 'function') {
                        loadFinanceData();
                    }
                } else {
                    if (typeof showMessage === 'function') {
                        showMessage(data.message || 'Ошибка при добавлении материала', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                if (typeof showMessage === 'function') {
                    showMessage('Произошла ошибка при добавлении материала', 'error');
                }
            });
        });
    }
});
</script>
