<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Приветствие для всех пользователей -->
            <div class="welcome-section mb-4">
                <div class="card border-0 bg-gradient-primary ">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="h3 mb-2">Добро пожаловать, <?php echo e($user->name); ?>!</h1>
                                <?php if($user->isClient()): ?>
                                    <p class="mb-0 opacity-75">Здесь вы можете отслеживать статус ваших проектов и получать актуальную информацию о ходе работ.</p>
                                <?php elseif($user->isPartner()): ?>
                                    <p class="mb-0 opacity-75">Управляйте своими проектами, отслеживайте прогресс и получайте последние обновления системы.</p>
                                <?php elseif($user->isEmployee() || $user->isForeman()): ?>
                                    <p class="mb-0 opacity-75">Добро пожаловать в рабочую панель. Здесь вы найдете актуальную информацию по проектам и новости системы.</p>
                                <?php elseif($user->isAdmin()): ?>
                                    <p class="mb-0 opacity-75">Панель администратора. Управляйте системой и отслеживайте общую статистику.</p>
                                <?php else: ?>
                                    <p class="mb-0 opacity-75">Добро пожаловать в систему управления проектами.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-4 text-end">
                                <i class="bi bi-house-heart-fill display-4 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($user->isClient()): ?>
                <!-- Секция для клиентов -->
                <div class="row">
                    <!-- Статистика проектов -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="display-6 text-primary mb-2"><?php echo e($projectsCount); ?></div>
                                <h6 class="card-title text-muted">Ваших проектов</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Быстрый доступ к проектам -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-building text-primary me-2"></i>
                                    Ваши проекты
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if($projects->count() > 0): ?>
                                    <div class="row">
                                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border project-card h-100">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title"><?php echo e($project->object_type); ?></h6>
                                                        <p class="card-text small text-muted mb-2">
                                                            <i class="bi bi-geo-alt me-1"></i>
                                                            <?php echo e($project->object_city); ?>, <?php echo e($project->object_street); ?>

                                                        </p>
                                                        <span class="badge bg-<?php echo e($project->project_status == 'completed' ? 'success' : ($project->project_status == 'in_progress' ? 'warning' : 'secondary')); ?>">
                                                            <?php echo e(ucfirst(str_replace('_', ' ', $project->project_status))); ?>

                                                        </span>
                                                        <div class="mt-2">
                                                            <small class="text-muted"><?php echo e($project->created_at->format('d.m.Y')); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php if($projectsCount > 3): ?>
                                        <div class="text-center mt-3">
                                            <a href="#" class="btn btn-outline-primary">Посмотреть все проекты</a>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-building display-4 text-muted mb-3"></i>
                                        <p class="text-muted">У вас пока нет проектов</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif($user->isPartner() || $user->isEmployee() || $user->isAdmin()): ?>
                <!-- Секция для партнеров и сотрудников -->
                <div class="row">
                    <!-- Статистика -->
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm bg-primary ">
                                    <div class="card-body text-center">
                                        <div class="display-6 mb-2"><?php echo e($totalProjects); ?></div>
                                        <h6 class="card-title">Всего проектов</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm bg-warning ">
                                    <div class="card-body text-center">
                                        <div class="display-6 mb-2"><?php echo e($activeProjects); ?></div>
                                        <h6 class="card-title">Активных проектов</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm bg-success ">
                                    <div class="card-body text-center">
                                        <div class="display-6 mb-2"><?php echo e($totalProjects - $activeProjects); ?></div>
                                        <h6 class="card-title">Завершенных</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Последние проекты -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock-history text-primary me-2"></i>
                                    Последние проекты
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if($recentProjects->count() > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php $__currentLoopData = $recentProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="list-group-item border-0 px-0">
                                                <div class="row align-items-center">
                                                    <div class="col-8">
                                                        <h6 class="mb-1"><?php echo e($project->client_first_name); ?> <?php echo e($project->client_last_name); ?></h6>
                                                        <p class="mb-1 small text-muted"><?php echo e($project->object_type); ?> - <?php echo e($project->work_type); ?></p>
                                                        <small class="text-muted"><?php echo e($project->created_at->format('d.m.Y')); ?></small>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <span class="badge bg-<?php echo e($project->project_status == 'completed' ? 'success' : ($project->project_status == 'in_progress' ? 'warning' : 'secondary')); ?>">
                                                            <?php echo e(ucfirst(str_replace('_', ' ', $project->project_status))); ?>

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-folder display-4 text-muted mb-3"></i>
                                        <p class="text-muted">Проекты не найдены</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Новости и обновления -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-newspaper text-primary me-2"></i>
                                    Новости и обновления
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="news-item mb-3 pb-3 <?php if(!$loop->last): ?> border-bottom <?php endif; ?>">
                                        <div class="d-flex align-items-start">
                                            <div class="news-icon me-2">
                                                <?php if($item['type'] == 'update'): ?>
                                                    <i class="bi bi-arrow-up-circle text-primary"></i>
                                                <?php elseif($item['type'] == 'feature'): ?>
                                                    <i class="bi bi-star text-warning"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-gear text-info"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="news-title mb-1"><?php echo e($item['title']); ?></h6>
                                                <p class="news-description small text-muted mb-1"><?php echo e($item['description']); ?></p>
                                                <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($item['date'])->format('d.m.Y')); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ -->
                    <div class="col-lg-12 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-question-circle text-primary me-2"></i>
                                    Часто задаваемые вопросы
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="faqAccordion">
                                    <?php $__currentLoopData = $faq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="accordion-item border-0 mb-3">
                                            <h2 class="accordion-header" id="heading<?php echo e($index); ?>">
                                                <button class="accordion-button collapsed bg-light border-0 rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($index); ?>" aria-expanded="false" aria-controls="collapse<?php echo e($index); ?>">
                                                    <?php echo e($item['question']); ?>

                                                </button>
                                            </h2>
                                            <div id="collapse<?php echo e($index); ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo e($index); ?>" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body bg-light">
                                                    <?php echo e($item['answer']); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.project-card {
    transition: transform 0.2s;
    cursor: pointer;
}

.project-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.news-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #495057;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.welcome-section .card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .display-6 {
        font-size: 1.5rem;
    }
    
    .welcome-section h1 {
        font-size: 1.5rem;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/home.blade.php ENDPATH**/ ?>