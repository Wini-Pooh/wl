

<?php $__env->startSection('title', 'Документы'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-folder-open me-2"></i>Система документооборота
                    </h3>
                    <button type="button" class="btn btn-primary" id="openSendDocumentBtn">
                        <i class="fas fa-plus"></i> Отправить документ
                    </button>
                </div>

                <div class="card-body">
                    <!-- Фильтры и поиск -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center filter-header-clickable" data-bs-toggle="collapse" data-bs-target="#filtersContent" aria-expanded="false" aria-controls="filtersContent" style="cursor: pointer;">
                            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Фильтры и поиск</h6>
                            <div class="d-flex align-items-center gap-2">
                                <?php
                                    $activeFilters = collect(request()->except(['tab', 'page']))->filter()->count();
                                ?>
                                <?php if($activeFilters > 0): ?>
                                    <span class="badge bg-primary"><?php echo e($activeFilters); ?> активных</span>
                                <?php endif; ?>
                                <i class="fas fa-chevron-down" id="toggleFiltersIcon"></i>
                            </div>
                        </div>
                        <div class="card-body collapse" id="filtersContent" <?php if($activeFilters > 0): ?> show <?php endif; ?>>
                            <form method="GET" action="<?php echo e(route('documents.index')); ?>" id="documentFilters">
                                <input type="hidden" name="tab" value="<?php echo e($tab); ?>" id="currentTabInput">
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label for="searchFilter" class="form-label">Поиск</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" id="searchFilter" name="search" 
                                                   value="<?php echo e(request('search')); ?>" placeholder="Название, содержимое, получатель...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="typeFilter" class="form-label">Тип документа</label>
                                        <select class="form-select" id="typeFilter" name="document_type">
                                            <option value="">Все типы</option>
                                            <option value="contract" <?php echo e(request('document_type') == 'contract' ? 'selected' : ''); ?>>Договор</option>
                                            <option value="act" <?php echo e(request('document_type') == 'act' ? 'selected' : ''); ?>>Акт</option>
                                            <option value="invoice" <?php echo e(request('document_type') == 'invoice' ? 'selected' : ''); ?>>Счет</option>
                                            <option value="estimate" <?php echo e(request('document_type') == 'estimate' ? 'selected' : ''); ?>>Смета</option>
                                            <option value="technical" <?php echo e(request('document_type') == 'technical' ? 'selected' : ''); ?>>Техническая документация</option>
                                            <option value="other" <?php echo e(request('document_type') == 'other' ? 'selected' : ''); ?>>Прочее</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="statusFilter" class="form-label">Статус</label>
                                        <select class="form-select" id="statusFilter" name="status">
                                            <option value="">Все статусы</option>
                                            <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Черновик</option>
                                            <option value="sent" <?php echo e(request('status') == 'sent' ? 'selected' : ''); ?>>Отправлен</option>
                                            <option value="received" <?php echo e(request('status') == 'received' ? 'selected' : ''); ?>>Получен</option>
                                            <option value="signed" <?php echo e(request('status') == 'signed' ? 'selected' : ''); ?>>Подписан</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="projectFilter" class="form-label">Проект</label>
                                        <select class="form-select" id="projectFilter" name="project_id">
                                            <option value="">Все проекты</option>
                                            <?php $__currentLoopData = \App\Models\Project::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($project->id); ?>" <?php echo e(request('project_id') == $project->id ? 'selected' : ''); ?>>
                                                    <?php echo e($project->client_last_name); ?> <?php echo e($project->client_first_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label for="dateFromFilter" class="form-label">Дата от</label>
                                        <input type="date" class="form-control" id="dateFromFilter" name="date_from" 
                                               value="<?php echo e(request('date_from')); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="dateToFilter" class="form-label">Дата до</label>
                                        <input type="date" class="form-control" id="dateToFilter" name="date_to" 
                                               value="<?php echo e(request('date_to')); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="signatureFilter" class="form-label">Статус подписи</label>
                                        <select class="form-select" id="signatureFilter" name="signature_status">
                                            <option value="">Все</option>
                                            <option value="not_required" <?php echo e(request('signature_status') == 'not_required' ? 'selected' : ''); ?>>Не требуется</option>
                                            <option value="pending" <?php echo e(request('signature_status') == 'pending' ? 'selected' : ''); ?>>Ожидает подписи</option>
                                            <option value="signed" <?php echo e(request('signature_status') == 'signed' ? 'selected' : ''); ?>>Подписан</option>
                                            <option value="rejected" <?php echo e(request('signature_status') == 'rejected' ? 'selected' : ''); ?>>Отклонен</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="sortFilter" class="form-label">Сортировка</label>
                                        <select class="form-select" id="sortFilter" name="sort">
                                            <option value="created_at_desc" <?php echo e(request('sort', 'created_at_desc') == 'created_at_desc' ? 'selected' : ''); ?>>Сначала новые</option>
                                            <option value="created_at_asc" <?php echo e(request('sort') == 'created_at_asc' ? 'selected' : ''); ?>>Сначала старые</option>
                                            <option value="title_asc" <?php echo e(request('sort') == 'title_asc' ? 'selected' : ''); ?>>По названию А-Я</option>
                                            <option value="title_desc" <?php echo e(request('sort') == 'title_desc' ? 'selected' : ''); ?>>По названию Я-А</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Применить фильтры
                                    </button>
                                    <a href="<?php echo e(route('documents.index', ['tab' => $tab])); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Сбросить
                                    </a>
                                    <button type="button" class="btn btn-outline-info" id="saveFilters">
                                        <i class="fas fa-bookmark"></i> Сохранить фильтры
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Навигационные вкладки -->
                    <ul class="nav nav-tabs mb-4" id="documentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e($tab === 'received' ? 'active' : ''); ?>" 
                                    id="received-tab" data-bs-toggle="tab" data-bs-target="#received-content" 
                                    type="button" role="tab" data-tab="received">
                                <i class="fas fa-inbox"></i> Полученные
                                <?php if($tab === 'received' && isset($documents)): ?>
                                    <span class="badge bg-primary ms-1"><?php echo e($documents->total() ?? 0); ?></span>
                                <?php endif; ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e($tab === 'created' ? 'active' : ''); ?>" 
                                    id="created-tab" data-bs-toggle="tab" data-bs-target="#created-content" 
                                    type="button" role="tab" data-tab="created">
                                <i class="fas fa-paper-plane"></i> Отправленные
                                <?php if($tab === 'created' && isset($documents)): ?>
                                    <span class="badge bg-primary ms-1"><?php echo e($documents->total() ?? 0); ?></span>
                                <?php endif; ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo e($tab === 'signed' ? 'active' : ''); ?>" 
                                    id="signed-tab" data-bs-toggle="tab" data-bs-target="#signed-content" 
                                    type="button" role="tab" data-tab="signed">
                                <i class="fas fa-signature"></i> Подписанные
                                <?php if($tab === 'signed' && isset($documents)): ?>
                                    <span class="badge bg-primary ms-1"><?php echo e($documents->total() ?? 0); ?></span>
                                <?php endif; ?>
                            </button>
                        </li>
                    </ul>

                    <!-- Содержимое вкладок -->
                    <div class="tab-content" id="documentsTabContent">
                        <!-- Индикатор загрузки -->
                        <div id="loading-indicator" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка данных...</div>
                        </div>

                        <!-- Контент вкладок -->
                        <div class="tab-pane fade <?php echo e($tab === 'received' ? 'show active' : ''); ?>" 
                             id="received-content" role="tabpanel">
                            <?php if($tab === 'received'): ?>
                                <?php echo $__env->make('documents.partials.documents-tab', ['documents' => $documents, 'tab' => 'received'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="tab-pane fade <?php echo e($tab === 'created' ? 'show active' : ''); ?>" 
                             id="created-content" role="tabpanel">
                            <?php if($tab === 'created'): ?>
                                <?php echo $__env->make('documents.partials.documents-tab', ['documents' => $documents, 'tab' => 'created'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="tab-pane fade <?php echo e($tab === 'signed' ? 'show active' : ''); ?>" 
                             id="signed-content" role="tabpanel">
                            <?php if($tab === 'signed'): ?>
                                <?php echo $__env->make('documents.partials.documents-tab', ['documents' => $documents, 'tab' => 'signed'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php endif; ?>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Боковая панель подписания документа -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="signatureOffcanvas" aria-labelledby="signatureOffcanvasLabel">
    <div class="offcanvas-header bg-success text-white">
        <h5 class="offcanvas-title" id="signatureOffcanvasLabel">
            <i class="fas fa-signature me-2"></i>Подписание документа
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="globalSignDocumentForm">
            <?php echo csrf_field(); ?>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Вы действительно хотите подписать этот документ?
            </div>
            
            <div class="mb-3">
                <label for="globalSignatureText" class="form-label">Введите вашу подпись:</label>
                <input type="text" class="form-control" id="globalSignatureText" name="signature" 
                       placeholder="Ваше полное имя" required>
                <div class="form-text">Введите ваше полное имя в качестве электронной подписи</div>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="globalSignAgreement" name="agreement" required>
                <label class="form-check-label" for="globalSignAgreement">
                    Я подтверждаю, что ознакомился с содержанием документа и согласен с его условиями
                </label>
            </div>
            <input type="hidden" id="documentId">
        </form>
    </div>
    
    <div class="offcanvas-footer border-top p-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">Отмена</button>
            <button type="submit" form="globalSignDocumentForm" class="btn btn-success flex-fill" id="signDocument">
                <i class="fas fa-signature me-1"></i>Подписать
            </button>
        </div>
    </div>
</div>

<!-- Боковое модальное окно отправки документа -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sendDocumentOffcanvas" aria-labelledby="sendDocumentOffcanvasLabel">
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title" id="sendDocumentOffcanvasLabel">
            <i class="fas fa-paper-plane me-2"></i>Отправить документ на подписание
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="sendDocumentForm" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <!-- Шаг 1: Выбор получателя -->
            <div class="step-section mb-4" id="recipientStep">
                <h6 class="step-title">
                    <span class="step-number">1</span>
                    Выберите получателя
                </h6>
                
                <div class="recipient-type-selector mb-3">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="recipient_type" id="employeeType" value="employee" autocomplete="off">
                        <label class="btn btn-outline-primary" for="employeeType">
                            <i class="fas fa-users me-2"></i>Сотрудник
                        </label>

                        <input type="radio" class="btn-check" name="recipient_type" id="clientType" value="client" autocomplete="off">
                        <label class="btn btn-outline-primary" for="clientType">
                            <i class="fas fa-building me-2"></i>Клиент
                        </label>

                        <input type="radio" class="btn-check" name="recipient_type" id="externalType" value="external" autocomplete="off">
                        <label class="btn btn-outline-primary" for="externalType">
                            <i class="fas fa-envelope me-2"></i>Внешний
                        </label>
                    </div>
                </div>

                <!-- Выбор сотрудника -->
                <div id="employeeSelection" class="recipient-section" style="display: none;">
                    <label class="form-label">Выберите сотрудника:</label>
                    <select class="form-select" name="employee_id">
                        <option value="">Выберите сотрудника...</option>
                        <?php $__currentLoopData = \App\Models\User::whereHas('roles', function($query) { $query->whereIn('name', ['partner', 'employee', 'foreman', 'estimator']); })->orWhereHas('defaultRole', function($query) { $query->whereIn('name', ['partner', 'employee', 'foreman', 'estimator']); })->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>">
                                <?php echo e($user->name); ?> 
                                <?php if($user->phone): ?>
                                    (<?php echo e($user->phone); ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Выбор клиента через проект -->
                <div id="clientSelection" class="recipient-section" style="display: none;">
                    <label class="form-label">Выберите проект/объект:</label>
                    <select class="form-select" name="project_id" id="projectSelect">
                        <option value="">Выберите проект...</option>
                        <?php $__currentLoopData = \App\Models\Project::where('partner_id', auth()->id())->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($project->id); ?>" 
                                    data-client-name="<?php echo e($project->client_first_name); ?> <?php echo e($project->client_last_name); ?>"
                                    data-client-phone="<?php echo e($project->client_phone); ?>"
                                    data-client-email="<?php echo e($project->client_email); ?>"
                                    data-object-address="<?php echo e($project->object_address); ?>">
                                <?php echo e($project->client_last_name); ?> <?php echo e($project->client_first_name); ?> - <?php echo e($project->object_address); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="clientInfo" class="mt-3" style="display: none;">
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-2">Информация о клиенте:</h6>
                                <div class="client-details">
                                    <div><strong>Имя:</strong> <span id="clientName">-</span></div>
                                    <div><strong>Телефон:</strong> <span id="clientPhone">-</span></div>
                                    <div><strong>Email:</strong> <span id="clientEmail">-</span></div>
                                    <div><strong>Объект:</strong> <span id="objectAddress">-</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Внешний получатель -->
                <div id="externalSelection" class="recipient-section" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label required">Имя получателя:</label>
                            <input type="text" class="form-control" name="recipient_name" placeholder="Введите имя">
                            <div class="invalid-feedback">Пожалуйста, введите имя получателя</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Телефон:</label>
                            <input type="tel" class="form-control" name="recipient_phone" placeholder="+7 (999) 123-45-67">
                            <div class="invalid-feedback">Пожалуйста, введите телефон получателя</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Email (опционально):</label>
                        <input type="email" class="form-control" name="recipient_email" placeholder="email@example.com">
                        <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                    </div>
                </div>
            </div>

            <!-- Шаг 2: Загрузка документа -->
            <div class="step-section mb-4" id="documentStep" style="display: none;">
                <h6 class="step-title">
                    <span class="step-number">2</span>
                    Загрузите документ
                </h6>
                
                <div class="mb-3">
                    <label class="form-label required">Заголовок документа:</label>
                    <input type="text" class="form-control" name="title" placeholder="Введите название документа" required>
                    <div class="invalid-feedback">Пожалуйста, введите название документа</div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Файл документа:</label>
                    <input type="file" class="form-control" name="document_file" 
                           accept=".pdf,.doc,.docx,.txt,.rtf" required>
                    <div class="form-text">Поддерживаемые форматы: PDF, DOC, DOCX, TXT, RTF (макс. 10 МБ)</div>
                    <div class="invalid-feedback">Пожалуйста, выберите файл документа</div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Тип документа:</label>
                    <select class="form-select" name="document_type" required>
                        <option value="">Выберите тип документа...</option>
                        <option value="contract">Договор</option>
                        <option value="act">Акт выполненных работ</option>
                        <option value="invoice">Счет</option>
                        <option value="estimate">Смета</option>
                        <option value="technical">Техническая документация</option>
                        <option value="other">Другое</option>
                    </select>
                    <div class="invalid-feedback">Пожалуйста, выберите тип документа</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Комментарий (опционально):</label>
                    <textarea class="form-control" name="description" rows="3" 
                              placeholder="Добавьте комментарий к документу..."></textarea>
                </div>
            </div>

            <!-- Шаг 3: Настройки подписания -->
            <div class="step-section mb-4" id="signatureStep" style="display: none;">
                <h6 class="step-title">
                    <span class="step-number">3</span>
                    Настройки подписания
                </h6>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="signature_required" id="signatureRequired" value="1" checked>
                    <label class="form-check-label" for="signatureRequired">
                        <strong>Требует электронной подписи</strong>
                    </label>
                    <div class="form-text">Документ будет отправлен на подпись</div>
                </div>

                <div id="signatureOptions">
                    <div class="mb-3">
                        <label class="form-label">Срок подписания:</label>
                        <select class="form-select" name="expires_in">
                            <option value="">Без ограничения</option>
                            <option value="1">1 день</option>
                            <option value="3">3 дня</option>
                            <option value="7" selected>7 дней</option>
                            <option value="14">14 дней</option>
                            <option value="30">30 дней</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Приоритет:</label>
                        <select class="form-select" name="priority">
                            <option value="normal" selected>Обычный</option>
                            <option value="high">Высокий</option>
                            <option value="urgent">Срочный</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Сообщение получателю (опционально):</label>
                        <textarea class="form-control" name="message" rows="3" 
                                  placeholder="Добавьте сообщение для получателя..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Шаг 4: Подтверждение -->
            <div class="step-section mb-4" id="confirmStep" style="display: none;">
                <h6 class="step-title">
                    <span class="step-number">4</span>
                    Подтверждение отправки
                </h6>
                
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Сводка:</h6>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Получатель:</strong> <span id="summaryRecipient">-</span></li>
                            <li><strong>Документ:</strong> <span id="summaryDocument">-</span></li>
                            <li><strong>Требует подпись:</strong> <span id="summarySignature">-</span></li>
                            <li><strong>Срок:</strong> <span id="summaryDeadline">-</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="offcanvas-footer border-top p-3">
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" id="prevStep" style="display: none;">
                <i class="fas fa-arrow-left"></i> Назад
            </button>
            <div class="ms-auto">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Отмена</button>
                <button type="button" class="btn btn-primary" id="nextStep">
                    Далее <i class="fas fa-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-success" id="sendDocument" style="display: none;">
                    <i class="fas fa-paper-plane"></i> Отправить
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Принудительная очистка backdrop при закрытии боковых панелей
    document.addEventListener('hidden.bs.offcanvas', function (event) {
        // Удаляем все backdrop элементы
        const backdrops = document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Восстанавливаем прокрутку body
        document.body.classList.remove('modal-open', 'offcanvas-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    // Глобальная функция очистки backdrop'ов
    window.cleanupModalBackdrops = function() {
        // Удаляем все существующие backdrop
        const backdrops = document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Восстанавливаем прокрутку body
        document.body.classList.remove('modal-open', 'offcanvas-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    };

    // AJAX переключение вкладок
    const documentTabs = document.getElementById('documentTabs');
    const tabContent = document.getElementById('documentsTabContent');
    const loadingIndicator = document.getElementById('loading-indicator');
    let currentTab = '<?php echo e($tab); ?>';

    // Обработка клика по вкладкам
    documentTabs.addEventListener('click', function(e) {
        const tabButton = e.target.closest('[data-tab]');
        if (!tabButton) return;

        e.preventDefault();
        
        const targetTab = tabButton.dataset.tab;
        if (targetTab === currentTab) return; // Уже активная вкладка

        window.loadTabContent(targetTab, tabButton);
    });

    // Обработка пагинации внутри вкладок
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('[data-page][data-tab]');
        if (!paginationLink) return;

        e.preventDefault();
        
        const page = paginationLink.dataset.page;
        const tab = paginationLink.dataset.tab;
        const activeTabButton = document.querySelector(`[data-tab="${tab}"]`);
        
        loadTabContent(tab, activeTabButton, page);
    });

    // Функция загрузки содержимого вкладки
    window.loadTabContent = function(tab, tabButton, page = 1) {
        console.log('Loading tab content:', { tab, page });
        
        // Показываем индикатор загрузки
        showLoadingIndicator();
        
        // Обновляем активную вкладку только если это не пагинация
        if (page === 1) {
            updateActiveTab(tabButton);
            currentTab = tab;
            
            // Обновляем скрытое поле фильтров
            const currentTabInput = document.getElementById('currentTabInput');
            if (currentTabInput) {
                currentTabInput.value = tab;
            }
        }
        
        // Формируем URL с параметрами фильтров
        const url = new URL(`<?php echo e(route('documents.index')); ?>`);
        url.searchParams.set('tab', tab);
        if (page > 1) {
            url.searchParams.set('page', page);
        }
        
        // Добавляем параметры фильтров
        const form = document.getElementById('documentFilters');
        if (form) {
            const formData = new FormData(form);
            for (let [key, value] of formData.entries()) {
                if (value && key !== 'tab') {
                    url.searchParams.set(key, value);
                }
            }
        }
        
        console.log('Sending AJAX request to:', url.toString());
        
        // Отправляем AJAX-запрос
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Проверяем, что ответ действительно JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Неожиданный тип ответа:', contentType);
                throw new Error('Сервер вернул не JSON ответ');
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Определяем целевую панель
                const targetPane = document.getElementById(`${tab}-content`);
                if (targetPane) {
                    targetPane.innerHTML = data.html;
                    
                    // Если это не пагинация, показываем панель
                    if (page === 1) {
                        showTabPane(tab);
                    }
                    
                    // Переинициализируем обработчики
                    initializeTabEventHandlers();
                }
            } else {
                showAlert('error', data.message || 'Ошибка при загрузке данных');
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке вкладки:', error);
            showAlert('error', 'Ошибка при загрузке данных: ' + error.message);
        })
        .finally(() => {
            hideLoadingIndicator();
        });
    };

    // Показать индикатор загрузки
    function showLoadingIndicator() {
        loadingIndicator.style.display = 'block';
    }

    // Скрыть индикатор загрузки
    function hideLoadingIndicator() {
        loadingIndicator.style.display = 'none';
    }

    // Обновить активную вкладку
    function updateActiveTab(activeButton) {
        // Убираем active класс со всех вкладок
        document.querySelectorAll('#documentTabs .nav-link').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Добавляем active класс к текущей вкладке
        activeButton.classList.add('active');
    }

    // Показать соответствующую панель вкладки
    function showTabPane(tab) {
        // Скрываем все панели
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Показываем нужную панель
        const targetPane = document.getElementById(`${tab}-content`);
        if (targetPane) {
            targetPane.classList.add('show', 'active');
        }
    }

    // Инициализация обработчиков для динамически загруженного контента
    function initializeTabEventHandlers() {
        // Переинициализируем все обработчики документов (используем нашу новую функцию)
        if (typeof initializeAllDocumentHandlers === 'function') {
            initializeAllDocumentHandlers();
        } else {
            // Fallback на старые функции
            if (typeof initializeSignatureHandlers === 'function') {
                initializeSignatureHandlers();
            }
            
            if (typeof initializeDocumentHandlers === 'function') {
                initializeDocumentHandlers();
            }
        }
        
        // Переинициализируем обработчики шаблонов
        if (typeof initializeTemplateHandlers === 'function') {
            initializeTemplateHandlers();
        }
    }

    // Обработка фильтров - клик по всей плашке для раскрытия
    const filtersContent = document.getElementById('filtersContent');
    const toggleIcon = document.getElementById('toggleFiltersIcon');

    // Обработчик переключения фильтров через Bootstrap Collapse
    if (filtersContent) {
        filtersContent.addEventListener('show.bs.collapse', function () {
            toggleIcon?.classList.replace('fa-chevron-down', 'fa-chevron-up');
            localStorage.setItem('filtersOpen', 'true');
        });
        
        filtersContent.addEventListener('hide.bs.collapse', function () {
            toggleIcon?.classList.replace('fa-chevron-up', 'fa-chevron-down');
            localStorage.setItem('filtersOpen', 'false');
        });
        
        // Восстанавливаем состояние фильтров из localStorage
        const filtersOpen = localStorage.getItem('filtersOpen');
        if (filtersOpen === 'true') {
            const bsCollapse = new bootstrap.Collapse(filtersContent, {show: true});
        }
    }

    // Автоотправка формы фильтров с улучшенной обработкой
    const documentFilters = document.getElementById('documentFilters');
    if (documentFilters) {
        // Обработчик отправки формы
        documentFilters.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Filter form submitted');
            loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
        });
        
        // Автоматическая фильтрация при изменении select-ов
        const selectElements = documentFilters.querySelectorAll('select');
        selectElements.forEach(select => {
            select.addEventListener('change', function(e) {
                console.log('Filter select changed:', e.target.name, e.target.value);
                // Небольшая задержка для предотвращения множественных запросов
                setTimeout(() => {
                    loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                }, 100);
            });
        });
        
        // Отложенная фильтрация для текстовых полей
        let searchTimeout;
        const searchFilter = document.getElementById('searchFilter');
        if (searchFilter) {
            searchFilter.addEventListener('input', function(e) {
                console.log('Search input changed:', e.target.value);
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                }, 800); // Увеличили задержку для поиска
            });
            
            // Обработка Enter в поле поиска
            searchFilter.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                }
            });
        }

        // Автоматическая фильтрация для полей дат
        const dateInputs = documentFilters.querySelectorAll('input[type="date"]');
        dateInputs.forEach(dateInput => {
            dateInput.addEventListener('change', function(e) {
                console.log('Date filter changed:', e.target.name, e.target.value);
                setTimeout(() => {
                    loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                }, 100);
            });
        });
        
        // Кнопка сброса фильтров
        const resetFiltersBtn = documentFilters.querySelector('a[href*="route"]');
        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Resetting filters');
                
                // Очищаем все поля формы
                documentFilters.reset();
                
                // Загружаем контент без фильтров
                setTimeout(() => {
                    loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                }, 100);
            });
        }
    }

    // Инициализация всех обработчиков
    initializeTabEventHandlers();
    initializeSendDocumentModal();
    initializeGlobalSignatureHandlers();

    // Инициализация кнопки "Отправить документ"
    const openSendDocumentBtn = document.getElementById('openSendDocumentBtn');
    if (openSendDocumentBtn) {
        openSendDocumentBtn.addEventListener('click', function() {
            openSendDocument();
        });
    }

    // Функция показа уведомлений (заменяем старую showAlert)
    function showAlert(type, message) {
        showNotification(type, message);
    }
});

// Функция открытия модального окна отправки документа
function openSendDocument(type = null) {
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('sendDocumentOffcanvas'));
    
    // Сброс формы и состояния
    resetSendDocumentForm();
    
    // Если указан тип, сразу выбираем его
    if (type) {
        const radioBtn = document.querySelector(`#sendDocumentForm input[name="recipient_type"][value="${type}"]`);
        if (radioBtn) {
            radioBtn.checked = true;
            radioBtn.dispatchEvent(new Event('change'));
        }
    }
    
    offcanvas.show();
}

// Глобальная переменная для отслеживания текущего шага
let currentStepGlobal = 1;
const totalSteps = 4;

function resetSendDocumentForm() {
    // Сброс формы
    const form = document.getElementById('sendDocumentForm');
    if (form) {
        form.reset();
    }
    
    // Очистка всех ошибок валидации
    document.querySelectorAll('.is-invalid').forEach(field => {
        field.classList.remove('is-invalid');
    });
    
    // Возврат к первому шагу
    currentStepGlobal = 1;
    document.querySelectorAll('.step-section').forEach((section, index) => {
        section.style.display = index === 0 ? 'block' : 'none';
    });
    
    // Скрываем все секции получателей
    document.querySelectorAll('.recipient-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Скрываем информацию о клиенте
    const clientInfo = document.getElementById('clientInfo');
    if (clientInfo) {
        clientInfo.style.display = 'none';
    }
    
    // Показываем настройки подписания по умолчанию
    const signatureOptions = document.getElementById('signatureOptions');
    if (signatureOptions) {
        signatureOptions.style.display = 'block';
    }
    
    // Обновляем кнопки
    updateStepButtons();
}

// Глобальная функция обновления кнопок шагов
function updateStepButtons() {
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const sendBtn = document.getElementById('sendDocument');
    
    if (prevBtn) prevBtn.style.display = currentStepGlobal > 1 ? 'inline-block' : 'none';
    
    if (currentStepGlobal < totalSteps) {
        if (nextBtn) nextBtn.style.display = 'inline-block';
        if (sendBtn) sendBtn.style.display = 'none';
    } else {
        if (nextBtn) nextBtn.style.display = 'none';
        if (sendBtn) sendBtn.style.display = 'inline-block';
    }
}

// Улучшенная функция показа уведомлений
function showNotification(type, message, options = {}) {
    const {
        icon = '',
        timeout = 5000,
        position = 'top-right'
    } = options;
    
    // Создаем контейнер для уведомлений если его нет
    let container = document.getElementById('notifications-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications-container';
        container.className = 'position-fixed';
        container.style.cssText = `
            top: 20px; 
            right: 20px; 
            z-index: 9999; 
            max-width: 400px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }
    
    // Определяем класс Bootstrap для типа уведомления
    const typeClasses = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alertClass = typeClasses[type] || 'alert-info';
    
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show mb-2 shadow-lg`;
    notification.style.cssText = `
        pointer-events: auto;
        animation: slideInRight 0.3s ease-out;
        border: none;
        border-radius: 8px;
    `;
    
    notification.innerHTML = `
        ${icon ? `<i class="${icon} me-2"></i>` : ''}
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    container.appendChild(notification);
    
    // Автоматически удаляем через указанное время
    if (timeout > 0) {
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, timeout);
    }
}

// Инициализация модального окна отправки документа
function initializeSendDocumentModal() {
    // Проверяем наличие обязательных элементов
    const form = document.getElementById('sendDocumentForm');
    const sendBtn = document.getElementById('sendDocument');
    
    if (!form) {
        console.error('Send document form not found');
        return;
    }
    
    if (!sendBtn) {
        console.error('Send document button not found');
        return;
    }
    // Обработчики выбора типа получателя
    document.querySelectorAll('#sendDocumentForm input[name="recipient_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                showRecipientSection(this.value);
                // НЕ автоматически переходим к следующему шагу
                // Пользователь должен заполнить данные и нажать "Далее"
            }
        });
    });
    
    // Кнопки навигации по шагам
    document.getElementById('nextStep').addEventListener('click', nextStep);
    document.getElementById('prevStep').addEventListener('click', prevStep);
    document.getElementById('sendDocument').addEventListener('click', sendDocument);
    
    // Обработчик выбора проекта для показа информации о клиенте
    const projectSelect = document.getElementById('projectSelect');
    if (projectSelect) {
        projectSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const clientInfo = document.getElementById('clientInfo');
            
            if (selectedOption.value) {
                const clientName = selectedOption.dataset.clientName;
                const clientPhone = selectedOption.dataset.clientPhone;
                const clientEmail = selectedOption.dataset.clientEmail;
                const objectAddress = selectedOption.dataset.objectAddress;
                
                document.getElementById('clientName').textContent = clientName || '-';
                document.getElementById('clientPhone').textContent = clientPhone || '-';
                document.getElementById('clientEmail').textContent = clientEmail || '-';
                document.getElementById('objectAddress').textContent = objectAddress || '-';
                
                clientInfo.style.display = 'block';
            } else {
                clientInfo.style.display = 'none';
            }
            
            updateSummary();
        });
    }
    
    // Обработчик для выбора сотрудника
    const employeeSelect = document.querySelector('#sendDocumentForm select[name="employee_id"]');
    if (employeeSelect) {
        employeeSelect.addEventListener('change', updateSummary);
    }
    
    // Обработчики для внешнего получателя
    const recipientNameInput = document.querySelector('#sendDocumentForm input[name="recipient_name"]');
    const recipientPhoneInput = document.querySelector('#sendDocumentForm input[name="recipient_phone"]');
    if (recipientNameInput) {
        recipientNameInput.addEventListener('input', updateSummary);
    }
    if (recipientPhoneInput) {
        recipientPhoneInput.addEventListener('input', updateSummary);
    }
    
    // Обработчики изменения полей формы для обновления сводки
    const titleInput = document.querySelector('#sendDocumentForm input[name="title"]');
    const typeSelect = document.querySelector('#sendDocumentForm select[name="document_type"]');
    const signatureCheckbox = document.getElementById('signatureRequired');
    
    if (titleInput) {
        titleInput.addEventListener('input', updateSummary);
    }
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            // Убираем стили ошибки при выборе типа
            this.classList.remove('is-invalid');
            updateSummary();
        });
    }
    if (signatureCheckbox) {
        signatureCheckbox.addEventListener('change', function() {
            const signatureOptions = document.getElementById('signatureOptions');
            if (signatureOptions) {
                signatureOptions.style.display = this.checked ? 'block' : 'none';
            }
            updateSummary();
        });
    }
    
    // Добавляем обработчики для очистки ошибок валидации
    document.querySelectorAll('#sendDocumentForm input, #sendDocumentForm select, #sendDocumentForm textarea').forEach(field => {
        field.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
        });
        
        // Дополнительный обработчик для файлового поля
        if (field.type === 'file') {
            field.addEventListener('change', function() {
                this.classList.remove('is-invalid');
            });
        }
    });
    
    function showRecipientSection(type) {
        // Скрываем все секции получателей
        document.querySelectorAll('.recipient-section').forEach(section => {
            section.style.display = 'none';
        });
        
        // Показываем выбранную секцию
        if (type === 'employee') {
            document.getElementById('employeeSelection').style.display = 'block';
        } else if (type === 'client') {
            document.getElementById('clientSelection').style.display = 'block';
        } else if (type === 'external') {
            document.getElementById('externalSelection').style.display = 'block';
        }
    }
    
    function nextStep() {
        if (currentStepGlobal < totalSteps) {
            // Валидация текущего шага
            if (!validateCurrentStep()) {
                return;
            }
            
            // Скрываем текущий шаг
            hideCurrentStep();
            
            currentStepGlobal++;
            
            // Показываем следующий шаг
            showCurrentStep();
            
            updateStepButtons();
            updateSummary();
        }
    }
    
    function prevStep() {
        if (currentStepGlobal > 1) {
            // Скрываем текущий шаг
            hideCurrentStep();
            
            currentStepGlobal--;
            
            // Показываем предыдущий шаг
            showCurrentStep();
            
            updateStepButtons();
        }
    }
    
    function hideCurrentStep() {
        const steps = document.querySelectorAll('.step-section');
        if (steps[currentStepGlobal - 1]) {
            steps[currentStepGlobal - 1].style.display = 'none';
        }
    }
    
    function showCurrentStep() {
        const steps = document.querySelectorAll('.step-section');
        if (steps[currentStepGlobal - 1]) {
            steps[currentStepGlobal - 1].style.display = 'block';
        }
    }
    
    function validateCurrentStep() {
        // Очищаем предыдущие ошибки
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        switch (currentStepGlobal) {
            case 1:
                const recipientType = document.querySelector('#sendDocumentForm input[name="recipient_type"]:checked');
                if (!recipientType) {
                    showValidationError('Выберите тип получателя');
                    return false;
                }
                
                if (recipientType.value === 'employee') {
                    const employeeSelect = document.querySelector('#sendDocumentForm select[name="employee_id"]');
                    const employee = employeeSelect ? employeeSelect.value : '';
                    if (!employee) {
                        if (employeeSelect) employeeSelect.classList.add('is-invalid');
                        showValidationError('Выберите сотрудника');
                        return false;
                    }
                } else if (recipientType.value === 'client') {
                    const projectSelect = document.querySelector('#sendDocumentForm select[name="project_id"]');
                    const project = projectSelect ? projectSelect.value : '';
                    if (!project) {
                        if (projectSelect) projectSelect.classList.add('is-invalid');
                        showValidationError('Выберите проект');
                        return false;
                    }
                } else if (recipientType.value === 'external') {
                    const nameInput = document.querySelector('#sendDocumentForm input[name="recipient_name"]');
                    const phoneInput = document.querySelector('#sendDocumentForm input[name="recipient_phone"]');
                    const name = nameInput ? nameInput.value.trim() : '';
                    const phone = phoneInput ? phoneInput.value.trim() : '';
                    
                    if (!name) {
                        if (nameInput) nameInput.classList.add('is-invalid');
                        showValidationError('Введите имя получателя');
                        return false;
                    }
                    if (!phone) {
                        if (phoneInput) phoneInput.classList.add('is-invalid');
                        showValidationError('Введите телефон получателя');
                        return false;
                    }
                    // Базовая проверка формата телефона
                    if (!/^[\+]?[0-9\(\)\-\s]+$/.test(phone)) {
                        if (phoneInput) phoneInput.classList.add('is-invalid');
                        showValidationError('Неверный формат телефона');
                        return false;
                    }
                }
                return true;
                
            case 2:
                const titleInput = document.querySelector('#sendDocumentForm input[name="title"]');
                const typeSelect = document.querySelector('#sendDocumentForm select[name="document_type"]');
                const fileInput = document.querySelector('#sendDocumentForm input[name="document_file"]');
                
                const title = titleInput ? titleInput.value.trim() : '';
                const type = typeSelect ? typeSelect.value : '';
                const file = fileInput ? fileInput.files[0] : null;
                
                // Отладочная информация
                console.log('Validation Step 2:', {
                    title: title,
                    type: type,
                    typeSelectExists: !!typeSelect,
                    typeSelectValue: typeSelect ? typeSelect.value : 'element not found',
                    selectedIndex: typeSelect ? typeSelect.selectedIndex : 'N/A',
                    file: file ? file.name : 'no file'
                });
                
                if (!title) {
                    if (titleInput) titleInput.classList.add('is-invalid');
                    showValidationError('Введите название документа');
                    return false;
                }
                if (!type || type === '') {
                    if (typeSelect) {
                        typeSelect.classList.add('is-invalid');
                        // Дополнительная проверка - возможно выбран пустой option
                        console.log('Type validation failed. Selected option:', typeSelect.options[typeSelect.selectedIndex]);
                    }
                    showValidationError('Выберите тип документа');
                    return false;
                }
                if (!file) {
                    if (fileInput) fileInput.classList.add('is-invalid');
                    showValidationError('Выберите файл документа');
                    return false;
                }
                
                // Проверка размера файла (10MB)
                if (file && file.size > 10 * 1024 * 1024) {
                    if (fileInput) fileInput.classList.add('is-invalid');
                    showValidationError('Размер файла не должен превышать 10 МБ');
                    return false;
                }
                
                // Проверка типа файла
                if (file) {
                    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'text/rtf'];
                    if (!allowedTypes.includes(file.type) && !file.name.match(/\.(pdf|doc|docx|txt|rtf)$/i)) {
                        if (fileInput) fileInput.classList.add('is-invalid');
                        showValidationError('Неподдерживаемый тип файла. Разрешены: PDF, DOC, DOCX, TXT, RTF');
                        return false;
                    }
                }
                
                return true;
                
            case 3:
                // Шаг 3 - настройки подписания всегда валидны
                // Все поля опциональны
                return true;
                
            case 4:
                return true; // Подтверждение всегда валидно
        }
        return false;
    }
    
    function updateSummary() {
        if (currentStepGlobal < 4) return;
        
        const recipientTypeInput = document.querySelector('#sendDocumentForm input[name="recipient_type"]:checked');
        let recipientText = '-';
        let documentText = '-';
        let signatureText = '-';
        let deadlineText = '-';
        
        // Получатель
        if (recipientTypeInput) {
            const recipientType = recipientTypeInput.value;
            
            if (recipientType === 'employee') {
                const employeeSelect = document.querySelector('#sendDocumentForm select[name="employee_id"]');
                if (employeeSelect && employeeSelect.selectedIndex > 0) {
                    recipientText = employeeSelect.options[employeeSelect.selectedIndex].text;
                }
            } else if (recipientType === 'client') {
                const projectSelect = document.querySelector('#sendDocumentForm select[name="project_id"]');
                if (projectSelect && projectSelect.selectedIndex > 0) {
                    const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                    const clientName = selectedOption.dataset.clientName;
                    if (clientName) {
                        recipientText = `Клиент: ${clientName}`;
                    }
                }
            } else if (recipientType === 'external') {
                const nameInput = document.querySelector('#sendDocumentForm input[name="recipient_name"]');
                const phoneInput = document.querySelector('#sendDocumentForm input[name="recipient_phone"]');
                const name = nameInput ? nameInput.value.trim() : '';
                const phone = phoneInput ? phoneInput.value.trim() : '';
                if (name && phone) {
                    recipientText = `${name} (${phone})`;
                }
            }
        }
        
        // Документ
        const titleInput = document.querySelector('#sendDocumentForm input[name="title"]');
        const typeSelect = document.querySelector('#sendDocumentForm select[name="document_type"]');
        const title = titleInput ? titleInput.value.trim() : '';
        
        if (title && typeSelect && typeSelect.selectedIndex > 0) {
            const typeText = typeSelect.options[typeSelect.selectedIndex].text;
            documentText = `${title} (${typeText})`;
        }
        
        // Подпись
        const signatureCheckbox = document.getElementById('signatureRequired');
        signatureText = signatureCheckbox && signatureCheckbox.checked ? 'Да' : 'Нет';
        
        // Срок
        const expiresSelect = document.querySelector('#sendDocumentForm select[name="expires_in"]');
        if (expiresSelect && expiresSelect.selectedIndex >= 0) {
            const selectedOption = expiresSelect.options[expiresSelect.selectedIndex];
            deadlineText = selectedOption.text || 'Без ограничения';
        }
        
        // Обновляем элементы сводки
        const summaryRecipient = document.getElementById('summaryRecipient');
        const summaryDocument = document.getElementById('summaryDocument');
        const summarySignature = document.getElementById('summarySignature');
        const summaryDeadline = document.getElementById('summaryDeadline');
        
        if (summaryRecipient) summaryRecipient.textContent = recipientText;
        if (summaryDocument) summaryDocument.textContent = documentText;
        if (summarySignature) summarySignature.textContent = signatureText;
        if (summaryDeadline) summaryDeadline.textContent = deadlineText;
    }
    
    function sendDocument() {
        // Финальная валидация перед отправкой
        if (!validateCurrentStep()) {
            return;
        }
        
        const form = document.getElementById('sendDocumentForm');
        const formData = new FormData();
        
        // Добавляем CSRF токен
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        
        // Добавляем основные поля документа
        const titleInput = form.querySelector('input[name="title"]');
        const typeSelect = form.querySelector('select[name="document_type"]');
        const descriptionInput = form.querySelector('textarea[name="description"]');
        const signatureCheckbox = form.querySelector('input[name="signature_required"]');
        const expiresSelect = form.querySelector('select[name="expires_in"]');
        const prioritySelect = form.querySelector('select[name="priority"]');
        const messageInput = form.querySelector('textarea[name="message"]');
        
        if (titleInput && titleInput.value.trim()) {
            formData.append('title', titleInput.value.trim());
        }
        if (typeSelect && typeSelect.value) {
            formData.append('document_type', typeSelect.value);
        }
        if (descriptionInput && descriptionInput.value.trim()) {
            formData.append('description', descriptionInput.value.trim());
        }
        if (signatureCheckbox) {
            formData.append('signature_required', signatureCheckbox.checked ? '1' : '0');
        }
        if (expiresSelect && expiresSelect.value) {
            formData.append('expires_in', expiresSelect.value);
        }
        if (prioritySelect && prioritySelect.value) {
            formData.append('priority', prioritySelect.value);
        }
        if (messageInput && messageInput.value.trim()) {
            formData.append('message', messageInput.value.trim());
        }
        
        // Добавляем поля получателя в зависимости от типа
        const recipientTypeInput = form.querySelector('input[name="recipient_type"]:checked');
        if (recipientTypeInput) {
            formData.append('recipient_type', recipientTypeInput.value);
            
            if (recipientTypeInput.value === 'employee') {
                const employeeSelect = form.querySelector('select[name="employee_id"]');
                if (employeeSelect && employeeSelect.value) {
                    formData.append('employee_id', employeeSelect.value);
                }
                // Для employee не отправляем поля получателя
            } else if (recipientTypeInput.value === 'client') {
                const projectSelect = form.querySelector('select[name="project_id"]');
                if (projectSelect && projectSelect.value && projectSelect.value !== '') {
                    formData.append('project_id', projectSelect.value);
                }
                // Для client не отправляем поля получателя
            } else if (recipientTypeInput.value === 'external') {
                const nameInput = form.querySelector('input[name="recipient_name"]');
                const phoneInput = form.querySelector('input[name="recipient_phone"]');
                const emailInput = form.querySelector('input[name="recipient_email"]');
                
                if (nameInput && nameInput.value.trim()) {
                    formData.append('recipient_name', nameInput.value.trim());
                }
                if (phoneInput && phoneInput.value.trim()) {
                    formData.append('recipient_phone', phoneInput.value.trim());
                }
                if (emailInput && emailInput.value.trim()) {
                    formData.append('recipient_email', emailInput.value.trim());
                }
            }
        }
        
        // Добавляем файл документа
        const fileInput = form.querySelector('input[name="document_file"]');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('document_file', fileInput.files[0]);
        }
        
        // Отладочный вывод FormData
        console.log('FormData contents after cleanup:');
        for (let [key, value] of formData.entries()) {
            console.log(key, ':', value);
        }
        
        // Показываем индикатор загрузки
        const sendBtn = document.getElementById('sendDocument');
        console.log('Send button found:', !!sendBtn);
        if (sendBtn) {
            console.log('Send button display style:', sendBtn.style.display);
        }
        
        let originalText = '';
        if (sendBtn && sendBtn.style.display !== 'none') {
            originalText = sendBtn.innerHTML;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
            sendBtn.disabled = true;
            console.log('Send button disabled');
        }
        
        fetch('<?php echo e(route("documents.store")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Логируем для отладки
            console.log('Response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);
            
            // Проверяем Content-Type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Если сервер вернул HTML вместо JSON
                return response.text().then(html => {
                    console.error('Server returned HTML instead of JSON:', html.substring(0, 200));
                    throw new Error('Сервер вернул неожиданный ответ. Проверьте консоль для деталей.');
                });
            }
            
            if (!response.ok) {
                return response.json().then(data => {
                    // Обрабатываем ошибки валидации
                    if (response.status === 422 && data.errors) {
                        handleValidationErrors(data.errors);
                        throw new Error('Исправьте ошибки в форме');
                    }
                    throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('success', 'Документ успешно отправлен!', {
                    icon: 'fas fa-check-circle',
                    timeout: 3000
                });
                
                // Закрываем модальное окно
                const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('sendDocumentOffcanvas'));
                if (offcanvas) {
                    offcanvas.hide();
                }
                
                // Обновляем список документов через AJAX
                setTimeout(() => {
                    loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                }, 500);
            } else {
                showNotification('error', data.message || 'Ошибка при отправке документа', {
                    icon: 'fas fa-exclamation-triangle'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', error.message || 'Произошла ошибка при отправке документа', {
                icon: 'fas fa-times-circle'
            });
        })
        .finally(() => {
            if (sendBtn && sendBtn.style.display !== 'none') {
                sendBtn.innerHTML = originalText || '<i class="fas fa-paper-plane"></i> Отправить';
                sendBtn.disabled = false;
            }
        });
    }
    
    // Функция для обработки ошибок валидации от сервера
    function handleValidationErrors(errors) {
        // Логируем все ошибки валидации для отладки
        console.log('Validation errors received:', errors);
        
        // Сначала очищаем все предыдущие ошибки
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        let firstErrorStep = null;
        let firstErrorField = null;
        
        // Отображаем ошибки для каждого поля
        Object.keys(errors).forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');
                
                // Определяем шаг с ошибкой
                let errorStep = 1;
                const stepSections = document.querySelectorAll('.step-section');
                stepSections.forEach((section, index) => {
                    if (section.contains(field)) {
                        errorStep = index + 1;
                    }
                });
                
                // Запоминаем первую ошибку
                if (firstErrorStep === null || errorStep < firstErrorStep) {
                    firstErrorStep = errorStep;
                    firstErrorField = field;
                }
                
                // Показываем первую ошибку для поля
                if (errors[fieldName].length > 0) {
                    showValidationError(errors[fieldName][0]);
                }
            }
        });
        
        // Переходим к шагу с первой ошибкой
        if (firstErrorStep !== null && firstErrorStep !== currentStepGlobal) {
            while (currentStepGlobal !== firstErrorStep) {
                if (currentStepGlobal < firstErrorStep) {
                    hideCurrentStep();
                    currentStepGlobal++;
                    showCurrentStep();
                } else {
                    hideCurrentStep();
                    currentStepGlobal--;
                    showCurrentStep();
                }
            }
            updateStepButtons();
        }
        
        // Фокусируемся на поле с ошибкой
        if (firstErrorField) {
            setTimeout(() => {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
    }
    
    // Функция для показа ошибок валидации
    function showValidationError(message) {
        showNotification('warning', message, {
            icon: 'fas fa-exclamation-triangle',
            timeout: 4000
        });
    }

    // Инициализация глобальных обработчиков подписи документов 
    // Это будет выполнено только один раз при загрузке страницы
    window.initializeGlobalSignatureHandlers = function() {
        // Удаляем обработчики если они уже существуют
        if (window.globalSignatureHandlerInitialized) {
            return;
        }
        window.globalSignatureHandlerInitialized = true;

        // Обработчик глобальной формы подписания
        const globalSignForm = document.getElementById('globalSignDocumentForm');
        if (globalSignForm) {
            globalSignForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const signatureText = document.getElementById('globalSignatureText').value.trim();
                const agreement = document.getElementById('globalSignAgreement').checked;
                const documentId = document.getElementById('documentId').value;
                
                if (!signatureText) {
                    showNotification('warning', 'Введите вашу подпись', {
                        icon: 'fas fa-exclamation-triangle'
                    });
                    return;
                }
                
                if (!agreement) {
                    showNotification('warning', 'Необходимо согласиться с условиями', {
                        icon: 'fas fa-exclamation-triangle'
                    });
                    return;
                }
                
                const submitBtn = document.getElementById('signDocument');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Подписание...';
                }
                
                // Используем функцию из documents-tab.blade.php
                if (typeof window.signDocumentRequest === 'function') {
                    window.signDocumentRequest(documentId, signatureText, agreement)
                    .then(data => {
                        if (data.success) {
                            showNotification('success', data.message || 'Документ успешно подписан', {
                                icon: 'fas fa-check-circle'
                            });
                            
                            // Закрываем боковую панель
                            const offcanvasElement = document.getElementById('signatureOffcanvas');
                            if (offcanvasElement) {
                                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                                if (offcanvas) {
                                    offcanvas.hide();
                                }
                            }
                            
                            // Перезагружаем текущую вкладку
                            const currentTab = document.querySelector('.nav-link.active')?.dataset.tab || currentStepGlobal;
                            if (typeof window.loadTabContent === 'function') {
                                setTimeout(() => {
                                    window.loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                                }, 500);
                            }
                            
                        } else {
                            showNotification('error', data.message || 'Ошибка при подписании документа', {
                                icon: 'fas fa-times-circle'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error signing document:', error);
                        showNotification('error', error.message || 'Произошла ошибка при подписании документа', {
                            icon: 'fas fa-times-circle'
                        });
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-signature me-1"></i>Подписать';
                        }
                    });
                } else {
                    console.error('signDocumentRequest function not found');
                    showNotification('error', 'Ошибка: функция подписания не найдена', {
                        icon: 'fas fa-times-circle'
                    });
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-signature me-1"></i>Подписать';
                    }
                }
            });
        }
    };
}

</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Стили для кликабельной плашки фильтров */
.filter-header-clickable {
    cursor: pointer;
    transition: background-color 0.2s ease;
    user-select: none;
}

.filter-header-clickable:hover {
    background-color: #f8f9fa;
}

.filter-header-clickable .card-header {
    background-color: transparent;
    border-bottom: 1px solid #dee2e6;
}

.document-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.document-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.filter-badge {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    font-size: 0.75rem;
}

.tab-pane {
    min-height: 300px;
}

#loading-indicator {
    background: rgba(255, 255, 255, 0.9);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10;
    border-radius: 0.375rem;
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-color: #007bff #007bff transparent;
}

.badge {
    font-size: 0.7rem;
}

/* Стили для бокового модального окна отправки документа */
.offcanvas-end {
    width: 500px !important;
}

.step-section {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

.step-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 25px;
    height: 25px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    font-size: 0.875rem;
    margin-right: 0.5rem;
}

.option-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.option-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.cursor-pointer {
    cursor: pointer;
}

.offcanvas-footer {
    background: #f8f9fa;
    margin-top: auto;
}

#clientInfo {
    transition: all 0.3s ease;
}

/* Анимации для уведомлений */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Стили для обязательных полей */
.form-label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Улучшенные стили для полей с ошибками */
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Стили для контейнера уведомлений */
#notifications-container .alert {
    backdrop-filter: blur(10px);
    border-left: 4px solid;
}

#notifications-container .alert-success {
    border-left-color: #198754;
    background-color: rgba(212, 237, 218, 0.95);
}

#notifications-container .alert-danger {
    border-left-color: #dc3545;
    background-color: rgba(248, 215, 218, 0.95);
}

#notifications-container .alert-warning {
    border-left-color: #ffc107;
    background-color: rgba(255, 243, 205, 0.95);
}

#notifications-container .alert-info {
    border-left-color: #0dcaf0;
    background-color: rgba(207, 244, 252, 0.95);
}

/* Улучшения для мобильных устройств */
@media (max-width: 768px) {
    .offcanvas-end {
        width: 100% !important;
    }
    
    #notifications-container {
        left: 10px !important;
        right: 10px !important;
        max-width: none !important;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/documents/index.blade.php ENDPATH**/ ?>