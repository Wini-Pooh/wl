

<div class="row">
    <div class="col-lg-8">
        <div class="card glassmorphism-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Детали проекта</h5>
                <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                    <button class="btn btn-primary btn-sm" data-modal-type="main">
                        <i class="bi bi-plus-circle me-1"></i>Быстрое добавление
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- Мобильная адаптация: стекаем колонки на маленьких экранах -->
                <div class="row">
                    <div class="col-12 col-md-6 mb-4 mb-md-0">
                        <h6 class="text-muted mb-3"><i class="bi bi-person me-1"></i>Информация о клиенте</h6>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">ФИО:</span>
                                <span class="info-value"><?php echo e($project->client_full_name); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Телефон:</span>
                                <span class="info-value">
                                    <a href="tel:<?php echo e($project->client_phone); ?>" class="text-primary"><?php echo e($project->client_phone); ?></a>
                                </span>
                            </div>
                            <?php if($project->client_email): ?>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value">
                                    <a href="mailto:<?php echo e($project->client_email); ?>" class="text-primary"><?php echo e($project->client_email); ?></a>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <h6 class="text-muted mb-3"><i class="bi bi-geo-alt me-1"></i>Информация об объекте</h6>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Адрес:</span>
                                <span class="info-value"><?php echo e($project->object_full_address); ?></span>
                            </div>
                            <?php if($project->object_type): ?>
                            <div class="info-item">
                                <span class="info-label">Тип объекта:</span>
                                <span class="info-value">
                                    <span class="badge bg-primary"><?php echo e($project->object_type); ?></span>
                                </span>
                            </div>
                            <?php endif; ?>
                            <?php if($project->object_area): ?>
                            <div class="info-item">
                                <span class="info-label">Площадь:</span>
                                <span class="info-value"><?php echo e($project->object_area); ?> м²</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if($project->description): ?>
                <hr class="my-4">
                <h6 class="text-muted mb-3">Описание проекта</h6>
                <p class="text-muted"><?php echo e($project->description); ?></p>
                <?php endif; ?>

                <?php if($project->notes): ?>
                <hr class="my-4">
                <h6 class="text-muted mb-3">Дополнительные заметки</h6>
                <p class="text-muted"><?php echo e($project->notes); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Последние события -->
        <?php if($project->events()->count() > 0): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Последние события</h5>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $project->events()->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="event-item">
                    <div class="event-icon">
                        <span class="badge bg-<?php echo e($event->type == 'meeting' ? 'primary' : ($event->type == 'call' ? 'success' : 'info')); ?> rounded-pill">
                            <i class="bi bi-<?php echo e($event->type == 'meeting' ? 'calendar-event' : ($event->type == 'call' ? 'telephone' : 'info-circle')); ?>"></i>
                        </span>
                    </div>
                    <div class="event-content">
                        <h6 class="event-title"><?php echo e($event->title); ?></h6>
                        <p class="event-description"><?php echo e($event->description); ?></p>
                        <small class="event-date">
                            <?php echo e($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d.m.Y') : ''); ?>

                            <?php echo e($event->event_time ? $event->event_time : ''); ?>

                        </small>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Статистика по вкладкам -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Статистика проекта</h5>
            </div>
            <div class="card-body">
                <!-- Файлы и медиа -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3"><i class="bi bi-folder me-1"></i>Файлы и медиа</h6>
                    <div class="row g-2">
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-camera text-primary"></i>
                                <div class="fw-bold"><?php echo e($project->photos()->count()); ?></div>
                                <small class="text-muted">Фото</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-paint-bucket text-info"></i>
                                <div class="fw-bold"><?php echo e($project->designFiles()->count()); ?></div>
                                <small class="text-muted">Дизайн</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-diagram-3 text-warning"></i>
                                <div class="fw-bold"><?php echo e($project->schemes()->count()); ?></div>
                                <small class="text-muted">Схемы</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-file-earmark-text text-secondary"></i>
                                <div class="fw-bold"><?php echo e($project->documents()->count()); ?></div>
                                <small class="text-muted">Документы</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Расписание и задачи -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3"><i class="bi bi-calendar me-1"></i>Расписание</h6>
                    <div class="row g-2">
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-list-task text-primary"></i>
                                <div class="fw-bold"><?php echo e($project->stages()->count()); ?></div>
                                <small class="text-muted">Этапы</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-calendar-event text-success"></i>
                                <div class="fw-bold"><?php echo e($project->events()->count()); ?></div>
                                <small class="text-muted">События</small>
                            </div>
                        </div>
                    </div>
                    <?php
                        $completedStages = $project->stages()->where('status', 'completed')->count();
                        $totalStages = $project->stages()->count();
                        $completedEvents = $project->events()->where('status', 'completed')->count();
                        $totalEvents = $project->events()->count();
                    ?>
                    <?php if($totalStages > 0): ?>
                    <div class="mt-2">
                        <small class="text-muted">Прогресс этапов:</small>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo e($totalStages > 0 ? round(($completedStages / $totalStages) * 100) : 0); ?>%"></div>
                        </div>
                        <small class="text-muted"><?php echo e($completedStages); ?>/<?php echo e($totalStages); ?> завершено</small>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Финансы -->
                <div class="mb-3">
                    <h6 class="text-muted mb-3"><i class="bi bi-wallet me-1"></i>Финансы</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-tools text-primary"></i>
                                <div class="fw-bold"><?php echo e($project->works()->count()); ?></div>
                                <small class="text-muted">Работы</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-box text-info"></i>
                                <div class="fw-bold"><?php echo e($project->materials()->count()); ?></div>
                                <small class="text-muted">Материалы</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-2">
                            <div class="border rounded p-2 text-center">
                                <i class="bi bi-truck text-warning"></i>
                                <div class="fw-bold"><?php echo e($project->transports()->count()); ?></div>
                                <small class="text-muted">Транспорт</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Работы:</span>
                            <strong class="small"><?php echo e(number_format($project->works()->sum('amount'), 0, ',', ' ')); ?> ₽</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Материалы:</span>
                            <strong class="small"><?php echo e(number_format($project->materials()->sum('amount'), 0, ',', ' ')); ?> ₽</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Транспорт:</span>
                            <strong class="small"><?php echo e(number_format($project->transports()->sum('amount'), 0, ',', ' ')); ?> ₽</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <strong>Итого:</strong>
                            <strong class="text-success">
                                <?php echo e(number_format($project->works()->sum('amount') + $project->materials()->sum('amount') + $project->transports()->sum('amount'), 0, ',', ' ')); ?> ₽
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Общий прогресс -->
                <?php
                    $totalItems = $project->photos()->count() + $project->designFiles()->count() + 
                                 $project->schemes()->count() + $project->documents()->count() + 
                                 $project->stages()->count() + $project->events()->count();
                ?>
                <?php if($totalItems > 0): ?>
                <div class="text-center">
                    <small class="text-muted">Всего элементов в проекте: <strong><?php echo e($totalItems); ?></strong></small>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Прогресс проекта -->
        <?php if($project->stages()->count() > 0): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Прогресс этапов</h5>
            </div>
            <div class="card-body">
                <?php
                $totalStages = $project->stages()->count();
                $completedStages = $project->stages()->where('status', 'completed')->count();
                $progressPercent = $totalStages > 0 ? round(($completedStages / $totalStages) * 100) : 0;
                ?>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Завершено этапов</span>
                        <span><?php echo e($completedStages); ?>/<?php echo e($totalStages); ?></span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo e($progressPercent); ?>%">
                            <?php echo e($progressPercent); ?>%
                        </div>
                    </div>
                </div>

                <?php $__currentLoopData = $project->stages()->orderBy('order')->take(3)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-<?php echo e($stage->status == 'completed' ? 'check-circle-fill text-success' : ($stage->status == 'in_progress' ? 'play-circle-fill text-primary' : 'circle text-muted')); ?> me-2"></i>
                    <span class="small"><?php echo e($stage->name); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/main.blade.php ENDPATH**/ ?>