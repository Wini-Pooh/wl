<!-- Аналитика сотрудников -->
<div class="row mb-4">
    <!-- Основные метрики сотрудников -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                <div class="display-4 text-primary">{{ $data['total_employees'] ?? 0 }}</div>
                <h6 class="card-title">Всего сотрудников</h6>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                <div class="display-4 text-success">{{ $data['active_employees'] ?? 0 }}</div>
                <h6 class="card-title">Активных</h6>
                <small class="text-muted">Работающих сотрудников</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                <div class="display-4 text-warning">{{ count($data['top_debtors'] ?? []) }}</div>
                <h6 class="card-title">С задолженностью</h6>
                <small class="text-muted">Требуют выплат</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card analytics-card">
            <div class="card-body text-center">
                <div class="display-4 text-info">{{ count($data['top_earners'] ?? []) }}</div>
                <h6 class="card-title">Топ исполнителей</h6>
                <small class="text-muted">С наибольшими выплатами</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Распределение по ролям -->
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Распределение по ролям
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="employeeRolesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Статусы сотрудников -->
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-person-check me-2"></i>
                    Статусы сотрудников
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="employeeStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Топ сотрудников с задолженностями -->
@if(isset($data['top_debtors']) && count($data['top_debtors']) > 0)
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Топ задолжников
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th class="text-end">Задолженность</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['top_debtors'] as $debtor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-warning text-dark rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            {{ substr($debtor->first_name, 0, 1) }}{{ substr($debtor->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('partner.employees.show', $debtor->id) }}" class="text-decoration-none">
                                                {{ $debtor->first_name }} {{ $debtor->last_name }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">{{ number_format($debtor->total_debt, 0, ',', ' ') }} ₽</strong>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewEmployee({{ $debtor->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Топ исполнители -->
    @if(isset($data['top_earners']) && count($data['top_earners']) > 0)
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success ">
                <h6 class="mb-0">
                    <i class="bi bi-trophy me-2"></i>
                    Топ исполнители
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th class="text-end">Выплачено</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['top_earners'] as $earner)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-success  rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            {{ substr($earner->first_name, 0, 1) }}{{ substr($earner->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('partner.employees.show', $earner->id) }}" class="text-decoration-none">
                                                {{ $earner->first_name }} {{ $earner->last_name }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">{{ number_format($earner->total_earned, 0, ',', ' ') }} ₽</strong>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewEmployee({{ $earner->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Статистика по ролям -->
@if(isset($data['employees_by_role']) && count($data['employees_by_role']) > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-diagram-3 me-2"></i>
                    Детализация по ролям
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($data['employees_by_role'] as $role => $count)
                        @php
                            $roleLabels = [
                                'employee' => ['label' => 'Сотрудники', 'color' => 'primary', 'icon' => 'bi-person'],
                                'foreman' => ['label' => 'Прорабы', 'color' => 'warning', 'icon' => 'bi-person-gear'],
                                'estimator' => ['label' => 'Сметчики', 'color' => 'info', 'icon' => 'bi-calculator'],
                                'admin' => ['label' => 'Администраторы', 'color' => 'danger', 'icon' => 'bi-shield-check']
                            ];
                            $roleInfo = $roleLabels[$role] ?? ['label' => $role, 'color' => 'secondary', 'icon' => 'bi-person'];
                        @endphp
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-{{ $roleInfo['color'] }}">
                                <div class="card-body text-center">
                                    <i class="bi {{ $roleInfo['icon'] }} text-{{ $roleInfo['color'] }}" style="font-size: 2rem;"></i>
                                    <h4 class="mt-2 text-{{ $roleInfo['color'] }}">{{ $count }}</h4>
                                    <p class="card-text small">{{ $roleInfo['label'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Дополнительная статистика -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>
                    Сводная информация
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-end">
                            <h5 class="text-primary">{{ $data['total_employees'] ?? 0 }}</h5>
                            <p class="text-muted mb-0">Общее количество</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            @php
                                $totalEmployees = $data['total_employees'] ?? 0;
                                $activeEmployees = $data['active_employees'] ?? 0;
                                $activePercentage = $totalEmployees > 0 ? ($activeEmployees / $totalEmployees * 100) : 0;
                            @endphp
                            <h5 class="text-success">{{ number_format($activePercentage, 1) }}%</h5>
                            <p class="text-muted mb-0">Активность</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            @php
                                $debtorCount = count($data['top_debtors'] ?? []);
                                $debtorPercentage = $totalEmployees > 0 ? ($debtorCount / $totalEmployees * 100) : 0;
                            @endphp
                            <h5 class="text-warning">{{ number_format($debtorPercentage, 1) }}%</h5>
                            <p class="text-muted mb-0">С задолженностью</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        @php
                            $earnerCount = count($data['top_earners'] ?? []);
                            $earnerPercentage = $totalEmployees > 0 ? ($earnerCount / $totalEmployees * 100) : 0;
                        @endphp
                        <h5 class="text-info">{{ number_format($earnerPercentage, 1) }}%</h5>
                        <p class="text-muted mb-0">Топ исполнители</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем графики для вкладки сотрудников
    if (document.getElementById('employeeRolesChart')) {
        initEmployeesCharts();
    }
});

function initEmployeesCharts() {
    // Распределение по ролям
    const employeeRolesCtx = document.getElementById('employeeRolesChart').getContext('2d');
    const employeeRoles = @json($data['employees_by_role'] ?? []);
    
    if (Object.keys(employeeRoles).length > 0) {
        const roleLabels = {
            'employee': 'Сотрудники',
            'foreman': 'Прорабы',
            'estimator': 'Сметчики',
            'admin': 'Администраторы'
        };
        
        const translatedRoles = {};
        Object.keys(employeeRoles).forEach(key => {
            translatedRoles[roleLabels[key] || key] = employeeRoles[key];
        });
        
        createPieChart(employeeRolesCtx, translatedRoles, [
            '#007bff', '#ffc107', '#17a2b8', '#dc3545'
        ]);
    }

    // Статусы сотрудников
    const employeeStatusCtx = document.getElementById('employeeStatusChart').getContext('2d');
    const employeeStatus = @json($data['employees_by_status'] ?? []);
    
    if (Object.keys(employeeStatus).length > 0) {
        const statusLabels = {
            'active': 'Активные',
            'inactive': 'Неактивные',
            'suspended': 'Приостановлены',
            'terminated': 'Уволены'
        };
        
        const translatedStatus = {};
        Object.keys(employeeStatus).forEach(key => {
            translatedStatus[statusLabels[key] || key] = employeeStatus[key];
        });
        
        createPieChart(employeeStatusCtx, translatedStatus, [
            '#28a745', '#6c757d', '#ffc107', '#dc3545'
        ]);
    }
}

function viewEmployee(employeeId) {
    window.location.href = `/partner/employees/${employeeId}`;
}
</script>


