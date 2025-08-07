@extends('layouts.app')

@section('title', $document->title)

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
                    <li class="breadcrumb-item active">{{ Str::limit($document->title, 50) }}</li>
                </ol>
            </nav>

            <div class="row">
                <!-- Основное содержимое -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-1">{{ $document->title }}</h3>
                                <div class="text-muted small">
                                    <span class="badge bg-{{ $document->getStatusColor() }} me-2">
                                        {{ $document->getStatusLabel() }}
                                    </span>
                                    <span class="badge bg-secondary me-2">
                                        {{ ucfirst($document->document_type) }}
                                    </span>
                                    @if($document->signature_required)
                                        <span class="badge bg-{{ $document->getSignatureStatusColor() }}">
                                            {{ $document->getSignatureStatusLabel() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="btn-group">
                                @if($document->status === 'draft')
                                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i> Редактировать
                                    </a>
                                @endif
                                
                                @if($document->file_path)
                                    <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download"></i> Скачать
                                    </a>
                                @endif
                                
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#" onclick="printDocument()">
                                            <i class="fas fa-print me-2"></i>Печать
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="copyLink()">
                                            <i class="fas fa-link me-2"></i>Копировать ссылку
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="createNewVersion()">
                                            <i class="fas fa-copy me-2"></i>Создать новую версию
                                        </a></li>
                                        {{-- УДАЛЕНИЕ ДОКУМЕНТОВ ОТКЛЮЧЕНО ПО ТРЕБОВАНИЯМ БЕЗОПАСНОСТИ --}}
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Информация о документе -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <small class="text-muted">Создан:</small><br>
                                    <strong>{{ $document->created_at->format('d.m.Y H:i') }}</strong>
                                    <span class="text-muted">от {{ $document->creator->name }}</span>
                                </div>
                                <div class="col-md-6">
                                    @if($document->updated_at->ne($document->created_at))
                                        <small class="text-muted">Изменен:</small><br>
                                        <strong>{{ $document->updated_at->format('d.m.Y H:i') }}</strong>
                                    @endif
                                </div>
                            </div>

                            @if($document->project)
                            <div class="alert alert-info">
                                <strong>Связанный проект:</strong> 
                                {{ $document->project->client_first_name }} {{ $document->project->client_last_name }} - 
                                {{ $document->project->object_type }}
                                <br>
                                <small>
                                    Телефон: {{ $document->project->client_phone }}, 
                                    Email: {{ $document->project->client_email }}
                                </small>
                            </div>
                            @endif

                            <!-- Содержимое документа -->
                            @if($document->content)
                            <div class="document-content mb-4">
                                <h5>Содержание документа:</h5>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($document->content)) !!}
                                </div>
                            </div>
                            @endif

                            <!-- Файл документа -->
                            @if($document->file_path)
                            <div class="document-file mb-4">
                                <h5>Прикрепленный файл:</h5>
                                <div class="file-info border rounded p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon me-3">
                                            <i class="fas fa-file-{{ $document->getFileIcon() }} fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>{{ $document->original_filename }}</strong><br>
                                            <small class="text-muted">
                                                {{ $document->getFormattedFileSize() }} • 
                                                {{ strtoupper(pathinfo($document->original_filename, PATHINFO_EXTENSION)) }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('documents.download', $document) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download"></i> Скачать
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Боковая панель -->
                <div class="col-lg-4">
                    <!-- Действия с документом -->
                    @if($document->signature_required && !$document->isSigned())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-signature me-2"></i>Электронная подпись
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($document->signature_status === \App\Models\Document::SIGNATURE_PENDING)
                                @if($document->recipient_phone || $document->recipient_email)
                                    <div class="alert alert-warning">
                                        <strong>Ожидает подписи от:</strong><br>
                                        {{ $document->recipient_name }}<br>
                                        @if($document->recipient_phone)
                                            <small>Телефон: {{ $document->recipient_phone }}</small><br>
                                        @endif
                                        @if($document->recipient_email)
                                            <small>Email: {{ $document->recipient_email }}</small>
                                        @endif
                                    </div>
                                    
                                    @if(auth()->id() === $document->created_by)
                                        <button type="button" class="btn btn-outline-info btn-sm w-100 mb-2" 
                                                onclick="resendSignatureRequest()">
                                            <i class="fas fa-paper-plane"></i> Отправить повторно
                                        </button>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-primary w-100" 
                                            data-bs-toggle="offcanvas" data-bs-target="#sendSignatureOffcanvas"
                                        <i class="fas fa-paper-plane"></i> Отправить на подпись
                                    </button>
                                @endif
                                
                                @if($document->canSignDocument())
                                    <hr>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" 
                                                data-bs-toggle="offcanvas" data-bs-target="#signOffcanvas"
                                            <i class="fas fa-check"></i> Подписать
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                data-bs-toggle="offcanvas" data-bs-target="#rejectOffcanvas"
                                            <i class="fas fa-times"></i> Отклонить
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Информация о документе -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Информация о документе</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">ID:</td>
                                    <td><code>{{ $document->id }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Версия:</td>
                                    <td>{{ $document->version }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Тип:</td>
                                    <td>{{ ucfirst($document->document_type) }}</td>
                                </tr>
                                @if($document->template)
                                <tr>
                                    <td class="text-muted">Шаблон:</td>
                                    <td>{{ $document->template->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Создан:</td>
                                    <td>{{ $document->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @if($document->signed_at)
                                <tr>
                                    <td class="text-muted">Подписан:</td>
                                    <td>{{ $document->signed_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- История действий -->
                    @if($document->signatureRequests->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">История подписей</h5>
                        </div>
                        <div class="card-body">
                            @foreach($document->signatureRequests as $request)
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    @if($request->status === 'signed')
                                        <i class="fas fa-check-circle text-success"></i>
                                    @elseif($request->status === 'rejected')
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @else
                                        <i class="fas fa-clock text-warning"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <strong>{{ $request->recipient_name }}</strong><br>
                                    <small class="text-muted">
                                        {{ $request->created_at->format('d.m.Y H:i') }}
                                        @if($request->signed_at)
                                            • Подписан {{ $request->signed_at->format('d.m.Y H:i') }}
                                        @elseif($request->rejected_at)
                                            • Отклонен {{ $request->rejected_at->format('d.m.Y H:i') }}
                                        @endif
                                    </small>
                                    @if($request->rejection_reason)
                                        <div class="text-danger small mt-1">
                                            Причина отклонения: {{ $request->rejection_reason }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Боковая панель отправки на подпись -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sendSignatureOffcanvas" aria-labelledby="sendSignatureOffcanvasLabel">
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title" id="sendSignatureOffcanvasLabel">
            <i class="fas fa-paper-plane me-2"></i>Отправить документ на подпись
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('documents.send', $document) }}" method="POST" id="sendSignatureForm">
            @csrf
            <div class="mb-3">
                <label for="recipient_name" class="form-label required">Имя получателя</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name" 
                       value="{{ $document->recipient_name }}" required>
            </div>
            
            <div class="mb-3">
                <label for="recipient_phone" class="form-label">Телефон</label>
                <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" 
                       value="{{ $document->recipient_phone }}">
            </div>
            
            <div class="mb-3">
                <label for="recipient_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="recipient_email" name="recipient_email" 
                       value="{{ $document->recipient_email }}">
            </div>
            
            <div class="mb-3">
                <label for="message" class="form-label">Сообщение</label>
                <textarea class="form-control" id="message" name="message" rows="3" 
                          placeholder="Дополнительное сообщение для получателя"></textarea>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <small>Необходимо указать хотя бы один способ связи: телефон или email.</small>
            </div>
        </form>
    </div>
    
    <div class="offcanvas-footer border-top p-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                <i class="fas fa-times me-1"></i>Отмена
            </button>
            <button type="submit" form="sendSignatureForm" class="btn btn-primary flex-fill">
                <i class="fas fa-paper-plane me-1"></i>Отправить
            </button>
        </div>
    </div>
</div>

<!-- Боковая панель подписи документа -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="signOffcanvas" aria-labelledby="signOffcanvasLabel">
    <div class="offcanvas-header bg-success text-white">
        <h5 class="offcanvas-title" id="signOffcanvasLabel">
            <i class="fas fa-signature me-2"></i>Подписать документ
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('documents.sign', $document) }}" method="POST" id="signDocumentForm">
            @csrf
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Внимание!</strong> Вы собираетесь поставить электронную подпись на документ 
                "{{ $document->title }}". Это действие нельзя будет отменить.
            </div>
            
            <div class="mb-3">
                <label for="signature_comment" class="form-label">Комментарий к подписи</label>
                <textarea class="form-control" id="signature_comment" name="signature_comment" rows="3" 
                          placeholder="Дополнительный комментарий (необязательно)"></textarea>
            </div>
        </form>
    </div>
    
    <div class="offcanvas-footer border-top p-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                <i class="fas fa-times me-1"></i>Отмена
            </button>
            <button type="submit" form="signDocumentForm" class="btn btn-success flex-fill">
                <i class="fas fa-signature me-1"></i>Подписать документ
            </button>
        </div>
    </div>
</div>

<!-- Боковая панель отклонения документа -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="rejectOffcanvas" aria-labelledby="rejectOffcanvasLabel">
    <div class="offcanvas-header bg-danger text-white">
        <h5 class="offcanvas-title" id="rejectOffcanvasLabel">
            <i class="fas fa-times me-2"></i>Отклонить документ
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('documents.reject', $document) }}" method="POST" id="rejectDocumentForm">
            @csrf
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Вы собираетесь отклонить документ "{{ $document->title }}". 
                Это действие изменит статус документа.
            </div>
            
            <div class="mb-3">
                <label for="rejection_reason" class="form-label required">Причина отклонения</label>
                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" 
                          placeholder="Укажите причину отклонения документа" required></textarea>
                <div class="form-text">Опишите подробно причину отклонения документа</div>
            </div>
        </form>
    </div>
    
    <div class="offcanvas-footer border-top p-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                <i class="fas fa-times me-1"></i>Отмена
            </button>
            <button type="submit" form="rejectDocumentForm" class="btn btn-danger flex-fill">
                <i class="fas fa-times me-1"></i>Отклонить
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function resendSignatureRequest() {
    if (confirm('Отправить повторный запрос на подпись?')) {
        // Отправка AJAX запроса на повторную отправку
        fetch('{{ route("documents.send", $document) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                resend: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification('error', 'Ошибка при отправке: ' + (data.message || 'Неизвестная ошибка'), {
                    icon: 'fas fa-exclamation-triangle'
                });
            }
        });
    }
}

function printDocument() {
    window.print();
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showNotification('success', 'Ссылка скопирована в буфер обмена', {
            icon: 'fas fa-copy',
            timeout: 2000
        });
    }).catch(() => {
        showNotification('error', 'Не удалось скопировать ссылку', {
            icon: 'fas fa-exclamation-triangle'
        });
    });
}

function createNewVersion() {
    if (confirm('Создать новую версию документа?')) {
        window.location.href = '{{ route("documents.create") }}?copy_from={{ $document->id }}';
    }
}

// ФУНКЦИЯ УДАЛЕНИЯ ДОКУМЕНТОВ ОТКЛЮЧЕНА ПО ТРЕБОВАНИЯМ БЕЗОПАСНОСТИ
// function deleteDocument(id) { ... }

// Улучшенная функция показа уведомлений
function showNotification(type, message, options = {}) {
    const {
        icon = '',
        timeout = 5000,
        position = 'top-right'
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

/* Стили для боковых панелей */
.offcanvas-end {
    width: 450px !important;
    max-width: 90vw;
}

.offcanvas-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.offcanvas-header {
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

/* Стили для обязательных полей */
.form-label.required::after {
    content: " *";
    color: #dc3545;
}

/* Адаптивность для мобильных устройств */
@media (max-width: 768px) {
    .offcanvas-end {
        width: 100% !important;
    }
}
</style>
@endpush
