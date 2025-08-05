<?php if(Auth::check() && Auth::user()->activeSubscription): ?>
    <?php
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription->subscriptionPlan;
        
        $resourceUsage = [
            'active_projects' => [
                'current' => $subscription->current_active_projects ?? 0,
                'limit' => $plan->max_active_projects,
                'percentage' => $plan->max_active_projects > 0 ? (($subscription->current_active_projects ?? 0) / $plan->max_active_projects) * 100 : 0,
            ],
            'employees' => [
                'current' => $subscription->current_employees ?? 0,
                'limit' => $plan->max_employees,
                'percentage' => $plan->max_employees > 0 ? (($subscription->current_employees ?? 0) / $plan->max_employees) * 100 : 0,
            ],
            'right_hand_employees' => [
                'current' => $subscription->current_right_hand_employees ?? 0,
                'limit' => $plan->max_right_hand_employees,
                'percentage' => $plan->max_right_hand_employees > 0 ? (($subscription->current_right_hand_employees ?? 0) / $plan->max_right_hand_employees) * 100 : 0,
            ],
        ];
        
        $nearLimitResources = [];
        $atLimitResources = [];
        
        foreach ($resourceUsage as $resource => $usage) {
            if ($usage['percentage'] >= 100) {
                $atLimitResources[] = $resource;
            } elseif ($usage['percentage'] >= 80) {
                $nearLimitResources[] = $resource;
            }
        }
        
        $resourceNames = [
            'active_projects' => 'активных проектов',
            'employees' => 'сотрудников',
            'right_hand_employees' => 'правых рук',
            'estimate_templates' => 'шаблонов смет',
        ];
    ?>

    <?php if(count($atLimitResources) > 0): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                Лимиты исчерпаны!
            </h6>
            <p class="mb-2">
                Достигнуты лимиты по следующим ресурсам:
            </p>
            <ul class="mb-2">
                <?php $__currentLoopData = $atLimitResources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($resourceNames[$resource] ?? $resource); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <hr>
            <p class="mb-0">
                <a href="<?php echo e(route('subscriptions.index')); ?>" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-arrow-up-circle"></i> Обновить тариф
                </a>
                <a href="<?php echo e(route('subscriptions.manage')); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-gear"></i> Управление подпиской
                </a>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif(count($nearLimitResources) > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
             <h6 class="alert-heading">
                <i class="bi bi-exclamation-triangle"></i> 
                Приближение к лимитам
                 <?php $__currentLoopData = $nearLimitResources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $usage = $resourceUsage[$resource] ?>
                   
                        <?php echo e($resourceNames[$resource] ?? $resource); ?>: 
                        <?php echo e($usage['current']); ?>/<?php echo e($usage['limit']); ?> 
                        (<?php echo e(round($usage['percentage'])); ?>%)
                   
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </h6>
         
         
         
               
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         
            
          
        </div>
    <?php endif; ?>

    <?php if(Auth::user()->activeSubscription->isExpiringSoon()): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="bi bi-calendar-x"></i> 
                Подписка истекает
            </h6>
            <p class="mb-2">
                Ваша подписка "<?php echo e(Auth::user()->activeSubscription->subscriptionPlan->name); ?>" 
                истекает <?php echo e(Auth::user()->activeSubscription->expires_at->format('d.m.Y')); ?>.
            </p>
            <hr>
            <p class="mb-0">
                <a href="<?php echo e(route('subscriptions.manage')); ?>" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-arrow-repeat"></i> Продлить подписку
                </a>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php elseif(Auth::check()): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6 class="alert-heading">
            <i class="bi bi-exclamation-triangle"></i> 
            Нет активной подписки
        </h6>
        <p class="mb-2">
            Для использования всех функций системы необходимо оформить подписку.
        </p>
        <hr>
        <p class="mb-0">
            <a href="<?php echo e(route('subscriptions.index')); ?>" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-credit-card"></i> Выбрать тариф
            </a>
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/components/subscription-alerts.blade.php ENDPATH**/ ?>