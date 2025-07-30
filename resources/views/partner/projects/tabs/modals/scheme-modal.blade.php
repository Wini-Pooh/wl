<!-- AJAX Модальное окно для загрузки схем (версия 2.0) -->
<div class="modal fade" id="uploadSchemeModal" tabindex="-1" aria-labelledby="uploadSchemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadSchemeModalLabel">
                    <i class="bi bi-cloud-upload me-2"></i>Загрузка схем
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="uploadSchemeForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="schemeProjectId" value="{{ $project->id ?? '' }}">
                    
                    <div class="mb-4">
                        <label for="schemeInput" class="form-label">Выберите файлы схем</label>
                        <input type="file" id="schemeInput" name="files[]" class="form-control" multiple>
                        <div class="form-text">Поддерживаемые форматы: JPG, PNG, PDF, DWG, SVG</div>
                    </div>
                    
                    <div id="schemePreviewContainer" class="mb-4" style="display: none;">
                        <h6 class="mb-3">Выбранные файлы: <span id="selectedSchemesCount">0</span></h6>
                        <div id="schemePreview" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="schemeType" class="form-label">Тип схемы</label>
                                <select class="form-select" id="schemeType" name="scheme_type">
                                    <option value="">Выберите тип</option>
                                    <option value="electrical">Электрическая</option>
                                    <option value="plumbing">Сантехническая</option>
                                    <option value="layout">Планировка</option>
                                    <option value="hvac">Вентиляция и кондиционирование</option>
                                    <option value="heating">Отопление</option>
                                    <option value="lighting">Освещение</option>
                                    <option value="furniture">Расстановка мебели</option>
                                    <option value="structural">Конструктивная</option>
                                    <option value="other">Другое</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="schemeFormat" class="form-label">Формат схемы</label>
                                <select class="form-select" id="schemeFormat" name="format">
                                    <option value="2d">2D</option>
                                    <option value="3d">3D</option>
                                    <option value="blueprint">Чертеж</option>
                                    <option value="sketch">Эскиз</option>
                                    <option value="other">Другое</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="schemeDescription" class="form-label">Описание (необязательно)</label>
                        <textarea class="form-control" id="schemeDescription" name="description" rows="3" placeholder="Добавьте описание схемы"></textarea>
                    </div>
                </form>
                
                <div class="progress mb-3" id="schemeUploadProgress" style="display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                
                <div class="alert alert-danger" id="schemeUploadError" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="uploadSchemesBtn" disabled>
                    <i class="bi bi-cloud-upload me-2"></i>Загрузить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра схемы -->
<div class="modal fade" id="viewSchemeModal" tabindex="-1" aria-labelledby="viewSchemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSchemeModalLabel">Просмотр схемы</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4" id="schemePreviewContent">
                    <!-- Содержимое будет добавлено динамически -->
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 id="viewSchemeTitle" class="card-title mb-3"></h5>
                        
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-primary" id="viewSchemeType"></span>
                            <span class="badge bg-secondary" id="viewSchemeFormat"></span>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-muted">Описание:</h6>
                            <p id="viewSchemeDescription" class="mb-0"></p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Размер файла:</strong> <span id="viewSchemeSize"></span></p>
                                <p class="mb-1"><strong>Тип файла:</strong> <span id="viewSchemeFileType"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Дата загрузки:</strong> <span id="viewSchemeDate"></span></p>
                                <p class="mb-1"><strong>Загрузил:</strong> <span id="viewSchemeUser"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="downloadSchemeBtn" target="_blank">
                    <i class="bi bi-download me-2"></i>Скачать
                </a>
                <button type="button" class="btn btn-danger" id="deleteSchemeBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="confirmDeleteSchemeModal" tabindex="-1" aria-labelledby="confirmDeleteSchemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteSchemeModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить эту схему?</p>
                <input type="hidden" id="schemeToDeleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmSchemeDeleteBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Инициализация модальных окон для схем');
    
    // Функция для отображения превью схем
    function showSchemePreview(files) {
        console.log('Вызов showSchemePreview с', files.length, 'файлами');
        const container = $('#schemePreview');
        
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
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeSchemePreview(this, ${index})">
                                <i class="bi bi-x"></i>
                            </button>
                            <small class="d-block text-muted text-center mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                                ${file.name}
                            </small>
                        </div>
                    `);
                    
                    loadedCount++;
                    $('#selectedSchemesCount').text(loadedCount);
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
                    case 'dwg':
                    case 'dxf':
                        icon = 'bi-file-earmark-ruled';
                        break;
                }
                
                container.append(`
                    <div class="position-relative d-inline-block me-2 mb-2" data-file-index="${index}" data-file-id="${fileId}">
                        <div class="scheme-preview text-center p-2 border rounded">
                            <i class="${icon} fs-1"></i>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeSchemePreview(this, ${index})">
                                <i class="bi bi-x"></i>
                            </button>
                            <small class="d-block text-muted mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                                ${file.name}
                            </small>
                        </div>
                    </div>
                `);
                
                loadedCount++;
                $('#selectedSchemesCount').text(loadedCount);
            }
        });
    }

    // Обработчик выбора файлов
    $('#schemeInput').off('change').one('change', function(e) {
        const files = this.files;
        console.log('Схемы выбраны:', files.length);
        
        if (files && files.length > 0) {
            $('#uploadSchemesBtn').prop('disabled', false);
            $('#schemePreviewContainer').show();
            showSchemePreview(files);
        } else {
            $('#uploadSchemesBtn').prop('disabled', true);
            $('#schemePreviewContainer').hide();
            $('#selectedSchemesCount').text(0);
        }
        
        // Переинициализируем обработчик для следующего выбора файлов
        $(this).off('change').one('change', arguments.callee);
    });
    
    // Обработчик загрузки схем
    $('#uploadSchemesBtn').click(function() {
        uploadSchemes();
    });
    
    // Обработчик удаления схемы
    $('#deleteSchemeBtn').click(function() {
        const schemeId = $(this).data('scheme-id');
        $('#schemeToDeleteId').val(schemeId);
        $('#viewSchemeModal').modal('hide');
        $('#confirmDeleteSchemeModal').modal('show');
    });
    
    // Подтверждение удаления
    $('#confirmSchemeDeleteBtn').click(function() {
        const schemeId = $('#schemeToDeleteId').val();
        if (schemeId) {
            deleteScheme(schemeId);
        }
    });
    
    // Сброс при закрытии модального окна
    $('#uploadSchemeModal').on('hidden.bs.modal', function() {
        const form = document.getElementById('uploadSchemeForm');
        if (form) form.reset();
        
        $('#schemePreview').empty();
        $('#schemePreviewContainer').hide();
        $('#selectedSchemesCount').text(0);
        $('#uploadSchemesBtn').prop('disabled', true);
        $('#schemeUploadProgress').hide();
        $('#schemeUploadError').hide();
    });
});

// Функция загрузки схем через AJAX
function uploadSchemes() {
    const form = $('#uploadSchemeForm')[0];
    const formData = new FormData(form);
    const projectId = $('#schemeProjectId').val();
    
    // Добавляем CSRF токен
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    $('#uploadSchemesBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...');
    $('#schemeUploadProgress').show();
    $('#schemeUploadError').hide();
    
    $.ajax({
        url: `/api/projects/${projectId}/schemes`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    $('#schemeUploadProgress .progress-bar')
                        .css('width', percent + '%')
                        .attr('aria-valuenow', percent)
                        .text(percent + '%');
                }
            });
            return xhr;
        },
        success: function(response) {
            console.log('Схемы успешно загружены', response);
            $('#uploadSchemeModal').modal('hide');
            
            // Обновляем список схем - используем правильный менеджер
            if (window.SchemeManager && typeof window.SchemeManager.loadFiles === 'function') {
                console.log('Обновляем список схем через SchemeManager.loadFiles()');
                window.SchemeManager.loadFiles();
            } else if (window.SchemeManagerFixed && typeof window.SchemeManagerFixed.loadFiles === 'function') {
                console.log('Обновляем список схем через SchemeManagerFixed.loadFiles()');
                window.SchemeManagerFixed.loadFiles();
            } else if (typeof loadSchemes === 'function') {
                console.log('Обновляем список схем через loadSchemes()');
                loadSchemes();
            } else {
                console.warn('Не найден менеджер для обновления списка схем');
            }
            
            // Показываем сообщение
            showMessage('Схемы успешно загружены', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке схем:', error);
            $('#uploadSchemesBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
            
            let errorMessage = 'Произошла ошибка при загрузке схем';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            $('#schemeUploadError').text(errorMessage).show();
        },
        complete: function() {
            $('#uploadSchemesBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
        }
    });
}

// Функция удаления схемы через AJAX
function deleteScheme(schemeId) {
    const projectId = $('#schemeProjectId').val();
    
    $('#confirmSchemeDeleteBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Удаление...');
    
    $.ajax({
        url: `/api/projects/${projectId}/schemes/${schemeId}`,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Схема успешно удалена', response);
            $('#confirmDeleteSchemeModal').modal('hide');
            
            // Обновляем список схем (замените на вашу функцию для обновления списка)
            if (typeof loadSchemes === 'function') {
                loadSchemes();
            }
            
            // Показываем сообщение
            showMessage('Схема успешно удалена', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при удалении схемы:', error);
            
            let errorMessage = 'Произошла ошибка при удалении схемы';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showMessage(errorMessage, 'error');
        },
        complete: function() {
            $('#confirmSchemeDeleteBtn').prop('disabled', false).html('<i class="bi bi-trash me-2"></i>Удалить');
        }
    });
}

// Открытие модального окна просмотра схемы
function viewScheme(schemeId) {
    const projectId = $('#schemeProjectId').val();
    
    $.ajax({
        url: `/api/projects/${projectId}/schemes/${schemeId}`,
        type: 'GET',
        success: function(response) {
            const scheme = response.data;
            
            // Заполняем данные в модальном окне
            $('#viewSchemeTitle').text(scheme.name || 'Схема без названия');
            $('#viewSchemeType').text(getSchemeTypeName(scheme.scheme_type));
            $('#viewSchemeFormat').text(getSchemeFormatName(scheme.format));
            $('#viewSchemeDescription').text(scheme.description || 'Описание отсутствует');
            $('#viewSchemeSize').text(formatFileSize(scheme.size));
            $('#viewSchemeFileType').text(scheme.extension.toUpperCase());
            $('#viewSchemeDate').text(formatDate(scheme.created_at));
            $('#viewSchemeUser').text(scheme.user ? scheme.user.name : 'Система');
            $('#downloadSchemeBtn').attr('href', scheme.download_url);
            $('#deleteSchemeBtn').data('scheme-id', scheme.id);
            
            // Подготавливаем предпросмотр в зависимости от типа файла
            const previewContainer = $('#schemePreviewContent');
            previewContainer.empty();
            
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(scheme.extension.toLowerCase());
            const isPdf = scheme.extension.toLowerCase() === 'pdf';
            
            if (isImage) {
                previewContainer.html(`<img src="${scheme.url}" class="img-fluid rounded" alt="${scheme.name}">`);
            } else if (isPdf) {
                previewContainer.html(`<iframe src="${scheme.url}" width="100%" height="400" class="border rounded"></iframe>`);
            } else {
                // Для других типов файлов показываем иконку
                const iconClass = getSchemeIconClass(scheme.extension);
                previewContainer.html(`
                    <div class="file-icon-large">
                        <i class="${iconClass} fa-5x text-primary"></i>
                        <p class="mt-3">${scheme.extension.toUpperCase()} файл</p>
                        <p class="text-muted">Предпросмотр недоступен</p>
                    </div>
                `);
            }
            
            $('#viewSchemeModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при получении данных схемы:', error);
            showMessage('Не удалось загрузить данные схемы', 'error');
        }
    });
}

// Вспомогательные функции
function showSchemePreview(files) {
    const container = $('#schemePreview');
    container.empty();
    
    if (!files || files.length === 0) return;
    
    Array.from(files).forEach((file, index) => {
        const extension = file.name.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension);
        const iconClass = getSchemeIconClass(extension);
        
        if (isImage) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`
                    <div class="position-relative d-inline-block me-2 mb-2">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeSchemePreview(this, ${index})">
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
                        <span class="file-extension">${extension.toUpperCase()}</span>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeSchemePreview(this, ${index})">
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

function removeSchemePreview(button, index) {
    const schemeInput = document.getElementById('schemeInput');
    if (schemeInput && schemeInput.files) {
        const dt = new DataTransfer();
        Array.from(schemeInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        schemeInput.files = dt.files;
        $(schemeInput).trigger('change');
    }
}

function getSchemeIconClass(extension) {
    const iconMap = {
        'pdf': 'bi bi-file-earmark-pdf',
        'dwg': 'bi bi-file-earmark-richtext',
        'svg': 'bi bi-file-earmark-image',
    };
    
    return iconMap[extension] || 'bi bi-file-earmark';
}

function getSchemeTypeName(type) {
    const types = {
        'electrical': 'Электрическая',
        'plumbing': 'Сантехническая',
        'layout': 'Планировка',
        'hvac': 'Вентиляция и кондиционирование',
        'heating': 'Отопление',
        'lighting': 'Освещение',
        'furniture': 'Расстановка мебели',
        'structural': 'Конструктивная',
        'other': 'Другое'
    };
    
    return types[type] || 'Не указано';
}

function getSchemeFormatName(format) {
    const formats = {
        '2d': '2D',
        '3d': '3D',
        'blueprint': 'Чертеж',
        'sketch': 'Эскиз',
        'other': 'Другое'
    };
    
    return formats[format] || 'Не указано';
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
