@extends('layouts.app')

@section('title', $document->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0">{{ $document->title }}</h3>
                            <small class="text-muted">{{ $document->type_name }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Статус документа -->
                            @php
                                $statusClass = match($document->status) {
                                    'draft' => 'bg-secondary',
                                    'sent' => 'bg-info',
                                    'received' => 'bg-success',
                                    'signed' => 'bg-primary',
                                    'expired' => 'bg-warning',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $document->status_name }}</span>
                            
                            <!-- Статус подписи -->
                            @php
                                $signatureClass = match($document->signature_status) {
                                    'not_required' => 'bg-light text-dark',
                                    'pending' => 'bg-warning',
                                    'signed' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'expired' => 'bg-secondary',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $signatureClass }}">
                                {{ $document->signature_status_name }}
                            </span>
                            
                            @if($document->digital_signature)
                                <span class="badge bg-success">
                                    <i class="fas fa-shield-alt"></i> ЭЦП
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Основное содержимое -->
                        <div class="col-md-8">
                            <!-- Содержимое документа -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Содержимое документа</h5>
                                </div>
                                <div class="card-body">
                                    <div class="document-content" style="white-space: pre-wrap; line-height: 1.6;">
                                        {{ $document->content }}
                                    </div>
                                </div>
                            </div>

                            <!-- Прикрепленный файл -->
                            @if($document->file_path)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Прикрепленный файл</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                                                <div>
                                                    <strong>{{ $document->original_name }}</strong><br>
                                                    <small class="text-muted">
                                                        {{ $document->formatted_size }} • {{ $document->mime_type }}
                                                    </small>
                                                </div>
                                            </div>
                                            <a href="{{ route('documents.download', $document) }}" 
                                               class="btn btn-primary">
                                                <i class="fas fa-download"></i> Скачать
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Информация о подписи -->
                            @if($document->signature_data)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-signature"></i> Информация о подписи
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $signatureData = $document->signature_data;
                                        @endphp
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Тип подписи:</strong><br>
                                                @if($signatureData['type'] === 'simple')
                                                    <span class="badge bg-info">Простая электронная подпись</span>
                                                @else
                                                    <span class="badge bg-success">Квалифицированная электронная подпись</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Дата подписания:</strong><br>
                                                {{ $document->signed_at->format('d.m.Y H:i:s') }}
                                            </div>
                                        </div>

                                        @if($signatureData['type'] === 'simple')
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Подписавший:</strong><br>
                                                    {{ $signatureData['user_name'] ?? 'Неизвестно' }}<br>
                                                    <small class="text-muted">{{ $signatureData['user_email'] ?? '' }}</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>IP адрес:</strong><br>
                                                    {{ $signatureData['ip_address'] ?? 'Неизвестно' }}
                                                </div>
                                            </div>
                                        @else
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Алгоритм подписи:</strong><br>
                                                    {{ $signatureData['algorithm'] ?? 'GOST R 34.10-2012' }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Алгоритм хеширования:</strong><br>
                                                    {{ $signatureData['hash_algorithm'] ?? 'GOST R 34.11-2012' }}
                                                </div>
                                            </div>
                                            
                                            @if(isset($signatureData['certificate']))
                                                <hr>
                                                <h6>Информация о сертификате:</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Серийный номер:</strong><br>
                                                        <code>{{ $signatureData['certificate']['serial_number'] ?? 'Неизвестно' }}</code>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Издатель:</strong><br>
                                                        {{ $signatureData['certificate']['issuer'] ?? 'Неизвестно' }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        <hr>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong>Проверка целостности:</strong>
                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        onclick="verifySignature({{ $document->id }})">
                                                    <i class="fas fa-shield-alt"></i> Проверить подпись
                                                </button>
                                            </span>
                                            
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-info" 
                                                        onclick="showSignatureDetails({{ $document->id }})">
                                                    <i class="fas fa-info-circle"></i> Подробнее
                                                </button>
                                                <a href="{{ route('documents.export-signature', $document) }}" 
                                                   class="btn btn-outline-secondary">
                                                    <i class="fas fa-download"></i> Экспорт CAdES
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Боковая панель -->
                        <div class="col-md-4">
                            <!-- Информация о документе -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Информация</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Отправитель:</strong><br>
                                        {{ $document->sender->name }}
                                        <br><small class="text-muted">{{ $document->sender->email }}</small>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Получатель:</strong><br>
                                        @if($document->recipient_type === 'user' && $document->recipient)
                                            {{ $document->recipient->name }}
                                            <br><small class="text-muted">{{ $document->recipient->email }}</small>
                                        @else
                                            {{ $document->recipient_type }}
                                        @endif
                                    </div>

                                    @if($document->project)
                                        <div class="mb-3">
                                            <strong>Проект:</strong><br>
                                            <a href="{{ route('projects.show', $document->project) }}" 
                                               class="text-decoration-none">
                                                {{ $document->project->title }}
                                            </a>
                                        </div>
                                    @endif

                                    @if($document->template)
                                        <div class="mb-3">
                                            <strong>Шаблон:</strong><br>
                                            {{ $document->template->name }}
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <strong>Создан:</strong><br>
                                        {{ $document->created_at->format('d.m.Y H:i') }}
                                    </div>

                                    @if($document->sent_at)
                                        <div class="mb-3">
                                            <strong>Отправлен:</strong><br>
                                            {{ $document->sent_at->format('d.m.Y H:i') }}
                                        </div>
                                    @endif

                                    @if($document->expires_at)
                                        <div class="mb-3">
                                            <strong>Действителен до:</strong><br>
                                            <span class="{{ $document->isSignatureExpired() ? 'text-danger' : 'text-success' }}">
                                                {{ $document->expires_at->format('d.m.Y H:i') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Действия -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Действия</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $user = Auth::user();
                                        $canSign = $document->recipient_type === 'user' && 
                                                  $document->recipient_id === $user->id && 
                                                  $document->requiresSignature();
                                        $canSend = $document->sender_id === $user->id && 
                                                  $document->status === 'draft';
                                        $canDelete = $document->sender_id === $user->id && 
                                                    !$document->isSigned();
                                    @endphp

                                    @if($canSign)
                                        <button type="button" 
                                                class="btn btn-success w-100 mb-2 btn-sign" 
                                                data-document-id="{{ $document->id }}">
                                            <i class="fas fa-signature"></i> Подписать документ
                                        </button>
                                    @endif

                                    @if($canSend)
                                        <button type="button" 
                                                class="btn btn-info w-100 mb-2 btn-send" 
                                                data-document-id="{{ $document->id }}">
                                            <i class="fas fa-paper-plane"></i> Отправить
                                        </button>
                                    @endif

                                    @if($document->file_path)
                                        <a href="{{ route('documents.download', $document) }}" 
                                           class="btn btn-primary w-100 mb-2">
                                            <i class="fas fa-download"></i> Скачать файл
                                        </a>
                                    @endif

                                    <a href="{{ route('documents.index') }}" 
                                       class="btn btn-secondary w-100 mb-2">
                                        <i class="fas fa-arrow-left"></i> Назад к документам
                                    </a>

                                    @if($canDelete)
                                        <button type="button" 
                                                class="btn btn-danger w-100 btn-delete" 
                                                data-document-id="{{ $document->id }}">
                                            <i class="fas fa-trash"></i> Удалить
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Подключаем модальное окно для подписи из основной страницы -->
@include('documents.partials.signature-modal')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка кнопок (используем код из основной страницы)
    @include('documents.partials.actions-script')
    
    // Функция проверки подписи
    window.verifySignature = function(documentId) {
        fetch(`/documents/${documentId}/verify`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const verification = data.verification;
                let message = '';
                let alertType = 'success';
                
                if (verification.valid) {
                    message = `✅ Подпись действительна\n`;
                    message += `Тип: ${verification.type === 'simple' ? 'Простая ЭП' : 'Квалифицированная ЭП'}\n`;
                    message += `Подписан: ${verification.signed_at || 'Неизвестно'}`;
                    
                    if (verification.type === 'qualified') {
                        message += `\nСертификат: ${verification.certificate_serial || 'Неизвестно'}`;
                        message += `\nУЦ: ${verification.certificate_issuer || 'Неизвестно'}`;
                    }
                } else {
                    message = `❌ Подпись недействительна\n${verification.error}`;
                    alertType = 'error';
                }
                
                alert(message);
            } else {
                alert('Ошибка при проверке подписи: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при проверке подписи');
        });
    };
    
    // Функция показа деталей подписи
    window.showSignatureDetails = function(documentId) {
        // Показываем модальное окно с подробной информацией о подписи
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подробная информация о подписи</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Загрузка информации о подписи...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        // Загружаем детальную информацию
        fetch(`/documents/${documentId}/verify`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            let content = '';
            
            if (data.success && data.verification) {
                const v = data.verification;
                content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Статус проверки</h6>
                            <p class="badge ${v.valid ? 'bg-success' : 'bg-danger'}">
                                ${v.valid ? '✅ Действительна' : '❌ Недействительна'}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Тип подписи</h6>
                            <p>${v.type === 'simple' ? 'Простая ЭП' : 'Квалифицированная ЭП'}</p>
                        </div>
                    </div>
                `;
                
                if (v.type === 'qualified') {
                    content += `
                        <hr>
                        <h6>Информация о сертификате</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Серийный номер:</strong><br>
                                <code>${v.certificate_serial || 'Неизвестно'}</code>
                            </div>
                            <div class="col-md-6">
                                <strong>Издатель:</strong><br>
                                ${v.certificate_issuer || 'Неизвестно'}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Алгоритм:</strong><br>
                                ${v.algorithm || 'GOST R 34.10-2012'}
                            </div>
                            <div class="col-md-6">
                                <strong>Дата подписания:</strong><br>
                                ${v.signed_at || 'Неизвестно'}
                            </div>
                        </div>
                    `;
                }
                
                if (!v.valid && v.error) {
                    content += `
                        <hr>
                        <div class="alert alert-danger">
                            <strong>Ошибка:</strong> ${v.error}
                        </div>
                    `;
                }
            } else {
                content = `
                    <div class="alert alert-danger">
                        Не удалось получить информацию о подписи
                    </div>
                `;
            }
            
            modal.querySelector('.modal-body').innerHTML = content;
        })
        .catch(error => {
            modal.querySelector('.modal-body').innerHTML = `
                <div class="alert alert-danger">
                    Произошла ошибка при загрузке информации о подписи
                </div>
            `;
        });
    };
});
</script>
@endpush
