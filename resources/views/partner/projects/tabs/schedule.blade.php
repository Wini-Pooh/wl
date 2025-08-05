
<div class="row">
    <div class="col-12">
        <!-- Статистика с улучшенной мобильной адаптацией -->
        <div class="stats-mobile-container d-md-none">
            <div class="stats-mobile-header">
                <i class="bi bi-calendar-check"></i>
                <h6>Статистика проекта</h6>
            </div>
            <div class="stats-mobile-grid">
                <div class="stat-mobile-item stat-primary">
                    <div class="stat-mobile-value" id="stagesCountMobile">0</div>
                    <div class="stat-mobile-label">Всего этапов</div>
                </div>
                <div class="stat-mobile-item stat-success">
                    <div class="stat-mobile-value" id="completedStagesCountMobile">0</div>
                    <div class="stat-mobile-label">Завершено</div>
                </div>
                <div class="stat-mobile-item stat-warning">
                    <div class="stat-mobile-value" id="eventsCountMobile">0</div>
                    <div class="stat-mobile-label">События</div>
                </div>
                <div class="stat-mobile-item stat-info">
                    <div class="stat-mobile-value" id="progressPercentMobile">0%</div>
                    <div class="stat-mobile-label">Прогресс</div>
                </div>
            </div>
        </div>
        
        <!-- Статистика для десктопа -->
        <div class="row mb-4 d-none d-md-flex">
            <div class="col-md-3 border-end text-center">
                <h4 class="text-primary" id="stagesCount">0</h4>
                <small class="text-muted">Всего этапов</small>
            </div>
            <div class="col-md-3 border-end text-center">
                <h4 class="text-success" id="completedStagesCount">0</h4>
                <small class="text-muted">Завершено</small>
            </div>
            <div class="col-md-3 border-end text-center">
                <h4 class="text-warning" id="eventsCount">0</h4>
                <small class="text-muted">События</small>
            </div>
            <div class="col-md-3 text-center">
                <h4 class="text-info" id="progressPercent">0%</h4>
                <small class="text-muted">Прогресс</small>
            </div>
        </div>

        <!-- Вкладки с мобильной адаптацией -->
        <div class="card">
            <div class="card-header p-0">
                <!-- Мобильные вкладки -->
                <ul class="nav nav-tabs-mobile d-md-none" id="scheduleSubTabsMobile" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="stages-tab-mobile" data-bs-toggle="tab" data-bs-target="#stages-pane" type="button" role="tab">
                            <i class="bi bi-list-check me-1"></i>Этапы
                            <span class="badge bg-light text-primary" id="stagesBadgeMobile">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="events-tab-mobile" data-bs-toggle="tab" data-bs-target="#events-pane" type="button" role="tab">
                            <i class="bi bi-calendar-event me-1"></i>События
                            <span class="badge bg-light text-info" id="eventsBadgeMobile">0</span>
                        </button>
                    </li>
                </ul>
                
                <!-- Десктопные вкладки -->
                <ul class="nav nav-tabs card-header-tabs d-none d-md-flex" id="scheduleSubTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="stages-tab" data-bs-toggle="tab" data-bs-target="#stages-pane" type="button" role="tab">
                            <i class="bi bi-list-check me-2"></i>Этапы
                            <span class="badge bg-primary ms-2" id="stagesBadge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events-pane" type="button" role="tab">
                            <i class="bi bi-calendar-event me-2"></i>События
                            <span class="badge bg-info ms-2" id="eventsBadge">0</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content tab-content-mobile" id="scheduleSubTabsContent">
                    <!-- Вкладка Этапы -->
                    <div class="tab-pane fade show active" id="stages-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Этапы проекта</h6>
                            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                            <button class="btn btn-primary btn-sm" type="button" data-modal-type="stage">
                                <i class="bi bi-plus-circle me-1"></i>Добавить этап
                            </button>
                            @endif
                        </div>
                        
                        <!-- Индикатор загрузки для этапов -->
                        <div id="stagesLoader" class="d-none">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                                <div class="mt-2">Загрузка этапов...</div>
                            </div>
                        </div>
                        
                        <!-- Контейнер для этапов -->
                        <div id="stagesContainer" class="stages-container">
                            <!-- Этапы будут загружаться через AJAX -->
                        </div>
                        
                        <!-- Пустое состояние для этапов -->
                        <div class="text-center py-5 d-none" id="emptyStagesState">
                            <i class="bi bi-list-check text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">Этапы не добавлены</h4>
                            <p class="text-muted">Добавьте первый этап для управления ходом проекта</p>
                            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                            <button class="btn btn-primary" data-modal-type="stage">
                                <i class="bi bi-plus-circle me-1"></i>Добавить первый этап
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Вкладка События -->
                    <div class="tab-pane fade" id="events-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">События проекта</h6>
                            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                            <button class="btn btn-info btn-sm" type="button" data-modal-type="event">
                                <i class="bi bi-plus-circle me-1"></i>Добавить событие
                            </button>
                            @endif
                        </div>
                        
                        <!-- Индикатор загрузки для событий -->
                        <div id="eventsLoader" class="d-none">
                            <div class="text-center py-5">
                                <div class="spinner-border text-info" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                                <div class="mt-2">Загрузка событий...</div>
                            </div>
                        </div>
                        
                        <!-- Контейнер для событий -->
                        <div id="eventsContainer" class="events-container">
                            <!-- События будут загружаться через AJAX -->
                        </div>
                        
                        <!-- Пустое состояние для событий -->
                        <div class="text-center py-5 d-none" id="emptyEventsState">
                            <i class="bi bi-calendar-event text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">События не добавлены</h4>
                            <p class="text-muted">Добавьте первое событие для планирования проекта</p>
                            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                            <button class="btn btn-info" data-modal-type="event">
                                <i class="bi bi-plus-circle me-1"></i>Добавить первое событие
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
