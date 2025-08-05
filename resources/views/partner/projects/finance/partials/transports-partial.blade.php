<!-- Частичное представление для транспорта -->
@if($transports->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><i class="bi bi-truck me-2 text-warning"></i>Наименование</th>
                    <th><i class="bi bi-123 me-2 text-muted"></i>Количество</th>
                    <th><i class="bi bi-rulers me-2 text-muted"></i>Единица</th>
                    <th><i class="bi bi-currency-ruble me-2 text-success"></i>Цена за единицу</th>
                    <th><i class="bi bi-calculator me-2 text-warning"></i>Общая стоимость</th>
                    <th><i class="bi bi-calendar-date me-2 text-info"></i>Дата</th>
                    <th class="text-end"><i class="bi bi-gear me-2 text-secondary"></i>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transports as $transport)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-truck text-warning"></i>
                                </div>
                                <div>
                                    <strong>{{ $transport->name }}</strong>
                                    @if($transport->description)
                                        <br><small class="text-muted">{{ $transport->description }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ number_format($transport->quantity, 2) }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $transport->unit }}</span>
                        </td>
                        <td>
                            <span class="text-success fw-semibold">{{ number_format($transport->price, 2) }} ₽</span>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">{{ number_format($transport->amount, 2) }} ₽</span>
                        </td>
                        <td>
                            <small class="text-info">
                                <i class="bi bi-calendar me-1"></i>{{ $transport->created_at->format('d.m.Y') }}
                            </small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" 
                                        data-action="edit-transport" 
                                        data-id="{{ $transport->id }}"
                                        title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" 
                                        data-action="delete-transport" 
                                        data-id="{{ $transport->id }}"
                                        title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="bi bi-truck fa-3x text-muted" style="font-size: 3rem;"></i>
        </div>
        <h5 class="text-muted">Транспортные расходы не добавлены</h5>
        <p class="text-muted">Добавьте первую запись о транспортных расходах</p>
        <button type="button" class="btn btn-warning" data-action="add-transport">
            <i class="bi bi-plus-circle me-2"></i>Добавить транспорт
        </button>
    </div>
@endif
