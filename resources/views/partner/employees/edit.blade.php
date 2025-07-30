@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-person-gear me-2"></i>
                    Редактирование сотрудника: {{ $employee->short_name }}
                </h2>
                <div class="btn-group">
                    <a href="{{ route('partner.employees.show', $employee) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        К карточке сотрудника
                    </a>
                    <a href="{{ route('partner.employees.index') }}" class="btn btn-outline-secondary">
                        К списку сотрудников
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('partner.employees.update', $employee) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Персональные данные</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="first_name" class="form-label">Имя *</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" 
                                               value="{{ old('first_name', $employee->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="last_name" class="form-label">Фамилия *</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" 
                                               value="{{ old('last_name', $employee->last_name) }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="middle_name" class="form-label">Отчество</label>
                                        <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                               id="middle_name" name="middle_name" 
                                               value="{{ old('middle_name', $employee->middle_name) }}">
                                        @error('middle_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Телефон *</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" 
                                               value="{{ old('phone', $employee->phone) }}" 
                                               placeholder="+7 (999) 123-45-67" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" 
                                               value="{{ old('email', $employee->email) }}" 
                                               placeholder="employee@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Рабочая информация</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="role" class="form-label">Роль *</label>
                                        <select class="form-select @error('role') is-invalid @enderror" 
                                                id="role" name="role" required>
                                            @foreach($roles as $key => $label)
                                                <option value="{{ $key }}" {{ old('role', $employee->role) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Статус *</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            @foreach($statuses as $key => $label)
                                                <option value="{{ $key }}" {{ old('status', $employee->status) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($employee->status !== 'active')
                                            <div class="form-text text-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Изменение статуса может повлиять на финансовые записи
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label for="description" class="form-label">Обязанности перед партнером</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Опишите основные обязанности и ответственность сотрудника...">{{ old('description', $employee->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <label for="notes" class="form-label">Дополнительные заметки</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="2" 
                                              placeholder="Дополнительная информация, контакты, особенности работы...">{{ old('notes', $employee->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>
                                    Удалить сотрудника
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('partner.employees.show', $employee) }}" class="btn btn-outline-secondary me-3">
                                    Отмена
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Информация
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Добавлен:</strong><br>
                                <span class="text-muted">{{ $employee->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Последнее обновление:</strong><br>
                                <span class="text-muted">{{ $employee->updated_at->format('d.m.Y H:i') }}</span>
                            </div>

                            @if($employee->finances->count() > 0)
                                <div class="mb-3">
                                    <strong>Финансовых записей:</strong><br>
                                    <span class="badge bg-info">{{ $employee->finances->count() }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>К выплате:</strong><br>
                                    <span class="text-warning">
                                        {{ number_format($employee->getTotalPendingAmount(), 0, ',', ' ') }} ₽
                                    </span>
                                </div>

                                @if($employee->getOverdueAmount() > 0)
                                    <div class="mb-3">
                                        <strong>Просрочено:</strong><br>
                                        <span class="text-danger">
                                            {{ number_format($employee->getOverdueAmount(), 0, ',', ' ') }} ₽
                                        </span>
                                    </div>
                                @endif
                            @endif

                            <hr>

                            <h6>Предупреждения:</h6>
                            <ul class="small text-muted">
                                <li>При изменении телефона проверьте уникальность номера</li>
                                <li>Смена роли может потребовать пересмотра обязанностей</li>
                                <li>При увольнении сотрудника (статус "Уволен") рекомендуется закрыть все финансовые обязательства</li>
                            </ul>
                        </div>
                    </div>

                    @if($employee->finances->where('status', 'pending')->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Предстоящие платежи
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($employee->finances->where('status', 'pending')->sortBy('due_date')->take(5) as $finance)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <div class="small">{{ $finance->title }}</div>
                                            <div class="text-muted small">{{ $finance->due_date->format('d.m.Y') }}</div>
                                        </div>
                                        <div class="text-end">
                                            <strong class="small">{{ number_format($finance->amount, 0, ',', ' ') }} ₽</strong>
                                        </div>
                                    </div>
                                @endforeach
                                @if($employee->finances->where('status', 'pending')->count() > 5)
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            ... и еще {{ $employee->finances->where('status', 'pending')->count() - 5 }}
                                        </small>
                                    </div>
                                @endif
                                <div class="text-center mt-3">
                                    <a href="{{ route('partner.employees.show', $employee) }}" class="btn btn-sm btn-outline-warning">
                                        Посмотреть все
                                    </a>
                                </div>
                            </div>
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
                <p>Вы уверены, что хотите удалить сотрудника <strong>{{ $employee->full_name }}</strong>?</p>
                <p class="text-danger"><strong>Внимание:</strong> Все финансовые записи сотрудника также будут удалены!</p>
                @if($employee->finances->where('status', 'pending')->count() > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        У сотрудника есть {{ $employee->finances->where('status', 'pending')->count() }} неоплаченных записей на сумму 
                        {{ number_format($employee->getTotalPendingAmount(), 0, ',', ' ') }} ₽
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form action="{{ route('partner.employees.destroy', $employee) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить сотрудника</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Форматирование телефона
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('8')) {
        value = '7' + value.slice(1);
    }
    
    if (value.startsWith('7') && value.length <= 11) {
        let formatted = '+7';
        if (value.length > 1) formatted += ' (' + value.slice(1, 4);
        if (value.length > 4) formatted += ') ' + value.slice(4, 7);
        if (value.length > 7) formatted += '-' + value.slice(7, 9);
        if (value.length > 9) formatted += '-' + value.slice(9, 11);
        
        e.target.value = formatted;
    }
});

// Предупреждение при смене статуса
document.getElementById('status').addEventListener('change', function() {
    const originalStatus = '{{ $employee->status }}';
    const newStatus = this.value;
    
    if (originalStatus === 'active' && newStatus !== 'active') {
        if (!confirm('Вы меняете статус сотрудника с "Активен". Это может повлиять на финансовые записи. Продолжить?')) {
            this.value = originalStatus;
        }
    }
});

// Автоматическое обновление описания при смене роли (только если поле пустое)
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const descriptionField = document.getElementById('description');
    
    if (role && !descriptionField.value.trim()) {
        const descriptions = {
            'foreman': 'Руководство строительными работами на объекте, контроль качества выполнения работ, координация действий рабочих, соблюдение техники безопасности.',
            'subcontractor': 'Выполнение специализированных строительных работ согласно договору подряда, соблюдение сроков и качества работ.',
            'estimator': 'Составление смет на строительные работы, расчет стоимости материалов и работ, контроль расходов по проекту.'
        };
        
        if (descriptions[role]) {
            if (confirm('Заполнить описание обязанностей автоматически для выбранной роли?')) {
                descriptionField.value = descriptions[role];
            }
        }
    }
});
</script>
@endpush
