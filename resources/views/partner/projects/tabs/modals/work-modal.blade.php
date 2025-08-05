<!-- Модальное окно для работ -->
<div class="modal fade" id="workModal" tabindex="-1" aria-labelledby="workModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workModalLabel">
                    <i class="bi bi-tools me-2"></i>
                    <span id="workModalTitle">Добавить работу</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('partner.projects.works.store', $project) }}" id="workForm">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="work_id" id="workId" value="">

                    <div class="row">
                        <!-- Название работы -->
                        <div class="col-12 mb-3">
                            <label for="workName" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Название работы *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="workName" name="name" required 
                                   value="{{ old('name') }}"
                                   placeholder="Введите название работы">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Описание -->
                        <div class="col-12 mb-3">
                            <label for="workDescription" class="form-label">
                                <i class="bi bi-card-list me-1"></i>Описание
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="workDescription" name="description" rows="3"
                                      placeholder="Описание работы (необязательно)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Цена за единицу -->
                        <div class="col-md-4 mb-3">
                            <label for="workPrice" class="form-label">
                                <i class="bi bi-currency-exchange me-1"></i>Цена за единицу *
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control price-mask @error('price') is-invalid @enderror" 
                                       id="workPrice" name="price" 
                                       required 
                                       value="{{ old('price') }}"
                                       placeholder="0,00"
                                       data-mask="currency"
                                       data-validate="required|currency">
                                <span class="input-group-text">₽</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Количество -->
                        <div class="col-md-4 mb-3">
                            <label for="workQuantity" class="form-label">
                                <i class="bi bi-hash me-1"></i>Количество *
                            </label>
                            <input type="text" class="form-control quantity-mask @error('quantity') is-invalid @enderror" 
                                   id="workQuantity" name="quantity" 
                                   required 
                                   value="{{ old('quantity', '1') }}"
                                   placeholder="1"
                                   data-mask="decimal"
                                   data-validate="required|quantity">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Единица измерения -->
                        <div class="col-md-4 mb-3">
                            <label for="workUnit" class="form-label">
                                <i class="bi bi-rulers me-1"></i>Единица измерения
                            </label>
                            <select class="form-select @error('unit') is-invalid @enderror" id="workUnit" name="unit">
                                <option value="шт" {{ old('unit') == 'шт' ? 'selected' : '' }}>шт</option>
                                <option value="м" {{ old('unit') == 'м' ? 'selected' : '' }}>м</option>
                                <option value="м²" {{ old('unit') == 'м²' ? 'selected' : '' }}>м²</option>
                                <option value="м³" {{ old('unit') == 'м³' ? 'selected' : '' }}>м³</option>
                                <option value="кг" {{ old('unit') == 'кг' ? 'selected' : '' }}>кг</option>
                                <option value="т" {{ old('unit') == 'т' ? 'selected' : '' }}>т</option>
                                <option value="час" {{ old('unit') == 'час' ? 'selected' : '' }}>час</option>
                                <option value="день" {{ old('unit') == 'день' ? 'selected' : '' }}>день</option>
                                <option value="комплект" {{ old('unit') == 'комплект' ? 'selected' : '' }}>комплект</option>
                                <option value="услуга" {{ old('unit') == 'услуга' ? 'selected' : '' }}>услуга</option>
                            </select>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Тип работы -->
                        <div class="col-md-6 mb-3">
                            <label for="workType" class="form-label">
                                <i class="bi bi-tags me-1"></i>Тип работы
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" id="workType" name="type">
                                <option value="basic" {{ old('type', 'basic') == 'basic' ? 'selected' : '' }}>Основная</option>
                                <option value="additional" {{ old('type') == 'additional' ? 'selected' : '' }}>Дополнительная</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Скрытое поле для общей суммы -->
                        <div class="col-md-6 mb-3">
                            <label for="workAmount" class="form-label">
                                <i class="bi bi-calculator me-1"></i>Общая сумма
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" 
                                       id="workAmount" name="amount" 
                                       step="0.01" min="0" readonly>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>

                        <!-- Оплаченная сумма -->
                        <div class="col-md-6 mb-3">
                            <label for="workPaidAmount" class="form-label">
                                <i class="bi bi-credit-card me-1"></i>Оплачено
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control price-mask @error('paid_amount') is-invalid @enderror" 
                                       id="workPaidAmount" name="paid_amount" 
                                       value="{{ old('paid_amount', '0') }}"
                                       placeholder="0,00"
                                       data-mask="currency"
                                       data-validate="currency">
                                <span class="input-group-text">₽</span>
                            </div>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Дата оплаты -->
                        <div class="col-md-6 mb-3">
                            <label for="workPaymentDate" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Дата оплаты
                            </label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                   id="workPaymentDate" name="payment_date" 
                                   value="{{ old('payment_date') }}">
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Общая стоимость (автоматически рассчитывается) -->
                        <div class="col-12 mb-3">
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="bi bi-calculator me-2"></i>
                                <strong>Общая стоимость: </strong>
                                <span id="workTotalCost" class="ms-2 fs-5 text-primary">0.00 ₽</span>
                            </div>
                        </div>

                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Отмена
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <span id="workSubmitText">Сохранить работу</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Унифицированная инициализация модального окна работ
$(document).ready(function() {
    if (window.projectManager) {
        // Используем новую централизованную систему
        window.projectManager.initModal('workModal', 'work', function() {
            console.log('🔧 Специфическая инициализация модального окна работ...');
            
            // Инициализируем финансовые компоненты если еще не инициализированы
            window.projectManager.initFinance();
            
            // Автоматический расчет общей стоимости для работ
            setupWorkCalculations();
            
            // Переинициализируем Select2 для этого модального окна
            if (typeof window.reinitializeSelect2 === 'function') {
                window.reinitializeSelect2(document.getElementById('workModal'));
            }
        });
    } else {
        // Fallback на старую систему только в случае отсутствия новой
        console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
        legacyWorkModalInit();
    }
});

function setupWorkCalculations() {
    const workPriceInput = document.getElementById('workPrice');
    const workQuantityInput = document.getElementById('workQuantity');
    const workAmountInput = document.getElementById('workAmount');
    const workTotalCost = document.getElementById('workTotalCost');
    
    function updateWorkTotal() {
        const priceInput = workPriceInput?.value.replace(/[^\d,]/g, '').replace(',', '.') || '0';
        const quantityInput = workQuantityInput?.value.replace(/[^\d,]/g, '').replace(',', '.') || '0';
        
        const price = parseFloat(priceInput) || 0;
        const quantity = parseFloat(quantityInput) || 0;
        const total = price * quantity;
        
        if (workAmountInput) {
            workAmountInput.value = total.toFixed(2);
        }
        
        if (workTotalCost) {
            workTotalCost.textContent = total.toLocaleString('ru-RU', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' ₽';
        }
    }
    
    // Обработчики изменений
    if (workPriceInput) {
        workPriceInput.addEventListener('input', updateWorkTotal);
        workPriceInput.addEventListener('blur', updateWorkTotal);
    }
    
    if (workQuantityInput) {
        workQuantityInput.addEventListener('input', updateWorkTotal);
        workQuantityInput.addEventListener('blur', updateWorkTotal);
    }
    
    // Первоначальный расчет
    updateWorkTotal();
}

// Fallback функция для совместимости
function legacyWorkModalInit() {
    if (typeof initFinanceAjax === 'function' && !window.financeAjaxInitialized) {
        console.log('⚠️ Используем fallback инициализацию для модального окна работ');
        initFinanceAjax();
        window.financeAjaxInitialized = true;
    }
    
    setTimeout(function() {
        if (typeof window.reinitializeSelect2 === 'function') {
            window.reinitializeSelect2(document.getElementById('workModal'));
        }
    }, 100);
    
    setupWorkCalculations();
}

console.log('✅ Модальное окно работ загружено');

// Функция для открытия модального окна в режиме редактирования
window.editWork = function(workId, workData) {
    console.log('✏️ Открытие модального окна работ:', { workId, workData });
    
    const modal = document.getElementById('workModal');
    const form = document.getElementById('workForm');
    const title = document.getElementById('workModalTitle');
    const submitText = document.getElementById('workSubmitText');
    
    if (workData) {
        // Режим редактирования
        const baseAction = form.action.replace(/\/works.*$/, '/works');
        form.action = `${baseAction}/${workId}`;
        
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
        document.getElementById('workId').value = workId;
        document.getElementById('workName').value = workData.name || '';
        document.getElementById('workDescription').value = workData.description || '';
        document.getElementById('workPrice').value = workData.price || '';
        document.getElementById('workQuantity').value = workData.quantity || '';
        document.getElementById('workUnit').value = workData.unit || 'шт';
        document.getElementById('workType').value = workData.type || 'basic';
        document.getElementById('workPaidAmount').value = workData.paid_amount || '0';
        document.getElementById('workPaymentDate').value = workData.payment_date || '';
        
        title.textContent = 'Редактировать работу';
        submitText.textContent = 'Обновить работу';
        
        // Пересчитываем общую стоимость
        setupWorkCalculations();
    } else {
        // Режим добавления
        const baseAction = form.action.replace(/\/works.*$/, '/works');
        form.action = `${baseAction}/store`;
        
        // Удаляем поле для PUT метода
        const methodField = form.querySelector('input[name="_method"]');
        if (methodField) {
            methodField.remove();
        }
        
        // Очищаем форму
        form.reset();
        document.getElementById('workId').value = '';
        
        title.textContent = 'Добавить работу';
        submitText.textContent = 'Сохранить работу';
        
        setupWorkCalculations();
    }
    
    // Очищаем предыдущие ошибки валидации
    if (typeof clearFormErrors === 'function') {
        clearFormErrors(form);
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
};

// Функция для удаления работы
window.deleteWork = function(workId) {
    console.log('🗑️ Удаление работы:', workId);
    
    if (typeof deleteFinanceRecord === 'function') {
        deleteFinanceRecord('work', workId);
    } else {
        console.error('❌ deleteFinanceRecord не найдена');
        if (confirm('Вы уверены, что хотите удалить эту работу?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/partner/projects/{{ $project->id }}/works/${workId}`;
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
const workModal = document.getElementById('workModal');
if (workModal) {
    workModal.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('workForm');
        form.reset();
        
        // Очищаем ошибки валидации
        if (typeof clearFormErrors === 'function') {
            clearFormErrors(form);
        }
    });
}

// Критический хотфикс для исправления масок в модальном окне работ
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
    
    // Обработчик для формы работы
    $('#addWorkForm').on('submit', function() {
        const quantityField = this.querySelector('input[name="quantity"]');
        const priceField = this.querySelector('input[name="price"]');
        
        [quantityField, priceField].forEach(field => {
            if (field && field.value && /[{}\[\]\\|]/.test(field.value)) {
                const cleaned = cleanMaskValue(field.value);
                console.log('🔧 WORK HOTFIX: Исправлено поле ' + field.name + ': "' + field.value + '" → "' + cleaned + '"');
                field.value = cleaned;
            }
        });
    });
    
    console.log('🔧 Критический хотфикс для модального окна работ активирован');
})();
</script>
