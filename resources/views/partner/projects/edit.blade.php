@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Редактирование объекта #{{ $project->id }}
                </h2>
                <div class="btn-group">
                    <a href="{{ route('partner.projects.show', $project) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Назад
                    </a>
                    <a href="{{ route('partner.projects.index') }}" class="btn btn-outline-secondary">
                        К списку
                    </a>
                </div>
            </div>
            
            <form action="{{ route('partner.projects.update', $project) }}" method="POST" id="projectForm">
                @csrf
                @method('PUT')
                
                <!-- Навигация по вкладкам -->
                <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="required-tab" data-bs-toggle="tab" data-bs-target="#required" 
                                type="button" role="tab">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Обязательные поля
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="client-data-tab" data-bs-toggle="tab" data-bs-target="#client-data" 
                                type="button" role="tab">
                            <i class="bi bi-person me-2"></i>
                            Данные клиента
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="object-details-tab" data-bs-toggle="tab" data-bs-target="#object-details" 
                                type="button" role="tab">
                            <i class="bi bi-building me-2"></i>
                            Детали объекта
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" 
                                type="button" role="tab">
                            <i class="bi bi-calendar me-2"></i>
                            Сроки
                        </button>
                    </li>
                </ul>
                
                <!-- Содержимое вкладок -->
                <div class="tab-content" id="projectTabsContent">
                    <!-- Вкладка "Обязательные поля" -->
                    <div class="tab-pane fade show active" id="required" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Обязательные поля</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="client_first_name" class="form-label">Имя клиента *</label>
                                        <input type="text" class="form-control @error('client_first_name') is-invalid @enderror" 
                                               id="client_first_name" name="client_first_name" 
                                               value="{{ old('client_first_name', $project->client_first_name) }}" required>
                                        @error('client_first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="client_last_name" class="form-label">Фамилия клиента *</label>
                                        <input type="text" class="form-control @error('client_last_name') is-invalid @enderror" 
                                               id="client_last_name" name="client_last_name" 
                                               value="{{ old('client_last_name', $project->client_last_name) }}" required>
                                        @error('client_last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="client_phone" class="form-label">Телефон клиента *</label>
                                        <input type="tel" class="form-control @error('client_phone') is-invalid @enderror" 
                                               id="client_phone" name="client_phone" 
                                               value="{{ old('client_phone', $project->client_phone) }}" 
                                               placeholder="+7 (999) 123-45-67" required>
                                        @error('client_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="searchByPhone()">
                                                <i class="bi bi-search me-1"></i> Найти другие объекты
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="object_type" class="form-label">Тип объекта *</label>
                                        <select class="form-select @error('object_type') is-invalid @enderror" 
                                                id="object_type" name="object_type" required>
                                            <option value="">Выберите тип объекта</option>
                                            @foreach(\App\Models\Project::getObjectTypes() as $key => $label)
                                                <option value="{{ $key }}" {{ old('object_type', $project->object_type) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('object_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="work_type" class="form-label">Тип работ *</label>
                                        <select class="form-select @error('work_type') is-invalid @enderror" 
                                                id="work_type" name="work_type" required>
                                            <option value="">Выберите тип работ</option>
                                            @foreach(\App\Models\Project::getWorkTypes() as $key => $label)
                                                <option value="{{ $key }}" {{ old('work_type', $project->work_type) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('work_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="project_status" class="form-label">Статус проекта *</label>
                                        <select class="form-select @error('project_status') is-invalid @enderror" 
                                                id="project_status" name="project_status" required>
                                            @foreach(\App\Models\Project::getStatuses() as $key => $label)
                                                <option value="{{ $key }}" {{ old('project_status', $project->project_status) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Результаты поиска по телефону -->
                                <div id="phoneSearchResults" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6><i class="bi bi-info-circle me-2"></i>Найденные объекты по этому номеру:</h6>
                                        <div id="searchResultsList"></div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary" onclick="nextTab('client-data-tab')">
                                        Далее <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Вкладка "Данные клиента" -->
                    <div class="tab-pane fade" id="client-data" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Данные клиента</h5>
                            </div>
                            <div class="card-body">
                                <!-- Паспортные данные -->
                                <h6 class="text-muted mb-3">Паспортные данные</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label for="passport_series" class="form-label">Серия паспорта</label>
                                        <input type="text" class="form-control" id="passport_series" 
                                               name="passport_series" value="{{ old('passport_series', $project->passport_series) }}" 
                                               placeholder="1234" maxlength="4">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="passport_number" class="form-label">Номер паспорта</label>
                                        <input type="text" class="form-control" id="passport_number" 
                                               name="passport_number" value="{{ old('passport_number', $project->passport_number) }}" 
                                               placeholder="567890" maxlength="6">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="passport_issued_by" class="form-label">Кем выдан</label>
                                        <input type="text" class="form-control" id="passport_issued_by" 
                                               name="passport_issued_by" value="{{ old('passport_issued_by', $project->passport_issued_by) }}" 
                                               placeholder="Отделом УФМС России по ...">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="passport_issued_date" class="form-label">Дата выдачи</label>
                                        <input type="date" class="form-control" id="passport_issued_date" 
                                               name="passport_issued_date" value="{{ old('passport_issued_date', $project->passport_issued_date?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="passport_department_code" class="form-label">Код подразделения</label>
                                        <input type="text" class="form-control" id="passport_department_code" 
                                               name="passport_department_code" value="{{ old('passport_department_code', $project->passport_department_code) }}" 
                                               placeholder="123-456">
                                    </div>
                                </div>
                                
                                <!-- Личные данные -->
                                <h6 class="text-muted mb-3">Личные данные</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label for="birth_date" class="form-label">Дата рождения</label>
                                        <input type="date" class="form-control" id="birth_date" 
                                               name="birth_date" value="{{ old('birth_date', $project->birth_date?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="birth_place" class="form-label">Место рождения</label>
                                        <input type="text" class="form-control" id="birth_place" 
                                               name="birth_place" value="{{ old('birth_place', $project->birth_place) }}" 
                                               placeholder="г. Москва">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="client_email" class="form-label">Email клиента</label>
                                        <input type="email" class="form-control" id="client_email" 
                                               name="client_email" value="{{ old('client_email', $project->client_email) }}" 
                                               placeholder="client@example.com">
                                    </div>
                                </div>
                                
                                <!-- Адрес прописки -->
                                <h6 class="text-muted mb-3">Адрес прописки</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="registration_postal_code" class="form-label">Почтовый индекс</label>
                                        <input type="text" class="form-control" id="registration_postal_code" 
                                               name="registration_postal_code" value="{{ old('registration_postal_code', $project->registration_postal_code) }}" 
                                               placeholder="123456">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="registration_city" class="form-label">Город</label>
                                        <input type="text" class="form-control" id="registration_city" 
                                               name="registration_city" value="{{ old('registration_city', $project->registration_city) }}" 
                                               placeholder="Москва">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="registration_street" class="form-label">Улица</label>
                                        <input type="text" class="form-control" id="registration_street" 
                                               name="registration_street" value="{{ old('registration_street', $project->registration_street) }}" 
                                               placeholder="ул. Тверская">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="registration_house" class="form-label">Дом</label>
                                        <input type="text" class="form-control" id="registration_house" 
                                               name="registration_house" value="{{ old('registration_house', $project->registration_house) }}" 
                                               placeholder="12">
                                    </div>
                                    <div class="col-md-1">
                                        <label for="registration_apartment" class="form-label">Квартира</label>
                                        <input type="text" class="form-control" id="registration_apartment" 
                                               name="registration_apartment" value="{{ old('registration_apartment', $project->registration_apartment) }}" 
                                               placeholder="45">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevTab('required-tab')">
                                        <i class="bi bi-arrow-left"></i> Назад
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextTab('object-details-tab')">
                                        Далее <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Вкладка "Детали объекта" -->
                    <div class="tab-pane fade" id="object-details" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Детали объекта</h5>
                            </div>
                            <div class="card-body">
                                <!-- Характеристики объекта -->
                                <h6 class="text-muted mb-3">Характеристики объекта</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label for="apartment_number" class="form-label">Номер квартиры/офиса</label>
                                        <input type="text" class="form-control" id="apartment_number" 
                                               name="apartment_number" value="{{ old('apartment_number', $project->apartment_number) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="object_city" class="form-label">Город</label>
                                        <input type="text" class="form-control" id="object_city" 
                                               name="object_city" value="{{ old('object_city', $project->object_city) }}" 
                                               placeholder="Москва">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="object_street" class="form-label">Улица</label>
                                        <input type="text" class="form-control" id="object_street" 
                                               name="object_street" value="{{ old('object_street', $project->object_street) }}" 
                                               placeholder="ул. Тверская">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="object_house" class="form-label">Номер дома</label>
                                        <input type="text" class="form-control" id="object_house" 
                                               name="object_house" value="{{ old('object_house', $project->object_house) }}" 
                                               placeholder="12">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="object_entrance" class="form-label">Подъезд</label>
                                        <input type="text" class="form-control" id="object_entrance" 
                                               name="object_entrance" value="{{ old('object_entrance', $project->object_entrance) }}" 
                                               placeholder="2">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="object_area" class="form-label">Площадь (м²)</label>
                                        <input type="number" class="form-control" id="object_area" 
                                               name="object_area" value="{{ old('object_area', $project->object_area) }}" 
                                               step="0.01" min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="camera_link" class="form-label">Ссылка на камеры наблюдения</label>
                                        <input type="url" class="form-control" id="camera_link" 
                                               name="camera_link" value="{{ old('camera_link', $project->camera_link) }}" 
                                               placeholder="https://...">
                                    </div>
                                </div>
                                
                                <!-- Финансовые показатели -->
                                <h6 class="text-muted mb-3">Финансовые показатели</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="work_cost" class="form-label">Стоимость работ, ₽</label>
                                        <input type="number" class="form-control bg-light" id="work_cost" 
                                               name="work_cost" value="{{ old('work_cost', $project->work_cost) }}" 
                                               step="0.01" min="0" readonly>
                                        <div class="form-text">Данные из основных смет объекта</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="materials_cost" class="form-label">Стоимость материалов, ₽</label>
                                        <input type="number" class="form-control bg-light" id="materials_cost" 
                                               name="materials_cost" value="{{ old('materials_cost', $project->materials_cost) }}" 
                                               step="0.01" min="0" readonly>
                                        <div class="form-text">Данные из смет материалов</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="additional_work_cost" class="form-label">Дополнительные работы, ₽</label>
                                        <input type="number" class="form-control bg-light" id="additional_work_cost" 
                                               name="additional_work_cost" value="{{ old('additional_work_cost', $project->additional_work_cost) }}" 
                                               step="0.01" min="0" readonly>
                                        <div class="form-text">Данные из дополнительных смет</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="total_cost" class="form-label">Общая стоимость, ₽</label>
                                        <input type="number" class="form-control bg-light" id="total_cost" 
                                               name="total_cost" value="{{ old('total_cost', $project->total_cost) }}" 
                                               readonly>
                                        <div class="form-text">Рассчитывается автоматически</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevTab('client-data-tab')">
                                        <i class="bi bi-arrow-left"></i> Назад
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextTab('timeline-tab')">
                                        Далее <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Вкладка "Сроки" -->
                    <div class="tab-pane fade" id="timeline" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Временные рамки</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="contract_date" class="form-label">Дата заключения договора</label>
                                        <input type="date" class="form-control" id="contract_date" 
                                               name="contract_date" value="{{ old('contract_date', $project->contract_date?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="work_start_date" class="form-label">Дата начала работ</label>
                                        <input type="date" class="form-control" id="work_start_date" 
                                               name="work_start_date" value="{{ old('work_start_date', $project->work_start_date?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="estimated_end_date" class="form-label">Приблизительное окончание ремонта</label>
                                        <input type="date" class="form-control" id="estimated_end_date" 
                                               name="estimated_end_date" value="{{ old('estimated_end_date', $project->estimated_end_date?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="contract_number" class="form-label">Номер договора</label>
                                        <input type="text" class="form-control" id="contract_number" 
                                               name="contract_number" value="{{ old('contract_number', $project->contract_number) }}">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevTab('object-details-tab')">
                                        <i class="bi bi-arrow-left"></i> Назад
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Сохранить изменения
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function nextTab(tabId) {
    const tab = document.getElementById(tabId);
    const tabTrigger = new bootstrap.Tab(tab);
    tabTrigger.show();
}

function prevTab(tabId) {
    const tab = document.getElementById(tabId);
    const tabTrigger = new bootstrap.Tab(tab);
    tabTrigger.show();
}

function calculateTotal() {
    // Финансовые показатели рассчитываются автоматически из смет
    // Эта функция оставлена для совместимости, но не изменяет значения
    console.log('Финансовые показатели обновляются автоматически из смет');
}

async function searchByPhone() {
    const phone = document.getElementById('client_phone').value;
    if (!phone) {
        alert('Введите номер телефона для поиска');
        return;
    }
    
    try {
        const response = await fetch('/partner/projects/search-by-phone', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ phone: phone })
        });
        
        const projects = await response.json();
        
        const resultsDiv = document.getElementById('phoneSearchResults');
        const listDiv = document.getElementById('searchResultsList');
        
        if (projects.length > 0) {
            let html = '<ul class="list-unstyled mb-0">';
            projects.forEach(project => {
                if (project.id !== {{ $project->id }}) { // Исключаем текущий проект
                    html += `<li class="mb-2">
                        <strong>ID #${project.id}</strong> - ${project.client_first_name} ${project.client_last_name}<br>
                        <small class="text-muted">${project.object_city || 'Адрес не указан'}, Статус: ${project.project_status}</small>
                        <a href="/partner/projects/${project.id}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                            Открыть
                        </a>
                    </li>`;
                }
            });
            html += '</ul>';
            
            if (html === '<ul class="list-unstyled mb-0"></ul>') {
                listDiv.innerHTML = '<p class="mb-0 text-muted">Других объектов по данному номеру не найдено.</p>';
            } else {
                listDiv.innerHTML = html;
            }
            resultsDiv.style.display = 'block';
        } else {
            listDiv.innerHTML = '<p class="mb-0 text-muted">По данному номеру телефона объекты не найдены.</p>';
            resultsDiv.style.display = 'block';
        }
    } catch (error) {
        console.error('Ошибка поиска:', error);
        alert('Произошла ошибка при поиске');
    }
}

// Форматирование телефона
document.getElementById('client_phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    let formattedValue = '';
    
    if (value.length > 0) {
        if (value.startsWith('8')) {
            value = '7' + value.slice(1);
        }
        formattedValue = '+7';
        if (value.length > 1) {
            formattedValue += ' (' + value.slice(1, 4);
        }
        if (value.length >= 4) {
            formattedValue += ') ' + value.slice(4, 7);
        }
        if (value.length >= 7) {
            formattedValue += '-' + value.slice(7, 9);
        }
        if (value.length >= 9) {
            formattedValue += '-' + value.slice(9, 11);
        }
    }
    
    e.target.value = formattedValue;
});

// Автоматический расчет общей стоимости при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endsection
