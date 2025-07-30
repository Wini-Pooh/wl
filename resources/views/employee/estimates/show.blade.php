@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-calculator-fill text-primary me-2"></i>
                    {{ $estimate->name }}
                </h1>
                
                <div class="btn-group">
                    <a href="{{ route('employee.estimates.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Назад к сметам
                    </a>
                    
                    @if(Auth::user()->isEstimator() || Auth::user()->isEmployee() || Auth::user()->isPartner() || Auth::user()->isAdmin())
                    <a href="{{ route('partner.estimates.edit', $estimate) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    @endif
                </div>
            </div>

            <!-- Информация о смете -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Информация о смете</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Проект:</strong>
                                    @if($estimate->project)
                                        <span class="badge bg-light text-dark">{{ $estimate->project->client_name ?? 'Без названия' }}</span>
                                    @else
                                        <span class="text-muted">Проект удален</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Тип:</strong>
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
                                </div>
                                <div class="col-md-6 mt-2">
                                    <strong>Статус:</strong>
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
                                </div>
                                <div class="col-md-6 mt-2">
                                    <strong>Дата создания:</strong>
                                    {{ $estimate->created_at->format('d.m.Y H:i') }}
                                </div>
                            </div>
                            
                            @if($estimate->description)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Описание:</strong>
                                    <p class="mt-1">{{ $estimate->description }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Итоги</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h3 class="text-primary mb-0">{{ number_format($estimate->total_amount, 0, ',', ' ') }} ₽</h3>
                                <small class="text-muted">Общая сумма</small>
                            </div>
                            
                            @if($estimate->items && count($estimate->items) > 0)
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="fw-semibold">{{ count($estimate->items) }}</div>
                                    <small class="text-muted">позиций</small>
                                </div>
                                <div class="col-6">
                                    <div class="fw-semibold">
                                        {{ number_format(array_sum(array_column($estimate->items, 'quantity')), 2) }}
                                    </div>
                                    <small class="text-muted">общее кол-во</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Позиции сметы -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Позиции сметы</h5>
                </div>
                <div class="card-body">
                    @if($estimate->items && count($estimate->items) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>№</th>
                                        <th>Наименование</th>
                                        <th>Количество</th>
                                        <th>Единица</th>
                                        <th>Цена</th>
                                        <th>Сумма</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estimate->items as $index => $item)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>{{ $item['name'] ?? 'Без названия' }}</td>
                                        <td>{{ number_format($item['quantity'] ?? 0, 2) }}</td>
                                        <td>{{ $item['unit'] ?? '-' }}</td>
                                        <td>{{ number_format($item['price'] ?? 0, 0, ',', ' ') }} ₽</td>
                                        <td class="fw-semibold">
                                            {{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 0, ',', ' ') }} ₽
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Итого:</th>
                                        <th class="fw-bold text-primary">
                                            {{ number_format($estimate->total_amount, 0, ',', ' ') }} ₽
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">Позиции не добавлены</h5>
                            <p class="text-muted">В данной смете пока нет позиций</p>
                            
                            @if(Auth::user()->isEstimator() || Auth::user()->isEmployee() || Auth::user()->isPartner() || Auth::user()->isAdmin())
                            <a href="{{ route('partner.estimates.edit', $estimate) }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Добавить позиции
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

@push('styles')
<style>
.table th,
.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.85em;
}
</style>
@endpush
