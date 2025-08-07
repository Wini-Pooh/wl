

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Тарифные планы</h4>
                </div>
                <div class="card-body">
                 

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Характеристика</th>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th scope="col" class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'bg-primary' : ''); ?>">
                                            <?php echo e($plan->name); ?>

                                            <?php if($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id): ?>
                                                <br><small class="badge bg-light text-dark">Текущий</small>
                                            <?php endif; ?>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Стоимость -->
                                <tr>
                                    <td><strong>Стоимость в месяц</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->monthly_price > 0): ?>
                                                <div class="h5 text-primary mb-1">
                                                    <?php echo e(number_format($plan->monthly_price, 0, ',', ' ')); ?> ₽
                                                </div>
                                            <?php else: ?>
                                                <div class="h5 text-success mb-1">
                                                    Бесплатно
                                                </div>
                                            <?php endif; ?>
                                            <?php if($plan->yearly_price > 0): ?>
                                                <small class="text-muted">
                                                    <?php echo e(number_format($plan->yearly_price, 0, ',', ' ')); ?> ₽/год<br>
                                                    <span class="text-success">скидка <?php echo e($plan->yearly_discount_percent); ?>%</span>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Активные проекты -->
                                <tr>
                                    <td><strong>Активных проектов</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <span class="badge bg-success fs-6"><?php echo e($plan->max_active_projects); ?></span>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Хранилище -->
                                <tr>
                                    <td><strong>Хранилище на проект</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <span class="badge bg-info fs-6"><?php echo e($plan->project_storage_limit_mb); ?> МБ</span>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Шаблоны смет -->
                                <tr>
                                    <td><strong>Шаблонов смет</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->max_estimate_templates > 0): ?>
                                                <span class="badge bg-warning fs-6"><?php echo e($plan->max_estimate_templates); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success fs-6">Неограниченно</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Сотрудники -->
                                <tr>
                                    <td><strong>Сотрудников</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <span class="badge bg-secondary fs-6"><?php echo e($plan->max_employees); ?></span>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Правые руки -->
                                <tr>
                                    <td><strong>Правых рук</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <span class="badge bg-secondary fs-6"><?php echo e($plan->max_right_hand_employees); ?></span>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Доступ к сметам -->
                                <tr>
                                    <td><strong>Доступ к сметам</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->access_estimates): ?>
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Доступ к документам -->
                                <tr>
                                    <td><strong>Доступ к документам</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->access_documents): ?>
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Доступ к проектам -->
                                <tr>
                                    <td><strong>Доступ к проектам</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->access_projects): ?>
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Аналитика -->
                                <tr>
                                    <td><strong>Аналитика</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->access_analytics): ?>
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Управление сотрудниками -->
                                <tr>
                                    <td><strong>Управление сотрудниками</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->access_employees): ?>
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Онлайн обучение -->
                                <tr>
                                    <td><strong>Онлайн обучение</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($plan->access_online_training): ?>
                                                <i class="bi bi-check-circle text-success fs-4"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-danger fs-4"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                
                                <!-- Действия -->
                                <tr class="table-light">
                                    <td><strong>Действие</strong></td>
                                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center <?php echo e($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'table-primary' : ''); ?>">
                                            <?php if($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id): ?>
                                                <button class="btn btn-outline-primary btn-sm" disabled>
                                                    Активный план
                                                </button>
                                            <?php elseif($currentSubscription): ?>
                                                <a href="<?php echo e(route('subscriptions.select-period', $plan)); ?>" class="btn btn-primary btn-sm">
                                                    Изменить план
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('subscriptions.select-period', $plan)); ?>" class="btn btn-primary btn-sm">
                                                    Выбрать план
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-light">
                                <h6><i class="bi bi-info-circle"></i> Дополнительная информация</h6>
                                <ul class="mb-0">
                                    <li>Тестовый план автоматически назначается новым пользователям</li>
                                    <li>Все планы включают полный доступ к базовому функционалу системы</li>
                                    <li>Годовая подписка предоставляет скидку 15%</li>
                                    <li>Возможность смены тарифного плана в любое время</li>
                                    <li>В дальнейшем планируется добавление нового функционала</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/subscriptions/index.blade.php ENDPATH**/ ?>