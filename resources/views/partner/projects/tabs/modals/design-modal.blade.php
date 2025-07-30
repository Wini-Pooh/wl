<!-- AJAX Модальное окно для загрузки дизайн-файлов (версия 2.0) -->
<div class="modal fade" id="uploadDesignModal" tabindex="-1" aria-labelledby="uploadDesignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDesignModalLabel">
                    <i class="bi bi-cloud-upload me-2"></i>Загрузка дизайн-файлов
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDesignForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="designProjectId" value="{{ $project->id ?? '' }}">
                    
                    <div class="mb-4">
                        <label for="designInput" class="form-label">Выберите файлы дизайна</label>
                        <input type="file" id="designInput" name="files[]" class="form-control" multiple>
                        <div class="form-text">Поддерживаемые форматы: JPG, PNG, PDF, PSD, AI, INDD, DWG, SKP, 3DS, MAX</div>
                    </div>
                    
                    <div id="designPreviewContainer" class="mb-4" style="display: none;">
                        <h6 class="mb-3">Выбранные файлы: <span id="selectedDesignCount">0</span></h6>
                        <div id="designPreview" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="designType" class="form-label">Тип дизайна</label>
                                <select class="form-select" id="designType" name="design_type">
                                    <option value="">Выберите тип</option>
                                    <option value="3d">3D визуализация</option>
                                    <option value="layout">Планировка</option>
                                    <option value="sketch">Эскиз</option>
                                    <option value="render">Рендер</option>
                                    <option value="draft">Черновик</option>
                                    <option value="concept">Концепт</option>
                                    <option value="detail">Детализация</option>
                                    <option value="material">Материалы</option>
                                    <option value="other">Другое</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="designRoom" class="form-label">Помещение</label>
                                <select class="form-select" id="designRoom" name="room">
                                    <option value="">Не выбрано</option>
                                    <option value="kitchen">Кухня</option>
                                    <option value="living_room">Гостиная</option>
                                    <option value="bedroom">Спальня</option>
                                    <option value="bathroom">Ванная</option>
                                    <option value="toilet">Туалет</option>
                                    <option value="hallway">Прихожая</option>
                                    <option value="all">Все помещения</option>
                                    <option value="other">Другое</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="designDescription" class="form-label">Описание (необязательно)</label>
                        <textarea class="form-control" id="designDescription" name="description" rows="3" placeholder="Добавьте описание файлов дизайна"></textarea>
                    </div>
                </form>
                
                <div class="progress mb-3" id="designUploadProgress" style="display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                
                <div class="alert alert-danger" id="designUploadError" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="uploadDesignsBtn" disabled>
                    <i class="bi bi-cloud-upload me-2"></i>Загрузить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра дизайн-файла -->
<div class="modal fade" id="viewDesignModal" tabindex="-1" aria-labelledby="viewDesignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDesignModalLabel">Просмотр дизайн-файла</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4" id="designPreviewContent">
                    <!-- Содержимое будет добавлено динамически -->
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 id="viewDesignTitle" class="card-title mb-3"></h5>
                        
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-primary" id="viewDesignType"></span>
                            <span class="badge bg-secondary" id="viewDesignRoom"></span>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-muted">Описание:</h6>
                            <p id="viewDesignDescription" class="mb-0"></p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Размер файла:</strong> <span id="viewDesignSize"></span></p>
                                <p class="mb-1"><strong>Тип файла:</strong> <span id="viewDesignFormat"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Дата загрузки:</strong> <span id="viewDesignDate"></span></p>
                                <p class="mb-1"><strong>Загрузил:</strong> <span id="viewDesignUser"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="downloadDesignBtn" target="_blank">
                    <i class="bi bi-download me-2"></i>Скачать
                </a>
                <button type="button" class="btn btn-danger" id="deleteDesignBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="confirmDeleteDesignModal" tabindex="-1" aria-labelledby="confirmDeleteDesignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteDesignModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этот файл дизайна?</p>
                <input type="hidden" id="designToDeleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmDesignDeleteBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Инициализация модальных окон для дизайна');
    
    // Функция для отображения превью дизайн-файлов
    function showDesignPreview(files) {
        console.log('Вызов showDesignPreview с', files.length, 'файлами');
        const container = $('#designPreview');
        
        // Очищаем контейнер
        container.html('');
        
        if (!files || files.length === 0) return;
        
        // Для отслеживания уже обработанных файлов
        const alreadyProcessed = new Set();
        let loadedCount = 0;
        
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
            
            // Для изображений создаем превью
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Проверяем, не был ли уже добавлен этот файл
                    if (container.find(`[data-file-id="${fileId}"]`).length > 0) {
                        console.log('Превью уже существует, пропускаем:', file.name);
                        return;
                    }
                    
                    container.append(`
                        <div class="position-relative d-inline-block me-2 mb-2" data-file-index="${index}" data-file-id="${fileId}">
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeDesignPreview(this, ${index})">
                                <i class="bi bi-x"></i>
                            </button>
                            <small class="d-block text-muted text-center mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                                ${file.name}
                            </small>
                        </div>
                    `);
                    
                    loadedCount++;
                    $('#selectedDesignCount').text(loadedCount);
                };
                reader.readAsDataURL(file);
            } else {
                // Для других файлов показываем иконку
                const extension = file.name.split('.').pop().toLowerCase();
                let icon = 'bi-file';
                
                switch (extension) {
                    case 'pdf':
                        icon = 'bi-file-pdf';
                        break;
                    case 'psd':
                    case 'ai':
                    case 'xd':
                        icon = 'bi-file-earmark-richtext';
                        break;
                    case 'dwg':
                    case 'dxf':
                        icon = 'bi-file-earmark-ruled';
                        break;
                }
                
                container.append(`
                    <div class="position-relative d-inline-block me-2 mb-2" data-file-index="${index}" data-file-id="${fileId}">
                        <div class="design-preview text-center p-2 border rounded">
                            <i class="${icon} fs-1"></i>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeDesignPreview(this, ${index})">
                                <i class="bi bi-x"></i>
                            </button>
                            <small class="d-block text-muted mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                                ${file.name}
                            </small>
                        </div>
                    </div>
                `);
                
                loadedCount++;
                $('#selectedDesignCount').text(loadedCount);
            }
        });
    }

    // Обработчик выбора файлов
    $('#designInput').off('change').one('change', function(e) {
        const files = this.files;
        console.log('Дизайн-файлы выбраны:', files.length);
        
        if (files && files.length > 0) {
            $('#uploadDesignsBtn').prop('disabled', false);
            $('#designPreviewContainer').show();
            showDesignPreview(files);
        } else {
            $('#uploadDesignsBtn').prop('disabled', true);
            $('#designPreviewContainer').hide();
            $('#selectedDesignCount').text(0);
        }
        
        // Переинициализируем обработчик для следующего выбора файлов
        $(this).off('change').one('change', arguments.callee);
    });
    
    // Обработчик загрузки дизайн-файлов
    $('#uploadDesignsBtn').click(function() {
        uploadDesignFiles();
    });
    
    // Обработчик удаления дизайн-файла
    $('#deleteDesignBtn').click(function() {
        const designId = $(this).data('design-id');
        $('#designToDeleteId').val(designId);
        $('#viewDesignModal').modal('hide');
        $('#confirmDeleteDesignModal').modal('show');
    });
    
    // Подтверждение удаления
    $('#confirmDesignDeleteBtn').click(function() {
        const designId = $('#designToDeleteId').val();
        if (designId) {
            deleteDesignFile(designId);
        }
    });
    
    // Сброс при закрытии модального окна
    $('#uploadDesignModal').on('hidden.bs.modal', function() {
        const form = document.getElementById('uploadDesignForm');
        if (form) form.reset();
        
        $('#designPreview').empty();
        $('#designPreviewContainer').hide();
        $('#selectedDesignCount').text(0);
        $('#uploadDesignsBtn').prop('disabled', true);
        $('#designUploadProgress').hide();
        $('#designUploadError').hide();
    });
});

// Функция загрузки дизайн-файлов через AJAX
function uploadDesignFiles() {
    const form = $('#uploadDesignForm')[0];
    const formData = new FormData(form);
    const projectId = $('#designProjectId').val();
    
    // Добавляем CSRF токен
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    $('#uploadDesignsBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...');
    $('#designUploadProgress').show();
    $('#designUploadError').hide();
    
    $.ajax({
        url: `/api/projects/${projectId}/design`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    $('#designUploadProgress .progress-bar')
                        .css('width', percent + '%')
                        .attr('aria-valuenow', percent)
                        .text(percent + '%');
                }
            });
            return xhr;
        },
        success: function(response) {
            console.log('Дизайн-файлы успешно загружены', response);
            $('#uploadDesignModal').modal('hide');
            
            // Обновляем список дизайн-файлов
            if (window.DesignManagerFixed && typeof window.DesignManagerFixed.loadFiles === 'function') {
                window.DesignManagerFixed.loadFiles();
            }
            
            // Показываем сообщение
            showMessage('Дизайн-файлы успешно загружены', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке дизайн-файлов:', error);
            $('#uploadDesignsBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
            
            let errorMessage = 'Произошла ошибка при загрузке дизайн-файлов';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            $('#designUploadError').text(errorMessage).show();
        },
        complete: function() {
            $('#uploadDesignsBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
        }
    });
}

// Функция удаления дизайн-файла через AJAX
function deleteDesignFile(designId) {
    const projectId = $('#designProjectId').val();
    
    $('#confirmDesignDeleteBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Удаление...');
    
    $.ajax({
        url: `/api/projects/${projectId}/design/${designId}`,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Дизайн-файл успешно удален', response);
            $('#confirmDeleteDesignModal').modal('hide');
            
            // Обновляем список дизайн-файлов
            if (window.DesignManagerFixed && typeof window.DesignManagerFixed.loadFiles === 'function') {
                window.DesignManagerFixed.loadFiles();
            }
            
            // Показываем сообщение
            showMessage('Дизайн-файл успешно удален', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при удалении дизайн-файла:', error);
            
            let errorMessage = 'Произошла ошибка при удалении дизайн-файла';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showMessage(errorMessage, 'error');
        },
        complete: function() {
            $('#confirmDesignDeleteBtn').prop('disabled', false).html('<i class="bi bi-trash me-2"></i>Удалить');
        }
    });
}

// Открытие модального окна просмотра дизайн-файла
function viewDesign(designId) {
    const projectId = $('#designProjectId').val();
    
    $.ajax({
        url: `/api/projects/${projectId}/design/${designId}`,
        type: 'GET',
        success: function(response) {
            const design = response.data;
            
            // Заполняем данные в модальном окне
            $('#viewDesignTitle').text(design.name || 'Файл дизайна без названия');
            $('#viewDesignType').text(getDesignTypeName(design.design_type));
            $('#viewDesignRoom').text(getDesignRoomName(design.room));
            $('#viewDesignDescription').text(design.description || 'Описание отсутствует');
            $('#viewDesignSize').text(formatFileSize(design.size));
            $('#viewDesignFormat').text(design.extension.toUpperCase());
            $('#viewDesignDate').text(formatDate(design.created_at));
            $('#viewDesignUser').text(design.user ? design.user.name : 'Система');
            $('#downloadDesignBtn').attr('href', design.download_url);
            $('#deleteDesignBtn').data('design-id', design.id);
            
            // Подготавливаем предпросмотр в зависимости от типа файла
            const previewContainer = $('#designPreviewContent');
            previewContainer.empty();
            
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(design.extension.toLowerCase());
            const isPdf = design.extension.toLowerCase() === 'pdf';
            
            if (isImage) {
                previewContainer.html(`<img src="${design.url}" class="img-fluid rounded" alt="${design.name}">`);
            } else if (isPdf) {
                previewContainer.html(`<iframe src="${design.url}" width="100%" height="400" class="border rounded"></iframe>`);
            } else {
                // Для других типов файлов показываем иконку
                const iconClass = getFileIconClass(design.extension);
                previewContainer.html(`
                    <div class="file-icon-large">
                        <i class="${iconClass} fa-5x text-primary"></i>
                        <p class="mt-3">${design.extension.toUpperCase()} файл</p>
                        <p class="text-muted">Предпросмотр недоступен</p>
                    </div>
                `);
            }
            
            $('#viewDesignModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при получении данных дизайн-файла:', error);
            showMessage('Не удалось загрузить данные дизайн-файла', 'error');
        }
    });
}

// Вспомогательные функции
function showDesignPreview(files) {
    const container = $('#designPreview');
    container.empty();
    
    if (!files || files.length === 0) return;
    
    Array.from(files).forEach((file, index) => {
        const extension = file.name.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
        const iconClass = getFileIconClass(extension);
        
        if (isImage) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`
                    <div class="position-relative d-inline-block me-2 mb-2">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeDesignPreview(this, ${index})">
                            <i class="bi bi-x"></i>
                        </button>
                        <small class="d-block text-muted text-center mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                            ${file.name}
                        </small>
                    </div>
                `);
                container.append(preview);
            };
            reader.readAsDataURL(file);
        } else {
            // Для не-изображений показываем иконку
            const preview = $(`
                <div class="position-relative d-inline-block me-2 mb-2">
                    <div class="file-icon-wrapper img-thumbnail d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="${iconClass} fa-2x"></i>
                        <span class="file-extension">${extension}</span>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeDesignPreview(this, ${index})">
                        <i class="bi bi-x"></i>
                    </button>
                    <small class="d-block text-muted text-center mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                        ${file.name}
                    </small>
                </div>
            `);
            container.append(preview);
        }
    });
}

function removeDesignPreview(button, index) {
    const designInput = document.getElementById('designInput');
    if (designInput && designInput.files) {
        const dt = new DataTransfer();
        Array.from(designInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        designInput.files = dt.files;
        $(designInput).trigger('change');
    }
}

function getFileIconClass(extension) {
    const iconMap = {
        'pdf': 'bi bi-file-earmark-pdf',
        'doc': 'bi bi-file-earmark-word',
        'docx': 'bi bi-file-earmark-word',
        'xls': 'bi bi-file-earmark-excel',
        'xlsx': 'bi bi-file-earmark-excel',
        'ppt': 'bi bi-file-earmark-ppt',
        'pptx': 'bi bi-file-earmark-ppt',
        'txt': 'bi bi-file-earmark-text',
        'zip': 'bi bi-file-earmark-zip',
        'rar': 'bi bi-file-earmark-zip',
        'psd': 'bi bi-file-earmark-image',
        'ai': 'bi bi-file-earmark-image',
        'indd': 'bi bi-file-earmark-image',
        'dwg': 'bi bi-file-earmark-richtext',
        'skp': 'bi bi-cube',
        '3ds': 'bi bi-box',
        'max': 'bi bi-box',
    };
    
    return iconMap[extension] || 'bi bi-file-earmark';
}

function getDesignTypeName(type) {
    const types = {
        '3d': '3D визуализация',
        'layout': 'Планировка',
        'sketch': 'Эскиз',
        'render': 'Рендер',
        'draft': 'Черновик',
        'concept': 'Концепт',
        'detail': 'Детализация',
        'material': 'Материалы',
        'other': 'Другое'
    };
    
    return types[type] || 'Не указано';
}

function getDesignRoomName(room) {
    const rooms = {
        'kitchen': 'Кухня',
        'living_room': 'Гостиная',
        'bedroom': 'Спальня',
        'bathroom': 'Ванная',
        'toilet': 'Туалет',
        'hallway': 'Прихожая',
        'all': 'Все помещения',
        'other': 'Другое'
    };
    
    return rooms[room] || 'Не указано';
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

// Дополнительная проверка и восстановление projectId при загрузке модального окна
$(document).ready(function() {
    console.log('=== ПРОВЕРКА PROJECT ID В DESIGN MODAL ===');
    
    // Проверяем projectId в форме
    const formProjectId = $('#designProjectId').val();
    console.log('Project ID в форме дизайна:', formProjectId);
    
    // Проверяем глобальный projectId
    const globalProjectId = window.projectId;
    console.log('Глобальный Project ID:', globalProjectId);
    
    // Проверяем projectId в modalManager
    const modalManagerProjectId = window.modalManager ? window.modalManager.projectId : null;
    console.log('Project ID в modalManager:', modalManagerProjectId);
    
    // Если в форме нет projectId, попытаемся восстановить его
    if (!formProjectId || formProjectId === '') {
        console.warn('Project ID не установлен в форме дизайна, пытаемся восстановить...');
        
        // Попробуем взять из различных источников
        let recoveredProjectId = null;
        
        if (globalProjectId && globalProjectId !== 'null') {
            recoveredProjectId = globalProjectId;
            console.log('Восстановлен Project ID из window.projectId:', recoveredProjectId);
        } else if (modalManagerProjectId && modalManagerProjectId !== 'null') {
            recoveredProjectId = modalManagerProjectId;
            console.log('Восстановлен Project ID из modalManager:', recoveredProjectId);
        }
        
        // Устанавливаем восстановленный projectId в форму
        if (recoveredProjectId) {
            $('#designProjectId').val(recoveredProjectId);
            console.log('Project ID установлен в форму дизайна:', recoveredProjectId);
            
            // Также обновляем DesignManagerFixed если он существует
            if (window.DesignManagerFixed) {
                window.DesignManagerFixed.projectId = recoveredProjectId;
                console.log('Project ID обновлен в DesignManagerFixed:', recoveredProjectId);
            }
        } else {
            console.error('НЕ УДАЛОСЬ ВОССТАНОВИТЬ PROJECT ID ДЛЯ ДИЗАЙНА! Проверьте конфигурацию.');
        }
    } else {
        console.log('Project ID корректно установлен в форме дизайна:', formProjectId);
        
        // Убедимся, что DesignManagerFixed также имеет правильный projectId
        if (window.DesignManagerFixed && (!window.DesignManagerFixed.projectId || window.DesignManagerFixed.projectId === 'null')) {
            window.DesignManagerFixed.projectId = formProjectId;
            console.log('Project ID синхронизирован с DesignManagerFixed:', formProjectId);
        }
    }
});
</script>
