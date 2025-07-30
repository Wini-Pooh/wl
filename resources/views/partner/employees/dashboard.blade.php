@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-2 mb-md-0">
            <i class="bi bi-graph-up me-2"></i>
            <span class="d-none d-sm-inline">Финансовый дашборд сотрудников</span>
            <span class="d-sm-none">Финансы сотрудников</span>
        </h2>
        <div class="btn-group flex-wrap">
            <a href="{{ route('partner.employees.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-people me-2"></i>
                <span class="d-none d-sm-inline">Сотрудники</span>
            </a>
            <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-2"></i>
                <span class="d-none d-sm-inline">Экспорт</span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                    <i class="bi bi-file-pdf me-2"></i>PDF отчёт
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportReport('excel')">
                    <i class="bi bi-file-excel me-2"></i>Excel отчёт
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Основная статистика -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Всего к выплате</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_pending'], 0, ',', ' ') }} ₽</h3>
                            <small class="opacity-75">За все время</small>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-danger  h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Просрочено</h6>
                            <h3 class="mb-0">{{ number_format($stats['overdue'], 0, ',', ' ') }} ₽</h3>
                            <small class="opacity-75">Критично</small>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning  h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">На этой неделе</h6>
                            <h3 class="mb-0">{{ number_format($stats['upcoming_week'], 0, ',', ' ') }} ₽</h3>
                            <small class="opacity-75">Ближайшие выплаты</small>
                        </div>
                        <i class="bi bi-calendar-week fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success  h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Выплачено</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_paid'], 0, ',', ' ') }} ₽</h3>
                            <small class="opacity-75">За месяц</small>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Распределение по типам -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Распределение по типам платежей
                    </h6>
                </div>
                <div class="card-body">
                    @if($byType && $byType->count() > 0)
                        @foreach($byType as $type => $amount)
                            @php
                                $percentage = $stats['total_pending'] > 0 ? ($amount / $stats['total_pending']) * 100 : 0;
                                $typeLabel = \App\Models\EmployeeFinance::getTypes()[$type] ?? $type;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-medium">{{ $typeLabel }}</span>
                                    <span class="small">{{ number_format($amount, 0, ',', ' ') }} ₽ ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-pie-chart" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">Нет данных для отображения</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Предстоящие платежи -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        Ближайшие платежи
                    </h6>
                </div>
                <div class="card-body">
                    @if($upcomingPayments && $upcomingPayments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingPayments as $payment)
                                <div class="list-group-item px-0 border-start-0 border-end-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-medium">{{ $payment->employee->short_name }}</div>
                                            <div class="small text-muted">{{ $payment->title }}</div>
                                            <div class="small">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $payment->due_date->format('d.m.Y') }}
                                                @if($payment->project)
                                                    • <i class="bi bi-building me-1"></i>{{ $payment->project->name }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-primary">
                                                {{ number_format($payment->amount, 0, ',', ' ') }} ₽
                                            </div>
                                            @if($payment->is_overdue)
                                                <span class="badge bg-danger small">Просрочено</span>
                                            @elseif($payment->days_until_due <= 3)
                                                <span class="badge bg-warning small">Срочно</span>
                                            @else
                                                <span class="badge bg-info small">{{ $payment->days_until_due }} дн.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('partner.employees.index') }}" class="btn btn-outline-primary btn-sm">
                                Посмотреть все платежи
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-check" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">Нет предстоящих платежей</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Сотрудники с наибольшими задолженностями -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-person-exclamation me-2"></i>
                Сотрудники с наибольшими задолженностями
            </h6>
        </div>
        <div class="card-body">
            @if($topDebtors && $topDebtors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th>Роль</th>
                                <th>Всего к выплате</th>
                                <th>Просрочено</th>
                                <th>Последний платеж</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topDebtors as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $employee->short_name }}</div>
                                                <div class="small text-muted">{{ $employee->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $employee->role_name }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-warning">
                                            {{ number_format($employee->total_pending, 0, ',', ' ') }} ₽
                                        </span>
                                    </td>
                                    <td>
                                        @if($employee->overdue_amount > 0)
                                            <span class="fw-bold text-danger">
                                                {{ number_format($employee->overdue_amount, 0, ',', ' ') }} ₽
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->last_payment_date)
                                            <span class="small">{{ $employee->last_payment_date->format('d.m.Y') }}</span>
                                        @else
                                            <span class="text-muted">Нет платежей</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('partner.employees.show', $employee) }}" 
                                               class="btn btn-outline-primary" title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-emoji-smile" style="font-size: 3rem; color: #28a745;"></i>
                    <h5 class="mt-3 text-success">Отлично!</h5>
                    <p class="text-muted">У всех сотрудников нет задолженностей</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Модальное окно быстрого платежа -->
<div class="modal fade" id="quickPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Быстрое добавление платежа</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickPaymentForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="quick_employee_id" name="employee_id">
                    
                    <div class="mb-3">
                        <label for="quick_type" class="form-label">Тип платежа *</label>
                        <select class="form-select" id="quick_type" name="type" required>
                            @foreach(\App\Models\EmployeeFinance::getTypes() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quick_amount" class="form-label">Сумма *</label>
                        <input type="number" class="form-control" id="quick_amount" name="amount" 
                               step="0.01" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quick_title" class="form-label">Описание *</label>
                        <input type="text" class="form-control" id="quick_title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quick_due_date" class="form-label">Дата к выплате *</label>
                        <input type="date" class="form-control" id="quick_due_date" name="due_date" required>
                    </div>
                    
                    <input type="hidden" name="currency" value="RUB">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить платеж</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function quickPayment(employeeId) {
    document.getElementById('quick_employee_id').value = employeeId;
    
    // Устанавливаем дату по умолчанию на завтра
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('quick_due_date').value = tomorrow.toISOString().split('T')[0];
    
    const modal = new bootstrap.Modal(document.getElementById('quickPaymentModal'));
    modal.show();
}

document.getElementById('quickPaymentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const employeeId = document.getElementById('quick_employee_id').value;
    
    try {
        const formData = new FormData(this);
        const response = await fetch(`/partner/employees/${employeeId}/finances`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Ошибка при добавлении платежа');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('Ошибка при добавлении платежа');
    }
});

function exportReport(format) {
    // Здесь можно добавить логику экспорта
    alert(`Экспорт в ${format.toUpperCase()} будет реализован позже`);
}
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
}

.progress {
    background-color: rgba(0,0,0,0.1);
}

.card .card-body .list-group-item:first-child {
    padding-top: 0;
}

.card .card-body .list-group-item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}
</style>
@endpush
