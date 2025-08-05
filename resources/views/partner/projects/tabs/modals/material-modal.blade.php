<!-- Модальное окно для материалов -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">
                    <i class="bi bi-box-seam me-2"></i>
                    <span id="materialModalTitle">Добавить материал</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('partner.projects.materials.store', $project) }}" id="materialForm">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="material_id" id="materialId" value="">

                    <div class="row">
                        <!-- Название материала -->
                        <div class="col-12 mb-3">
                            <label for="materialName" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Название материала *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="materialName" name="name" required 
                                   value="{{ old('name') }}"
                                   placeholder="Введите название материала">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Описание -->
                        <div class="col-12 mb-3">
                            <label for="materialDescription" class="form-label">
                                <i class="bi bi-card-list me-1"></i>Описание
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="materialDescription" name="description" rows="3"
                                      placeholder="Описание материала (необязательно)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Цена за единицу -->
                        <div class="col-md-4 mb-3">
                            <label for="materialPrice" class="form-label">
                                <i class="bi bi-currency-exchange me-1"></i>Цена за единицу *
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control price-mask @error('price') is-invalid @enderror" 
                                       id="materialPrice" name="price" 
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

                        <!-- Количество -->
                        <div class="col-md-4 mb-3">
                            <label for="materialQuantity" class="form-label">
                                <i class="bi bi-hash me-1"></i>Количество *
                            </label>
                            <input type="text" class="form-control quantity-mask @error('quantity') is-invalid @enderror" 
                                   id="materialQuantity" name="quantity" 
                                   required 
                                   value="{{ old('quantity', '1') }}"
                                   placeholder="1"
                                   data-mask="decimal">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Единица измерения -->
                        <div class="col-md-4 mb-3">
                            <label for="materialUnit" class="form-label">
                                <i class="bi bi-rulers me-1"></i>Единица измерения
                            </label>
                            <select class="form-select @error('unit') is-invalid @enderror" id="materialUnit" name="unit">
                                <option value="шт" {{ old('unit') == 'шт' ? 'selected' : '' }}>шт</option>
                                <option value="м" {{ old('unit') == 'м' ? 'selected' : '' }}>м</option>
                                <option value="м²" {{ old('unit') == 'м²' ? 'selected' : '' }}>м²</option>
                                <option value="м³" {{ old('unit') == 'м³' ? 'selected' : '' }}>м³</option>
                                <option value="кг" {{ old('unit') == 'кг' ? 'selected' : '' }}>кг</option>
                                <option value="т" {{ old('unit') == 'т' ? 'selected' : '' }}>т</option>
                                <option value="л" {{ old('unit') == 'л' ? 'selected' : '' }}>л</option>
                                <option value="упак" {{ old('unit') == 'упак' ? 'selected' : '' }}>упак</option>
                                <option value="комплект" {{ old('unit') == 'комплект' ? 'selected' : '' }}>комплект</option>
                                <option value="рулон" {{ old('unit') == 'рулон' ? 'selected' : '' }}>рулон</option>
                                <option value="мешок" {{ old('unit') == 'мешок' ? 'selected' : '' }}>мешок</option>
                            </select>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Тип материала -->
                        <div class="col-md-6 mb-3">
                            <label for="materialType" class="form-label">
                                <i class="bi bi-tags me-1"></i>Тип материала
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" id="materialType" name="type">
                                <option value="basic" {{ old('type', 'basic') == 'basic' ? 'selected' : '' }}>Основной</option>
                                <option value="additional" {{ old('type') == 'additional' ? 'selected' : '' }}>Дополнительный</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Скрытое поле для общей суммы -->
                        <div class="col-md-6 mb-3">
                            <label for="materialAmount" class="form-label">
                                <i class="bi bi-calculator me-1"></i>Общая сумма
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" 
                                       id="materialAmount" name="amount" 
                                       step="0.01" min="0" readonly>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>

                        <!-- Оплаченная сумма -->
                        <div class="col-md-6 mb-3">
                            <label for="materialPaidAmount" class="form-label">
                                <i class="bi bi-credit-card me-1"></i>Оплачено
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control price-mask @error('paid_amount') is-invalid @enderror" 
                                       id="materialPaidAmount" name="paid_amount" 
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
                            <label for="materialPaymentDate" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Дата оплаты
                            </label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                   id="materialPaymentDate" name="payment_date" 
                                   value="{{ old('payment_date') }}">
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Общая стоимость (автоматически рассчитывается) -->
                        <div class="col-12 mb-3">
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="bi bi-calculator me-2"></i>
                                <strong>Общая стоимость: </strong>
                                <span id="materialTotalCost" class="ms-2 fs-5 text-success">0.00 ₽</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Отмена
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>
                            <span id="materialSubmitText">Сохранить материал</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Унифицированная инициализация модального окна материалов
$(document).ready(function() {
    if (window.projectManager) {
        // Используем новую централизованную систему
        window.projectManager.initModal('materialModal', 'material', function() {
            console.log('📦 Специфическая инициализация модального окна материалов...');
            
            // Инициализируем финансовые компоненты если еще не инициализированы
            window.projectManager.initFinance();
            
            // Автоматический расчет общей стоимости для материалов
            setupMaterialCalculations();
            
            // Переинициализируем Select2 для этого модального окна
            if (typeof window.reinitializeSelect2 === 'function') {
                window.reinitializeSelect2(document.getElementById('materialModal'));
            }
        });
    } else {
        // Fallback на старую систему только в случае отсутствия новой
        console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
        legacyMaterialModalInit();
    }
});

function setupMaterialCalculations() {
    const materialPriceInput = document.getElementById('materialPrice');
    const materialQuantityInput = document.getElementById('materialQuantity');
    const materialAmountInput = document.getElementById('materialAmount');
    const materialTotalCost = document.getElementById('materialTotalCost');
    
    function updateMaterialTotal() {
        const priceInput = materialPriceInput?.value.replace(/[^\d,]/g, '').replace(',', '.') || '0';
        const quantityInput = materialQuantityInput?.value.replace(/[^\d,]/g, '').replace(',', '.') || '0';
        
        const price = parseFloat(priceInput) || 0;
        const quantity = parseFloat(quantityInput) || 0;
        const total = price * quantity;
        
        if (materialAmountInput) {
            materialAmountInput.value = total.toFixed(2);
        }
        
        if (materialTotalCost) {
            materialTotalCost.textContent = total.toLocaleString('ru-RU', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' ₽';
        }
    }
    
    // Обработчики изменений
    if (materialPriceInput) {
        materialPriceInput.addEventListener('input', updateMaterialTotal);
        materialPriceInput.addEventListener('blur', updateMaterialTotal);
    }
    
    if (materialQuantityInput) {
        materialQuantityInput.addEventListener('input', updateMaterialTotal);
        materialQuantityInput.addEventListener('blur', updateMaterialTotal);
    }
    
    // Первоначальный расчет
    updateMaterialTotal();
}

// Fallback функция для совместимости
function legacyMaterialModalInit() {
    if (typeof initFinanceAjax === 'function' && !window.financeAjaxInitialized) {
        console.log('⚠️ Используем fallback инициализацию для модального окна материалов');
        initFinanceAjax();
        window.financeAjaxInitialized = true;
    }
    
    setTimeout(function() {
        if (typeof window.reinitializeSelect2 === 'function') {
            window.reinitializeSelect2(document.getElementById('materialModal'));
        }
    }, 100);
    
    setupMaterialCalculations();
}

// Функция для открытия модального окна в режиме редактирования
window.editMaterial = function(materialId, materialData) {
    console.log('✏️ Открытие модального окна материалов:', { materialId, materialData });
    
    const modal = document.getElementById('materialModal');
    const form = document.getElementById('materialForm');
    const title = document.getElementById('materialModalTitle');
    const submitText = document.getElementById('materialSubmitText');
    
    if (materialData) {
        // Режим редактирования
        const baseAction = form.action.replace(/\/materials.*$/, '/materials');
        form.action = `${baseAction}/${materialId}`;
        
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
        document.getElementById('materialId').value = materialId;
        document.getElementById('materialName').value = materialData.name || '';
        document.getElementById('materialDescription').value = materialData.description || '';
        document.getElementById('materialPrice').value = materialData.price || '';
        document.getElementById('materialQuantity').value = materialData.quantity || '';
        document.getElementById('materialUnit').value = materialData.unit || 'шт';
        document.getElementById('materialType').value = materialData.type || 'basic';
        document.getElementById('materialPaidAmount').value = materialData.paid_amount || '0';
        document.getElementById('materialPaymentDate').value = materialData.payment_date || '';
        
        title.textContent = 'Редактировать материал';
        submitText.textContent = 'Обновить материал';
        
        // Пересчитываем общую стоимость
        setupMaterialCalculations();
    } else {
        // Режим добавления
        const baseAction = form.action.replace(/\/materials.*$/, '/materials');
        form.action = `${baseAction}/store`;
        
        // Удаляем поле для PUT метода
        const methodField = form.querySelector('input[name="_method"]');
        if (methodField) {
            methodField.remove();
        }
        
        // Очищаем форму
        form.reset();
        document.getElementById('materialId').value = '';
        
        title.textContent = 'Добавить материал';
        submitText.textContent = 'Сохранить материал';
        
        setupMaterialCalculations();
    }
    
    // Очищаем предыдущие ошибки валидации
    if (typeof clearFormErrors === 'function') {
        clearFormErrors(form);
    }
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
};

// Функция для удаления материала
window.deleteMaterial = function(materialId) {
    console.log('🗑️ Удаление материала:', materialId);
    
    if (typeof deleteFinanceRecord === 'function') {
        deleteFinanceRecord('material', materialId);
    } else {
        console.error('❌ deleteFinanceRecord не найдена');
        if (confirm('Вы уверены, что хотите удалить этот материал?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/partner/projects/{{ $project->id }}/materials/${materialId}`;
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
const materialModal = document.getElementById('materialModal');
if (materialModal) {
    materialModal.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('materialForm');
        form.reset();
        
        // Очищаем ошибки валидации
        if (typeof clearFormErrors === 'function') {
            clearFormErrors(form);
        }
    });
}

console.log('✅ Модальное окно материалов загружено');

// Критический хотфикс для исправления масок прямо в модальном окне
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
    
    // Обработчик для формы материала
    $('#addMaterialForm').on('submit', function() {
        const quantityField = this.querySelector('input[name="quantity"]');
        const priceField = this.querySelector('input[name="price"]') || this.querySelector('input[name="unit_price"]');
        
        [quantityField, priceField].forEach(field => {
            if (field && field.value && /[{}\[\]\\|]/.test(field.value)) {
                const cleaned = cleanMaskValue(field.value);
                console.log('🔧 MATERIAL HOTFIX: Исправлено поле ' + field.name + ': "' + field.value + '" → "' + cleaned + '"');
                field.value = cleaned;
            }
        });
    });
    
    console.log('🔧 Критический хотфикс для модального окна материала активирован');
})();
</script>
