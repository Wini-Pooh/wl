

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h2 class="mb-2 mb-md-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    <span class="d-none d-sm-inline">Управление сметами</span>
                    <span class="d-sm-none">Сметы</span>
                </h2>
                <a href="<?php echo e(route('partner.estimates.create')); ?>" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    <span class="d-none d-sm-inline">Создать смету</span>
                    <span class="d-sm-none">Создать</span>
                </a>
            </div>

            <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Фильтры и поиск -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="<?php echo e(route('partner.estimates.index')); ?>" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Поиск</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Название или номер сметы" value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="project" class="form-label">Объект</label>
                            <select class="form-select" id="project" name="project_id">
                                <option value="">Все объекты</option>
                                <?php $__currentLoopData = App\Models\Project::forPartner(Auth::id())->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($project->id); ?>" <?php echo e(request('project_id') == $project->id ? 'selected' : ''); ?>>
                                    <?php echo e($project->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Тип сметы</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Все типы</option>
                                <?php $__currentLoopData = App\Models\Estimate::getTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>>
                                    <?php echo e($value); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Статус</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Все статусы</option>
                                <?php $__currentLoopData = App\Models\Estimate::getStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                                    <?php echo e($value); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Найти
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Список смет -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col" width="20%">Название</th>
                                    <th scope="col" width="20%">Объект</th>
                                    <th scope="col" width="10%">Тип</th>
                                    <th scope="col" width="10%">Статус</th>
                                    <th scope="col" width="10%">Сумма</th>
                                    <th scope="col" width="15%">Создана</th>
                                    <th scope="col" width="10%">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $estimates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estimate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold">EST-<?php echo e(str_pad($estimate->id, 4, '0', STR_PAD_LEFT)); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('partner.estimates.edit', $estimate->id)); ?>" class="text-decoration-none fw-medium">
                                            <?php echo e($estimate->name); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($estimate->project->name ?? 'Не указана'); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo e($estimate->type_name); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($estimate->status_color); ?>">
                                            <?php echo e($estimate->status_name); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            <?php echo e(number_format($estimate->total_amount, 2, ',', ' ')); ?> ₽
                                        </span>
                                    </td>
                                    <td><?php echo e($estimate->created_at->format('d.m.Y H:i')); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('partner.estimates.edit', $estimate->id)); ?>" class="btn btn-sm btn-outline-primary" title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                           
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-estimate" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                data-id="<?php echo e($estimate->id); ?>"
                                                data-name="<?php echo e($estimate->name); ?>"
                                                title="Удалить">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-file-earmark-x text-secondary mb-3" style="font-size: 2.5rem;"></i>
                                            <h5 class="text-secondary">Сметы не найдены</h5>
                                            <p class="text-muted">Создайте новую смету, чтобы начать работу</p>
                                            <a href="<?php echo e(route('partner.estimates.create')); ?>" class="btn btn-primary">
                                                <i class="bi bi-plus-lg me-1"></i> Создать смету
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if($estimates->hasPages()): ?>
                <div class="card-footer bg-white border-top-0 py-3">
                    <?php echo e($estimates->withQueryString()->links()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения удаления -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3 text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0">Вы собираетесь удалить смету</h5>
                </div>
                <p>Смета: <span id="estimate-name" class="fw-bold"></span></p>
                <p class="text-danger">Это действие невозможно отменить. Все данные сметы будут безвозвратно удалены.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="delete-form" action="" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Удалить
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Обработка нажатия на кнопку удаления
        document.querySelectorAll('.delete-estimate').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                
                document.getElementById('estimate-name').textContent = name;
                document.getElementById('delete-form').action = `/partner/estimates/${id}`;
            });
        });
        
        // Активация всплывающих подсказок
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'top',
                delay: { show: 300, hide: 100 }
            });
        });
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
    }
    
    .table th {
        font-weight: 600;
        color: #495057;
    }
    
    .table-light {
        background-color: #f8f9fa;
    }
    
    .badge {
        padding: 0.4em 0.65em;
    }
    
    .btn-group .btn {
        margin-right: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .pagination {
        margin-bottom: 0;
    }
</style>
       
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/estimates/index.blade.php ENDPATH**/ ?>