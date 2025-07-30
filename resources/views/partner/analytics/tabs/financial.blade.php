<!-- Финансовая аналитика -->
<div class="row mb-4">
    <!-- Основные метрики -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card bg-revenue">
            <div class="metric-value">{{ number_format($data['project_revenue'] ?? 0, 0, ',', ' ') }} ₽</div>
            <div class="metric-label">Доходы</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card bg-expenses">
            <div class="metric-value">{{ number_format($data['project_expenses'] ?? 0, 0, ',', ' ') }} ₽</div>
            <div class="metric-label">Расходы</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card bg-profit">
            <div class="metric-value">{{ number_format($data['net_profit'] ?? 0, 0, ',', ' ') }} ₽</div>
            <div class="metric-label">Прибыль</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card bg-projects">
            <div class="metric-value">{{ number_format($data['total_employee_debt'] ?? 0, 0, ',', ' ') }} ₽</div>
            <div class="metric-label">Задолженность</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Динамика доходов и расходов -->
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up-arrow me-2"></i>
                    Динамика доходов и расходов
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueExpensesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Распределение расходов по типам -->
    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Расходы по типам
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="expensesByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Статус платежей сотрудникам -->
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-credit-card me-2"></i>
                    Статус платежей сотрудникам
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Топ проектов по доходности -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-trophy me-2"></i>
                    Топ проектов по доходности
                </h6>
            </div>
            <div class="card-body">
                @if(isset($data['top_projects']) && $data['top_projects']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Проект</th>
                                    <th>Клиент</th>
                                    <th class="text-end">Стоимость</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['top_projects'] as $project)
                                <tr>
                                    <td>
                                        <a href="{{ route('partner.projects.show', $project->id) }}" class="text-decoration-none">
                                            #{{ $project->id }}
                                        </a>
                                    </td>
                                    <td>{{ $project->client_first_name }} {{ $project->client_last_name }}</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($project->total_cost, 0, ',', ' ') }} ₽</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'active' => 'primary', 
                                                'in_progress' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'draft' => 'Черновик',
                                                'active' => 'Активный',
                                                'in_progress' => 'В работе', 
                                                'completed' => 'Завершен',
                                                'cancelled' => 'Отменен'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$project->project_status] ?? 'secondary' }}">
                                            {{ $statusLabels[$project->project_status] ?? $project->project_status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

<!-- Просроченные платежи -->
@if(isset($data['overdue_finances']) && $data['overdue_finances']->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-danger ">
                <h6 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Просроченные платежи ({{ $data['overdue_finances']->count() }})
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th>Тип</th>
                                <th class="text-end">Сумма</th>
                                <th>Срок платежа</th>
                                <th>Просрочено</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['overdue_finances'] as $finance)
                            <tr>
                                <td>
                                    <a href="{{ route('partner.employees.show', $finance->employee->id) }}" class="text-decoration-none">
                                        {{ $finance->employee->full_name }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $types = [
                                            'salary' => 'Зарплата',
                                            'bonus' => 'Премия', 
                                            'advance' => 'Аванс',
                                            'expense' => 'Расходы'
                                        ];
                                    @endphp
                                    <span class="badge bg-info">{{ $types[$finance->type] ?? $finance->type }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">{{ number_format($finance->amount, 0, ',', ' ') }} ₽</strong>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($finance->due_date)->format('d.m.Y') }}</td>
                                <td>
                                    @php
                                        $daysOverdue = \Carbon\Carbon::parse($finance->due_date)->diffInDays(now());
                                    @endphp
                                    <span class="badge bg-danger">{{ $daysOverdue }} дн.</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success" onclick="markAsPaid({{ $finance->id }})">
                                        <i class="bi bi-check-circle"></i> Оплачено
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
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем графики для финансовой вкладки
    if (document.getElementById('revenueExpensesChart')) {
        initFinancialCharts();
    }
});

function initFinancialCharts() {
    // Динамика доходов и расходов
    const revenueExpensesCtx = document.getElementById('revenueExpensesChart').getContext('2d');
    
    const revenueData = @json($data['revenue_by_days'] ?? []);
    const expensesData = @json($data['employee_expenses_by_days'] ?? []);
    
    new Chart(revenueExpensesCtx, {
        type: 'line',
        data: {
            datasets: [{
                label: 'Доходы',
                data: revenueData,
                borderColor: '#28a745',
                backgroundColor: '#28a74520',
                tension: 0.4,
                fill: false
            }, {
                label: 'Расходы',
                data: expensesData,
                borderColor: '#dc3545', 
                backgroundColor: '#dc354520',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day',
                        displayFormats: {
                            day: 'dd.MM'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('ru-RU', {
                                style: 'currency',
                                currency: 'RUB'
                            }).format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat('ru-RU', {
                                style: 'currency',
                                currency: 'RUB'
                            }).format(context.parsed.y);
                        }
                    }
                }
            }
        }
    });

    // Расходы по типам
    const expensesByTypeCtx = document.getElementById('expensesByTypeChart').getContext('2d');
    const expensesByType = @json($data['employee_finances_by_type'] ?? []);
    
    if (Object.keys(expensesByType).length > 0) {
        createPieChart(expensesByTypeCtx, expensesByType, ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']);
    }

    // Статус платежей
    const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    const paymentStatus = @json($data['employee_finances_by_status'] ?? []);
    
    if (Object.keys(paymentStatus).length > 0) {
        const statusLabels = {
            'pending': 'Ожидает',
            'paid': 'Оплачено',
            'overdue': 'Просрочено'
        };
        
        const translatedStatus = {};
        Object.keys(paymentStatus).forEach(key => {
            translatedStatus[statusLabels[key] || key] = paymentStatus[key];
        });
        
        createPieChart(paymentStatusCtx, translatedStatus, ['#ffc107', '#28a745', '#dc3545']);
    }
}

function markAsPaid(financeId) {
    if (confirm('Отметить платеж как выполненный?')) {
        fetch(`/partner/employees/finances/${financeId}/mark-paid`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка');
        });
    }
}
</script>


