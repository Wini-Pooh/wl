<!-- Документы проекта -->
<div id="documents-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>
            <span class="d-none d-md-inline">Документы проекта</span>
            <span class="d-md-none">Документы</span>
            (<?php echo e($documents->total()); ?>)
        </h5>
        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline ms-2">Загрузить документ</span>
        </button>
        <?php endif; ?>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4" id="documentFiltersCard">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Фильтры и сортировка</h6>
            <?php
                $activeFiltersCount = 0;
                if (!empty($filters['search'])) $activeFiltersCount++;
                if (!empty($filters['document_type'])) $activeFiltersCount++;
                if (!empty($filters['status'])) $activeFiltersCount++;
            ?>
            <small class="text-muted">Активных фильтров: <?php echo e($activeFiltersCount); ?></small>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('partner.projects.documents', $project)); ?>" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <label for="documentTypeFilter" class="form-label">Тип документа</label>
                        <select class="form-select" id="documentTypeFilter" name="document_type">
                            <option value="">Все типы</option>
                            <?php $__currentLoopData = $documentTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['document_type'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="documentStatusFilter" class="form-label">Статус</label>
                        <select class="form-select" id="documentStatusFilter" name="status">
                            <option value="">Все статусы</option>
                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['status'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="documentSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="documentSortFilter" name="sort">
                            <?php $__currentLoopData = $sortOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($filters['sort'] == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <a href="<?php echo e(route('partner.projects.documents', $project)); ?>" class="btn btn-outline-secondary" title="Сбросить все фильтры">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                            <button type="submit" class="btn btn-primary" title="Применить фильтры">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Дополнительные фильтры -->
                <div class="row g-3 mt-2">
                    <div class="col-12 col-md-6">
                        <label for="documentSearchFilter" class="form-label">Поиск по названию</label>
                        <input type="text" class="form-control" id="documentSearchFilter" name="search" 
                               value="<?php echo e($filters['search']); ?>" placeholder="Введите название файла...">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Галерея документов -->
    <div id="documentsGallery">
        <?php if($documents->count() > 0): ?>
            <div class="row g-3">
                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card document-card h-100">
                            <div class="document-preview">
                                <div class="file-icon">
                                    <?php switch(strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION))):
                                        case ('pdf'): ?>
                                            <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 3rem;"></i>
                                            <?php break; ?>
                                        <?php case ('doc'): ?>
                                        <?php case ('docx'): ?>
                                            <i class="bi bi-file-earmark-word text-primary" style="font-size: 3rem;"></i>
                                            <?php break; ?>
                                        <?php case ('xls'): ?>
                                        <?php case ('xlsx'): ?>
                                            <i class="bi bi-file-earmark-excel text-success" style="font-size: 3rem;"></i>
                                            <?php break; ?>
                                        <?php case ('ppt'): ?>
                                        <?php case ('pptx'): ?>
                                            <i class="bi bi-file-earmark-ppt text-warning" style="font-size: 3rem;"></i>
                                            <?php break; ?>
                                        <?php case ('jpg'): ?>
                                        <?php case ('jpeg'): ?>
                                        <?php case ('png'): ?>
                                        <?php case ('gif'): ?>
                                        <?php case ('webp'): ?>
                                        <?php case ('svg'): ?>
                                            <i class="bi bi-file-earmark-image text-info" style="font-size: 3rem;"></i>
                                            <?php break; ?>
                                        <?php case ('zip'): ?>
                                        <?php case ('rar'): ?>
                                        <?php case ('7z'): ?>
                                            <i class="bi bi-file-earmark-zip text-secondary" style="font-size: 3rem;"></i>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <i class="bi bi-file-earmark text-secondary" style="font-size: 3rem;"></i>
                                    <?php endswitch; ?>
                                </div>
                                
                                <!-- Действия -->
                                <div class="document-actions">
                                    <div class="btn-group-vertical" role="group">
                                        <a href="<?php echo e(route('partner.projects.documents.download', [$project, $document->id])); ?>" 
                                           class="btn btn-sm btn-outline-light" title="Скачать">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                                            <form method="POST" action="<?php echo e(route('partner.projects.documents.delete', [$project, $document->id])); ?>" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить документ <?php echo e($document->original_name); ?>?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Тип документа -->
                                <?php if($document->document_type): ?>
                                    <div class="document-type-badge">
                                        <?php echo e($documentTypeOptions[$document->document_type] ?? $document->document_type); ?>

                                    </div>
                                <?php endif; ?>

                                <!-- Статус -->
                                <?php if($document->status): ?>
                                    <div class="document-status-badge">
                                        <?php echo e($statusOptions[$document->status] ?? $document->status); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1 text-truncate" title="<?php echo e($document->original_name); ?>">
                                    <?php echo e($document->original_name); ?>

                                </h6>
                                <div class="text-muted small">
                                    <div class="d-flex justify-content-between">
                                        <span><?php echo e(number_format($document->file_size / 1024, 1)); ?> КБ</span>
                                        <span><?php echo e($document->created_at->format('d.m.Y')); ?></span>
                                    </div>
                                    <?php if($document->description): ?>
                                        <div class="mt-1" title="<?php echo e($document->description); ?>">
                                            <i class="bi bi-info-circle me-1"></i><?php echo e(\Illuminate\Support\Str::limit($document->description, 30)); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Пагинация -->
            <?php if($documents->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($documents->links()); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">Документы не найдены</h5>
                <p class="text-muted">
                    <?php if($activeFiltersCount > 0): ?>
                        Попробуйте изменить фильтры поиска
                    <?php else: ?>
                        Загрузите первый документ проекта
                    <?php endif; ?>
                </p>
                <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                        <i class="bi bi-plus-lg me-2"></i>Загрузить документ
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно для загрузки документов -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Загрузить документы
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('partner.projects.documents.upload', $project)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Зона загрузки файлов -->
                    <div class="upload-zone mb-4">
                        <div class="upload-content text-center p-4 border border-dashed rounded">
                            <i class="bi bi-file-earmark-text display-4 text-muted mb-3"></i>
                            <h5>Выберите документы для загрузки</h5>
                            <p class="text-muted mb-3">Поддерживаемые форматы: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, RTF, ZIP, RAR, 7Z, JPG, PNG, GIF, WEBP, SVG</p>
                            <input type="file" name="documents[]" multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,.zip,.rar,.7z,.jpg,.jpeg,.png,.gif,.webp,.svg" 
                                   class="form-control mb-3" required>
                            <small class="text-muted">Максимальный размер файла: 50 МБ</small>
                        </div>
                    </div>
                    
                    <!-- Дополнительные параметры -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="documentTypeSelect" class="form-label">Тип документа</label>
                            <select class="form-select" name="document_type">
                                <option value="">Выберите тип</option>
                                <?php $__currentLoopData = $documentTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="documentStatusSelect" class="form-label">Статус</label>
                            <select class="form-select" name="status">
                                <option value="active">Активный</option>
                                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="documentDescription" class="form-label">Описание (необязательно)</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Добавьте описание к документам..."></textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-2"></i>Загрузить документы
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.document-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.document-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.document-preview {
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.file-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.document-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.document-card:hover .document-actions {
    opacity: 1;
}

.document-type-badge {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0, 123, 255, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
}

.document-status-badge {
    position: absolute;
    bottom: 40px;
    left: 10px;
    background: rgba(40, 167, 69, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
}

.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.upload-content {
    padding: 2rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоматическая отправка формы при изменении основных фильтров
    const mainFilters = ['documentTypeFilter', 'documentStatusFilter', 'documentSortFilter'];
    mainFilters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.addEventListener('change', function() {
                filter.closest('form').submit();
            });
        }
    });
});
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/documents.blade.php ENDPATH**/ ?>