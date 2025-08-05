<?php if(Auth::check() && Auth::user()->activeSubscription): ?>
    <?php
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription->subscriptionPlan;
        
        $resourceUsage = [
            'active_projects' => [
                'current' => $subscription->current_active_projects ?? 0,
                'limit' => $plan->max_active_projects,
                'name' => 'Проекты',
                'icon' => 'bi-folder',
            ],
            'employees' => [
                'current' => $subscription->current_employees ?? 0,
                'limit' => $plan->max_employees,
                'name' => 'Сотрудники',
                'icon' => 'bi-people',
            ],
        ];
    ?>

    <div class="sidebar-limits">
        <div class="sidebar-section">
            <h6 class="sidebar-section-title">
                <i class="bi bi-speedometer2"></i>
                <span>Лимиты тарифа</span>
            </h6>
            
            <?php $__currentLoopData = $resourceUsage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource => $usage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $percentage = $usage['limit'] > 0 ? ($usage['current'] / $usage['limit']) * 100 : 0;
                    $isNearLimit = $percentage >= 80;
                    $isAtLimit = $percentage >= 100;
                ?>
                
                <div class="limit-item <?php echo e($isAtLimit ? 'limit-exceeded' : ($isNearLimit ? 'limit-warning' : '')); ?>">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="limit-info">
                            <i class="bi <?php echo e($usage['icon']); ?> text-muted"></i>
                            <span class="limit-name"><?php echo e($usage['name']); ?></span>
                        </div>
                        <div class="limit-count">
                            <span class="current"><?php echo e($usage['current']); ?></span>
                            <span class="separator">/</span>
                            <span class="total"><?php echo e($usage['limit']); ?></span>
                        </div>
                    </div>
                    <div class="progress limit-progress">
                        <div class="progress-bar 
                            <?php if($isAtLimit): ?> bg-danger
                            <?php elseif($isNearLimit): ?> bg-warning
                            <?php else: ?> bg-success <?php endif; ?>" 
                            style="width: <?php echo e(min(100, $percentage)); ?>%">
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <div class="sidebar-plan-info">
                <div class="plan-name"><?php echo e($plan->name); ?></div>
                <div class="plan-expires">
                    до <?php echo e($subscription->expires_at->format('d.m.Y')); ?>

                </div>
                <a href="<?php echo e(route('subscriptions.manage')); ?>" class="btn btn-sm btn-outline-primary w-100 mt-2">
                    <i class="bi bi-gear"></i> Управление
                </a>
            </div>
        </div>
    </div>

    <style>
    .sidebar-limits {
        margin-top: 1rem;
        padding: 0 1rem;
    }

    .sidebar-section-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .limit-item {
        margin-bottom: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    .limit-item.limit-warning {
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }

    .limit-item.limit-exceeded {
        background-color: #f8d7da;
        border-color: #f5c2c7;
    }

    .limit-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .limit-name {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .limit-count {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .separator {
        color: #6c757d;
        margin: 0 0.2rem;
    }

    .limit-progress {
        height: 4px;
        margin-top: 0.5rem;
        border-radius: 2px;
    }

    .sidebar-plan-info {
        margin-top: 1rem;
        padding: 0.75rem;
        background-color: #e7f3ff;
        border-radius: 0.375rem;
        text-align: center;
    }

    .plan-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0d6efd;
    }

    .plan-expires {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    /* Адаптация для свернутой боковой панели */
    body.sidebar-collapsed .sidebar-limits {
        display: none;
    }
    </style>
<?php endif; ?>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/components/subscription-limits.blade.php ENDPATH**/ ?>