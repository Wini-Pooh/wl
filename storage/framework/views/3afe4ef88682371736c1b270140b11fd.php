

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-person me-2"></i>
                    <?php echo e($employee->full_name); ?>

                    <span class="badge bg-info ms-2"><?php echo e($employee->role_name); ?></span>
                    <?php if($employee->status === 'active'): ?>
                        <span class="badge bg-success ms-1"><?php echo e($employee->status_name); ?></span>
                    <?php elseif($employee->status === 'inactive'): ?>
                        <span class="badge bg-warning ms-1"><?php echo e($employee->status_name); ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger ms-1"><?php echo e($employee->status_name); ?></span>
                    <?php endif; ?>
                </h2>
                <div class="btn-group">
                    <a href="<?php echo e(route('partner.employees.index')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        К списку
                    </a>
                    <a href="<?php echo e(route('partner.employees.edit', $employee)); ?>" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>
                        Редактировать
                    </a>
                    <button class="btn btn-success" onclick="openAddFinanceModal()">
                        <i class="bi bi-plus-circle me-2"></i>
                        Добавить платеж
                    </button>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>
                            Действия
                        </button>
                        <ul class="dropdown-menu">
                           
                            <li><a class="dropdown-item" href="#" onclick="markAllOverdue()">
                                <i class="bi bi-clock me-2"></i>Проверить просрочки
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-warning" href="<?php echo e(route('partner.employees.edit', $employee)); ?>">
                                <i class="bi bi-pencil me-2"></i>Редактировать
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Основная информация -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-person-badge me-2"></i>
                                Персональные данные
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>ФИО:</strong><br>
                                <?php echo e($employee->full_name); ?>

                            </div>
                            <div class="mb-3">
                                <strong>Телефон:</strong><br>
                                <a href="tel:<?php echo e($employee->phone); ?>" class="text-decoration-none">
                                    <?php echo e($employee->phone); ?>

                                </a>
                            </div>
                            <?php if($employee->email): ?>
                                <div class="mb-3">
                                    <strong>Email:</strong><br>
                                    <a href="mailto:<?php echo e($employee->email); ?>" class="text-decoration-none">
                                        <?php echo e($employee->email); ?>

                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <strong>Роль:</strong><br>
                                <span class="badge bg-info"><?php echo e($employee->role_name); ?></span>
                            </div>
                            <div class="mb-3">
                                <strong>Статус:</strong><br>
                                <?php if($employee->status === 'active'): ?>
                                    <span class="badge bg-success"><?php echo e($employee->status_name); ?></span>
                                <?php elseif($employee->status === 'inactive'): ?>
                                    <span class="badge bg-warning"><?php echo e($employee->status_name); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?php echo e($employee->status_name); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if($employee->description): ?>
                                <div class="mb-3">
                                    <strong>Обязанности:</strong><br>
                                    <p class="text-muted small mb-0"><?php echo e($employee->description); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if($employee->notes): ?>
                                <div class="mb-3">
                                    <strong>Заметки:</strong><br>
                                    <p class="text-muted small mb-0"><?php echo e($employee->notes); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Финансовая сводка -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-cash-stack me-2"></i>
                                Финансовая сводка
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-warning mb-1">
                                            <i class="bi bi-clock-fill"></i>
                                        </div>
                                        <h6 class="text-warning mb-1">К выплате</h6>
                                        <h5 class="mb-0"><?php echo e(number_format($financeStats['total_pending'], 0, ',', ' ')); ?> ₽</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-success mb-1">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                        <h6 class="text-success mb-1">Выплачено</h6>
                                        <h5 class="mb-0"><?php echo e(number_format($financeStats['total_paid'], 0, ',', ' ')); ?> ₽</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-danger mb-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                        <h6 class="text-danger mb-1">Просрочено</h6>
                                        <h5 class="mb-0"><?php echo e(number_format($financeStats['overdue'], 0, ',', ' ')); ?> ₽</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-info mb-1">
                                            <i class="bi bi-calendar-week-fill"></i>
                                        </div>
                                        <h6 class="text-info mb-1">На неделе</h6>
                                        <h5 class="mb-0"><?php echo e(number_format($financeStats['upcoming'], 0, ',', ' ')); ?> ₽</h5>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if($financeStats['total_pending'] > 0): ?>
                                <div class="mt-3">
                                    <div class="progress" style="height: 20px;">
                                        <?php
                                            $total = $financeStats['total_pending'] + $financeStats['total_paid'];
                                            $paidPercent = $total > 0 ? ($financeStats['total_paid'] / $total) * 100 : 0;
                                            $overduePercent = $total > 0 ? ($financeStats['overdue'] / $total) * 100 : 0;
                                            $pendingPercent = 100 - $paidPercent - $overduePercent;
                                        ?>
                                        <div class="progress-bar bg-success" style="width: <?php echo e($paidPercent); ?>%" 
                                             title="Выплачено: <?php echo e(number_format($paidPercent, 1)); ?>%"></div>
                                        <div class="progress-bar bg-warning" style="width: <?php echo e($pendingPercent); ?>%" 
                                             title="К выплате: <?php echo e(number_format($pendingPercent, 1)); ?>%"></div>
                                        <div class="progress-bar bg-danger" style="width: <?php echo e($overduePercent); ?>%" 
                                             title="Просрочено: <?php echo e(number_format($overduePercent, 1)); ?>%"></div>
                                    </div>
                                    <div class="small text-muted mt-1 text-center">
                                        Соотношение: выплачено / к выплате / просрочено
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Финансовые записи -->
                <div class="col-lg-8">
                    <!-- Навигация по вкладкам -->
                    <ul class="nav nav-tabs mb-4" id="employeeTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="finances-tab" data-bs-toggle="tab" href="#finances">
                                <i class="bi bi-cash-stack me-2"></i>Финансы
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="upcoming-tab" data-bs-toggle="tab" href="#upcoming">
                                <i class="bi bi-calendar-event me-2"></i>Предстоящие платежи
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="projects-tab" data-bs-toggle="tab" href="#projects">
                                <i class="bi bi-building me-2"></i>Проекты
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Вкладка "Финансы" -->
                        <div class="tab-pane fade show active" id="finances">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Финансовые записи</h6>
                                    <button class="btn btn-sm btn-primary" onclick="openAddFinanceModal()">
                                        <i class="bi bi-plus"></i> Добавить
                                    </button>
                                </div>
                                <div class="card-body">
                                    <?php if($employee->finances->count() > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Дата</th>
                                                        <th>Тип</th>
                                                        <th>Описание</th>
                                                        <th>Сумма</th>
                                                        <th>Статус</th>
                                                        <th>Действия</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $employee->finances->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $finance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($finance->due_date->format('d.m.Y')); ?></td>
                                                            <td>
                                                                <span class="badge bg-secondary small"><?php echo e($finance->type_name); ?></span>
                                                            </td>
                                                            <td>
                                                                <div><?php echo e($finance->title); ?></div>
                                                                <?php if($finance->project): ?>
                                                                    <small class="text-muted">
                                                                        <i class="bi bi-building"></i> <?php echo e($finance->project->name); ?>

                                                                    </small>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <strong><?php echo e(number_format($finance->amount, 0, ',', ' ')); ?> ₽</strong>
                                                            </td>
                                                            <td>
                                                                <?php if($finance->status === 'paid'): ?>
                                                                    <span class="badge bg-success"><?php echo e($finance->status_name); ?></span>
                                                                    <?php if($finance->paid_date): ?>
                                                                        <br><small class="text-muted"><?php echo e($finance->paid_date->format('d.m.Y')); ?></small>
                                                                    <?php endif; ?>
                                                                <?php elseif($finance->status === 'overdue'): ?>
                                                                    <span class="badge bg-danger"><?php echo e($finance->status_name); ?></span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning"><?php echo e($finance->status_name); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <?php if($finance->status === 'pending'): ?>
                                                                        <button class="btn btn-outline-success" 
                                                                                onclick="markAsPaid(<?php echo e($finance->id); ?>)" 
                                                                                title="Отметить как оплаченное">
                                                                            <i class="bi bi-check-circle"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                    <button class="btn btn-outline-warning" 
                                                                            onclick="editFinance(<?php echo e($finance->id); ?>)" 
                                                                            title="Редактировать">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-danger" 
                                                                            onclick="deleteFinance(<?php echo e($finance->id); ?>)" 
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
                                        <?php if($employee->finances->count() > 20): ?>
                                            <div class="text-center">
                                                <a href="<?php echo e(route('partner.employees.finances.index', $employee)); ?>" class="btn btn-outline-primary">
                                                    Просмотреть все записи (<?php echo e($employee->finances->count()); ?>)
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-cash-stack" style="font-size: 2rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">Финансовых записей пока нет</p>
                                            <button class="btn btn-primary btn-sm" onclick="openAddFinanceModal()">
                                                Добавить первую запись
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка "Предстоящие платежи" -->
                        <div class="tab-pane fade" id="upcoming">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Предстоящие платежи</h6>
                                </div>
                                <div class="card-body">
                                    <?php if($upcomingPayments->count() > 0): ?>
                                        <?php $__currentLoopData = $upcomingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $payments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <h6 class="text-muted"><?php echo e(\Carbon\Carbon::parse($date)->format('d.m.Y')); ?></h6>
                                            <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="border rounded p-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo e($payment->title); ?></h6>
                                                            <div class="small text-muted">
                                                                <span class="badge bg-secondary me-2"><?php echo e($payment->type_name); ?></span>
                                                                <?php if($payment->project): ?>
                                                                    <i class="bi bi-building me-1"></i><?php echo e($payment->project->name); ?>

                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if($payment->description): ?>
                                                                <p class="small text-muted mt-2 mb-0"><?php echo e($payment->description); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="text-end">
                                                            <h5 class="mb-1"><?php echo e(number_format($payment->amount, 0, ',', ' ')); ?> ₽</h5>
                                                            <?php if($payment->is_overdue): ?>
                                                                <span class="badge bg-danger">Просрочено</span>
                                                            <?php elseif($payment->days_until_due <= 3): ?>
                                                                <span class="badge bg-warning">Срочно</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-calendar-check" style="font-size: 2rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">Предстоящих платежей нет</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка "Проекты" -->
                        <div class="tab-pane fade" id="projects">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Проекты сотрудника</h6>
                                </div>
                                <div class="card-body">
                                    <?php if($employee->projects->count() > 0): ?>
                                        <div class="row">
                                            <?php $__currentLoopData = $employee->projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border">
                                                        <div class="card-body">
                                                            <h6 class="card-title"><?php echo e($project->name); ?></h6>
                                                            <p class="card-text small text-muted"><?php echo e($project->object_full_address); ?></p>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="badge bg-info"><?php echo e($project->project_status); ?></span>
                                                                <a href="<?php echo e(route('partner.projects.show', $project)); ?>" class="btn btn-sm btn-outline-primary">
                                                                    Открыть
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-building" style="font-size: 2rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">Сотрудник пока не привязан к проектам</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно добавления финансовой записи -->
<div class="modal fade" id="addFinanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить финансовую запись</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFinanceForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Тип *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Выберите тип</option>
                                <?php $__currentLoopData = \App\Models\EmployeeFinance::getTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Сумма *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="title" class="form-label">Название *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Дата к выплате *</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Проект</label>
                            <select class="form-select" id="project_id" name="project_id">
                                <option value="">Не привязан к проекту</option>
                                <?php $__currentLoopData = \App\Models\Project::forPartner(Auth::id())->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($project->id); ?>"><?php echo e($project->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <input type="hidden" name="currency" value="RUB">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования финансовой записи -->
<div class="modal fade" id="editFinanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать финансовую запись</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFinanceForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" id="edit_finance_id" name="finance_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_type" class="form-label">Тип *</label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="">Выберите тип</option>
                                <?php $__currentLoopData = \App\Models\EmployeeFinance::getTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_amount" class="form-label">Сумма *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" required>
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="edit_title" class="form-label">Название *</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_description" class="form-label">Описание</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_due_date" class="form-label">Дата к выплате *</label>
                            <input type="date" class="form-control" id="edit_due_date" name="due_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_project_id" class="form-label">Проект</label>
                            <select class="form-select" id="edit_project_id" name="project_id">
                                <option value="">Не привязан к проекту</option>
                                <?php $__currentLoopData = \App\Models\Project::forPartner(Auth::id())->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($project->id); ?>"><?php echo e($project->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <input type="hidden" name="currency" value="RUB">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openAddFinanceModal() {
    // Устанавливаем дату по умолчанию на завтра
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('due_date').value = tomorrow.toISOString().split('T')[0];
    
    const modal = new bootstrap.Modal(document.getElementById('addFinanceModal'));
    modal.show();
}

document.getElementById('addFinanceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    try {
        // Показываем индикатор загрузки
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Добавление...';
        
        const formData = new FormData(this);
        
        const response = await fetch(`/partner/employees/<?php echo e($employee->id); ?>/finances`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        let result;
        try {
            result = await response.json();
        } catch (parseError) {
            console.error('Ошибка парсинга JSON:', parseError);
            const text = await response.text();
            console.error('Полученный ответ:', text);
            throw new Error('Сервер вернул некорректный JSON-ответ');
        }
        
        if (response.ok && result.success) {
            // Показываем сообщение об успехе
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <strong>Успешно!</strong> ${result.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container-fluid');
            if (container.firstElementChild) {
                container.insertBefore(alertDiv, container.firstElementChild);
            }
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('addFinanceModal'));
            if (modal) modal.hide();
            
            // Очищаем форму
            this.reset();
            
            // Перезагружаем страницу через небольшую задержку
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            throw new Error(result.message || 'Неизвестная ошибка');
        }
    } catch (error) {
        console.error('Ошибка при добавлении записи:', error);
        
        // Показываем ошибку пользователю
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <strong>Ошибка!</strong> ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const modalBody = this.closest('.modal-body');
        if (modalBody.firstElementChild) {
            modalBody.insertBefore(alertDiv, modalBody.firstElementChild);
        }
    } finally {
        // Возвращаем кнопку в исходное состояние
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});

async function editFinance(financeId) {
    try {
        // Получаем данные записи
        const response = await fetch(`/partner/employees/<?php echo e($employee->id); ?>/finances/${financeId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) {
            throw new Error('Не удалось загрузить данные записи');
        }
        
        const finance = await response.json();
        
        // Заполняем форму данными
        document.getElementById('edit_finance_id').value = finance.id;
        document.getElementById('edit_type').value = finance.type;
        document.getElementById('edit_amount').value = finance.amount;
        document.getElementById('edit_title').value = finance.title;
        document.getElementById('edit_description').value = finance.description || '';
        document.getElementById('edit_due_date').value = finance.due_date;
        document.getElementById('edit_project_id').value = finance.project_id || '';
        
        // Показываем модальное окно
        const modal = new bootstrap.Modal(document.getElementById('editFinanceModal'));
        modal.show();
        
    } catch (error) {
        console.error('Ошибка при загрузке данных записи:', error);
        alert('Ошибка при загрузке данных записи: ' + error.message);
    }
}

// Обработчик формы редактирования
document.getElementById('editFinanceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    const financeId = document.getElementById('edit_finance_id').value;
    
    try {
        // Показываем индикатор загрузки
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Сохранение...';
        
        const formData = new FormData(this);
        
        const response = await fetch(`/partner/employees/<?php echo e($employee->id); ?>/finances/${financeId}`, {
            method: 'PUT',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        let result;
        try {
            result = await response.json();
        } catch (parseError) {
            console.error('Ошибка парсинга JSON:', parseError);
            const text = await response.text();
            console.error('Полученный ответ:', text);
            throw new Error('Сервер вернул некорректный JSON-ответ');
        }
        
        if (response.ok && result.success) {
            // Показываем сообщение об успехе
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <strong>Успешно!</strong> ${result.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container-fluid');
            if (container.firstElementChild) {
                container.insertBefore(alertDiv, container.firstElementChild);
            }
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('editFinanceModal'));
            if (modal) modal.hide();
            
            // Перезагружаем страницу через небольшую задержку
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            throw new Error(result.message || 'Неизвестная ошибка');
        }
    } catch (error) {
        console.error('Ошибка при обновлении записи:', error);
        
        // Показываем ошибку пользователю
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <strong>Ошибка!</strong> ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const modalBody = this.closest('.modal-body');
        if (modalBody.firstElementChild) {
            modalBody.insertBefore(alertDiv, modalBody.firstElementChild);
        }
    } finally {
        // Возвращаем кнопку в исходное состояние
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});

async function markAsPaid(financeId) {
    if (!confirm('Отметить платеж как выполненный?')) return;
    
    try {
        const response = await fetch(`/partner/employees/<?php echo e($employee->id); ?>/finances/${financeId}/mark-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        let result;
        try {
            result = await response.json();
        } catch (parseError) {
            console.error('Ошибка парсинга JSON:', parseError);
            throw new Error('Сервер вернул некорректный ответ');
        }
        
        if (response.ok && result.success) {
            // Показываем сообщение об успехе
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <strong>Успешно!</strong> ${result.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container-fluid');
            if (container.firstElementChild) {
                container.insertBefore(alertDiv, container.firstElementChild);
            }
            
            // Перезагружаем страницу
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            throw new Error(result.message || 'Ошибка при обновлении статуса');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        
        // Показываем ошибку пользователю
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <strong>Ошибка!</strong> ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        if (container.firstElementChild) {
            container.insertBefore(alertDiv, container.firstElementChild);
        }
    }
}

async function deleteFinance(financeId) {
    if (!confirm('Удалить финансовую запись?')) return;
    
    try {
        const response = await fetch(`/partner/employees/<?php echo e($employee->id); ?>/finances/${financeId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        let result;
        try {
            result = await response.json();
        } catch (parseError) {
            console.error('Ошибка парсинга JSON:', parseError);
            throw new Error('Сервер вернул некорректный ответ');
        }
        
        if (response.ok && result.success) {
            // Показываем сообщение об успехе
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <strong>Успешно!</strong> ${result.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container-fluid');
            if (container.firstElementChild) {
                container.insertBefore(alertDiv, container.firstElementChild);
            }
            
            // Перезагружаем страницу
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            throw new Error(result.message || 'Ошибка при удалении записи');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        
        // Показываем ошибку пользователю
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <strong>Ошибка!</strong> ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        if (container.firstElementChild) {
            container.insertBefore(alertDiv, container.firstElementChild);
        }
    }
}

// Функция для проверки и обновления просрочек
async function markAllOverdue() {
    try {
        const response = await fetch(`/partner/employees/<?php echo e($employee->id); ?>/check-overdue`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            if (result.updated > 0) {
                alert(`Обновлено ${result.updated} просроченных платежей`);
                location.reload();
            } else {
                alert('Просроченных платежей не найдено');
            }
        } else {
            alert('Ошибка при проверке просрочек');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('Ошибка при проверке просрочек');
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/employees/show.blade.php ENDPATH**/ ?>