@extends('layouts.app')

@section('title', 'Документы')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Документы</h3>
                    <div class="btn-group">
                        <a href="{{ route('documents.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Создать документ
                        </a>
                        <a href="{{ route('document-templates.create') }}" class="btn btn-secondary">
                            <i class="fas fa-file-alt"></i> Создать шаблон
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Навигационные вкладки -->
                    <ul class="nav nav-tabs mb-4" id="documentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab === 'received' ? 'active' : '' }}" 
                                    id="received-tab" data-bs-toggle="tab" data-bs-target="#received-content" 
                                    type="button" role="tab" data-tab="received">
                                <i class="fas fa-inbox"></i> Полученные
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab === 'created' ? 'active' : '' }}" 
                                    id="created-tab" data-bs-toggle="tab" data-bs-target="#created-content" 
                                    type="button" role="tab" data-tab="created">
                                <i class="fas fa-paper-plane"></i> Созданные
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab === 'signed' ? 'active' : '' }}" 
                                    id="signed-tab" data-bs-toggle="tab" data-bs-target="#signed-content" 
                                    type="button" role="tab" data-tab="signed">
                                <i class="fas fa-signature"></i> Подписанные
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab === 'templates' ? 'active' : '' }}" 
                                    id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates-content" 
                                    type="button" role="tab" data-tab="templates">
                                <i class="fas fa-file-alt"></i> Шаблоны
                            </button>
                        </li>
                    </ul>

                    <!-- Содержимое вкладок -->
                    <div class="tab-content" id="documentsTabContent">
                        <!-- Индикатор загрузки -->
                        <div id="loading-indicator" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <div class="mt-2">Загрузка данных...</div>
                        </div>

                        <!-- Контент вкладок -->
                        <div class="tab-pane fade {{ $tab === 'received' ? 'show active' : '' }}" 
                             id="received-content" role="tabpanel">
                            @if($tab === 'received')
                                @include('documents.partials.documents-tab', ['documents' => $documents, 'tab' => 'received'])
                            @endif
                        </div>
                        
                        <div class="tab-pane fade {{ $tab === 'created' ? 'show active' : '' }}" 
                             id="created-content" role="tabpanel">
                            @if($tab === 'created')
                                @include('documents.partials.documents-tab', ['documents' => $documents, 'tab' => 'created'])
                            @endif
                        </div>
                        
                        <div class="tab-pane fade {{ $tab === 'signed' ? 'show active' : '' }}" 
                             id="signed-content" role="tabpanel">
                            @if($tab === 'signed')
                                @include('documents.partials.documents-tab', ['documents' => $documents, 'tab' => 'signed'])
                            @endif
                        </div>
                        
                        <div class="tab-pane fade {{ $tab === 'templates' ? 'show active' : '' }}" 
                             id="templates-content" role="tabpanel">
                            @if($tab === 'templates')
                                @include('documents.partials.templates-tab', ['templates' => $templates])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для подписи -->
<div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signatureModalLabel">Подписание документа</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="signatureForm">
                    <input type="hidden" id="documentId" name="document_id">
                    
                    <div class="mb-3">
                        <label for="signatureType" class="form-label">Тип подписи</label>
                        <select class="form-select" id="signatureType" name="signature_type" required>
                            <option value="">Выберите тип подписи</option>
                            <option value="simple">Простая электронная подпись</option>
                            <option value="qualified">Квалифицированная электронная подпись</option>
                        </select>
                    </div>

                    <div id="qualifiedSignatureFields" style="display: none;">
                        <div class="mb-3">
                            <label for="certificateFile" class="form-label">Файл сертификата</label>
                            <input type="file" class="form-control" id="certificateFile" accept=".p12,.pfx">
                            <div class="form-text">Загрузите файл сертификата (.p12 или .pfx)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pinCode" class="form-label">PIN-код</label>
                            <input type="password" class="form-control" id="pinCode" name="pin_code">
                            <div class="form-text">Введите PIN-код для доступа к закрытому ключу</div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6>Информация о подписи:</h6>
                        <ul class="mb-0">
                            <li><strong>Простая ЭП:</strong> Подтверждает авторство и целостность документа</li>
                            <li><strong>Квалифицированная ЭП:</strong> Соответствует требованиям 63-ФЗ "Об электронной подписи", имеет юридическую силу</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="signDocument">Подписать</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX переключение вкладок
    const documentTabs = document.getElementById('documentTabs');
    const tabContent = document.getElementById('documentsTabContent');
    const loadingIndicator = document.getElementById('loading-indicator');
    let currentTab = '{{ $tab }}';

    // Обработка клика по вкладкам
    documentTabs.addEventListener('click', function(e) {
        const tabButton = e.target.closest('[data-tab]');
        if (!tabButton) return;

        e.preventDefault();
        
        const targetTab = tabButton.dataset.tab;
        if (targetTab === currentTab) return; // Уже активная вкладка

        loadTabContent(targetTab, tabButton);
    });

    // Функция загрузки содержимого вкладки
    function loadTabContent(tab, tabButton, page = 1) {
        // Показываем индикатор загрузки
        showLoadingIndicator();
        
        // Обновляем активную вкладку только если это не пагинация
        if (page === 1) {
            updateActiveTab(tabButton);
        }
        
        // Формируем URL с параметрами
        const url = new URL(`{{ route('documents.index') }}`);
        url.searchParams.set('tab', tab);
        if (page > 1) {
            url.searchParams.set('page', page);
        }
        
        // Отправляем AJAX-запрос
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка сервера');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Обновляем содержимое соответствующей вкладки
                const targetPane = document.getElementById(`${tab}-content`);
                if (targetPane) {
                    targetPane.innerHTML = data.html;
                    
                    // Показываем новую вкладку
                    showTabPane(tab);
                    
                    // Обновляем URL без перезагрузки страницы только для первой страницы
                    if (page === 1) {
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('tab', tab);
                        newUrl.searchParams.delete('page'); // Убираем page для первой страницы
                        window.history.pushState({tab: tab, page: page}, '', newUrl);
                    } else {
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('tab', tab);
                        newUrl.searchParams.set('page', page);
                        window.history.pushState({tab: tab, page: page}, '', newUrl);
                    }
                    
                    currentTab = tab;
                    
                    // Переинициализируем обработчики событий для новых элементов
                    initializeTabEventHandlers();
                }
            } else {
                throw new Error(data.message || 'Ошибка загрузки данных');
            }
        })
        .catch(error => {
            console.error('Error loading tab content:', error);
            showAlert('error', 'Ошибка загрузки данных: ' + error.message);
        })
        .finally(() => {
            hideLoadingIndicator();
        });
    }

    // Показать индикатор загрузки
    function showLoadingIndicator() {
        // Скрываем все вкладки
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        loadingIndicator.style.display = 'block';
    }

    // Скрыть индикатор загрузки
    function hideLoadingIndicator() {
        loadingIndicator.style.display = 'none';
    }

    // Обновить активную вкладку
    function updateActiveTab(activeButton) {
        // Убираем active класс со всех вкладок
        document.querySelectorAll('#documentTabs .nav-link').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Добавляем active класс к текущей вкладке
        activeButton.classList.add('active');
    }

    // Показать соответствующую панель вкладки
    function showTabPane(tab) {
        // Скрываем все панели
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Показываем нужную панель
        const targetPane = document.getElementById(`${tab}-content`);
        if (targetPane) {
            targetPane.classList.add('show', 'active');
        }
    }

    // Инициализация обработчиков для динамически загруженного контента
    function initializeTabEventHandlers() {
        // Переинициализируем обработчики кнопок подписи
        initializeSignatureHandlers();
        
        // Переинициализируем обработчики других кнопок
        initializeDocumentHandlers();
        initializeTemplateHandlers();
        
        // Переинициализируем пагинацию
        initializePagination();
    }

    // Инициализация AJAX-пагинации
    function initializePagination() {
        document.querySelectorAll('.pagination a[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const page = this.dataset.page;
                const tab = this.dataset.tab || currentTab;
                const tabButton = document.querySelector(`[data-tab="${tab}"]`);
                
                if (page && tab && tabButton) {
                    loadTabContent(tab, tabButton, page);
                }
            });
        });
    }

    // Инициализация обработчиков подписи
    function initializeSignatureHandlers() {
        document.querySelectorAll('.btn-sign').forEach(button => {
            button.addEventListener('click', function() {
                const documentId = this.dataset.documentId;
                document.getElementById('documentId').value = documentId;
                
                const modal = new bootstrap.Modal(document.getElementById('signatureModal'));
                modal.show();
            });
        });
    }

    // Инициализация обработчиков документов
    function initializeDocumentHandlers() {
        // Обработка отправки документов
        document.querySelectorAll('.btn-send').forEach(button => {
            button.addEventListener('click', function() {
                const documentId = this.dataset.documentId;
                
                if (confirm('Вы уверены, что хотите отправить этот документ?')) {
                    fetch(`/documents/${documentId}/send`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            // Перезагружаем текущую вкладку
                            loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                        } else {
                            showAlert('error', data.message);
                        }
                    })
                    .catch(error => {
                        showAlert('error', 'Ошибка при отправке документа');
                    });
                }
            });
        });

        // Обработка удаления документов
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const documentId = this.dataset.documentId;
                
                if (confirm('Вы уверены, что хотите удалить этот документ? Это действие нельзя отменить.')) {
                    fetch(`/documents/${documentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            // Перезагружаем текущую вкладку
                            loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
                        } else {
                            showAlert('error', data.message);
                        }
                    })
                    .catch(error => {
                        showAlert('error', 'Ошибка при удалении документа');
                    });
                }
            });
        });
    }

    // Инициализация обработчиков шаблонов
    function initializeTemplateHandlers() {
        document.querySelectorAll('.btn-use-template').forEach(button => {
            button.addEventListener('click', function() {
                const templateId = this.dataset.templateId;
                window.location.href = `/documents/create?template=${templateId}`;
            });
        });
    }

    // Инициализация обработчиков шаблонов
    function initializeTemplateHandlers() {
        // Обработка использования шаблона
        document.querySelectorAll('.btn-use-template').forEach(button => {
            button.addEventListener('click', function() {
                const templateId = this.dataset.templateId;
                window.location.href = `{{ route('documents.create') }}?template=${templateId}`;
            });
        });
    }

    // Обработка истории браузера
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.tab) {
            const tabButton = document.querySelector(`[data-tab="${event.state.tab}"]`);
            if (tabButton && event.state.tab !== currentTab) {
                loadTabContent(event.state.tab, tabButton);
            }
        }
    });

    // Обработка смены типа подписи
    const signatureType = document.getElementById('signatureType');
    const qualifiedFields = document.getElementById('qualifiedSignatureFields');
    
    signatureType.addEventListener('change', function() {
        if (this.value === 'qualified') {
            qualifiedFields.style.display = 'block';
        } else {
            qualifiedFields.style.display = 'none';
        }
    });

    // Обработка подписания документа
    document.getElementById('signDocument').addEventListener('click', function() {
        const form = document.getElementById('signatureForm');
        const formData = new FormData(form);
        
        // Обработка файла сертификата
        const certificateFile = document.getElementById('certificateFile').files[0];
        if (formData.get('signature_type') === 'qualified' && certificateFile) {
            const reader = new FileReader();
            reader.onload = function(e) {
                formData.append('certificate_data', JSON.stringify({
                    file_content: e.target.result,
                    file_name: certificateFile.name
                }));
                submitSignature(formData);
            };
            reader.readAsDataURL(certificateFile);
        } else {
            submitSignature(formData);
        }
    });

    function submitSignature(formData) {
        const documentId = formData.get('document_id');
        
        fetch(`/documents/${documentId}/sign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                // Перезагружаем текущую вкладку
                loadTabContent(currentTab, document.querySelector(`[data-tab="${currentTab}"]`));
            } else {
                showAlert('error', data.message || 'Произошла ошибка при подписании');
            }
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('signatureModal'));
            modal.hide();
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Произошла ошибка при подписании документа');
        });
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

    // Инициализируем обработчики для изначально загруженного контента
    initializeTabEventHandlers();
});
</script>
@endpush
