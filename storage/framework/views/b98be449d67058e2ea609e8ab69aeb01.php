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
                <form method="POST" action="<?php echo e(route('partner.projects.materials.store', $project)); ?>" id="materialForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="project_id" value="<?php echo e($project->id); ?>">
                    <input type="hidden" name="material_id" id="materialId" value="">

                    <div class="row">
                        <!-- Название материала -->
                        <div class="col-12 mb-3">
                            <label for="materialName" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Название материала *
                            </label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="materialName" name="name" required 
                                   value="<?php echo e(old('name')); ?>"
                                   placeholder="Введите название материала">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Описание -->
                        <div class="col-12 mb-3">
                            <label for="materialDescription" class="form-label">
                                <i class="bi bi-card-list me-1"></i>Описание
                            </label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="materialDescription" name="description" rows="3"
                                      placeholder="Описание материала (необязательно)"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Цена за единицу -->
                        <div class="col-md-4 mb-3">
                            <label for="materialPrice" class="form-label">
                                <i class="bi bi-currency-exchange me-1"></i>Цена за единицу *
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control price-mask <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="materialPrice" name="price" 
                                       required 
                                       value="<?php echo e(old('price')); ?>"
                                       placeholder="0,00"
                                       data-mask="currency">
                                <span class="input-group-text">₽</span>
                            </div>
                            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Количество -->
                        <div class="col-md-4 mb-3">
                            <label for="materialQuantity" class="form-label">
                                <i class="bi bi-hash me-1"></i>Количество *
                            </label>
                            <input type="text" class="form-control quantity-mask <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="materialQuantity" name="quantity" 
                                   required 
                                   value="<?php echo e(old('quantity', '1')); ?>"
                                   placeholder="1"
                                   data-mask="decimal">
                            <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Единица измерения -->
                        <div class="col-md-4 mb-3">
                            <label for="materialUnit" class="form-label">
                                <i class="bi bi-rulers me-1"></i>Единица измерения
                            </label>
                            <select class="form-select <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="materialUnit" name="unit">
                                <option value="шт" <?php echo e(old('unit') == 'шт' ? 'selected' : ''); ?>>шт</option>
                                <option value="м" <?php echo e(old('unit') == 'м' ? 'selected' : ''); ?>>м</option>
                                <option value="м²" <?php echo e(old('unit') == 'м²' ? 'selected' : ''); ?>>м²</option>
                                <option value="м³" <?php echo e(old('unit') == 'м³' ? 'selected' : ''); ?>>м³</option>
                                <option value="кг" <?php echo e(old('unit') == 'кг' ? 'selected' : ''); ?>>кг</option>
                                <option value="т" <?php echo e(old('unit') == 'т' ? 'selected' : ''); ?>>т</option>
                                <option value="л" <?php echo e(old('unit') == 'л' ? 'selected' : ''); ?>>л</option>
                                <option value="упак" <?php echo e(old('unit') == 'упак' ? 'selected' : ''); ?>>упак</option>
                                <option value="комплект" <?php echo e(old('unit') == 'комплект' ? 'selected' : ''); ?>>комплект</option>
                                <option value="рулон" <?php echo e(old('unit') == 'рулон' ? 'selected' : ''); ?>>рулон</option>
                                <option value="мешок" <?php echo e(old('unit') == 'мешок' ? 'selected' : ''); ?>>мешок</option>
                            </select>
                            <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Тип материала -->
                        <div class="col-md-6 mb-3">
                            <label for="materialType" class="form-label">
                                <i class="bi bi-tags me-1"></i>Тип материала
                            </label>
                            <select class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="materialType" name="type">
                                <option value="basic" <?php echo e(old('type', 'basic') == 'basic' ? 'selected' : ''); ?>>Основной</option>
                                <option value="additional" <?php echo e(old('type') == 'additional' ? 'selected' : ''); ?>>Дополнительный</option>
                            </select>
                            <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                <input type="text" class="form-control price-mask <?php $__errorArgs = ['paid_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="materialPaidAmount" name="paid_amount" 
                                       value="<?php echo e(old('paid_amount', '0')); ?>"
                                       placeholder="0,00"
                                       data-mask="currency">
                                <span class="input-group-text">₽</span>
                            </div>
                            <?php $__errorArgs = ['paid_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Дата оплаты -->
                        <div class="col-md-6 mb-3">
                            <label for="materialPaymentDate" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Дата оплаты
                            </label>
                            <input type="date" class="form-control <?php $__errorArgs = ['payment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="materialPaymentDate" name="payment_date" 
                                   value="<?php echo e(old('payment_date')); ?>">
                            <?php $__errorArgs = ['payment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
            form.action = `/partner/projects/<?php echo e($project->id); ?>/materials/${materialId}`;
            form.style.display = 'none';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '<?php echo e(csrf_token()); ?>';
            
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
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/modals/material-modal.blade.php ENDPATH**/ ?>