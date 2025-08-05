<!-- Модальное окно для транспорта -->
<div class="modal fade" id="transportModal" tabindex="-1" aria-labelledby="transportModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transportModalLabel">
                    <i class="bi bi-truck me-2"></i>
                    <span id="transportModalTitle">Добавить транспорт</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('partner.projects.transports.store', $project) }}" id="transportForm">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="transport_id" id="transportId" value="">
                    
                    <div class="row">
                        <!-- Название/Описание -->
                        <div class="col-md-8 mb-3">
                            <label for="transportName" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Название/Описание *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="transportName" name="name" required 
                                   value="{{ old('name') }}"
                                   placeholder="Например: Аренда КамАЗа на 3 дня">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Единица измерения -->
                        <div class="col-md-4 mb-3">
                            <label for="transportUnit" class="form-label">
                                <i class="bi bi-rulers me-1"></i>Единица измерения
                            </label>
                            <select class="form-select @error('unit') is-invalid @enderror" id="transportUnit" name="unit">
                                <option value="час" {{ old('unit') == 'час' ? 'selected' : '' }}>час</option>
                                <option value="день" {{ old('unit') == 'день' ? 'selected' : '' }}>день</option>
                                <option value="км" {{ old('unit') == 'км' ? 'selected' : '' }}>км</option>
                                <option value="поездка" {{ old('unit') == 'поездка' ? 'selected' : '' }}>поездка</option>
                                <option value="шт" {{ old('unit') == 'шт' ? 'selected' : '' }}>шт</option>
                            </select>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Количество -->
                        <div class="col-md-4 mb-3">
                            <label for="transportQuantity" class="form-label">
                                <i class="bi bi-hash me-1"></i>Количество *
                            </label>
                            <input type="text" class="form-control quantity-mask @error('quantity') is-invalid @enderror" 
                                   id="transportQuantity" name="quantity" 
                                   required 
                                   value="{{ old('quantity', '1') }}"
                                   placeholder="1"
                                   data-mask="decimal">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Цена за единицу -->
                        <div class="col-md-4 mb-3">
                            <label for="transportPrice" class="form-label">
                                <i class="bi bi-currency-exchange me-1"></i>Цена за единицу *
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control price-mask @error('price') is-invalid @enderror" 
                                       id="transportPrice" name="price" 
                                       required 
                                       value="{{ old('price') }}"
                                       placeholder="0,00"
                                       data-mask="currency">
                                <span class="input-group-text">₽</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Итого (автоматически рассчитывается) -->
                        <div class="col-md-4 mb-3">
                            <label for="transportAmount" class="form-label">
                                <i class="bi bi-calculator me-1"></i>Итого
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" 
                                       id="transportAmount" name="amount" 
                                       step="0.01" min="0" readonly>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительные поля для оплаты -->
                    <div class="row">
                        <!-- Оплаченная сумма -->
                        <div class="col-md-6 mb-3">
                            <label for="transportPaidAmount" class="form-label">
                                <i class="bi bi-credit-card me-1"></i>Оплачено
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control price-mask @error('paid_amount') is-invalid @enderror" 
                                       id="transportPaidAmount" name="paid_amount" 
                                       value="{{ old('paid_amount', '0') }}"
                                       placeholder="0,00"
                                       data-mask="currency">
                                <span class="input-group-text">₽</span>
                            </div>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Дата оплаты -->
                        <div class="col-md-6 mb-3">
                            <label for="transportPaymentDate" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Дата оплаты
                            </label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                   id="transportPaymentDate" name="payment_date" 
                                   value="{{ old('payment_date') }}">
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Общая стоимость (автоматически рассчитывается) -->
                    <div class="col-12 mb-3">
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="bi bi-calculator me-2"></i>
                            <strong>Общая стоимость: </strong>
                            <span id="transportTotalCost" class="ms-2 fs-5 text-warning">0.00 ₽</span>
                        </div>
                    </div>
                    
                    <!-- Описание -->
                    <div class="mb-3">
                        <label for="transportDescription" class="form-label">
                            <i class="bi bi-card-list me-1"></i>Подробное описание
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="transportDescription" name="description" rows="3" 
                                  placeholder="Подробное описание транспорта, условия аренды...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Отмена
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i>
                            <span id="transportSubmitText">Сохранить транспорт</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Унифицированная инициализация модального окна транспорта
$(document).ready(function() {
    if (window.projectManager) {
        // Используем новую централизованную систему
        window.projectManager.initModal('transportModal', 'transport', function() {
            console.log('🚛 Специфическая инициализация модального окна транспорта...');
            
            // Инициализируем финансовые компоненты если еще не инициализированы
            window.projectManager.initFinance();
            
            // Автоматический расчет общей стоимости для транспорта
            setupTransportCalculations();
            
            // Переинициализируем Select2 для этого модального окна
            if (typeof window.reinitializeSelect2 === 'function') {
                window.reinitializeSelect2(document.getElementById('transportModal'));
            }
        });
    } else {
        // Fallback на старую систему только в случае отсутствия новой
        console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
        legacyTransportModalInit();
    }
});

function setupTransportCalculations() {
    const transportPriceInput = document.getElementById('transportPrice');
    const transportQuantityInput = document.getElementById('transportQuantity');
    const transportAmountInput = document.getElementById('transportAmount');
    const transportTotalCost = document.getElementById('transportTotalCost');
    
    function updateTransportTotal() {
        const priceInput = transportPriceInput?.value.replace(/[^\d,]/g, '').replace(',', '.') || '0';
        const quantityInput = transportQuantityInput?.value.replace(/[^\d,]/g, '').replace(',', '.') || '0';
        
        const price = parseFloat(priceInput) || 0;
        const quantity = parseFloat(quantityInput) || 0;
        const total = price * quantity;
        
        if (transportAmountInput) {
            transportAmountInput.value = total.toFixed(2);
        }
        
        if (transportTotalCost) {
            transportTotalCost.textContent = total.toLocaleString('ru-RU', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' ₽';
        }
    }
    
    // Обработчики изменений
    if (transportPriceInput) {
        transportPriceInput.addEventListener('input', updateTransportTotal);
        transportPriceInput.addEventListener('blur', updateTransportTotal);
    }
    
    if (transportQuantityInput) {
        transportQuantityInput.addEventListener('input', updateTransportTotal);
        transportQuantityInput.addEventListener('blur', updateTransportTotal);
    }
    
    // Первоначальный расчет
    updateTransportTotal();
}

// Fallback функция для совместимости
function legacyTransportModalInit() {
    if (typeof initFinanceAjax === 'function' && !window.financeAjaxInitialized) {
        console.log('⚠️ Используем fallback инициализацию для модального окна транспорта');
        initFinanceAjax();
        window.financeAjaxInitialized = true;
    }
    
    setTimeout(function() {
        if (typeof window.reinitializeSelect2 === 'function') {
            window.reinitializeSelect2(document.getElementById('transportModal'));
        }
    }, 100);
    
    setupTransportCalculations();
}

console.log('✅ Модальное окно транспорта загружено');

// Функция для открытия модального окна в режиме редактирования
window.editTransport = function(transportId, transportData) {
    console.log('✏️ Открытие модального окна транспорта:', { transportId, transportData });
    
    const modal = document.getElementById('transportModal');
    const form = document.getElementById('transportForm');
    const title = document.getElementById('transportModalTitle');
    const submitText = document.getElementById('transportSubmitText');
    
    if (transportData) {
        // Режим редактирования
        const baseAction = form.action.replace(/\/transports.*$/, '/transports');
        form.action = `${baseAction}/${transportId}`;
        
        // Добавляем скрытое поле для PUT метода
        let methodField = form.querySelector('input[name="_method"]');
        if (!methodField) {
            methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PUT';
            form.appendChild(methodField);
        } else {
            methodField.value = 'PUT';
        }
        
        // Заполняем поля формы
        document.getElementById('transportId').value = transportId;
        document.getElementById('transportName').value = transportData.name || '';
        document.getElementById('transportDescription').value = transportData.description || '';
        document.getElementById('transportPrice').value = transportData.price || '';
        document.getElementById('transportQuantity').value = transportData.quantity || '';
        document.getElementById('transportUnit').value = transportData.unit || 'час';
        document.getElementById('transportPaidAmount').value = transportData.paid_amount || '0';
        document.getElementById('transportPaymentDate').value = transportData.payment_date || '';
        
        title.textContent = 'Редактировать транспорт';
        submitText.textContent = 'Обновить транспорт';
        
        // Пересчитываем общую стоимость
        setupTransportCalculations();
    } else {
        // Режим добавления
        const baseAction = form.action.replace(/\/transports.*$/, '/transports');
        form.action = `${baseAction}/store`;
        
        // Удаляем поле для PUT метода
        const methodField = form.querySelector('input[name="_method"]');
        if (methodField) {
            methodField.remove();
        }
        
        // Очищаем форму
        form.reset();
        document.getElementById('transportId').value = '';
        
        title.textContent = 'Добавить транспорт';
        submitText.textContent = 'Сохранить транспорт';
        
        setupTransportCalculations();
    }
    
    // Очищаем предыдущие ошибки валидации
    if (typeof clearFormErrors === 'function') {
        clearFormErrors(form);
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
};

// Функция для удаления транспорта
window.deleteTransport = function(transportId) {
    console.log('🗑️ Удаление транспорта:', transportId);
    
    if (typeof deleteFinanceRecord === 'function') {
        deleteFinanceRecord('transport', transportId);
    } else {
        console.error('❌ deleteFinanceRecord не найдена');
        if (confirm('Вы уверены, что хотите удалить этот транспорт?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/partner/projects/{{ $project->id }}/transports/${transportId}`;
            form.style.display = 'none';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
};

// Сброс формы при закрытии модального окна
const transportModal = document.getElementById('transportModal');
if (transportModal) {
    transportModal.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('transportForm');
        form.reset();
        
        // Очищаем ошибки валидации
        if (typeof clearFormErrors === 'function') {
            clearFormErrors(form);
        }
    });
}

// Критический хотфикс для исправления масок в модальном окне транспорта
(function() {
    function cleanMaskValue(value) {
        if (typeof value !== 'string') return value;
        
        // Удаляем символы масок
        let cleaned = value.replace(/[{}\[\]\\|]/g, '');
        
        // Если это числовое значение с запятой, конвертируем
        if (/^\d+,\d+$/.test(cleaned)) {
            return parseFloat(cleaned.replace(',', '.')).toString();
        }
        
        return cleaned;
    }
    
    // Обработчик для формы транспорта
    $('#addTransportForm').on('submit', function() {
        const quantityField = this.querySelector('input[name="quantity"]');
        const priceField = this.querySelector('input[name="price"]');
        
        [quantityField, priceField].forEach(field => {
            if (field && field.value && /[{}\[\]\\|]/.test(field.value)) {
                const cleaned = cleanMaskValue(field.value);
                console.log('🔧 TRANSPORT HOTFIX: Исправлено поле ' + field.name + ': "' + field.value + '" → "' + cleaned + '"');
                field.value = cleaned;
            }
        });
    });
    
    console.log('🔧 Критический хотфикс для модального окна транспорта активирован');
})();
</script>
