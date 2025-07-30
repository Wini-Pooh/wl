<!-- AJAX Модальное окно для загрузки документов (версия 2.0) -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">
                    <i class="bi bi-cloud-upload me-2"></i>Загрузка документов
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDocumentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="documentProjectId" value="{{ $project->id ?? '' }}">
                    
                    <div class="mb-4">
                        <label for="documentInput" class="form-label">Выберите документы</label>
                        <input type="file" id="documentInput" name="files[]" class="form-control" multiple>
                        <div class="form-text">Поддерживаемые форматы: PDF, DOC, DOCX, XLS, XLSX, TXT, RTF</div>
                    </div>
                    
                    <div id="documentPreviewContainer" class="mb-4" style="display: none;">
                        <h6 class="mb-3">Выбранные документы: <span id="selectedDocumentsCount">0</span></h6>
                        <div id="documentPreview" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="documentCategory" class="form-label">Категория документа</label>
                                <select class="form-select" id="documentCategory" name="category">
                                    <option value="">Выберите категорию</option>
                                    <option value="contract">Договор</option>
                                    <option value="specification">Спецификация</option>
                                    <option value="estimate">Смета</option>
                                    <option value="act">Акт</option>
                                    <option value="invoice">Счет</option>
                                    <option value="technical">Техническая документация</option>
                                    <option value="legal">Юридический документ</option>
                                    <option value="permits">Разрешительная документация</option>
                                    <option value="report">Отчет</option>
                                    <option value="other">Другое</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="documentStatus" class="form-label">Статус</label>
                                <select class="form-select" id="documentStatus" name="status">
                                    <option value="active">Актуальный</option>
                                    <option value="draft">Черновик</option>
                                    <option value="archived">Архивный</option>
                                    <option value="pending">На согласовании</option>
                                    <option value="approved">Утвержден</option>
                                    <option value="rejected">Отклонен</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="documentDescription" class="form-label">Описание (необязательно)</label>
                        <textarea class="form-control" id="documentDescription" name="description" rows="3" placeholder="Добавьте описание документа"></textarea>
                    </div>
                </form>
                
                <div class="progress mb-3" id="documentUploadProgress" style="display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                
                <div class="alert alert-danger" id="documentUploadError" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="uploadDocumentsBtn" disabled>
                    <i class="bi bi-cloud-upload me-2"></i>Загрузить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра документа -->
<div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDocumentModalLabel">Просмотр документа</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4" id="documentPreviewContent">
                    <!-- Содержимое будет добавлено динамически -->
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 id="viewDocumentTitle" class="card-title mb-3"></h5>
                        
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-primary" id="viewDocumentCategory"></span>
                            <span class="badge bg-secondary" id="viewDocumentStatus"></span>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-muted">Описание:</h6>
                            <p id="viewDocumentDescription" class="mb-0"></p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Размер файла:</strong> <span id="viewDocumentSize"></span></p>
                                <p class="mb-1"><strong>Тип файла:</strong> <span id="viewDocumentFormat"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Дата загрузки:</strong> <span id="viewDocumentDate"></span></p>
                                <p class="mb-1"><strong>Загрузил:</strong> <span id="viewDocumentUser"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="downloadDocumentBtn" target="_blank">
                    <i class="bi bi-download me-2"></i>Скачать
                </a>
                <button type="button" class="btn btn-danger" id="deleteDocumentBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="confirmDeleteDocumentModal" tabindex="-1" aria-labelledby="confirmDeleteDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteDocumentModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этот документ?</p>
                <input type="hidden" id="documentToDeleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmDocumentDeleteBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Инициализация модальных окон для документов');
    
    // Функция создания превью для документов
    function showDocumentPreview(files) {
        console.log('Вызов showDocumentPreview с', files.length, 'файлами');
        const container = $('#documentPreview');
        
        // Очищаем контейнер
        container.html('');
        
        if (!files || files.length === 0) return;
        
        // Для отслеживания уже обработанных файлов
        const alreadyProcessed = new Set();
        
        // Создаем превью для файлов
        Array.from(files).forEach((file, index) => {
            // Создаем уникальный идентификатор файла
            const fileId = file.name + '_' + file.size + '_' + index;
            
            // Пропускаем, если файл уже обработан
            if (alreadyProcessed.has(fileId)) {
                console.log('Файл уже обработан, пропускаем:', file.name);
                return;
            }
            
            alreadyProcessed.add(fileId);
            
            // Определяем иконку в зависимости от типа файла
            const extension = file.name.split('.').pop().toLowerCase();
            let icon = 'bi-file';
            
            switch (extension) {
                case 'pdf':
                    icon = 'bi-file-pdf';
                    break;
                case 'doc':
                case 'docx':
                    icon = 'bi-file-word';
                    break;
                case 'xls':
                case 'xlsx':
                    icon = 'bi-file-excel';
                    break;
                case 'txt':
                    icon = 'bi-file-text';
                    break;
            }
            
            // Добавляем элемент с иконкой в превью
            container.append(`
                <div class="position-relative d-inline-block me-2 mb-2" data-file-index="${index}" data-file-id="${fileId}">
                    <div class="document-preview text-center p-2 border rounded">
                        <i class="${icon} fs-1"></i>
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeDocumentPreview(this, ${index})">
                            <i class="bi bi-x"></i>
                        </button>
                        <small class="d-block text-muted mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                            ${file.name}
                        </small>
                    </div>
                </div>
            `);
        });
        
        // Обновляем счетчик
        $('#selectedDocumentsCount').text(alreadyProcessed.size);
    }

    // Обработчик выбора файлов
    $('#documentInput').off('change').one('change', function(e) {
        const files = this.files;
        console.log('Файлы выбраны:', files.length);
        
        if (files && files.length > 0) {
            $('#uploadDocumentsBtn').prop('disabled', false);
            $('#documentPreviewContainer').show();
            showDocumentPreview(files);
        } else {
            $('#uploadDocumentsBtn').prop('disabled', true);
            $('#documentPreviewContainer').hide();
            $('#selectedDocumentsCount').text(0);
        }
        
        // Переинициализируем обработчик для следующего выбора файлов
        $(this).off('change').one('change', arguments.callee);
    });
    
    // Обработчик загрузки документов
    $('#uploadDocumentsBtn').click(function() {
        uploadDocuments();
    });
    
    // Обработчик удаления документа
    $('#deleteDocumentBtn').click(function() {
        const documentId = $(this).data('document-id');
        $('#documentToDeleteId').val(documentId);
        $('#viewDocumentModal').modal('hide');
        $('#confirmDeleteDocumentModal').modal('show');
    });
    
    // Подтверждение удаления
    $('#confirmDocumentDeleteBtn').click(function() {
        const documentId = $('#documentToDeleteId').val();
        if (documentId) {
            deleteDocument(documentId);
        }
    });
    
    // Сброс при закрытии модального окна
    $('#uploadDocumentModal').on('hidden.bs.modal', function() {
        const form = document.getElementById('uploadDocumentForm');
        if (form) form.reset();
        
        $('#documentPreview').empty();
        $('#documentPreviewContainer').hide();
        $('#selectedDocumentsCount').text(0);
        $('#uploadDocumentsBtn').prop('disabled', true);
        $('#documentUploadProgress').hide();
        $('#documentUploadError').hide();
    });
});

// Функция загрузки документов через AJAX
function uploadDocuments() {
    const form = $('#uploadDocumentForm')[0];
    const formData = new FormData(form);
    const projectId = $('#documentProjectId').val();
    
    // Добавляем CSRF токен
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    $('#uploadDocumentsBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...');
    $('#documentUploadProgress').show();
    $('#documentUploadError').hide();
    
    $.ajax({
        url: `/api/projects/${projectId}/documents`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    $('#documentUploadProgress .progress-bar')
                        .css('width', percent + '%')
                        .attr('aria-valuenow', percent)
                        .text(percent + '%');
                }
            });
            return xhr;
        },
        success: function(response) {
            console.log('Документы успешно загружены', response);
            $('#uploadDocumentModal').modal('hide');
            
            // Обновляем список документов
            if (window.DocumentManagerFixed && typeof window.DocumentManagerFixed.loadFiles === 'function') {
                window.DocumentManagerFixed.loadFiles();
            }
            
            // Показываем сообщение
            showMessage('Документы успешно загружены', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке документов:', error);
            $('#uploadDocumentsBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
            
            let errorMessage = 'Произошла ошибка при загрузке документов';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            $('#documentUploadError').text(errorMessage).show();
        },
        complete: function() {
            $('#uploadDocumentsBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
        }
    });
}

// Функция удаления документа через AJAX
function deleteDocument(documentId) {
    const projectId = $('#documentProjectId').val();
    
    $('#confirmDocumentDeleteBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Удаление...');
    
    $.ajax({
        url: `/api/projects/${projectId}/documents/${documentId}`,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Документ успешно удален', response);
            $('#confirmDeleteDocumentModal').modal('hide');
            
            // Обновляем список документов
            if (window.DocumentManagerFixed && typeof window.DocumentManagerFixed.loadFiles === 'function') {
                window.DocumentManagerFixed.loadFiles();
            }
            
            // Показываем сообщение
            showMessage('Документ успешно удален', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при удалении документа:', error);
            
            let errorMessage = 'Произошла ошибка при удалении документа';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showMessage(errorMessage, 'error');
        },
        complete: function() {
            $('#confirmDocumentDeleteBtn').prop('disabled', false).html('<i class="bi bi-trash me-2"></i>Удалить');
        }
    });
}

// Открытие модального окна просмотра документа
function viewDocument(documentId) {
    const projectId = $('#documentProjectId').val();
    
    $.ajax({
        url: `/api/projects/${projectId}/documents/${documentId}`,
        type: 'GET',
        success: function(response) {
            const document = response.data;
            
            // Заполняем данные в модальном окне
            $('#viewDocumentTitle').text(document.name || 'Документ без названия');
            $('#viewDocumentCategory').text(getDocumentCategoryName(document.category));
            $('#viewDocumentStatus').text(getDocumentStatusName(document.status));
            $('#viewDocumentDescription').text(document.description || 'Описание отсутствует');
            $('#viewDocumentSize').text(formatFileSize(document.size));
            $('#viewDocumentFormat').text(document.extension.toUpperCase());
            $('#viewDocumentDate').text(formatDate(document.created_at));
            $('#viewDocumentUser').text(document.user ? document.user.name : 'Система');
            $('#downloadDocumentBtn').attr('href', document.download_url);
            $('#deleteDocumentBtn').data('document-id', document.id);
            
            // Подготавливаем предпросмотр в зависимости от типа файла
            const previewContainer = $('#documentPreviewContent');
            previewContainer.empty();
            
            const isPdf = document.extension.toLowerCase() === 'pdf';
            
            if (isPdf) {
                previewContainer.html(`<iframe src="${document.url}" width="100%" height="400" class="border rounded"></iframe>`);
            } else {
                // Для других типов файлов показываем иконку
                const iconClass = getDocumentIconClass(document.extension);
                previewContainer.html(`
                    <div class="file-icon-large">
                        <i class="${iconClass} fa-5x text-primary"></i>
                        <p class="mt-3">${document.extension.toUpperCase()} файл</p>
                        <p class="text-muted">Предпросмотр недоступен</p>
                    </div>
                `);
            }
            
            $('#viewDocumentModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при получении данных документа:', error);
            showMessage('Не удалось загрузить данные документа', 'error');
        }
    });
}

// Вспомогательные функции
function showDocumentPreview(files) {
    const container = $('#documentPreview');
    container.empty();
    
    if (!files || files.length === 0) return;
    
    Array.from(files).forEach((file, index) => {
        const extension = file.name.split('.').pop().toLowerCase();
        const iconClass = getDocumentIconClass(extension);
        
        const preview = $(`
            <div class="position-relative d-inline-block me-2 mb-2">
                <div class="file-icon-wrapper img-thumbnail d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="${iconClass} fa-2x"></i>
                    <span class="file-extension">${extension.toUpperCase()}</span>
                </div>
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeDocumentPreview(this, ${index})">
                    <i class="bi bi-x"></i>
                </button>
                <small class="d-block text-muted text-center mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                    ${file.name}
                </small>
            </div>
        `);
        container.append(preview);
    });
}

function removeDocumentPreview(button, index) {
    const documentInput = document.getElementById('documentInput');
    if (documentInput && documentInput.files) {
        const dt = new DataTransfer();
        Array.from(documentInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        documentInput.files = dt.files;
        $(documentInput).trigger('change');
    }
}

function getDocumentIconClass(extension) {
    const iconMap = {
        'pdf': 'bi bi-file-earmark-pdf',
        'doc': 'bi bi-file-earmark-word',
        'docx': 'bi bi-file-earmark-word',
        'xls': 'bi bi-file-earmark-excel',
        'xlsx': 'bi bi-file-earmark-excel',
        'ppt': 'bi bi-file-earmark-ppt',
        'pptx': 'bi bi-file-earmark-ppt',
        'txt': 'bi bi-file-earmark-text',
        'rtf': 'bi bi-file-earmark-richtext',
        'zip': 'bi bi-file-earmark-zip',
        'rar': 'bi bi-file-earmark-zip'
    };
    
    return iconMap[extension] || 'bi bi-file-earmark';
}

function getDocumentCategoryName(category) {
    const categories = {
        'contract': 'Договор',
        'specification': 'Спецификация',
        'estimate': 'Смета',
        'act': 'Акт',
        'invoice': 'Счет',
        'technical': 'Техническая документация',
        'legal': 'Юридический документ',
        'permits': 'Разрешительная документация',
        'report': 'Отчет',
        'other': 'Другое'
    };
    
    return categories[category] || 'Не указано';
}

function getDocumentStatusName(status) {
    const statuses = {
        'active': 'Актуальный',
        'draft': 'Черновик',
        'archived': 'Архивный',
        'pending': 'На согласовании',
        'approved': 'Утвержден',
        'rejected': 'Отклонен'
    };
    
    return statuses[status] || 'Не указано';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Байт';
    const k = 1024;
    const sizes = ['Байт', 'КБ', 'МБ', 'ГБ'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Функция для отображения сообщений
function showMessage(message, type = 'info') {
    let bgClass = 'bg-info';
    let icon = 'bi-info-circle';
    
    if (type === 'success') {
        bgClass = 'bg-success';
        icon = 'bi-check-circle';
    } else if (type === 'error') {
        bgClass = 'bg-danger';
        icon = 'bi-exclamation-circle';
    } else if (type === 'warning') {
        bgClass = 'bg-warning';
        icon = 'bi-exclamation-triangle';
    }
    
    const toast = $(`
        <div class="toast align-items-center ${bgClass} text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрыть"></button>
            </div>
        </div>
    `);
    
    $('.toast-container').append(toast);
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
