@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h2 class="mb-2 mb-md-0">
                    <i class="bi bi-people me-2"></i>
                    <span class="d-none d-sm-inline">Управление сотрудниками</span>
                    <span class="d-sm-none">Сотрудники</span>
                </h2>
                <div class="btn-group flex-wrap">
                    <a href="{{ route('partner.employees.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Добавить сотрудника</span>
                        <span class="d-sm-none">Добавить</span>
                    </a>
                    <a href="{{ route('partner.employees.dashboard') }}" class="btn btn-success">
                        <i class="bi bi-graph-up me-2"></i>
                        <span class="d-none d-sm-inline">Финансовый дашборд</span>
                        <span class="d-sm-none">Финансы</span>
                    </a>
                </div>
            </div>

            <!-- Статистика -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Всего сотрудников</h6>
                                    <h3>{{ $stats['total'] }}</h3>
                                </div>
                                <i class="bi bi-people fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Активных</h6>
                                    <h3>{{ $stats['active'] }}</h3>
                                </div>
                                <i class="bi bi-person-check fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">К выплате</h6>
                                    <h3>{{ number_format($stats['pending_payments'], 0, ',', ' ') }} ₽</h3>
                                    <small class="text-light">Общая сумма задолженности</small>
                                </div>
                                <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger ">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Просрочено</h6>
                                    <h3>{{ number_format($stats['overdue_payments'], 0, ',', ' ') }} ₽</h3>
                                    <small class="text-light">Критические долги</small>
                                </div>
                                <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Фильтры и поиск -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('partner.employees.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Поиск по ФИО/телефону</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Введите для поиска...">
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Роль</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">Все роли</option>
                                    @foreach(\App\Models\Employee::getRoles() as $key => $label)
                                        <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Статус</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Все статусы</option>
                                    @foreach(\App\Models\Employee::getStatuses() as $key => $label)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Поиск
                                </button>
                                <a href="{{ route('partner.employees.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Сброс
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Список сотрудников -->
            <div class="card">
                <div class="card-body">
                    @if($employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ФИО</th>
                                        <th>Телефон</th>
                                        <th>Роль</th>
                                        <th>Статус</th>
                                        <th>К выплате</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $employee->short_name }}</h6>
                                                        @if($employee->email)
                                                            <small class="text-muted">{{ $employee->email }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="tel:{{ $employee->phone }}" class="text-decoration-none">
                                                    {{ $employee->phone }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $employee->role_name }}</span>
                                            </td>
                                            <td>
                                                @if($employee->status === 'active')
                                                    <span class="badge bg-success">{{ $employee->status_name }}</span>
                                                @elseif($employee->status === 'inactive')
                                                    <span class="badge bg-warning">{{ $employee->status_name }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $employee->status_name }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $pendingAmount = $employee->finances->where('status', 'pending')->sum('amount');
                                                    $overdueAmount = $employee->finances->where('status', 'overdue')->sum('amount');
                                                @endphp
                                                <div>
                                                    @if($pendingAmount > 0)
                                                        <div class="text-warning">
                                                            <i class="bi bi-clock-fill"></i>
                                                            {{ number_format($pendingAmount, 0, ',', ' ') }} ₽
                                                        </div>
                                                    @endif
                                                    @if($overdueAmount > 0)
                                                        <div class="text-danger">
                                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                                            {{ number_format($overdueAmount, 0, ',', ' ') }} ₽
                                                        </div>
                                                    @endif
                                                    @if($pendingAmount == 0 && $overdueAmount == 0)
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('partner.employees.show', $employee) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Просмотр">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('partner.employees.edit', $employee) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Редактировать">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete({{ $employee->id }})" title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагинация -->
                        @if($employees->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $employees->withQueryString()->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">Сотрудники не найдены</h5>
                            <p class="text-muted">Добавьте первого сотрудника или измените параметры поиска</p>
                            <a href="{{ route('partner.employees.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Добавить сотрудника
                            </a>
                        </div>
                    @endif
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
                <p>Вы уверены, что хотите удалить этого сотрудника?</p>
                <p class="text-danger"><strong>Внимание:</strong> Все финансовые записи сотрудника также будут удалены!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(employeeId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/partner/employees/${employeeId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Автоотправка формы при изменении фильтров
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const selects = form.querySelectorAll('select');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            form.submit();
        });
    });
});
</script>
@endpush
