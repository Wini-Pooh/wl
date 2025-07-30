@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i>
                    Добавление сотрудника
                </h2>
                <a href="{{ route('partner.employees.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    К списку сотрудников
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('partner.employees.store') }}" method="POST">
                        @csrf
                        
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
                                               value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="last_name" class="form-label">Фамилия *</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" 
                                               value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="middle_name" class="form-label">Отчество</label>
                                        <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                               id="middle_name" name="middle_name" 
                                               value="{{ old('middle_name') }}">
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
                                               value="{{ old('phone') }}" 
                                               placeholder="+7 (999) 123-45-67" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-info" onclick="searchByPhone()">
                                                    <i class="bi bi-search me-1"></i> Проверить в базе
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatPhone()">
                                                    <i class="bi bi-telephone me-1"></i> Форматировать
                                                </button>
                                            </div>
                                            <small class="text-muted">Проверьте, нет ли уже сотрудника с таким номером</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="employee@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Результаты поиска по телефону -->
                                <div id="phoneSearchResults" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6><i class="bi bi-info-circle me-2"></i>Результаты поиска:</h6>
                                        <div id="searchResultsList"></div>
                                    </div>
                                </div>

                                <!-- Скрытое поле для связи с пользователем -->
                                <input type="hidden" id="user_id" name="user_id" value="{{ old('user_id') }}">
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Первоначальные финансовые обязательства (необязательно)</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="addInitialFinance">
                                    <label class="form-check-label" for="addInitialFinance">
                                        Добавить первоначальное финансовое обязательство
                                    </label>
                                </div>
                                
                                <div id="initialFinanceFields" style="display: none;">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="finance_type" class="form-label">Тип платежа</label>
                                            <select class="form-select" id="finance_type" name="finance_type">
                                                <option value="">Выберите тип</option>
                                                @foreach(\App\Models\EmployeeFinance::getTypes() as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="finance_amount" class="form-label">Сумма (₽)</label>
                                            <input type="number" class="form-control" id="finance_amount" 
                                                   name="finance_amount" step="0.01" min="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="finance_title" class="form-label">Описание</label>
                                            <input type="text" class="form-control" id="finance_title" 
                                                   name="finance_title" placeholder="Например: Зарплата за октябрь">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="finance_due_date" class="form-label">Дата к выплате</label>
                                            <input type="date" class="form-control" id="finance_due_date" 
                                                   name="finance_due_date">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            После создания сотрудника вы сможете добавить дополнительные финансовые записи
                                        </small>
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
                                            <option value="">Выберите роль</option>
                                            @foreach($roles as $key => $label)
                                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
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
                                                <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label for="description" class="form-label">Обязанности перед партнером</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Опишите основные обязанности и ответственность сотрудника...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <label for="notes" class="form-label">Дополнительные заметки</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="2" 
                                              placeholder="Дополнительная информация, контакты, особенности работы...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('partner.employees.index') }}" class="btn btn-outline-secondary me-3">
                                Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Добавить сотрудника
                            </button>
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
                            <h6>Роли сотрудников:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="badge bg-info me-2">Прораб</span>
                                    Руководит строительными работами на объекте
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-warning me-2">Субподрядчик</span>
                                    Выполняет специализированные работы
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-success me-2">Сметчик</span>
                                    Составляет сметы и контролирует расходы
                                </li>
                            </ul>

                            <hr>

                            <h6>После добавления сотрудника:</h6>
                            <ul class="small text-muted">
                                <li>Можно будет добавить финансовые обязательства</li>
                                <li>Настроить график выплат</li>
                                <li>Привязать к проектам</li>
                                <li>Отслеживать задолженности</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function searchByPhone() {
    const phone = document.getElementById('phone').value;
    if (!phone) {
        alert('Введите номер телефона для поиска');
        return;
    }
    
    // Показываем индикатор загрузки
    const resultsDiv = document.getElementById('phoneSearchResults');
    const listDiv = document.getElementById('searchResultsList');
    listDiv.innerHTML = '<div class="d-flex align-items-center"><span class="spinner-border spinner-border-sm me-2"></span>Поиск...</div>';
    resultsDiv.style.display = 'block';
    
    try {
        const response = await fetch(`/partner/employees/search-users-by-phone?phone=${encodeURIComponent(phone)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) {
            throw new Error('Ошибка при поиске');
        }
        
        const data = await response.json();
        
        if (data.users.length === 0 && data.employees.length === 0) {
            listDiv.innerHTML = `
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Новый пользователь</strong><br>
                    Этот номер телефона не зарегистрирован в системе. Сотрудник будет добавлен и сможет зарегистрироваться позже.
                </div>
            `;
            // Очищаем скрытое поле user_id
            document.getElementById('user_id').value = '';
        } else {
            let html = '';
            
            // Показываем найденных пользователей
            if (data.users.length > 0) {
                html += '<div class="mb-3"><strong>Зарегистрированные пользователи:</strong></div>';
                data.users.forEach(user => {
                    html += `
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${user.name}</strong><br>
                                        <small class="text-muted">
                                            ${user.phone} ${user.email ? '• ' + user.email : ''}<br>
                                            Роли: ${user.roles || 'Нет ролей'}
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            onclick="selectUser(${user.id}, '${user.name}', '${user.phone}', '${user.email || ''}')">
                                        <i class="bi bi-person-check me-1"></i>
                                        Использовать аккаунт
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            // Показываем существующих сотрудников
            if (data.employees.length > 0) {
                html += '<div class="mb-3 mt-3"><strong>Уже добавленные сотрудники:</strong></div>';
                data.employees.forEach(employee => {
                    html += `
                        <div class="alert alert-warning mb-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>${employee.name}</strong> (${employee.role})<br>
                            <small>Уже добавлен как сотрудник с номером ${employee.phone}</small>
                        </div>
                    `;
                });
            }
            
            listDiv.innerHTML = html;
        }
    } catch (error) {
        console.error('Ошибка поиска:', error);
        listDiv.innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Ошибка при поиске. Попробуйте еще раз.
            </div>
        `;
    }
}
            `).join('');
        } else {
            listDiv.innerHTML = '<p class="mb-0 text-success">Сотрудников с таким номером телефона не найдено. Можно продолжить добавление.</p>';
        }
    } catch (error) {
        console.error('Ошибка поиска:', error);
        listDiv.innerHTML = `<div class="alert alert-danger mb-0">Ошибка при поиске: ${error.message}</div>`;
    }
}

// Функция для выбора пользователя (привязка к существующему аккаунту)
function selectUser(userId, userName, userPhone, userEmail) {
    // Устанавливаем скрытое поле user_id
    document.getElementById('user_id').value = userId;
    
    // Заполняем форму данными пользователя
    document.getElementById('phone').value = userPhone;
    if (userEmail) {
        document.getElementById('email').value = userEmail;
    }
    
    // Парсим имя пользователя и заполняем поля
    const nameParts = userName.split(' ');
    if (nameParts.length >= 2) {
        document.getElementById('first_name').value = nameParts[0];
        document.getElementById('last_name').value = nameParts[nameParts.length - 1];
        if (nameParts.length >= 3) {
            document.getElementById('middle_name').value = nameParts.slice(1, -1).join(' ');
        }
    }
    
    // Показываем подтверждение
    const resultsDiv = document.getElementById('searchResultsList');
    resultsDiv.innerHTML = `
        <div class="alert alert-info mb-0">
            <i class="bi bi-person-check me-2"></i>
            <strong>Выбран аккаунт: ${userName}</strong><br>
            <small>Этот пользователь будет привязан к создаваемому сотруднику. Данные формы заполнены автоматически.</small>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearUserSelection()">
                    <i class="bi bi-x me-1"></i> Отменить выбор
                </button>
            </div>
        </div>
    `;
    
    // Делаем поля формы доступными для редактирования
    document.getElementById('phone').readOnly = false;
    document.getElementById('email').readOnly = false;
}

// Функция для отмены выбора пользователя
function clearUserSelection() {
    document.getElementById('user_id').value = '';
    document.getElementById('phoneSearchResults').style.display = 'none';
    
    // Очищаем поля формы
    document.getElementById('first_name').value = '';
    document.getElementById('last_name').value = '';
    document.getElementById('middle_name').value = '';
    document.getElementById('email').value = '';
    
    // Делаем поля снова доступными для редактирования
    document.getElementById('phone').readOnly = false;
    document.getElementById('email').readOnly = false;
}

// Функция для форматирования номера телефона
function formatPhone() {
    const phoneInput = document.getElementById('phone');
    let value = phoneInput.value.replace(/\D/g, '');
    
    if (value.startsWith('8')) {
        value = '7' + value.slice(1);
    }
    
    if (value.startsWith('7') && value.length <= 11) {
        let formatted = '+7';
        if (value.length > 1) formatted += ' (' + value.slice(1, 4);
        if (value.length > 4) formatted += ') ' + value.slice(4, 7);
        if (value.length > 7) formatted += '-' + value.slice(7, 9);
        if (value.length > 9) formatted += '-' + value.slice(9, 11);
        
        phoneInput.value = formatted;
    }
}

// Форматирование телефона при вводе
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

// Автозаполнение при выборе роли
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const descriptionField = document.getElementById('description');
    
    if (role && !descriptionField.value) {
        const descriptions = {
            'foreman': 'Руководство строительными работами на объекте, контроль качества выполнения работ, координация действий рабочих, соблюдение техники безопасности.',
            'subcontractor': 'Выполнение специализированных строительных работ согласно договору подряда, соблюдение сроков и качества работ.',
            'estimator': 'Составление смет на строительные работы, расчет стоимости материалов и работ, контроль расходов по проекту.'
        };
        
        if (descriptions[role]) {
            descriptionField.value = descriptions[role];
        }
    }
});

// Показ/скрытие полей для начальных финансов
document.getElementById('addInitialFinance').addEventListener('change', function() {
    const fieldsDiv = document.getElementById('initialFinanceFields');
    if (this.checked) {
        fieldsDiv.style.display = 'block';
        // Устанавливаем дату по умолчанию на следующий месяц
        const nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        document.getElementById('finance_due_date').value = nextMonth.toISOString().split('T')[0];
    } else {
        fieldsDiv.style.display = 'none';
    }
});

// Автозаполнение описания платежа при выборе типа
document.getElementById('finance_type').addEventListener('change', function() {
    const type = this.value;
    const titleField = document.getElementById('finance_title');
    
    if (type && !titleField.value) {
        const titles = {
            'salary': 'Зарплата за текущий месяц',
            'bonus': 'Премия',
            'penalty': 'Штраф',
            'expense': 'Возмещение расходов',
            'debt': 'Задолженность'
        };
        
        if (titles[type]) {
            titleField.value = titles[type];
        }
    }
});
</script>
@endpush
