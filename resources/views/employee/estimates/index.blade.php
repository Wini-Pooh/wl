@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-calculator-fill text-primary me-2"></i>
                    Сметы
                </h1>
                
                @if(Auth::user()->isEstimator() || Auth::user()->isEmployee() || Auth::user()->isPartner() || Auth::user()->isAdmin())
                <div class="btn-group">
                    <a href="{{ route('partner.estimates.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Создать смету
                    </a>
                </div>
                @endif
            </div>

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('employee.estimates.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Поиск</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Название, описание или клиент">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="status" class="form-label">Статус</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Все статусы</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
                                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Отправлено</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Одобрено</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Отклонено</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="type" class="form-label">Тип</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Все типы</option>
                                <option value="work" {{ request('type') === 'work' ? 'selected' : '' }}>Работы</option>
                                <option value="material" {{ request('type') === 'material' ? 'selected' : '' }}>Материалы</option>
                                <option value="transport" {{ request('type') === 'transport' ? 'selected' : '' }}>Транспорт</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i> Найти
                            </button>
                            <a href="{{ route('employee.estimates.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Список смет -->
            <div class="card">
                <div class="card-body">
                    @if($estimates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Название</th>
                                        <th>Проект</th>
                                        <th>Тип</th>
                                        <th>Статус</th>
                                        <th>Сумма</th>
                                        <th>Дата создания</th>
                                        <th width="120">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estimates as $estimate)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $estimate->name }}</div>
                                            @if($estimate->description)
                                                <small class="text-muted">{{ Str::limit($estimate->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($estimate->project)
                                                <span class="badge bg-light text-dark">{{ $estimate->project->client_name ?? 'Без названия' }}</span>
                                            @else
                                                <span class="text-muted">Проект удален</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($estimate->type)
                                                @case('work')
                                                    <span class="badge bg-primary">Работы</span>
                                                    @break
                                                @case('material')
                                                    <span class="badge bg-warning">Материалы</span>
                                                    @break
                                                @case('transport')
                                                    <span class="badge bg-info">Транспорт</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $estimate->type }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($estimate->status)
                                                @case('draft')
                                                    <span class="badge bg-secondary">Черновик</span>
                                                    @break
                                                @case('sent')
                                                    <span class="badge bg-primary">Отправлено</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">Одобрено</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Отклонено</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ $estimate->status }}</span>
                                            @endswitch
                                        </td>
                                        <td class="fw-semibold">
                                            {{ number_format($estimate->total_amount, 0, ',', ' ') }} ₽
                                        </td>
                                        <td>
                                            {{ $estimate->created_at->format('d.m.Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('partner.estimates.show', $estimate) }}" 
                                                   class="btn btn-outline-primary" title="Просмотр">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                @if(Auth::user()->isEstimator() || Auth::user()->isEmployee() || Auth::user()->isPartner() || Auth::user()->isAdmin())
                                                <a href="{{ route('partner.estimates.edit', $estimate) }}" 
                                                   class="btn btn-outline-secondary" title="Редактировать">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Пагинация -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Показано {{ $estimates->firstItem() }}-{{ $estimates->lastItem() }} 
                                из {{ $estimates->total() }} записей
                            </div>
                            {{ $estimates->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calculator display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Смет не найдено</h5>
                            <p class="text-muted mb-4">
                                @if(request()->hasAny(['search', 'status', 'type']))
                                    Попробуйте изменить параметры поиска
                                @else
                                    Здесь будут отображаться ваши сметы
                                @endif
                            </p>
                            @if(Auth::user()->isEstimator() || Auth::user()->isEmployee() || Auth::user()->isPartner() || Auth::user()->isAdmin())
                            <a href="{{ route('partner.estimates.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Создать первую смету
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоотправка формы при изменении фильтров
    const statusSelect = document.getElementById('status');
    const typeSelect = document.getElementById('type');
    
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endpush
