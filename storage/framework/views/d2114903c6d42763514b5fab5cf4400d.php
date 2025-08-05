

<?php $__env->startSection('styles'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
<link href="<?php echo e(asset('css/design-standard.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
<div class="container-fluid">
    <!-- Заголовок страницы -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-paint-bucket me-2"></i>
            <span class="d-none d-md-inline">Дизайн проекта</span>
            <span class="d-md-none">Дизайн</span>
            (<span><?php echo e($designFiles->total() ?? 0); ?></span>)
        </h5>
        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDesignModal">
            <i class="bi bi-upload me-1"></i>
            <span class="d-none d-md-inline">Загрузить дизайн</span>
            <span class="d-md-none">Загрузить</span>
        </button>
        <?php endif; ?>
    </div>

    <!-- Форма фильтров -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>
                Фильтры и поиск
                <?php if(count(array_filter($filters))): ?>
                    <span class="badge bg-info ms-2">Активно: <?php echo e(count(array_filter($filters))); ?></span>
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('partner.projects.design', $project)); ?>">
                <div class="row g-3">
                    <!-- Поиск -->
                    <div class="col-12">
                        <label for="search" class="form-label">Поиск по названию</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo e($filters['search'] ?? ''); ?>"
                                   placeholder="Введите название файла дизайна...">
                            <?php if(!empty($filters['search'])): ?>
                            <a href="<?php echo e(route('partner.projects.design', $project)); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Тип дизайна -->
                    <div class="col-md-3">
                        <label for="design_type" class="form-label">Тип дизайна</label>
                        <select class="form-select" id="design_type" name="design_type" onchange="this.form.submit()">
                            <option value="">Все типы</option>
                            <?php $__currentLoopData = $designTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(($filters['design_type'] ?? '') == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Помещение -->
                    <div class="col-md-3">
                        <label for="room" class="form-label">Помещение</label>
                        <select class="form-select" id="room" name="room" onchange="this.form.submit()">
                            <option value="">Все помещения</option>
                            <?php $__currentLoopData = $roomOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(($filters['room'] ?? '') == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Стиль -->
                    <div class="col-md-3">
                        <label for="style" class="form-label">Стиль</label>
                        <select class="form-select" id="style" name="style" onchange="this.form.submit()">
                            <option value="">Все стили</option>
                            <?php $__currentLoopData = $styleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(($filters['style'] ?? '') == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Сортировка -->
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Сортировка</label>
                        <select class="form-select" id="sort" name="sort" onchange="this.form.submit()">
                            <?php $__currentLoopData = $sortOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(($filters['sort'] ?? 'newest') == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Применить фильтры
                        </button>
                        <a href="<?php echo e(route('partner.projects.design', $project)); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Сбросить
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Список файлов дизайна -->
    <div class="row g-3" id="designGallery">
        <?php if($designFiles->count() > 0): ?>
            <?php $__currentLoopData = $designFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $designFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card design-card h-100">
                        <div class="design-preview position-relative">
                            <?php if($designFile->isImage()): ?>
                                <img src="<?php echo e($designFile->url); ?>" alt="<?php echo e($designFile->original_name); ?>" 
                                     class="card-img-top design-image"
                                     onclick="openDesignView('<?php echo e($designFile->id); ?>')">
                            <?php else: ?>
                                <div class="design-file-icon text-center p-4" onclick="openDesignView('<?php echo e($designFile->id); ?>')">
                                    <?php
                                        $extension = strtolower(pathinfo($designFile->original_name, PATHINFO_EXTENSION));
                                        $iconClass = match($extension) {
                                            'pdf' => 'bi-file-pdf',
                                            'psd' => 'bi-file-image',
                                            'ai', 'eps' => 'bi-file-earmark-richtext',
                                            'dwg', 'dxf' => 'bi-file-earmark-ruled',
                                            'xd', 'fig' => 'bi-palette',
                                            default => 'bi-file-earmark'
                                        };
                                    ?>
                                    <i class="<?php echo e($iconClass); ?> display-4 text-primary"></i>
                                    <div class="mt-2 small text-muted"><?php echo e(strtoupper($extension)); ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Действия с файлом -->
                            <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                            <div class="design-actions">
                                <a href="<?php echo e(route('partner.projects.design.download', [$project, $designFile])); ?>" 
                                   class="btn btn-sm btn-outline-light" title="Скачать">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete('<?php echo e($designFile->id); ?>', '<?php echo e($designFile->original_name); ?>')" 
                                        title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Бейджи -->
                            <div class="design-badges">
                                <?php if($designFile->design_type): ?>
                                    <span class="badge bg-primary"><?php echo e($designFile->design_type_name ?? $designFile->design_type); ?></span>
                                <?php endif; ?>
                                <?php if($designFile->room): ?>
                                    <span class="badge bg-secondary"><?php echo e($designFile->room_name ?? $designFile->room); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title text-truncate" title="<?php echo e($designFile->original_name); ?>">
                                <?php echo e($designFile->original_name); ?>

                            </h6>
                            
                            <div class="design-meta small text-muted">
                                <?php if($designFile->style): ?>
                                    <div><i class="bi bi-palette me-1"></i><?php echo e($designFile->style_name ?? $designFile->style); ?></div>
                                <?php endif; ?>
                                <?php if($designFile->designer): ?>
                                    <div><i class="bi bi-person me-1"></i><?php echo e($designFile->designer); ?></div>
                                <?php endif; ?>
                                <div><i class="bi bi-file-earmark me-1"></i><?php echo e($designFile->formatted_size); ?></div>
                                <div><i class="bi bi-clock me-1"></i><?php echo e($designFile->created_at->format('d.m.Y H:i')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-folder2-open display-1 text-muted"></i>
                    <h5 class="mt-3">Нет файлов дизайна</h5>
                    <p class="text-muted">
                        <?php if(count(array_filter($filters))): ?>
                            По заданным фильтрам ничего не найдено. 
                            <a href="<?php echo e(route('partner.projects.design', $project)); ?>">Сбросить фильтры</a>
                        <?php else: ?>
                            Загрузите файлы дизайна проекта, нажав кнопку "Загрузить дизайн" вверху страницы
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Пагинация -->
    <?php if($designFiles->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($designFiles->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
</div>

<!-- Модальное окно загрузки дизайна -->
<?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
<?php echo $__env->make('partner.projects.modals.upload-design-standard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
<script>
// Функция подтверждения удаления
function confirmDelete(designId, filename) {
    if (confirm(`Вы уверены, что хотите удалить файл "${filename}"? Это действие нельзя отменить.`)) {
        // Создаем форму для DELETE запроса
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?php echo e(route('partner.projects.design.destroy', [$project, '__ID__'])); ?>`.replace('__ID__', designId);
        
        // Добавляем CSRF токен
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfInput);
        
        // Добавляем method override
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Функция открытия просмотра файла
function openDesignView(designId) {
    window.open(`<?php echo e(route('partner.projects.design.view', [$project, '__ID__'])); ?>`.replace('__ID__', designId), '_blank');
}

// Автоотправка формы фильтров при изменении select
document.addEventListener('DOMContentLoaded', function() {
    // Сообщения об успехе/ошибке
    <?php if(session('success')): ?>
        showToast('<?php echo e(session('success')); ?>', 'success');
    <?php endif; ?>
    
    <?php if(session('error')): ?>
        showToast('<?php echo e(session('error')); ?>', 'error');
    <?php endif; ?>
    
    <?php if($errors->any()): ?>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            showToast('<?php echo e($error); ?>', 'error');
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
});

// Функция для показа toast уведомлений
function showToast(message, type = 'info') {
    // Создаем контейнер для toast, если его нет
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Показываем toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Удаляем элемент после скрытия
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

console.log('✅ Скрипты страницы дизайна загружены (без AJAX)');
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partner.projects.layouts.project-base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/pages/design-standard.blade.php ENDPATH**/ ?>