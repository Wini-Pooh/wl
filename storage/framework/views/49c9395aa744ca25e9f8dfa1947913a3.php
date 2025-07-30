

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/mobile-projects.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <h2 class="mb-0 fs-mobile-3">
                    <i class="bi bi-building me-2"></i>
                    <?php if(Auth::check() && Auth::user()->isClient()): ?>
                        Мои объекты
                    <?php else: ?>
                        Управление объектами
                    <?php endif; ?>
                </h2>
                <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                <a href="<?php echo e(route('partner.projects.create')); ?>" class="btn btn-primary btn-lg d-md-none w-100">
                    <i class="bi bi-plus-circle me-2"></i>
                    Создать объект
                </a>
                <a href="<?php echo e(route('partner.projects.create')); ?>" class="btn btn-primary d-none d-md-inline-flex">
                    <i class="bi bi-plus-circle me-2"></i>
                    Создать объект
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Фильтры и поиск -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Мобильная кнопка сворачивания фильтров -->
                    <div class="d-md-none mb-3">
                        <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                            <i class="bi bi-funnel me-2"></i>
                            Фильтры и поиск
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </button>
                    </div>
                    
                    <div class="collapse d-md-block" id="filtersCollapse">
                        <form method="GET" action="<?php echo e(route('partner.projects.index')); ?>" id="filterForm">
                            <input type="hidden" name="view_mode" value="<?php echo e($viewMode ?? 'list'); ?>" id="viewModeInput">
                            <div class="row g-3">
                                <!-- Основной поиск - всегда полная ширина на мобильных -->
                                <div class="col-12 col-md-3">
                                    <label for="search" class="form-label fs-mobile-6">Поиск по клиенту/адресу</label>
                                    <input type="text" class="form-control form-control-lg d-md-none" id="search" name="search" 
                                           value="<?php echo e(request('search')); ?>" placeholder="Введите для поиска...">
                                    <input type="text" class="form-control d-none d-md-block" id="search-desktop" name="search" 
                                           value="<?php echo e(request('search')); ?>" placeholder="Введите для поиска...">
                                </div>
                                
                                <!-- Телефон -->
                                <div class="col-12 col-md-3">
                                    <label for="phone" class="form-label fs-mobile-6">Телефон клиента</label>
                                    <input type="text" class="form-control form-control-lg d-md-none" id="phone" name="phone" 
                                           value="<?php echo e(request('phone')); ?>" placeholder="+7 (999) 123-45-67">
                                    <input type="text" class="form-control d-none d-md-block" id="phone-desktop" name="phone" 
                                           value="<?php echo e(request('phone')); ?>" placeholder="+7 (999) 123-45-67">
                                </div>
                                
                                <!-- Статус -->
                                <div class="col-6 col-md-2">
                                    <label for="status" class="form-label fs-mobile-6">Статус</label>
                                    <select class="form-select form-select-lg d-md-none" id="status" name="status">
                                        <option value="">Все статусы</option>
                                        <?php $__currentLoopData = \App\Models\Project::getStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <select class="form-select d-none d-md-block" id="status-desktop" name="status">
                                        <option value="">Все статусы</option>
                                        <?php $__currentLoopData = \App\Models\Project::getStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e(request('status') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <!-- Тип объекта -->
                                <div class="col-6 col-md-2">
                                    <label for="object_type" class="form-label fs-mobile-6">Тип объекта</label>
                                    <select class="form-select form-select-lg d-md-none" id="object_type" name="object_type">
                                        <option value="">Все типы</option>
                                        <?php $__currentLoopData = \App\Models\Project::getObjectTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e(request('object_type') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <select class="form-select d-none d-md-block" id="object_type-desktop" name="object_type">
                                        <option value="">Все типы</option>
                                        <?php $__currentLoopData = \App\Models\Project::getObjectTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e(request('object_type') == $key ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <!-- Кнопки -->
                                <div class="col-12 col-md-2 d-flex align-items-end">
                                    <div class="d-grid d-md-flex gap-2 w-100">
                                        <button type="submit" class="btn btn-primary btn-lg d-md-none">
                                            <i class="bi bi-search me-2"></i> Найти
                                        </button>
                                        <button type="submit" class="btn btn-outline-primary d-none d-md-inline-flex me-2">
                                            <i class="bi bi-search"></i> Поиск
                                        </button>
                                        <a href="<?php echo e(route('partner.projects.index')); ?>" class="btn btn-outline-secondary btn-lg d-md-none">
                                            <i class="bi bi-x-circle me-2"></i> Сбросить
                                        </a>
                                        <a href="<?php echo e(route('partner.projects.index')); ?>" class="btn btn-outline-secondary d-none d-md-inline-flex">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Список проектов -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0 mb-2 mb-md-0">Список объектов (<?php echo e($projects->total()); ?>)</h5>
                    
                    <!-- Переключатель режимов - адаптивный -->
                    <div class="btn-group w-100 w-md-auto" role="group" aria-label="Режим отображения">
                        <input type="radio" class="btn-check" name="viewMode" id="listView" value="list" <?php echo e(($viewMode ?? 'list') === 'list' ? 'checked' : ''); ?>>
                        <label class="btn btn-outline-primary btn-lg d-md-none" for="listView" title="8 объектов на странице">
                            <i class="bi bi-list-ul me-2"></i> Список
                        </label>
                        <label class="btn btn-outline-primary d-none d-md-inline-flex" for="listView" title="8 объектов на странице">
                            <i class="bi bi-list-ul"></i> Список
                            <span class="view-mode-indicator">(8)</span>
                        </label>
                        
                        <input type="radio" class="btn-check" name="viewMode" id="cardView" value="cards" <?php echo e(($viewMode ?? 'list') === 'cards' ? 'checked' : ''); ?>>
                        <label class="btn btn-outline-primary btn-lg d-md-none" for="cardView" title="6 карточек на странице">
                            <i class="bi bi-grid-3x3-gap me-2"></i> Карточки
                        </label>
                        <label class="btn btn-outline-primary d-none d-md-inline-flex" for="cardView" title="6 карточек на странице">
                            <i class="bi bi-grid-3x3-gap"></i> Карточки
                            <span class="view-mode-indicator">(6)</span>
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($projects->count() > 0): ?>
                        <!-- Табличный вид -->
                        <div id="listViewContent" class="view-content">
                            <!-- Мобильные карточки для списка -->
                            <div class="d-md-none">
                                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="card mb-3 mobile-project-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 text-primary">
                                                    <i class="bi bi-building me-2"></i>
                                                    #<?php echo e($project->id); ?> <?php echo e($project->client_full_name); ?>

                                                </h6>
                                                <?php
                                                    $statusClass = match($project->project_status) {
                                                        'draft' => 'bg-secondary',
                                                        'in_progress' => 'bg-warning text-dark',
                                                        'completed' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>
                                                <span class="badge <?php echo e($statusClass); ?> fs-mobile-xs">
                                                    <?php echo e(\App\Models\Project::getStatuses()[$project->project_status] ?? $project->project_status); ?>

                                                </span>
                                            </div>
                                            
                                            <!-- Основная информация в две колонки -->
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Телефон:</small>
                                                    <a href="tel:<?php echo e($project->client_phone); ?>" class="text-decoration-none fw-bold">
                                                        <?php echo e($project->client_phone); ?>

                                                    </a>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Тип:</small>
                                                    <span class="badge bg-info fs-mobile-xs">
                                                        <?php echo e(\App\Models\Project::getObjectTypes()[$project->object_type] ?? $project->object_type); ?>

                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <?php if($project->object_full_address): ?>
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-geo-alt me-1"></i>
                                                        <?php echo e($project->object_full_address); ?>

                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <small class="text-muted d-block">Стоимость:</small>
                                                    <?php if($project->total_cost > 0): ?>
                                                        <strong class="text-success"><?php echo e(number_format($project->total_cost, 0, ',', ' ')); ?> ₽</strong>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block">Создан:</small>
                                                    <small><?php echo e($project->created_at->format('d.m.Y')); ?></small>
                                                </div>
                                            </div>
                                            
                                            <!-- Действия -->
                                            <div class="d-grid gap-2">
                                                <a href="<?php echo e(route('partner.projects.show', $project)); ?>" 
                                                   class="btn btn-outline-info btn-lg">
                                                    <i class="bi bi-eye me-2"></i>
                                                    Просмотр объекта
                                                </a>
                                                <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                                                <div class="btn-group w-100" role="group">
                                                    <a href="<?php echo e(route('partner.projects.edit', $project)); ?>" 
                                                       class="btn btn-outline-warning btn-lg">
                                                        <i class="bi bi-pencil me-2"></i>
                                                        Редактировать
                                                    </a>
                                                    <button class="btn btn-outline-danger btn-lg" 
                                                            onclick="confirmDelete(<?php echo e($project->id); ?>)">
                                                        <i class="bi bi-trash me-2"></i>
                                                        Удалить
                                                    </button>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            
                            <!-- Десктопная таблица -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Клиент</th>
                                            <th>Телефон</th>
                                            <th>Тип объекта</th>
                                            <th>Адрес</th>
                                            <th>Статус</th>
                                            <th>Общая стоимость</th>
                                            <th>Дата создания</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><strong>#<?php echo e($project->id); ?></strong></td>
                                                <td>
                                                    <div><?php echo e($project->client_full_name); ?></div>
                                                    <?php if($project->client_email): ?>
                                                        <small class="text-muted"><?php echo e($project->client_email); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="tel:<?php echo e($project->client_phone); ?>" class="text-decoration-none">
                                                        <?php echo e($project->client_phone); ?>

                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo e(\App\Models\Project::getObjectTypes()[$project->object_type] ?? $project->object_type); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($project->object_full_address): ?>
                                                        <small><?php echo e($project->object_full_address); ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">Не указан</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $statusClass = match($project->project_status) {
                                                            'draft' => 'bg-secondary',
                                                            'in_progress' => 'bg-warning text-dark',
                                                            'completed' => 'bg-success',
                                                            'cancelled' => 'bg-danger',
                                                            default => 'bg-secondary'
                                                        };
                                                    ?>
                                                    <span class="badge <?php echo e($statusClass); ?>">
                                                        <?php echo e(\App\Models\Project::getStatuses()[$project->project_status] ?? $project->project_status); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($project->total_cost > 0): ?>
                                                        <strong class="text-success"><?php echo e(number_format($project->total_cost, 0, ',', ' ')); ?> ₽</strong>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($project->created_at->format('d.m.Y')); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?php echo e(route('partner.projects.show', $project)); ?>" 
                                                           class="btn btn-sm btn-outline-info" title="Просмотр">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                                                        <a href="<?php echo e(route('partner.projects.edit', $project)); ?>" 
                                                           class="btn btn-sm btn-outline-warning" title="Редактировать">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="confirmDelete(<?php echo e($project->id); ?>)" title="Удалить">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Карточный вид -->
                        <div id="cardViewContent" class="view-content" style="display: none;">
                            <div class="row g-3 g-md-4">
                                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="card h-100 project-card shadow-sm">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-primary fs-mobile-6">
                                                    <i class="bi bi-building me-2"></i>
                                                    Объект #<?php echo e($project->id); ?>

                                                </h6>
                                                <?php
                                                    $statusClass = match($project->project_status) {
                                                        'draft' => 'bg-secondary',
                                                        'in_progress' => 'bg-warning text-dark',
                                                        'completed' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>
                                                <span class="badge <?php echo e($statusClass); ?> fs-mobile-xs">
                                                    <?php echo e(\App\Models\Project::getStatuses()[$project->project_status] ?? $project->project_status); ?>

                                                </span>
                                            </div>
                                            <div class="card-body p-mobile-2">
                                                <!-- Информация о клиенте -->
                                                <div class="mb-3">
                                                    <h6 class="text-dark fs-mobile-5">
                                                        <i class="bi bi-person me-2"></i>
                                                        <?php echo e($project->client_full_name); ?>

                                                    </h6>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-telephone me-2"></i>
                                                        <a href="tel:<?php echo e($project->client_phone); ?>" class="text-decoration-none">
                                                            <?php echo e($project->client_phone); ?>

                                                        </a>
                                                    </div>
                                                    <?php if($project->client_email): ?>
                                                        <div class="small text-muted">
                                                            <i class="bi bi-envelope me-2"></i>
                                                            <?php echo e($project->client_email); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Тип объекта -->
                                                <div class="mb-3">
                                                    <span class="badge bg-info fs-mobile-xs">
                                                        <?php echo e(\App\Models\Project::getObjectTypes()[$project->object_type] ?? $project->object_type); ?>

                                                    </span>
                                                </div>

                                                <!-- Адрес -->
                                                <div class="mb-3">
                                                    <small class="text-muted fs-mobile-xs">
                                                        <i class="bi bi-geo-alt me-2"></i>
                                                        <?php if($project->object_full_address): ?>
                                                            <?php echo e($project->object_full_address); ?>

                                                        <?php else: ?>
                                                            Адрес не указан
                                                        <?php endif; ?>
                                                    </small>
                                                </div>

                                                <!-- Стоимость -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">Общая стоимость:</small>
                                                        <?php if($project->total_cost > 0): ?>
                                                            <strong class="text-success"><?php echo e(number_format($project->total_cost, 0, ',', ' ')); ?> ₽</strong>
                                                        <?php else: ?>
                                                            <span class="text-muted">—</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Дата создания -->
                                                <div class="mb-3">
                                                    <small class="text-muted fs-mobile-xs">
                                                        <i class="bi bi-calendar me-2"></i>
                                                        Создан: <?php echo e($project->created_at->format('d.m.Y')); ?>

                                                    </small>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <!-- Мобильная версия кнопок -->
                                                <div class="d-md-none d-grid gap-2">
                                                    <a href="<?php echo e(route('partner.projects.show', $project)); ?>" 
                                                       class="btn btn-outline-info btn-lg">
                                                        <i class="bi bi-eye me-2"></i>
                                                        Просмотр
                                                    </a>
                                                    <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                                                    <div class="btn-group w-100" role="group">
                                                        <a href="<?php echo e(route('partner.projects.edit', $project)); ?>" 
                                                           class="btn btn-outline-warning btn-lg">
                                                            <i class="bi bi-pencil me-2"></i>
                                                            Редактировать
                                                        </a>
                                                        <button class="btn btn-outline-danger btn-lg" 
                                                                onclick="confirmDelete(<?php echo e($project->id); ?>)">
                                                            <i class="bi bi-trash me-2"></i>
                                                            Удалить
                                                        </button>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Десктопная версия кнопок -->
                                                <div class="d-none d-md-flex justify-content-between">
                                                    <a href="<?php echo e(route('partner.projects.show', $project)); ?>" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye me-1"></i>
                                                        Просмотр
                                                    </a>
                                                    <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?php echo e(route('partner.projects.edit', $project)); ?>" 
                                                           class="btn btn-sm btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="confirmDelete(<?php echo e($project->id); ?>)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        
                        <!-- Пагинация с улучшенным дизайном -->
                        <div class="mt-4">
                            <?php echo e($projects->appends(request()->query())->links('custom.pagination')); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-building" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted fs-mobile-4">Объекты не найдены</h5>
                            <p class="text-muted fs-mobile-6">Создайте первый объект или измените параметры поиска</p>
                            <?php if(\App\Helpers\UserRoleHelper::canManageProjects()): ?>
                            <a href="<?php echo e(route('partner.projects.create')); ?>" class="btn btn-primary btn-lg d-md-none">
                                <i class="bi bi-plus-circle me-2"></i>
                                Создать объект
                            </a>
                            <a href="<?php echo e(route('partner.projects.create')); ?>" class="btn btn-primary d-none d-md-inline-flex">
                                <i class="bi bi-plus-circle me-2"></i>
                                Создать объект
                            </a>
                            <?php endif; ?>
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
                Вы уверены, что хотите удалить этот объект? Это действие нельзя отменить.
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

<script>
function confirmDelete(projectId) {
    const form = document.getElementById('deleteForm');
    form.action = `/partner/projects/${projectId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Переключение между режимами просмотра
document.addEventListener('DOMContentLoaded', function() {
    const listViewRadio = document.getElementById('listView');
    const cardViewRadio = document.getElementById('cardView');
    const listViewContent = document.getElementById('listViewContent');
    const cardViewContent = document.getElementById('cardViewContent');
    const viewModeInput = document.getElementById('viewModeInput');
    const filterForm = document.getElementById('filterForm');
    
    // Загружаем сохраненный режим из localStorage или из серверного состояния
    const savedViewMode = localStorage.getItem('projectsViewMode') || '<?php echo e($viewMode ?? "list"); ?>';
    
    function updateViewMode(mode) {
        if (mode === 'cards') {
            cardViewRadio.checked = true;
            listViewContent.style.display = 'none';
            cardViewContent.style.display = 'block';
            viewModeInput.value = 'cards';
        } else {
            listViewRadio.checked = true;
            listViewContent.style.display = 'block';
            cardViewContent.style.display = 'none';
            viewModeInput.value = 'list';
        }
        localStorage.setItem('projectsViewMode', mode);
    }
    
    // Устанавливаем начальное состояние
    updateViewMode(savedViewMode);
    
    // Обработчики переключения
    listViewRadio.addEventListener('change', function() {
        if (this.checked) {
            updateViewMode('list');
            // Отправляем форму для перезагрузки с новой пагинацией
            filterForm.submit();
        }
    });
    
    cardViewRadio.addEventListener('change', function() {
        if (this.checked) {
            updateViewMode('cards');
            // Отправляем форму для перезагрузки с новой пагинацией
            filterForm.submit();
        }
    });

    // Синхронизация значений между мобильными и десктопными полями
    function syncFormFields() {
        // Поиск
        const searchMobile = document.getElementById('search');
        const searchDesktop = document.getElementById('search-desktop');
        if (searchMobile && searchDesktop) {
            searchMobile.addEventListener('input', () => {
                searchDesktop.value = searchMobile.value;
            });
            searchDesktop.addEventListener('input', () => {
                searchMobile.value = searchDesktop.value;
            });
        }

        // Телефон
        const phoneMobile = document.getElementById('phone');
        const phoneDesktop = document.getElementById('phone-desktop');
        if (phoneMobile && phoneDesktop) {
            phoneMobile.addEventListener('input', () => {
                phoneDesktop.value = phoneMobile.value;
            });
            phoneDesktop.addEventListener('input', () => {
                phoneMobile.value = phoneDesktop.value;
            });
        }

        // Статус
        const statusMobile = document.getElementById('status');
        const statusDesktop = document.getElementById('status-desktop');
        if (statusMobile && statusDesktop) {
            statusMobile.addEventListener('change', () => {
                statusDesktop.value = statusMobile.value;
            });
            statusDesktop.addEventListener('change', () => {
                statusMobile.value = statusDesktop.value;
            });
        }

        // Тип объекта
        const objectTypeMobile = document.getElementById('object_type');
        const objectTypeDesktop = document.getElementById('object_type-desktop');
        if (objectTypeMobile && objectTypeDesktop) {
            objectTypeMobile.addEventListener('change', () => {
                objectTypeDesktop.value = objectTypeMobile.value;
            });
            objectTypeDesktop.addEventListener('change', () => {
                objectTypeMobile.value = objectTypeDesktop.value;
            });
        }
    }

    syncFormFields();

    // Автоматическое скрытие коллапса фильтров после поиска на мобильных
    const filterSubmitBtn = document.querySelector('#filterForm button[type="submit"]');
    if (filterSubmitBtn && window.innerWidth <= 768) {
        filterSubmitBtn.addEventListener('click', () => {
            setTimeout(() => {
                const filtersCollapse = document.getElementById('filtersCollapse');
                if (filtersCollapse) {
                    const bsCollapse = bootstrap.Collapse.getInstance(filtersCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            }, 100);
        });
    }

    // Touch-friendly улучшения
    if ('ontouchstart' in window) {
        // Добавляем активные состояния для touch-устройств
        document.querySelectorAll('.btn, .card').forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            element.addEventListener('touchend', function() {
                this.style.transform = 'scale(1)';
            });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/index.blade.php ENDPATH**/ ?>