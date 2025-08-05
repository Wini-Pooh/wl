

<?php $__env->startSection('styles'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
<link href="<?php echo e(asset('css/photos-standard.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
<div class="container-fluid">
    <!-- Заголовок страницы -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-camera me-2"></i>
            Фотографии проекта 
            <span class="badge bg-primary"><?php echo e($photos->total()); ?></span>
        </h4>
        
        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                <i class="bi bi-plus-lg me-1"></i>
                <span class="d-none d-md-inline">Загрузить фотографии</span>
                <span class="d-md-none">Загрузить</span>
            </button>
        </div>
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
            <form method="GET" action="<?php echo e(route('partner.projects.photos', $project)); ?>">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label for="category" class="form-label">Категория</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Все категории</option>
                            <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['category'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="location" class="form-label">Помещение</label>
                        <select class="form-select" id="location" name="location">
                            <option value="">Все помещения</option>
                            <?php $__currentLoopData = $locationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['location'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="sort" class="form-label">Сортировка</label>
                        <select class="form-select" id="sort" name="sort">
                            <?php $__currentLoopData = $sortOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['sort'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="search" class="form-label">Поиск</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo e($filters['search']); ?>" placeholder="Поиск по названию...">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="document.getElementById('search').value=''; this.form.submit();">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Применить фильтры
                            </button>
                            <a href="<?php echo e(route('partner.projects.photos', $project)); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Сбросить все
                            </a>
                            <?php if(count(array_filter($filters))): ?>
                                <div class="text-muted align-self-center">
                                    <small>Найдено: <?php echo e($photos->total()); ?> фотографий</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Галерея фотографий -->
    <?php if($photos->count() > 0): ?>
        <div class="row g-3 mb-4">
            <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $categoryName = $categoryOptions[$photo->category] ?? $photo->category ?? 'Без категории';
                    $locationName = $locationOptions[$photo->location] ?? $photo->location ?? '';
                    
                    // Генерируем URL для изображения
                    $imageUrl = $photo->url ?? asset('storage/' . $photo->path);
                ?>
                
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="card photo-card h-100">
                        <div class="position-relative">
                            <img src="<?php echo e($imageUrl); ?>" 
                                 alt="<?php echo e($photo->original_name ?? $photo->filename); ?>" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;"
                                 loading="lazy">
                            
                            <!-- Бейджи категории и помещения -->
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-primary me-1"><?php echo e($categoryName); ?></span>
                                <?php if($locationName): ?>
                                    <span class="badge bg-secondary"><?php echo e($locationName); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Кнопки действий -->
                            <div class="position-absolute top-0 end-0 p-2">
                                <div class="btn-group-vertical" role="group">
                                    <a href="<?php echo e(route('partner.projects.photos.show', [$project, $photo->id])); ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-light mb-1" 
                                       title="Просмотр в полном размере">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-light" 
                                                onclick="confirmDelete(<?php echo e($photo->id); ?>, '<?php echo e($photo->original_name ?? $photo->filename); ?>')"
                                                title="Удалить">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title text-truncate mb-2" 
                                title="<?php echo e($photo->original_name ?? $photo->filename); ?>">
                                <?php echo e($photo->original_name ?? $photo->filename); ?>

                            </h6>
                            
                            <div class="text-muted small">
                                <?php if($photo->file_size): ?>
                                    <div><i class="bi bi-file-earmark me-1"></i><?php echo e(number_format($photo->file_size / 1024, 1)); ?> КБ</div>
                                <?php endif; ?>
                                <?php if($photo->created_at): ?>
                                    <div><i class="bi bi-calendar me-1"></i><?php echo e($photo->created_at->format('d.m.Y H:i')); ?></div>
                                <?php endif; ?>
                                <?php if($photo->comment): ?>
                                    <div class="mt-2">
                                        <i class="bi bi-chat-text me-1"></i>
                                        <span class="text-break"><?php echo e(Str::limit($photo->comment, 100)); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <!-- Пагинация -->
        <?php if($photos->hasPages()): ?>
            <div class="d-flex justify-content-center">
                <?php echo e($photos->links('custom.pagination')); ?>

            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Пустое состояние -->
        <div class="text-center py-5">
            <i class="bi bi-images display-1 text-muted"></i>
            <h5 class="mt-3">Фотографии не найдены</h5>
            <p class="text-muted">
                <?php if(count(array_filter($filters))): ?>
                    По заданным фильтрам фотографии не найдены.<br>
                    <a href="<?php echo e(route('partner.projects.photos', $project)); ?>" class="btn btn-outline-primary mt-2">
                        Сбросить фильтры
                    </a>
                <?php else: ?>
                    В этом проекте пока нет фотографий.<br>
                    <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="bi bi-plus-lg me-1"></i>
                            Загрузить первые фотографии
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Скрытые формы для удаления -->
    <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <form id="deleteForm<?php echo e($photo->id); ?>" 
                  method="POST" 
                  action="<?php echo e(route('partner.projects.photos.delete', [$project, $photo->id])); ?>" 
                  style="display: none;">
                <?php echo csrf_field(); ?>
            </form>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>

<!-- Модальное окно загрузки фотографий -->
<?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
<?php echo $__env->make('partner.projects.modals.upload-photo-standard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
<script>
// Функция подтверждения удаления
function confirmDelete(photoId, filename) {
    if (confirm(`Вы уверены, что хотите удалить фотографию "${filename}"?\n\nЭто действие нельзя отменить.`)) {
        document.getElementById('deleteForm' + photoId).submit();
    }
}

// Автоотправка формы фильтров при изменении select
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('#category, #location, #sort');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Поиск с задержкой
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 1000); // Отправляем форму через 1 секунду после окончания ввода
    });
});

// Функция для предпросмотра выбранных файлов
function previewSelectedFiles() {
    const fileInput = document.getElementById('photoFiles');
    const preview = document.getElementById('filePreview');
    
    if (fileInput.files.length > 0) {
        let html = '<h6>Выбранные файлы:</h6><ul class="list-group">';
        
        Array.from(fileInput.files).forEach((file, index) => {
            const size = (file.size / 1024 / 1024).toFixed(2);
            html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-image me-2"></i>${file.name}</span>
                        <span class="badge bg-secondary">${size} МБ</span>
                     </li>`;
        });
        
        html += '</ul>';
        preview.innerHTML = html;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Сообщения об успехе/ошибке
<?php if(session('success')): ?>
    alert('✅ <?php echo e(session('success')); ?>');
<?php endif; ?>

<?php if(session('error')): ?>
    alert('❌ <?php echo e(session('error')); ?>');
<?php endif; ?>

<?php if($errors->any()): ?>
    alert('❌ Ошибка: <?php echo e($errors->first()); ?>');
<?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partner.projects.layouts.project-base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/pages/photos-standard.blade.php ENDPATH**/ ?>