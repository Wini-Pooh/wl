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
                <form method="POST" action="<?php echo e(route('partner.projects.works.store', $project)); ?>" id="workForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="project_id" value="<?php echo e($project->id); ?>">
                    <input type="hidden" name="work_id" id="workId" value="">

                    <div class="row">
                        <!-- Название работы -->
                        <div class="col-12 mb-3">
                            <label for="workName" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Название работы *
                            </label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="workName" name="name" required 
                                   value="<?php echo e(old('name')); ?>"
                                   placeholder="Введите название работы">
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
                            <label for="workDescription" class="form-label">
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
                                      id="workDescription" name="description" rows="3"
                                      placeholder="Описание работы (необязательно)"><?php echo e(old('description')); ?></textarea>
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
                            <label for="workPrice" class="form-label">
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
                                       id="workPrice" name="price" 
                                       required 
                                       value="<?php echo e(old('price')); ?>"
                                       placeholder="0,00"
                                       data-mask="currency"
                                       data-validate="required|currency">
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
                            <label for="workQuantity" class="form-label">
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
                                   id="workQuantity" name="quantity" 
                                   required 
                                   value="<?php echo e(old('quantity', '1')); ?>"
                                   placeholder="1"
                                   data-mask="decimal"
                                   data-validate="required|quantity">
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
                            <label for="workUnit" class="form-label">
                                <i class="bi bi-rulers me-1"></i>Единица измерения
                            </label>
                            <select class="form-select <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="workUnit" name="unit">
                                <option value="шт" <?php echo e(old('unit') == 'шт' ? 'selected' : ''); ?>>шт</option>
                                <option value="м" <?php echo e(old('unit') == 'м' ? 'selected' : ''); ?>>м</option>
                                <option value="м²" <?php echo e(old('unit') == 'м²' ? 'selected' : ''); ?>>м²</option>
                                <option value="м³" <?php echo e(old('unit') == 'м³' ? 'selected' : ''); ?>>м³</option>
                                <option value="кг" <?php echo e(old('unit') == 'кг' ? 'selected' : ''); ?>>кг</option>
                                <option value="т" <?php echo e(old('unit') == 'т' ? 'selected' : ''); ?>>т</option>
                                <option value="час" <?php echo e(old('unit') == 'час' ? 'selected' : ''); ?>>час</option>
                                <option value="день" <?php echo e(old('unit') == 'день' ? 'selected' : ''); ?>>день</option>
                                <option value="комплект" <?php echo e(old('unit') == 'комплект' ? 'selected' : ''); ?>>комплект</option>
                                <option value="услуга" <?php echo e(old('unit') == 'услуга' ? 'selected' : ''); ?>>услуга</option>
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

                        <!-- Тип работы -->
                        <div class="col-md-6 mb-3">
                            <label for="workType" class="form-label">
                                <i class="bi bi-tags me-1"></i>Тип работы
                            </label>
                            <select class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="workType" name="type">
                                <option value="basic" <?php echo e(old('type', 'basic') == 'basic' ? 'selected' : ''); ?>>Основная</option>
                                <option value="additional" <?php echo e(old('type') == 'additional' ? 'selected' : ''); ?>>Дополнительная</option>
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
                                <input type="text" class="form-control price-mask <?php $__errorArgs = ['paid_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="workPaidAmount" name="paid_amount" 
                                       value="<?php echo e(old('paid_amount', '0')); ?>"
                                       placeholder="0,00"
                                       data-mask="currency"
                                       data-validate="currency">
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
                            <label for="workPaymentDate" class="form-label">
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
                                   id="workPaymentDate" name="payment_date" 
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
            form.action = `/partner/projects/<?php echo e($project->id); ?>/works/${workId}`;
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
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/modals/work-modal.blade.php ENDPATH**/ ?>