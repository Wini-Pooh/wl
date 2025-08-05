<!-- Схемы проекта -->
<div id="schemes-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-diagram-3 me-2"></i>
            <span class="d-none d-md-inline">Схемы проекта</span>
            <span class="d-md-none">Схемы</span>
            (<?php echo e($schemes->total()); ?>)
        </h5>
        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadSchemeModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline ms-2">Загрузить схему</span>
        </button>
        <?php endif; ?>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4" id="schemeFiltersCard">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Фильтры и сортировка</h6>
            <?php
                $activeFiltersCount = 0;
                if (!empty($filters['search'])) $activeFiltersCount++;
                if (!empty($filters['scheme_type'])) $activeFiltersCount++;
                if (!empty($filters['room'])) $activeFiltersCount++;
            ?>
            <small class="text-muted">Активных фильтров: <?php echo e($activeFiltersCount); ?></small>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('partner.projects.schemes', $project)); ?>" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <label for="schemeTypeFilter" class="form-label">Тип схемы</label>
                        <select class="form-select" id="schemeTypeFilter" name="scheme_type">
                            <option value="">Все типы</option>
                            <?php $__currentLoopData = $schemeTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['scheme_type'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="schemeRoomFilter" class="form-label">Помещение</label>
                        <select class="form-select" id="schemeRoomFilter" name="room">
                            <option value="">Все помещения</option>
                            <?php $__currentLoopData = $roomOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['room'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="schemeSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="schemeSortFilter" name="sort">
                            <?php $__currentLoopData = $sortOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['sort'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <a href="<?php echo e(route('partner.projects.schemes', $project)); ?>" class="btn btn-outline-secondary" title="Сбросить все фильтры">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                            <button type="button" class="btn btn-outline-primary" id="toggleSchemeFiltersBtn" title="Поиск по названию">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Поиск по названию (скрываемый) -->
                <div class="row g-3 mt-2" id="advancedSchemeFilters" style="display: none;">
                    <div class="col-md-8">
                        <label for="schemeSearchFilter" class="form-label">Поиск по названию</label>
                        <input type="text" class="form-control" id="schemeSearchFilter" name="search" 
                               value="<?php echo e($filters['search']); ?>" placeholder="Введите название...">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Применить фильтры
                        </button>
                    </div>
                </div>
                
                <!-- Скрытая кнопка применения для основных фильтров -->
                <button type="submit" style="display: none;" id="hiddenSubmitBtn"></button>
            </form>
        </div>
    </div>

    <!-- Галерея схем -->
    <div id="schemesGallery">
        <?php if($schemes->count() > 0): ?>
            <div class="row g-3">
                <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card scheme-card h-100">
                            <div class="scheme-preview">
                                <?php if(in_array(strtolower(pathinfo($scheme->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])): ?>
                                    <img src="<?php echo e(asset('storage/' . $scheme->file_path)); ?>" alt="<?php echo e($scheme->original_name); ?>" class="img-fluid">
                                <?php else: ?>
                                    <div class="file-icon">
                                        <?php switch(strtolower(pathinfo($scheme->original_name, PATHINFO_EXTENSION))):
                                            case ('pdf'): ?>
                                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 3rem;"></i>
                                                <?php break; ?>
                                            <?php case ('dwg'): ?>
                                            <?php case ('dxf'): ?>
                                                <i class="bi bi-file-earmark-code text-info" style="font-size: 3rem;"></i>
                                                <?php break; ?>
                                            <?php default: ?>
                                                <i class="bi bi-file-earmark text-secondary" style="font-size: 3rem;"></i>
                                        <?php endswitch; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Действия -->
                                <div class="scheme-actions">
                                    <div class="btn-group-vertical" role="group">
                                        <?php if(in_array(strtolower(pathinfo($scheme->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])): ?>
                                            <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#viewSchemeModal" 
                                                    data-image="<?php echo e(asset('storage/' . $scheme->file_path)); ?>" data-title="<?php echo e($scheme->original_name); ?>"
                                                    title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('partner.projects.schemes.download', [$project, $scheme->id])); ?>" 
                                           class="btn btn-sm btn-outline-light" title="Скачать">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                                            <form method="POST" action="<?php echo e(route('partner.projects.schemes.delete', [$project, $scheme->id])); ?>" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить схему <?php echo e($scheme->original_name); ?>?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Тип схемы -->
                                <?php if($scheme->scheme_type): ?>
                                    <div class="scheme-type-badge">
                                        <?php echo e($schemeTypeOptions[$scheme->scheme_type] ?? $scheme->scheme_type); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1 text-truncate" title="<?php echo e($scheme->original_name); ?>">
                                    <?php echo e($scheme->original_name); ?>

                                </h6>
                                <div class="text-muted small">
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo e(number_format($scheme->file_size / 1024, 1)); ?> КБ</span>
                                        <span><?php echo e($scheme->created_at->format('d.m.Y')); ?></span>
                                    </div>
                                    <?php if($scheme->room): ?>
                                        <div class="mt-1">
                                            <i class="bi bi-geo-alt me-1"></i><?php echo e($roomOptions[$scheme->room] ?? $scheme->room); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Пагинация -->
            <?php if($schemes->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($schemes->links()); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-diagram-3 text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Схемы не найдены</h5>
                <p class="text-muted">
                    <?php if($activeFiltersCount > 0): ?>
                        Попробуйте изменить фильтры поиска
                    <?php else: ?>
                        Загрузите первую схему проекта
                    <?php endif; ?>
                </p>
                <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadSchemeModal">
                        <i class="bi bi-plus-lg me-2"></i>Загрузить схему
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно для просмотра изображения -->
<div class="modal fade" id="viewSchemeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Просмотр схемы</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalSchemeImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоматическая отправка формы при изменении основных фильтров
    const mainFilters = ['schemeTypeFilter', 'schemeRoomFilter', 'schemeSortFilter'];
    mainFilters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.addEventListener('change', function() {
                document.getElementById('hiddenSubmitBtn').click();
            });
        }
    });

    // Переключение видимости поиска
    const toggleBtn = document.getElementById('toggleSchemeFiltersBtn');
    const advancedFilters = document.getElementById('advancedSchemeFilters');
    
    if (toggleBtn && advancedFilters) {
        toggleBtn.addEventListener('click', function() {
            const isVisible = advancedFilters.style.display !== 'none';
            advancedFilters.style.display = isVisible ? 'none' : 'block';
            const icon = toggleBtn.querySelector('i');
            icon.className = isVisible ? 'bi bi-search' : 'bi bi-chevron-up';
        });
    }

    // Показать поиск, если есть значение поиска
    const searchInput = document.getElementById('schemeSearchFilter');
    if (searchInput && searchInput.value) {
        advancedFilters.style.display = 'block';
        const icon = toggleBtn.querySelector('i');
        icon.className = 'bi bi-chevron-up';
    }

    // Модальное окно просмотра
    const viewModal = document.getElementById('viewSchemeModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const imageUrl = button.getAttribute('data-image');
            const title = button.getAttribute('data-title');
            
            const modalImage = document.getElementById('modalSchemeImage');
            const modalTitle = viewModal.querySelector('.modal-title');
            
            modalImage.src = imageUrl;
            modalImage.alt = title;
            modalTitle.textContent = title;
        });
    }
});


</script>

<style>
.scheme-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.scheme-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: #28a745;
}

.scheme-preview {
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.scheme-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}

.file-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.scheme-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.scheme-card:hover .scheme-actions {
    opacity: 1;
}

.scheme-type-badge {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(40, 167, 69, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
}
</style>

<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/schemes.blade.php ENDPATH**/ ?>