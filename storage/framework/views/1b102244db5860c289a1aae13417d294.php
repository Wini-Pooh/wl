

<?php $__env->startSection('title', 'Редактирование шаблона документа'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Редактирование шаблона: <?php echo e($template->name); ?></h3>
                </div>
                
                <form id="templateForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="card-body">
                        <div class="row">
                            <!-- Основная информация -->
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label for="name" class="form-label">Название шаблона *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo e(old('name', $template->name)); ?>" required
                                               placeholder="Например: Договор на строительные работы">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="document_type" class="form-label">Тип документа *</label>
                                        <select class="form-select" id="document_type" name="document_type" required>
                                            <option value="">Выберите тип</option>
                                            <option value="contract" <?php echo e(old('document_type', $template->document_type) == 'contract' ? 'selected' : ''); ?>>Договор</option>
                                            <option value="act" <?php echo e(old('document_type', $template->document_type) == 'act' ? 'selected' : ''); ?>>Акт</option>
                                            <option value="invoice" <?php echo e(old('document_type', $template->document_type) == 'invoice' ? 'selected' : ''); ?>>Счет</option>
                                            <option value="estimate" <?php echo e(old('document_type', $template->document_type) == 'estimate' ? 'selected' : ''); ?>>Смета</option>
                                            <option value="technical" <?php echo e(old('document_type', $template->document_type) == 'technical' ? 'selected' : ''); ?>>Техническая документация</option>
                                            <option value="other" <?php echo e(old('document_type', $template->document_type) == 'other' ? 'selected' : ''); ?>>Прочее</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Описание шаблона</label>
                                    <textarea class="form-control" id="description" name="description" rows="2"
                                              placeholder="Краткое описание назначения шаблона"><?php echo e(old('description', $template->description)); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                               <?php echo e(old('is_active', $template->is_active) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="is_active">
                                            Активный шаблон
                                        </label>
                                        <div class="form-text">Неактивные шаблоны не отображаются при создании документов</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="template_content" class="form-label">Содержимое шаблона *</label>
                                    <textarea class="form-control" id="template_content" name="template_content" 
                                              rows="20" required placeholder="Введите содержимое шаблона. Используйте <?php echo e('{{переменная); ?>'}} для создания переменных"><?php echo e(old('template_content', $template->template_content)); ?></textarea>
                                    <div class="form-text">
                                        Используйте двойные фигурные скобки для создания переменных: <?php echo e('{{client_name); ?>'}}, <?php echo e('{{contract_date); ?>'}} и т.д.
                                    </div>
                                </div>
                            </div>

                            <!-- Боковая панель -->
                            <div class="col-md-4">
                                <!-- Переменные -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Переменные шаблона</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="variablesList" class="mb-3">
                                            <small class="text-muted">Переменные будут обнаружены автоматически при вводе текста</small>
                                        </div>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100" 
                                                onclick="scanVariables()">
                                            <i class="fas fa-search"></i> Найти переменные
                                        </button>
                                    </div>
                                </div>

                                <!-- Примеры переменных -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Примеры переменных</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="variable-examples">
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{client_name); ?>'}}}"><?php echo e('{{client_name); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{contract_date); ?>'}}}"><?php echo e('{{contract_date); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{contract_number); ?>'}}}"><?php echo e('{{contract_number); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{object_address); ?>'}}}"><?php echo e('{{object_address); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{work_description); ?>'}}}"><?php echo e('{{work_description); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{total_cost); ?>'}}}"><?php echo e('{{total_cost); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{start_date); ?>'}}}"><?php echo e('{{start_date); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{end_date); ?>'}}}"><?php echo e('{{end_date); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{client_phone); ?>'}}}"><?php echo e('{{client_phone); ?>'}}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mb-1 variable-btn" 
                                                    data-variable="<?php echo e('{{payment_terms); ?>'}}}"><?php echo e('{{payment_terms); ?>'}}</button>
                                        </div>
                                        <small class="text-muted">Нажмите на переменную, чтобы вставить её в текст</small>
                                    </div>
                                </div>

                                <!-- Предварительный просмотр -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Предварительный просмотр</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="preview" class="border rounded p-2" style="max-height: 300px; overflow-y: auto; font-size: 12px;">
                                            <small class="text-muted">Введите содержимое шаблона для просмотра</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Информация о шаблоне -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Информация о шаблоне</h6>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>Создан:</strong> <?php echo e($template->created_at->format('d.m.Y H:i')); ?><br>
                                            <strong>Создатель:</strong> <?php echo e($template->creator->name); ?><br>
                                            <strong>Обновлен:</strong> <?php echo e($template->updated_at->format('d.m.Y H:i')); ?><br>
                                            <strong>Документов:</strong> <?php echo e($template->documents_count ?? 0); ?>

                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="<?php echo e(route('document-templates.show', $template)); ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Отмена
                                </a>
                                <a href="<?php echo e(route('documents.index', ['tab' => 'templates'])); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> К списку
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить изменения
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentField = document.getElementById('template_content');
    const previewDiv = document.getElementById('preview');
    const variablesList = document.getElementById('variablesList');

    // Инициализация при загрузке страницы
    updatePreview();
    scanVariables();

    // Обновление предварительного просмотра
    contentField.addEventListener('input', function() {
        updatePreview();
        scanVariables();
    });

    // Вставка переменных
    document.querySelectorAll('.variable-btn').forEach(button => {
        button.addEventListener('click', function() {
            const variable = this.dataset.variable;
            insertAtCursor(contentField, variable);
            updatePreview();
            scanVariables();
        });
    });

    // Функция вставки текста в позицию курсора
    function insertAtCursor(textarea, text) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const value = textarea.value;
        
        textarea.value = value.substring(0, start) + text + value.substring(end);
        textarea.setSelectionRange(start + text.length, start + text.length);
        textarea.focus();
    }

    // Обновление предварительного просмотра
    function updatePreview() {
        const content = contentField.value;
        if (content.trim()) {
            // Заменяем переменные на примеры для предварительного просмотра
            let preview = content
                .replace(/\{\{client_name\}\}/g, 'Иванов Иван Иванович')
                .replace(/\{\{contract_date\}\}/g, '<?php echo e(date("d.m.Y")); ?>')
                .replace(/\{\{contract_number\}\}/g, '001')
                .replace(/\{\{object_address\}\}/g, 'г. Москва, ул. Примерная, д. 1')
                .replace(/\{\{work_description\}\}/g, 'Ремонт квартиры')
                .replace(/\{\{total_cost\}\}/g, '500 000')
                .replace(/\{\{start_date\}\}/g, '01.08.2025')
                .replace(/\{\{end_date\}\}/g, '31.08.2025')
                .replace(/\{\{client_phone\}\}/g, '+7 (999) 123-45-67')
                .replace(/\{\{payment_terms\}\}/g, 'предоплата 50%, остаток по завершении работ');
            
            previewDiv.innerHTML = '<div style="white-space: pre-wrap; line-height: 1.4;">' + preview + '</div>';
        } else {
            previewDiv.innerHTML = '<small class="text-muted">Введите содержимое шаблона для просмотра</small>';
        }
    }

    // Сканирование переменных
    window.scanVariables = function() {
        const content = contentField.value;
        const regex = /\{\{([^}]+)\}\}/g;
        const variables = new Set();
        let match;

        while ((match = regex.exec(content)) !== null) {
            variables.add(match[1]);
        }

        if (variables.size > 0) {
            variablesList.innerHTML = '<div class="mb-2"><strong>Найденные переменные:</strong></div>';
            variables.forEach(variable => {
                const span = document.createElement('span');
                span.className = 'badge bg-primary me-1 mb-1';
                span.textContent = variable;
                variablesList.appendChild(span);
            });
        } else {
            variablesList.innerHTML = '<small class="text-muted">Переменные не найдены</small>';
        }
    };

    // Обработка отправки формы
    const form = document.getElementById('templateForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Показываем индикатор загрузки
        submitButton.disabled = true;
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
        
        fetch('<?php echo e(route("document-templates.update", $template)); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Шаблон успешно обновлен');
                setTimeout(() => {
                    window.location.href = '<?php echo e(route("document-templates.show", $template)); ?>';
                }, 1500);
            } else {
                if (data.errors) {
                    let errorMessage = 'Ошибки валидации:\n';
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => {
                            errorMessage += '- ' + error + '\n';
                        });
                    });
                    showAlert('error', errorMessage);
                } else {
                    showAlert('error', data.message || 'Произошла ошибка при обновлении шаблона');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Произошла ошибка при обновлении шаблона');
        })
        .finally(() => {
            // Восстанавливаем кнопку
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });

    // Функция показа уведомлений
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message.replace(/\n/g, '<br>')}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/documents/templates/edit.blade.php ENDPATH**/ ?>