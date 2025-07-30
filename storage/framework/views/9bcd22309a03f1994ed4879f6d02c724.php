<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/mobile-utilities.css')); ?>">
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/home-page.css', 'public/js/home-page.js']); ?>
<style>
/* Современная адаптивная главная страница */
:root {
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-secondary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
    --card-shadow-hover: 0 8px 30px rgba(0,0,0,0.15);
    --border-radius: 12px;
}

.welcome-section .card {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
}

.main-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
}

.main-card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-2px);
}


.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--card-shadow-hover);
}

.stats-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stats-card.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-card.info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.project-card {
    border: 1px solid #e9ecef;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
    overflow: hidden;
}

.project-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
    border-color: #007bff;
}

.quick-access-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: var(--border-radius);
   
    text-decoration: none;
    transition: all 0.3s ease;
}

.quick-access-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--card-shadow-hover);
   
    text-decoration: none;
}

.news-item {
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.news-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.badge-status {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

/* Мобильная адаптация */
@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .welcome-section h1 {
        font-size: 1.5rem !important;
    }
    
    .welcome-section p {
        font-size: 0.9rem;
    }
    
    .display-4 {
        font-size: 2rem !important;
    }
    
    .display-6 {
        font-size: 1.5rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .stats-card .card-body {
        padding: 1.5rem 1rem !important;
    }
    
    .project-card .card-body {
        padding: 0.75rem !important;
    }
    
    .news-item {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .card-header h5 {
        font-size: 1.1rem;
    }
}

/* Адаптация для очень маленьких экранов */
@media (max-width: 576px) {
    .col-md-4, .col-lg-4 {
        margin-bottom: 1rem;
    }
    
    .stats-card .display-6 {
        font-size: 1.25rem !important;
    }
    
    .stats-card h6 {
        font-size: 0.85rem;
    }
}

/* Планшетная адаптация */
@media (min-width: 769px) and (max-width: 1024px) {
    .col-lg-8 {
        flex: 0 0 auto;
        width: 70%;
    }
    
    .col-lg-4 {
        flex: 0 0 auto;
        width: 30%;
    }
}

/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease forwards;
}

.fade-in-up:nth-child(1) { animation-delay: 0.1s; }
.fade-in-up:nth-child(2) { animation-delay: 0.2s; }
.fade-in-up:nth-child(3) { animation-delay: 0.3s; }
.fade-in-up:nth-child(4) { animation-delay: 0.4s; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Приветствие для всех пользователей -->
            <div class="welcome-section mb-4 fade-in-up">
                <div class="card border-0">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="h3 mb-2 ">Добро пожаловать, <?php echo e($user->name); ?>!</h1>
                                <?php if($user->isClient()): ?>
                                    <p class="mb-0 opacity-75 ">Здесь вы можете отслеживать статус ваших проектов и получать актуальную информацию о ходе работ.</p>
                                <?php elseif($user->isPartner()): ?>
                                    <p class="mb-0 opacity-75 ">Управляйте своими проектами, отслеживайте прогресс и получайте последние обновления системы.</p>
                                <?php elseif($user->isEmployee() || $user->isForeman()): ?>
                                    <p class="mb-0 opacity-75 ">Добро пожаловать в рабочую панель. Здесь вы найдете актуальную информацию по проектам и новости системы.</p>
                                <?php elseif($user->isAdmin()): ?>
                                    <p class="mb-0 opacity-75 ">Панель администратора. Управляйте системой и отслеживайте общую статистику.</p>
                                <?php else: ?>
                                    <p class="mb-0 opacity-75 ">Добро пожаловать в систему управления проектами.</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-4 text-end d-none d-lg-block">
                                <i class="bi bi-house-heart-fill display-4 opacity-50 "></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($user->isClient()): ?>
                <!-- Секция для клиентов -->
                <div class="row">
                    <!-- Статистика проектов -->
                    <div class="col-md-4 mb-4 fade-in-up">
                        <div class="card main-card stats-card text-center">
                            <div class="card-body" style="color: #333;">
                                <div class="display-6 mb-2"><?php echo e($projectsCount ?? 0); ?></div>
                                <h6 class="card-title mb-0">Ваших проектов</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Быстрый доступ к последнему объекту -->
                    <?php if(isset($lastProject) && $lastProject): ?>
                    <div class="col-md-8 mb-4 fade-in-up">
                        <a href="<?php echo e(route('partner.projects.show', $lastProject)); ?>" class="quick-access-card card text-decoration-none">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <h5 class="card-title mb-1">
                                            <i class="bi bi-building me-2"></i>
                                            Быстрый доступ к объекту
                                        </h5>
                                        <p class="mb-1"><?php echo e($lastProject->object_type ?? 'Проект'); ?></p>
                                        <small class="opacity-75">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <?php echo e($lastProject->object_city); ?>, <?php echo e($lastProject->object_street); ?>

                                        </small>
                                    </div>
                                    <div class="col-3 text-end">
                                        <i class="bi bi-arrow-right-circle display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="col-md-8 mb-4 fade-in-up">
                        <div class="card main-card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-building display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">У вас пока нет проектов</h5>
                                <p class="text-muted">Свяжитесь с нашими специалистами для создания первого проекта</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Ваши проекты -->
                    <?php if(isset($projects) && $projects->count() > 0): ?>
                    <div class="col-12 mb-4 fade-in-up">
                        <div class="card main-card" >
                            <div class="card-header  border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0" style="color: #333;">
                                            <i class="bi bi-building text-primary me-2"></i>
                                            Ваши проекты
                                        </h5>
                                    </div>
                                    <?php if($projectsCount > 3): ?>
                                    <div class="col-auto" style="color: #333;">
                                        <a href="<?php echo e(route('partner.projects.index')); ?>" class="btn btn-outline-primary btn-sm">
                                            Посмотреть все
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="project-card card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?php echo e($project->object_type ?? 'Проект'); ?></h6>
                                                    <p class="card-text small text-muted mb-2">
                                                        <i class="bi bi-geo-alt me-1"></i>
                                                        <?php echo e($project->object_city); ?>, <?php echo e($project->object_street); ?>

                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="badge badge-status bg-<?php echo e($project->project_status == 'completed' ? 'success' : ($project->project_status == 'in_progress' ? 'warning' : 'secondary')); ?>">
                                                            <?php
                                                                $statusMap = [
                                                                    'new' => 'Новый',
                                                                    'in_progress' => 'В работе',
                                                                    'design' => 'Проектирование',
                                                                    'materials_preparation' => 'Подготовка материалов',
                                                                    'paused' => 'Приостановлен',
                                                                    'completed' => 'Завершен',
                                                                    'cancelled' => 'Отменен'
                                                                ];
                                                            ?>
                                                            <?php echo e($statusMap[$project->project_status] ?? $project->project_status); ?>

                                                        </span>
                                                        <small class="text-muted"><?php echo e($project->created_at->format('d.m.Y')); ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

            <?php elseif($user->isPartner() || $user->isEmployee() || $user->isAdmin()): ?>
                <!-- Секция для партнеров и сотрудников -->
                <div class="row">
                    <!-- Статистика -->
                    <div class="col-12 mb-4 fade-in-up">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="stats-card card text-center">
                                    <div class="card-body">
                                        <div class="display-6 mb-2"><?php echo e($totalProjects ?? 0); ?></div>
                                        <h6 class="card-title mb-0">Всего проектов</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stats-card warning card text-center">
                                    <div class="card-body">
                                        <div class="display-6 mb-2"><?php echo e($activeProjects ?? 0); ?></div>
                                        <h6 class="card-title mb-0">Активных проектов</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stats-card success card text-center">
                                    <div class="card-body">
                                        <div class="display-6 mb-2"><?php echo e($completedProjects ?? 0); ?></div>
                                        <h6 class="card-title mb-0">Завершенных</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Последние проекты -->
                    <div class="col-lg-8 mb-4 fade-in-up">
                        <div class="card main-card">
                            <div class="card-header bg-white border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0" style="color: #333;">
                                            <i class="bi bi-clock-history text-primary me-2"></i>
                                            Последние проекты
                                        </h5>
                                    </div>
                                    <div class="col-auto" style="color: #333;">
                                        <a href="<?php echo e(route('partner.projects.index')); ?>" class="btn btn-outline-primary btn-sm">
                                            Все проекты
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if(isset($recentProjects) && $recentProjects->count() > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php $__currentLoopData = $recentProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="list-group-item border-0 px-0 py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-8">
                                                        <h6 class="mb-1"><?php echo e($project->client_first_name); ?> <?php echo e($project->client_last_name); ?></h6>
                                                        <p class="mb-1 small text-muted"><?php echo e($project->object_type); ?> - <?php echo e($project->work_type); ?></p>
                                                        <small class="text-muted"><?php echo e($project->created_at->format('d.m.Y')); ?></small>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <span class="badge badge-status bg-<?php echo e($project->project_status == 'completed' ? 'success' : ($project->project_status == 'in_progress' ? 'warning' : 'secondary')); ?>">
                                                            <?php
                                                                $statusMap = [
                                                                    'new' => 'Новый',
                                                                    'in_progress' => 'В работе',
                                                                    'design' => 'Проектирование',
                                                                    'materials_preparation' => 'Подготовка материалов',
                                                                    'paused' => 'Приостановлен',
                                                                    'completed' => 'Завершен',
                                                                    'cancelled' => 'Отменен'
                                                                ];
                                                            ?>
                                                            <?php echo e($statusMap[$project->project_status] ?? $project->project_status); ?>

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
                    <div class="col-lg-4 mb-4 fade-in-up">
                        <div class="card main-card">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0" style="color: #333;">
                                    <i class="bi bi-newspaper text-primary me-2"></i>
                                    Нововведения
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if(isset($news)): ?>
                                    <?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="news-item">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <?php if($item['type'] == 'update'): ?>
                                                        <i class="bi bi-arrow-up-circle text-primary"></i>
                                                    <?php elseif($item['type'] == 'feature'): ?>
                                                        <i class="bi bi-star text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-gear text-info"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo e($item['title']); ?></h6>
                                                    <p class="small text-muted mb-1"><?php echo e($item['description']); ?></p>
                                                    <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($item['date'])->format('d.m.Y')); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ -->
                    <div class="col-12 mb-4 fade-in-up">
                        <div class="card main-card">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0" style="color: #333;">
                                    <i class="bi bi-question-circle text-primary me-2"></i>
                                    Вопросы и ответы
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="faqAccordion">
                                    <?php if(isset($faq)): ?>
                                        <?php $__currentLoopData = $faq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="accordion-item border-0 mb-3">
                                                <h2 class="accordion-header" id="heading<?php echo e($index); ?>">
                                                    <button class="accordion-button collapsed bg-light border-0 rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($index); ?>" aria-expanded="false" aria-controls="collapse<?php echo e($index); ?>">
                                                        <?php echo e($item['question']); ?>

                                                    </button>
                                                </h2>
                                                <div id="collapse<?php echo e($index); ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo e($index); ?>" data-bs-parent="#faqAccordion">
                                                    <div class="accordion-body bg-light rounded">
                                                        <?php echo e($item['answer']); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Добавляем touch-friendly возможности
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
        
        // Улучшаем клики по карточкам на мобильных
        document.querySelectorAll('.project-card, .quick-access-card').forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            card.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    }
    
    // Анимация счетчиков
    const counters = document.querySelectorAll('.display-6');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent);
        let current = 0;
        const increment = target / 30;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 50);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/home.blade.php ENDPATH**/ ?>