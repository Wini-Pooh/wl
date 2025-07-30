<!-- Аналитика проектов -->
<div class="row mb-4">
    <!-- Основные метрики проектов -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                <div class="display-4 text-primary">{{ $data['total_projects'] ?? 0 }}</div>
                <h6 class="card-title">Всего проектов</h6>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                <div class="display-4 text-success">{{ $data['new_projects'] ?? 0 }}</div>
                <h6 class="card-title">Новые проекты</h6>
                <small class="text-muted">За выбранный период</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                <div class="display-4 text-info">{{ number_format($data['avg_project_cost'] ?? 0, 0, ',', ' ') }} ₽</div>
                <h6 class="card-title">Средняя стоимость</h6>
                <small class="text-muted">За проект</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                @php
                    $activeProjects = collect($data['projects_by_status'] ?? [])->get('in_progress', 0);
                @endphp
                <div class="display-4 text-warning">{{ $activeProjects }}</div>
                <h6 class="card-title">В работе</h6>
                <small class="text-muted">Активных проектов</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Динамика создания проектов -->
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Динамика создания проектов
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="projectsDynamicsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Статусы проектов -->
    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Статусы проектов
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="projectsStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Типы работ -->
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-tools me-2"></i>
                    Распределение по типам работ
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="workTypesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Типы объектов -->
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-building me-2"></i>
                    Распределение по типам объектов
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="objectTypesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Детальная таблица проектов -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Статистика по статусам проектов
                </h6>
            </div>
            <div class="card-body">
                @if(isset($data['projects_by_status']) && count($data['projects_by_status']) > 0)
                    <div class="row">
                        @foreach($data['projects_by_status'] as $status => $count)
                            @php
                                $statusLabels = [
                                    'draft' => ['label' => 'Черновики', 'color' => 'secondary', 'icon' => 'bi-file-text'],
                                    'active' => ['label' => 'Активные', 'color' => 'primary', 'icon' => 'bi-play-circle'],
                                    'in_progress' => ['label' => 'В работе', 'color' => 'warning', 'icon' => 'bi-gear'],
                                    'completed' => ['label' => 'Завершены', 'color' => 'success', 'icon' => 'bi-check-circle'],
                                    'cancelled' => ['label' => 'Отменены', 'color' => 'danger', 'icon' => 'bi-x-circle']
                                ];
                                $statusInfo = $statusLabels[$status] ?? ['label' => $status, 'color' => 'secondary', 'icon' => 'bi-circle'];
                            @endphp
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                <div class="card border-{{ $statusInfo['color'] }}">
                                    <div class="card-body text-center">
                                        <i class="bi {{ $statusInfo['icon'] }} text-{{ $statusInfo['color'] }}" style="font-size: 2rem;"></i>
                                        <h4 class="mt-2 text-{{ $statusInfo['color'] }}">{{ $count }}</h4>
                                        <p class="card-text small">{{ $statusInfo['label'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">Нет данных о проектах</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Типы работ детально -->
@if(isset($data['projects_by_work_type']) && count($data['projects_by_work_type']) > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-tools me-2"></i>
                    Детализация по типам работ
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Тип работ</th>
                                <th class="text-center">Количество проектов</th>
                                <th class="text-end">Процент</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalWorkTypeProjects = $data['projects_by_work_type']->sum();
                                $workTypeLabels = [
                                    'construction' => 'Строительство',
                                    'renovation' => 'Ремонт', 
                                    'design' => 'Дизайн',
                                    'consulting' => 'Консультирование',
                                    'maintenance' => 'Обслуживание'
                                ];
                            @endphp
                            @foreach($data['projects_by_work_type'] as $type => $count)
                                @php
                                    $percentage = $totalWorkTypeProjects > 0 ? ($count / $totalWorkTypeProjects * 100) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $workTypeLabels[$type] ?? $type }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="progress me-2" style="width: 100px; height: 20px;">
                                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="small">{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем графики для вкладки проектов
    if (document.getElementById('projectsDynamicsChart')) {
        initProjectsCharts();
    }
});

function initProjectsCharts() {
    // Динамика создания проектов
    const projectsDynamicsCtx = document.getElementById('projectsDynamicsChart').getContext('2d');
    const projectsDynamics = @json($data['projects_by_days'] ?? []);
    
    createLineChart(projectsDynamicsCtx, 'Новые проекты', projectsDynamics, '#007bff');

    // Статусы проектов
    const projectsStatusCtx = document.getElementById('projectsStatusChart').getContext('2d');
    const projectsStatus = @json($data['projects_by_status'] ?? []);
    
    if (Object.keys(projectsStatus).length > 0) {
        const statusLabels = {
            'draft': 'Черновики',
            'active': 'Активные',
            'in_progress': 'В работе',
            'completed': 'Завершены',
            'cancelled': 'Отменены'
        };
        
        const translatedStatus = {};
        Object.keys(projectsStatus).forEach(key => {
            translatedStatus[statusLabels[key] || key] = projectsStatus[key];
        });
        
        createPieChart(projectsStatusCtx, translatedStatus, [
            '#6c757d', '#007bff', '#ffc107', '#28a745', '#dc3545'
        ]);
    }

    // Типы работ
    const workTypesCtx = document.getElementById('workTypesChart').getContext('2d');
    const workTypes = @json($data['projects_by_work_type'] ?? []);
    
    if (Object.keys(workTypes).length > 0) {
        const workTypeLabels = {
            'construction': 'Строительство',
            'renovation': 'Ремонт',
            'design': 'Дизайн',
            'consulting': 'Консультирование',
            'maintenance': 'Обслуживание'
        };
        
        const translatedTypes = {};
        Object.keys(workTypes).forEach(key => {
            translatedTypes[workTypeLabels[key] || key] = workTypes[key];
        });
        
        createPieChart(workTypesCtx, translatedTypes);
    }

    // Типы объектов
    const objectTypesCtx = document.getElementById('objectTypesChart').getContext('2d');
    const objectTypes = @json($data['projects_by_object_type'] ?? []);
    
    if (Object.keys(objectTypes).length > 0) {
        const objectTypeLabels = {
            'residential' => 'Жилое',
            'commercial' => 'Коммерческое',
            'industrial' => 'Промышленное',
            'office' => 'Офисное',
            'other' => 'Другое'
        };
        
        const translatedObjectTypes = {};
        Object.keys(objectTypes).forEach(key => {
            translatedObjectTypes[objectTypeLabels[key] || key] = objectTypes[key];
        });
        
        createPieChart(objectTypesCtx, translatedObjectTypes);
    }
}
</script>


