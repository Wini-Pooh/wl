@extends('layouts.app')

@section('title', 'Создание документа')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Создание документа</h3>
                </div>
                
                <form id="documentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Левая колонка -->
                            <div class="col-md-8">
                                <!-- Основная информация -->
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label for="title" class="form-label">Название документа *</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="document_type" class="form-label">Тип документа *</label>
                                        <select class="form-select" id="document_type" name="document_type" required>
                                            <option value="">Выберите тип</option>
                                            <option value="contract">Договор</option>
                                            <option value="act">Акт</option>
                                            <option value="invoice">Счет</option>
                                            <option value="estimate">Смета</option>
                                            <option value="technical">Техническая документация</option>
                                            <option value="other">Прочее</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Шаблон -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="template_id" class="form-label">Шаблон документа</label>
                                        <select class="form-select" id="template_id" name="template_id">
                                            <option value="">Без шаблона</option>
                                            @foreach($templates as $template)
                                                <option value="{{ $template->id }}" 
                                                        data-type="{{ $template->document_type }}"
                                                        {{ request('template') == $template->id ? 'selected' : '' }}>
                                                    {{ $template->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="project_id" class="form-label">Проект</label>
                                        <select class="form-select" id="project_id" name="project_id">
                                            <option value="">Не привязан к проекту</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->client_last_name }} {{ $project->client_first_name }} - {{ $project->object_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Переменные шаблона -->
                                <div id="templateVariables" class="mb-3" style="display: none;">
                                    <h6>Переменные шаблона:</h6>
                                    <div id="variablesContainer"></div>
                                </div>

                                <!-- Содержимое документа -->
                                <div class="mb-3">
                                    <label for="content" class="form-label">Содержимое документа *</label>
                                    <textarea class="form-control" id="content" name="content" rows="15" required></textarea>
                                </div>

                                <!-- Файл документа -->
                                <div class="mb-3">
                                    <label for="file" class="form-label">Прикрепить файл</label>
                                    <input type="file" class="form-control" id="file" name="file" 
                                           accept=".pdf,.doc,.docx" max="20480">
                                    <div class="form-text">Максимальный размер файла: 20 МБ. Поддерживаемые форматы: PDF, DOC, DOCX</div>
                                </div>
                            </div>

                            <!-- Правая колонка -->
                            <div class="col-md-4">
                                <!-- Получатель -->
                                <div class="mb-3">
                                    <label for="recipient_type" class="form-label">Тип получателя *</label>
                                    <select class="form-select" id="recipient_type" name="recipient_type" required>
                                        <option value="">Выберите тип</option>
                                        <option value="user">Сотрудник</option>
                                        <option value="client">Клиент</option>
                                        <option value="external">Внешний получатель</option>
                                    </select>
                                </div>

                                <div id="userRecipient" class="mb-3" style="display: none;">
                                    <label for="recipient_id" class="form-label">Сотрудник *</label>
                                    <select class="form-select" id="recipient_id" name="recipient_id">
                                        <option value="">Выберите сотрудника</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="externalRecipient" class="mb-3" style="display: none;">
                                    <label for="recipient_email" class="form-label">Email получателя *</label>
                                    <input type="email" class="form-control" id="recipient_email" name="recipient_email">
                                </div>

                                <!-- Настройки подписи -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Настройки подписи</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="signature_required" 
                                                   name="signature_required" value="1">
                                            <label class="form-check-label" for="signature_required">
                                                Требуется подпись получателя
                                            </label>
                                        </div>

                                        <div id="signatureSettings" style="display: none;">
                                            <div class="mb-3">
                                                <label for="expires_at" class="form-label">Срок подписания</label>
                                                <input type="datetime-local" class="form-control" id="expires_at" 
                                                       name="expires_at" min="{{ now()->format('Y-m-d\TH:i') }}">
                                                <div class="form-text">Оставьте пустым для бессрочного документа</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Дополнительная информация -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Дополнительно</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Описание</label>
                                            <textarea class="form-control" id="description" name="description" 
                                                      rows="3" placeholder="Краткое описание документа"></textarea>
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
                            <div>
                                <button type="submit" class="btn btn-primary me-2" data-action="save">
                                    <i class="fas fa-save"></i> Сохранить как черновик
                                </button>
                                <button type="submit" class="btn btn-success" data-action="send">
                                    <i class="fas fa-paper-plane"></i> Создать и отправить
                                </button>
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
    // Обработка выбора типа получателя
    const recipientType = document.getElementById('recipient_type');
    const userRecipient = document.getElementById('userRecipient');
    const externalRecipient = document.getElementById('externalRecipient');
    
    recipientType.addEventListener('change', function() {
        userRecipient.style.display = 'none';
        externalRecipient.style.display = 'none';
        
        if (this.value === 'user') {
            userRecipient.style.display = 'block';
        } else if (this.value === 'external') {
            externalRecipient.style.display = 'block';
        }
    });

    // Обработка настроек подписи
    const signatureRequired = document.getElementById('signature_required');
    const signatureSettings = document.getElementById('signatureSettings');
    
    signatureRequired.addEventListener('change', function() {
        signatureSettings.style.display = this.checked ? 'block' : 'none';
    });

    // Обработка выбора шаблона
    const templateSelect = document.getElementById('template_id');
    const documentType = document.getElementById('document_type');
    const contentField = document.getElementById('content');
    const templateVariables = document.getElementById('templateVariables');
    const variablesContainer = document.getElementById('variablesContainer');
    
    templateSelect.addEventListener('change', function() {
        if (this.value) {
            // Автоматически устанавливаем тип документа
            const selectedOption = this.options[this.selectedIndex];
            const type = selectedOption.dataset.type;
            if (type) {
                documentType.value = type;
            }
            
            // Загружаем шаблон
            fetch(`/document-templates/${this.value}/get`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        contentField.value = data.template.content;
                        
                        // Показываем переменные шаблона
                        if (data.template.variables && data.template.variables.length > 0) {
                            showTemplateVariables(data.template.variables);
                        } else {
                            templateVariables.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading template:', error);
                });
        } else {
            contentField.value = '';
            templateVariables.style.display = 'none';
        }
    });

    // Показать переменные шаблона
    function showTemplateVariables(variables) {
        variablesContainer.innerHTML = '';
        
        variables.forEach(variable => {
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.innerHTML = `
                <label class="form-label">${variable}</label>
                <input type="text" class="form-control template-variable" 
                       data-variable="${variable}" placeholder="Введите значение для {{${variable}}}">
            `;
            variablesContainer.appendChild(div);
        });
        
        templateVariables.style.display = 'block';
        
        // Обработка изменения переменных
        document.querySelectorAll('.template-variable').forEach(input => {
            input.addEventListener('input', function() {
                updateTemplateContent();
            });
        });
    }

    // Обновление содержимого с учетом переменных
    function updateTemplateContent() {
        let content = contentField.value;
        
        document.querySelectorAll('.template-variable').forEach(input => {
            const variable = input.dataset.variable;
            const value = input.value || `{{${variable}}}`;
            content = content.replace(new RegExp(`{{${variable}}}`, 'g'), value);
        });
        
        // Обновляем предварительный просмотр (если есть)
        // contentField.value = content;
    }

    // Обработка отправки формы
    const form = document.getElementById('documentForm');
    let currentAction = 'save';
    
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function(e) {
            currentAction = this.dataset.action;
        });
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Обновляем содержимое с переменными перед отправкой
        updateTemplateContent();
        
        const formData = new FormData(this);
        const submitButton = document.querySelector(`button[data-action="${currentAction}"]`);
        
        // Показываем индикатор загрузки
        submitButton.disabled = true;
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
        
        fetch('/documents', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Если нужно отправить документ сразу
                if (currentAction === 'send') {
                    return fetch(`/documents/${data.document_id}/send`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    });
                }
                return { ok: true, json: () => Promise.resolve(data) };
            } else {
                throw new Error(data.message || 'Ошибка при создании документа');
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Ошибка при отправке документа');
        })
        .then(data => {
            showAlert('success', 'Документ успешно создан' + (currentAction === 'send' ? ' и отправлен' : ''));
            window.location.href = '/documents';
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', error.message || 'Произошла ошибка при создании документа');
        })
        .finally(() => {
            // Восстанавливаем кнопку
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });

    // Автозагрузка шаблона из URL
    const urlParams = new URLSearchParams(window.location.search);
    const templateId = urlParams.get('template');
    if (templateId) {
        templateSelect.value = templateId;
        templateSelect.dispatchEvent(new Event('change'));
    }

    // Функция показа уведомлений
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
});
</script>
@endpush
