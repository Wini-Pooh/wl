<!-- Частичное представление для материалов -->
<?php if($materials->count() > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><i class="bi bi-box-seam me-2 text-success"></i>Наименование</th>
                    <th><i class="bi bi-123 me-2 text-muted"></i>Количество</th>
                    <th><i class="bi bi-rulers me-2 text-muted"></i>Единица</th>
                    <th><i class="bi bi-currency-ruble me-2 text-success"></i>Цена за единицу</th>
                    <th><i class="bi bi-calculator me-2 text-warning"></i>Общая стоимость</th>
                    <th><i class="bi bi-calendar-date me-2 text-info"></i>Дата</th>
                    <th class="text-end"><i class="bi bi-gear me-2 text-secondary"></i>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-box-seam text-success"></i>
                                </div>
                                <div>
                                    <strong><?php echo e($material->name); ?></strong>
                                    <?php if($material->description): ?>
                                        <br><small class="text-muted"><?php echo e($material->description); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark"><?php echo e(number_format($material->quantity, 2)); ?></span>
                        </td>
                        <td>
                            <span class="text-muted"><?php echo e($material->unit); ?></span>
                        </td>
                        <td>
                            <span class="text-success fw-semibold"><?php echo e(number_format($material->unit_price, 2)); ?> ₽</span>
                        </td>
                        <td>
                            <span class="badge bg-success text-white"><?php echo e(number_format($material->amount, 2)); ?> ₽</span>
                        </td>
                        <td>
                            <small class="text-info">
                                <i class="bi bi-calendar me-1"></i><?php echo e($material->created_at->format('d.m.Y')); ?>

                            </small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" 
                                        data-action="edit-material" 
                                        data-id="<?php echo e($material->id); ?>"
                                        title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" 
                                        data-action="delete-material" 
                                        data-id="<?php echo e($material->id); ?>"
                                        title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="bi bi-box-seam fa-3x text-muted" style="font-size: 3rem;"></i>
        </div>
        <h5 class="text-muted">Материалы не добавлены</h5>
        <p class="text-muted">Добавьте первую запись о материалах</p>
        <button type="button" class="btn btn-success" data-action="add-material">
            <i class="bi bi-plus-circle me-2"></i>Добавить материал
        </button>
    </div>
<?php endif; ?>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/finance/partials/materials-partial.blade.php ENDPATH**/ ?>