@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-calculator me-2"></i>
                    Создание новой сметы
                </h2>
                <a href="{{ route('partner.estimates.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Вернуться к списку
                </a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Основная информация</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('partner.estimates.store') }}" method="POST" id="estimateForm">
                        @csrf
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="project_id" class="form-label required">Выберите объект</label>
                                <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">-- Выберите объект --</option>
                                    @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} - {{ $project->client_full_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label required">Название сметы</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label required">Тип сметы</label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">-- Выберите тип --</option>
                                    @foreach($estimateTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="description" class="form-label">Описание (необязательно)</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="2">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Блок выбора шаблона -->
                        <div class="row g-3 mb-4" id="templateSection" style="display: none;">
                            <div class="col-md-12">
                                <label for="template_id" class="form-label">Выберите шаблон</label>
                                <select name="template_id" id="template_id" class="form-select">
                                    <option value="">-- Стандартный шаблон --</option>
                                </select>
                                <small class="form-text text-muted">
                                    Выберите сохраненный шаблон или оставьте пустым для использования стандартного шаблона.
                                </small>
                            </div>
                        </div>
                        
                        <!-- Здесь был удален блок предварительного просмотра шаблона -->
                        
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back()">Отмена</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Создать смету
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // При изменении типа сметы
    const typeSelect = document.getElementById('type');
    const templateSection = document.getElementById('templateSection');
    const templateSelect = document.getElementById('template_id');
    
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        if (selectedType) {
            // Показываем секцию шаблонов
            templateSection.style.display = 'block';
            
            // Очищаем список шаблонов
            templateSelect.innerHTML = '<option value="">-- Стандартный шаблон --</option>';
            
            // Загружаем шаблоны для выбранного типа
            fetch(`/partner/estimates/templates/${selectedType}`)
                .then(response => response.json())
                .then(data => {
                    if (data.templates && data.templates.length > 0) {
                        data.templates.forEach(template => {
                            const option = document.createElement('option');
                            option.value = template.id;
                            option.textContent = template.name;
                            if (template.description) {
                                option.title = template.description;
                            }
                            templateSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Ошибка при загрузке шаблонов:', error);
                });
        } else {
            // Скрываем секцию шаблонов
            templateSection.style.display = 'none';
        }
    });
    
    // Валидация формы
    document.getElementById('estimateForm').addEventListener('submit', function(e) {
        const projectId = document.getElementById('project_id').value;
        const name = document.getElementById('name').value;
        const type = document.getElementById('type').value;
        
        if (!projectId || !name || !type) {
            e.preventDefault();
            alert('Пожалуйста, заполните все обязательные поля.');
        }
    });
});
</script>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-label.required::after {
    content: " *";
    color: red;
}

.table th {
    font-weight: 600;
    border-top: none;
}


</style>
@endsection
                                        