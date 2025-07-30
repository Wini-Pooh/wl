

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Управление сотрудниками
                </h2>
                <div class="btn-group">
                    <a href="<?php echo e(route('partner.employees.create')); ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Добавить сотрудника
                    </a>
                    <a href="<?php echo e(route('partner.employees.dashboard')); ?>" class="btn btn-success">
                        <i class="bi bi-graph-up me-2"></i>
                        Финансовый дашборд
                    </a>
                </div>
            </div>

            <!-- Статистика -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Всего сотрудников</h6>
                                    <h3><?php echo e($stats['total']); ?></h3>
                                </div>
                                <i class="bi bi-people fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Активных</h6>
                                    <h3><?php echo e($stats['active']); ?></h3>
                                </div>
                                <i class="bi bi-person-check fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">К выплате</h6>
                                    <h3><?php echo e(number_format($stats['pending_payments'], 0, ',', ' ')); ?> ₽</h3>
                                    <small class="text-light">Общая сумма задолженности</small>
                                </div>
                                <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Просрочено</h6>
                                    <h3><?php echo e(number_format($stats['overdue_payments'], 0, ',', ' ')); ?> ₽</h3>
                                    <small class="text-light">Критические долги</small>
                                </div>
                                <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Фильтры и поиск -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('partner.employees.index')); ?>" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Поиск по ФИО/телефону</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo e(request('search')); ?>" placeholder="Введите для поиска...">
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Роль</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">Все роли</option>
                                    <?php $__currentLoopData = \App\Models\Employee::getRoles(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(request('role') == $key ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Статус</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Все статусы</option>
                                    <?php $__currentLoopData = \App\Models\Employee::getStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Поиск
                                </button>
                                <a href="<?php echo e(route('partner.employees.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Сброс
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Список сотрудников -->
            <div class="card">
                <div class="card-body">
                    <?php if($employees->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ФИО</th>
                                        <th>Телефон</th>
                                        <th>Роль</th>
                                        <th>Статус</th>
                                        <th>К выплате</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <?php echo e(substr($employee->first_name, 0, 1)); ?><?php echo e(substr($employee->last_name, 0, 1)); ?>

                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo e($employee->short_name); ?></h6>
                                                        <?php if($employee->email): ?>
                                                            <small class="text-muted"><?php echo e($employee->email); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo e($employee->phone); ?>" class="text-decoration-none">
                                                    <?php echo e($employee->phone); ?>

                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($employee->role_name); ?></span>
                                            </td>
                                            <td>
                                                <?php if($employee->status === 'active'): ?>
                                                    <span class="badge bg-success"><?php echo e($employee->status_name); ?></span>
                                                <?php elseif($employee->status === 'inactive'): ?>
                                                    <span class="badge bg-warning"><?php echo e($employee->status_name); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger"><?php echo e($employee->status_name); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $pendingAmount = $employee->finances->where('status', 'pending')->sum('amount');
                                                    $overdueAmount = $employee->finances->where('status', 'overdue')->sum('amount');
                                                ?>
                                                <div>
                                                    <?php if($pendingAmount > 0): ?>
                                                        <div class="text-warning">
                                                            <i class="bi bi-clock-fill"></i>
                                                            <?php echo e(number_format($pendingAmount, 0, ',', ' ')); ?> ₽
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if($overdueAmount > 0): ?>
                                                        <div class="text-danger">
                                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                                            <?php echo e(number_format($overdueAmount, 0, ',', ' ')); ?> ₽
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if($pendingAmount == 0 && $overdueAmount == 0): ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('partner.employees.show', $employee)); ?>" 
                                                       class="btn btn-sm btn-outline-info" title="Просмотр">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('partner.employees.edit', $employee)); ?>" 
                                                       class="btn btn-sm btn-outline-warning" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete(<?php echo e($employee->id); ?>)" title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагинация -->
                        <?php if($employees->hasPages()): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <?php echo e($employees->withQueryString()->links()); ?>

                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">Сотрудники не найдены</h5>
                            <p class="text-muted">Добавьте первого сотрудника или измените параметры поиска</p>
                            <a href="<?php echo e(route('partner.employees.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Добавить сотрудника
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этого сотрудника?</p>
                <p class="text-danger"><strong>Внимание:</strong> Все финансовые записи сотрудника также будут удалены!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmDelete(employeeId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/partner/employees/${employeeId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Автоотправка формы при изменении фильтров
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const selects = form.querySelectorAll('select');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            form.submit();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/employees/index.blade.php ENDPATH**/ ?>