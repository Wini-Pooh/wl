@extends('layouts.app')

@section('title', 'Создать документ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Хлебные крошки -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('documents.index') }}">Документы</a>
                    </li>
                    <li class="breadcrumb-item active">Создать документ</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-plus me-2"></i>Создание нового документа
                    </h3>
                </div>

                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Основная информация -->
                            <div class="col-lg-8">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">Основная информация</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Название документа -->
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Название документа *</label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title') }}" 
                                                   placeholder="Введите название документа" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Тип документа -->
                                        <div class="mb-3">
                                            <label for="document_type" class="form-label">Тип документа *</label>
                                            <select class="form-select @error('document_type') is-invalid @enderror" 
                                                    id="document_type" name="document_type" required>
                                                <option value="">Выберите тип документа</option>
                                                <option value="contract" {{ old('document_type') === 'contract' ? 'selected' : '' }}>Договор</option>
                                                <option value="act" {{ old('document_type') === 'act' ? 'selected' : '' }}>Акт</option>
                                                <option value="invoice" {{ old('document_type') === 'invoice' ? 'selected' : '' }}>Счет</option>
                                                <option value="estimate" {{ old('document_type') === 'estimate' ? 'selected' : '' }}>Смета</option>
                                                <option value="technical" {{ old('document_type') === 'technical' ? 'selected' : '' }}>Техническая документация</option>
                                                <option value="certificate" {{ old('document_type') === 'certificate' ? 'selected' : '' }}>Сертификат</option>
                                                <option value="correspondence" {{ old('document_type') === 'correspondence' ? 'selected' : '' }}>Переписка</option>
                                                <option value="other" {{ old('document_type') === 'other' ? 'selected' : '' }}>Другое</option>
                                            </select>
                                            @error('document_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Шаблон -->
                                        <div class="mb-3">
                                            <label for="template_id" class="form-label">Шаблон документа</label>
                                            <select class="form-select @error('template_id') is-invalid @enderror" 
                                                    id="template_id" name="template_id">
                                                <option value="">Без шаблона</option>
                                                @foreach($templates as $template)
                                                    <option value="{{ $template->id }}" 
                                                            data-type="{{ $template->document_type }}"
                                                            {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                                        {{ $template->name }} ({{ ucfirst($template->document_type) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('template_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Проект -->
                                        <div class="mb-3">
                                            <label for="project_id" class="form-label">Связанный проект</label>
                                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                                    id="project_id" name="project_id">
                                                <option value="">Без привязки к проекту</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}" 
                                                            data-client-name="{{ $project->client_first_name }} {{ $project->client_last_name }}"
                                                            data-client-phone="{{ $project->client_phone }}"
                                                            data-client-email="{{ $project->client_email }}"
                                                            {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                        {{ $project->client_first_name }} {{ $project->client_last_name }} - 
                                                        {{ $project->object_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('project_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Содержание документа -->
                                        <div class="mb-3">
                                            <label for="content" class="form-label">Содержание документа</label>
                                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                                      id="content" name="content" rows="8" 
                                                      placeholder="Введите содержание документа">{{ old('content') }}</textarea>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Загрузка файла -->
                                        <div class="mb-3">
                                            <label for="document_file" class="form-label">Файл документа</label>
                                            <input type="file" class="form-control @error('document_file') is-invalid @enderror" 
                                                   id="document_file" name="document_file" 
                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                            <div class="form-text">
                                                Поддерживаемые форматы: PDF, DOC, DOCX, JPG, PNG. Максимальный размер: 10MB
                                            </div>
                                            @error('document_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Настройки -->
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0">Настройки документа</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Требует подписи -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="signature_required" name="signature_required" value="1"
                                                       {{ old('signature_required') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="signature_required">
                                                    <strong>Требует электронной подписи</strong>
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                Если включено, документ будет отправлен на подпись после создания
                                            </div>
                                        </div>

                                        <!-- Информация о проекте (если выбран) -->
                                        <div id="projectInfo" class="mb-3" style="display: none;">
                                            <h6>Информация о клиенте:</h6>
                                            <div class="small text-muted">
                                                <div><strong>Имя:</strong> <span id="clientName">-</span></div>
                                                <div><strong>Телефон:</strong> <span id="clientPhone">-</span></div>
                                                <div><strong>Email:</strong> <span id="clientEmail">-</span></div>
                                            </div>
                                        </div>

                                        <!-- Быстрые действия -->
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                    onclick="previewDocument()">
                                                <i class="fas fa-eye"></i> Предварительный просмотр
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" 
                                                    onclick="saveAsDraft()">
                                                <i class="fas fa-save"></i> Сохранить как черновик
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('documents.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Отмена
                            </a>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Создать документ
                                </button>
                                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" 
                                        data-bs-toggle="dropdown">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="createAndSend()">
                                        <i class="fas fa-paper-plane me-2"></i>Создать и отправить
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автозаполнение типа документа при выборе шаблона
    const templateSelect = document.getElementById('template_id');
    const documentTypeSelect = document.getElementById('document_type');
    
    templateSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.type && !documentTypeSelect.value) {
            documentTypeSelect.value = selectedOption.dataset.type;
        }
    });

    // Показ информации о проекте
    const projectSelect = document.getElementById('project_id');
    const projectInfo = document.getElementById('projectInfo');
    
    projectSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            document.getElementById('clientName').textContent = selectedOption.dataset.clientName || '-';
            document.getElementById('clientPhone').textContent = selectedOption.dataset.clientPhone || '-';
            document.getElementById('clientEmail').textContent = selectedOption.dataset.clientEmail || '-';
            projectInfo.style.display = 'block';
        } else {
            projectInfo.style.display = 'none';
        }
    });

    // Проверка размера файла
    const fileInput = document.getElementById('document_file');
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.size > 10 * 1024 * 1024) { // 10MB
            showNotification('warning', 'Размер файла превышает 10MB. Пожалуйста, выберите файл меньшего размера.', {
                icon: 'fas fa-exclamation-triangle'
            });
            this.value = '';
        }
    });
});

// Предварительный просмотр
function previewDocument() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    
    if (!title && !content) {
        showNotification('warning', 'Заполните название или содержание документа для предварительного просмотра', {
            icon: 'fas fa-exclamation-triangle'
        });
        return;
    }
    
    // Открываем модальное окно с предварительным просмотром
    const previewModal = new bootstrap.Modal(document.createElement('div'));
    // Здесь можно добавить логику предварительного просмотра
}

// Сохранить как черновик
function saveAsDraft() {
    const form = document.querySelector('form');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'save_as_draft';
    input.value = '1';
    form.appendChild(input);
    form.submit();
}

// Создать и отправить
function createAndSend() {
    const signatureCheckbox = document.getElementById('signature_required');
    signatureCheckbox.checked = true;
    
    const form = document.querySelector('form');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'create_and_send';
    input.value = '1';
    form.appendChild(input);
    form.submit();
}

// Функция показа уведомлений
function showNotification(type, message, options = {}) {
    const {
        icon = '',
        timeout = 5000
    } = options;
    
    // Создаем контейнер для уведомлений если его нет
    let container = document.getElementById('notifications-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications-container';
        container.className = 'position-fixed';
        container.style.cssText = `
            top: 20px; 
            right: 20px; 
            z-index: 9999; 
            max-width: 400px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }
    
    // Определяем класс Bootstrap для типа уведомления
    const typeClasses = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alertClass = typeClasses[type] || 'alert-info';
    
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show mb-2 shadow-lg`;
    notification.style.cssText = `
        pointer-events: auto;
        animation: slideInRight 0.3s ease-out;
        border: none;
        border-radius: 8px;
    `;
    
    notification.innerHTML = `
        ${icon ? `<i class="${icon} me-2"></i>` : ''}
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    container.appendChild(notification);
    
    // Автоматически удаляем через указанное время
    if (timeout > 0) {
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, timeout);
    }
}
</script>
@endpush

@push('styles')
<style>
/* Анимации для уведомлений */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Стили для контейнера уведомлений */
#notifications-container .alert {
    backdrop-filter: blur(10px);
    border-left: 4px solid;
}

#notifications-container .alert-success {
    border-left-color: #198754;
    background-color: rgba(212, 237, 218, 0.95);
}

#notifications-container .alert-danger {
    border-left-color: #dc3545;
    background-color: rgba(248, 215, 218, 0.95);
}

#notifications-container .alert-warning {
    border-left-color: #ffc107;
    background-color: rgba(255, 243, 205, 0.95);
}

#notifications-container .alert-info {
    border-left-color: #0dcaf0;
    background-color: rgba(207, 244, 252, 0.95);
}
</style>
@endpush
