<!-- Финансовая сводка проекта -->
<div class="row g-4">
    <!-- Работы -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-tools fa-2x text-primary"></i>
                    </div>
                </div>
                <h6 class="card-title mb-2">Работы</h6>
                <div class="mb-2">
                    <span class="fs-5 fw-bold"><?php echo e(number_format($summary['works_total'], 0, '.', ' ')); ?> ₽</span>
                </div>
                <div class="small text-muted">
                    Оплачено: <?php echo e(number_format($summary['works_paid'], 0, '.', ' ')); ?> ₽
                </div>
                <?php if($summary['works_total'] > 0): ?>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-primary" 
                         style="width: <?php echo e(round(($summary['works_paid'] / $summary['works_total']) * 100)); ?>%"></div>
                </div>
                <?php endif; ?>
             
            </div>
        </div>
    </div>

    <!-- Материалы -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-box-seam fa-2x text-success"></i>
                    </div>
                </div>
                <h6 class="card-title mb-2">Материалы</h6>
                <div class="mb-2">
                    <span class="fs-5 fw-bold"><?php echo e(number_format($summary['materials_total'], 0, '.', ' ')); ?> ₽</span>
                </div>
                <div class="small text-muted">
                    Оплачено: <?php echo e(number_format($summary['materials_paid'], 0, '.', ' ')); ?> ₽
                </div>
                <?php if($summary['materials_total'] > 0): ?>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-success" 
                         style="width: <?php echo e(round(($summary['materials_paid'] / $summary['materials_total']) * 100)); ?>%"></div>
                </div>
                <?php endif; ?>
               
            </div>
        </div>
    </div>

    <!-- Транспорт -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-truck fa-2x text-warning"></i>
                    </div>
                </div>
                <h6 class="card-title mb-2">Транспорт</h6>
                <div class="mb-2">
                    <span class="fs-5 fw-bold"><?php echo e(number_format($summary['transport_total'], 0, '.', ' ')); ?> ₽</span>
                </div>
                <div class="small text-muted">
                    Оплачено: <?php echo e(number_format($summary['transport_paid'], 0, '.', ' ')); ?> ₽
                </div>
                <?php if($summary['transport_total'] > 0): ?>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-warning" 
                         style="width: <?php echo e(round(($summary['transport_paid'] / $summary['transport_total']) * 100)); ?>%"></div>
                </div>
                <?php endif; ?>
              
            </div>
        </div>
    </div>

    <!-- Общий итог -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 bg-light">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-graph-up fa-2x text-info"></i>
                    </div>
                </div>
                <h6 class="card-title mb-2">Общий итог</h6>
                <div class="mb-2">
                    <span class="fs-4 fw-bold text-info"><?php echo e(number_format($summary['grand_total'], 0, '.', ' ')); ?> ₽</span>
                </div>
                <div class="small text-muted">
                    Оплачено: <?php echo e(number_format($summary['total_paid'], 0, '.', ' ')); ?> ₽
                </div>
                <?php if($summary['grand_total'] > 0): ?>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" 
                         style="width: <?php echo e(round(($summary['total_paid'] / $summary['grand_total']) * 100)); ?>%"></div>
                </div>
                <div class="small text-info mt-1">
                    <?php echo e(round(($summary['total_paid'] / $summary['grand_total']) * 100, 1)); ?>% выполнено
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Дополнительная статистика -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-check me-2 text-primary"></i>
                            <div>
                                <div class="fw-bold"><?php echo e($counts['works'] ?? 0); ?></div>
                                <small class="text-muted">Работ запланировано</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-box-seam me-2 text-success"></i>
                            <div>
                                <div class="fw-bold"><?php echo e($counts['materials'] ?? 0); ?></div>
                                <small class="text-muted">Материалов</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-truck me-2 text-warning"></i>
                            <div>
                                <div class="fw-bold"><?php echo e($counts['transports'] ?? 0); ?></div>
                                <small class="text-muted">Транспортных услуг</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-currency-ruble me-2 text-info"></i>
                            <div>
                                <div class="fw-bold">
                                    <?php echo e(number_format($summary['grand_total'] - $summary['total_paid'], 0, '.', ' ')); ?> ₽
                                </div>
                                <small class="text-muted">Остаток к доплате</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/finance/partials/finance-summary.blade.php ENDPATH**/ ?>