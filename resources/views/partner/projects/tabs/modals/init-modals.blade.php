<!-- Инициализация AJAX модальных окон для всех вкладок проекта -->
<!-- Версия 2.0 - Полностью асинхронные модальные окна -->

<!-- Базовый контейнер для уведомлений -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<!-- Модальные окна подгружаются динамически через AJAX -->
<div id="modalContainer"></div>

<!-- Общие стили для модальных окон -->
<style>
.modal-dialog {
    max-width: 800px;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.progress {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    background-color: #0d6efd;
    transition: width 0.3s ease;
}

/* Анимация для загрузки модального окна */
.modal-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}

.modal-loading .spinner-border {
    width: 3rem;
    height: 3rem;
    color: #0d6efd;
}

/* Улучшенные стили для предпросмотра файлов */
.file-preview {
    position: relative;
    display: inline-block;
    margin: 0.25rem;
    border-radius: 4px;
    overflow: hidden;
}

.file-preview img {
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.file-preview .remove-file {
    position: absolute;
    top: 2px;
    right: 2px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 20px;
    cursor: pointer;
    color: #dc3545;
}

.file-preview .remove-file:hover {
    background: rgba(255, 255, 255, 0.9);
    color: #b02a37;
}

.file-meta {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.file-icon-wrapper {
    background-color: #f8f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
}

.file-extension {
    font-size: 10px;
    margin-top: 5px;
    background-color: #e9ecef;
    padding: 2px 4px;
    border-radius: 3px;
}

.file-icon-large {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background-color: #f8f9fa;
    border-radius: 8px;
}

/* Стили для тостов */
.toast {
    transition: all 0.3s ease;
    min-width: 250px;
}

.toast-body {
    padding: 0.75rem 1rem;
    font-weight: 500;
}

.toast-container {
    z-index: 1080;
}
</style>

<!-- Общий JavaScript для AJAX модальных окон -->
<script>
/**
 * Настройка jQuery AJAX для работы с CSRF-токеном Laravel
 */
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    xhrFields: {
        withCredentials: true
    }
});

/**
 * Модуль управления AJAX модальными окнами
 * Версия: 2.1
 */
$(function() {
    // Предотвращаем дублирование инициализации
    if (window.modalManagerInitialized) {
        console.warn('Менеджер модальных окон уже инициализирован');
        return;
    }
    
    // Устанавливаем глобальный projectId для использования в JavaScript файлах
    window.projectId = {{ $project->id ?? 'null' }};
    
    // Вспомогательные функции для управления модальными окнами
    window.modalManager = {
        activeModal: null,
        projectId: {{ $project->id ?? 'null' }},
        initialized: true,
        
        // Универсальная функция для восстановления projectId во всех менеджерах
        ensureProjectId: function() {
            console.log('=== ПРОВЕРКА И ВОССТАНОВЛЕНИЕ PROJECT ID ===');
            
            // Проверяем текущий projectId
            if (!this.projectId || this.projectId === 'null') {
                console.warn('ProjectId не установлен в modalManager, пытаемся восстановить...');
                
                // Попробуем взять из window.projectId
                if (window.projectId && window.projectId !== 'null') {
                    this.projectId = window.projectId;
                    console.log('ProjectId восстановлен из window.projectId:', this.projectId);
                } else {
                    console.error('НЕ УДАЛОСЬ ВОССТАНОВИТЬ PROJECT ID!');
                    return false;
                }
            }
            
            // Убеждаемся, что все менеджеры файлов имеют правильный projectId
            const managers = [
                { name: 'PhotoManagerFixed', obj: window.PhotoManagerFixed },
                { name: 'DesignManagerFixed', obj: window.DesignManagerFixed },
                { name: 'DocumentManagerFixed', obj: window.DocumentManagerFixed },
                { name: 'SchemeManagerFixed', obj: window.SchemeManagerFixed }
            ];
            
            managers.forEach(manager => {
                if (manager.obj) {
                    if (!manager.obj.projectId || manager.obj.projectId === 'null') {
                        manager.obj.projectId = this.projectId;
                        console.log(`${manager.name}.projectId установлен в:`, this.projectId);
                    } else {
                        console.log(`${manager.name}.projectId уже корректен:`, manager.obj.projectId);
                    }
                }
            });
            
            return true;
        },
        
        // Загрузка модального окна через AJAX
        loadModal: function(type, params = {}) {
            // Убеждаемся, что projectId корректно установлен
            if (!this.ensureProjectId()) {
                console.error('Не удалось загрузить модальное окно: projectId недоступен');
                this.showToast('Ошибка', 'ID проекта недоступен', 'danger');
                return;
            }
            
            // Проверка на активное модальное окно
            if (this.activeModal) {
                try {
                    this.activeModal.hide();
                    this.activeModal.dispose();
                    this.activeModal = null;
                } catch (e) {
                    console.warn('Ошибка при попытке скрыть активное модальное окно:', e);
                }
            }
            
            // Удаляем все существующие модальные окна перед созданием нового
            $('.modal').each(function() {
                try {
                    const modalInstance = bootstrap.Modal.getInstance(this);
                    if (modalInstance) {
                        modalInstance.dispose();
                    }
                } catch (e) {
                    console.warn('Ошибка при удалении модального окна:', e);
                }
            });
            
            const container = $('#modalContainer');
            container.empty(); // Очищаем контейнер перед добавлением нового модального окна
            
            // Используем роут партнёра для лучшей поддержки аутентификации
            const url = `/partner/projects/${this.projectId}/modals/${type}`;
            
            // Добавляем индикатор загрузки
            container.html(`
                <div class="modal fade" id="loading-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body modal-loading">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Показываем модальное окно загрузки
            const loadingModal = new bootstrap.Modal(document.getElementById('loading-modal'));
            loadingModal.show();
            
            // Выполняем запрос к серверу
            $.ajax({
                url: url,
                type: 'GET',
                data: params,
                success: function(response) {
                    // Скрываем индикатор загрузки
                    loadingModal.hide();
                    
                    // Проверяем формат ответа
                    if (response && response.html) {
                        // JSON-ответ с HTML-контентом
                        container.html(response.html);
                    } else {
                        // Прямой HTML-ответ (устаревший формат)
                        container.html(response);
                    }
                    
                    // Показываем новое модальное окно
                    const modalId = container.find('.modal').attr('id');
                    if (modalId) {
                        modalManager.activeModal = new bootstrap.Modal(document.getElementById(modalId));
                        modalManager.activeModal.show();
                        
                        // Инициализируем обработчики событий
                        modalManager.initHandlers(type);
                    } else {
                        console.error('Не удалось найти ID модального окна в ответе');
                        modalManager.showToast('Ошибка', 'Неверный формат ответа сервера', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    // Скрываем индикатор загрузки
                    loadingModal.hide();
                    
                    // Определяем текст сообщения об ошибке
                    let errorMessage = 'Не удалось загрузить модальное окно.';
                    
                    if (xhr.status === 401) {
                        errorMessage = 'Ошибка аутентификации. Пожалуйста, войдите в систему и попробуйте снова.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'Недостаточно прав для выполнения операции.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Запрашиваемый ресурс не найден.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.';
                    }
                    
                    // Показываем сообщение об ошибке
                    modalManager.showToast('Ошибка загрузки', errorMessage, 'danger');
                    console.error('Ошибка загрузки модального окна:', { status: xhr.status, error: error, responseText: xhr.responseText });
                    
                    // Если ошибка аутентификации, предложим обновить страницу
                    if (xhr.status === 401) {
                        if (confirm('Сессия истекла или вы не авторизованы. Обновить страницу?')) {
                            window.location.reload();
                        }
                    }
                }
            });
        },
        
        // Инициализация обработчиков событий в зависимости от типа модального окна
        initHandlers: function(type) {
            console.log(`Инициализация обработчиков для модального окна типа: ${type}`);
            
            // Убеждаемся, что projectId корректно установлен
            this.ensureProjectId();
            
            switch(type) {
                case 'photo':
                    this.initPhotoHandlers();
                    break;
                case 'document':
                    this.initDocumentHandlers();
                    break;
                case 'design':
                    this.initDesignHandlers();
                    break;
                case 'scheme':
                    this.initSchemeHandlers();
                    break;
                case 'event-add':
                case 'event-edit':
                    this.initEventHandlers();
                    break;
                case 'stage-add':
                case 'stage-edit':
                    this.initStageHandlers();
                    break;
                // Другие типы модальных окон
            }
        },
        
        // Обработчики для модального окна фотографий
        initPhotoHandlers: function() {
            console.log('Инициализация обработчиков фото - логика в photo-modal.blade.php');
            
            // Проверяем наличие формы фотографий
            const modal = $('#uploadPhotoModal');
            const form = $('#uploadPhotoForm');
            
            if (!form.length) {
                console.warn('Форма загрузки фотографий не найдена');
                return;
            }
            
            // Убеждаемся, что форма имеет правильный project_id
            const projectIdInput = form.find('input[name="project_id"]');
            if (projectIdInput.length && this.projectId) {
                projectIdInput.val(this.projectId);
                console.log('Project ID установлен в форме фотографий:', this.projectId);
            }
            
            // Убеждаемся, что PhotoManagerFixed знает о projectId
            if (window.PhotoManagerFixed) {
                window.PhotoManagerFixed.projectId = this.projectId;
                console.log('PhotoManagerFixed.projectId синхронизирован:', this.projectId);
            }
        },
        
        // Обработчики для модального окна документов
        initDocumentHandlers: function() {
            const modal = $('#uploadDocumentModal');
            const form = $('#uploadDocumentForm');
            const previewContainer = $('#documentPreviewContainer');
            const previewElement = $('#documentPreview');
            const countElement = $('#selectedDocumentsCount');
            const progressBar = $('#documentUploadProgress');
            
            // Обработчик выбора файлов
            $('#documentInput').on('change', function() {
                const files = this.files;
                previewElement.html('');
                
                if (files.length > 0) {
                    previewContainer.show();
                    countElement.text(files.length);
                    
                    // Генерация предпросмотра
                    Array.from(files).forEach(function(file, index) {
                        const extension = file.name.split('.').pop().toLowerCase();
                        let icon;
                        
                        // Определяем иконку в зависимости от типа файла
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
                            case 'ppt':
                            case 'pptx':
                                icon = 'bi-file-ppt';
                                break;
                            default:
                                icon = 'bi-file-earmark';
                        }
                        
                        previewElement.append(`
                            <div class="file-preview" data-index="${index}">
                                <div class="p-3 text-center">
                                    <i class="bi ${icon} fs-1"></i>
                                </div>
                                <span class="remove-file" data-index="${index}">&times;</span>
                                <div class="file-meta">${file.name}</div>
                            </div>
                        `);
                    });
                } else {
                    previewContainer.hide();
                }
            });
            
            // Обработчик удаления файла из предпросмотра
            previewElement.on('click', '.remove-file', function() {
                const index = $(this).data('index');
                $(`.file-preview[data-index="${index}"]`).remove();
                
                // Обновляем счетчик
                const count = previewElement.find('.file-preview').length;
                countElement.text(count);
                
                if (count === 0) {
                    previewContainer.hide();
                }
            });
            
            // Обработчик отправки формы
            form.on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = form.find('button[type="submit"]');
                
                // Деактивация кнопки отправки
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...');
                
                // Показываем прогресс-бар
                progressBar.parent().show();
                progressBar.css('width', '0%');
                
                // Добавляем CSRF-токен
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                // Отправка данных через AJAX
                $.ajax({
                    url: '/api/projects/' + modalManager.projectId + '/documents',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhrFields: {
                        withCredentials: true
                    },
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        
                        // Отслеживание прогресса загрузки
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBar.css('width', percent + '%');
                                progressBar.attr('aria-valuenow', percent);
                            }
                        });
                        
                        return xhr;
                    },
                    success: function(response) {
                        // Закрываем модальное окно
                        modalManager.activeModal.hide();
                        
                        // Показываем уведомление об успешной загрузке
                        modalManager.showToast('Успешно', 'Документы успешно загружены', 'success');
                        
                        // Обновляем список файлов
                        if (window.fileManager) {
                            window.fileManager.loadFiles('documents');
                        }
                    },
                    error: function(xhr) {
                        // Активация кнопки отправки
                        submitBtn.prop('disabled', false).html('Загрузить');
                        
                        // Скрываем прогресс-бар
                        progressBar.parent().hide();
                        
                        // Показываем сообщение об ошибке
                        try {
                            const response = JSON.parse(xhr.responseText);
                            modalManager.showToast('Ошибка загрузки', response.message || 'Произошла ошибка при загрузке файлов', 'danger');
                        } catch (e) {
                            modalManager.showToast('Ошибка загрузки', 'Произошла ошибка при загрузке файлов', 'danger');
                        }
                        
                        console.error('Ошибка загрузки файлов:', xhr);
                    }
                });
            });
        },
        
        // Обработчики для модального окна файлов дизайна
        initDesignHandlers: function() {
            const modal = $('#uploadDesignModal');
            const form = $('#uploadDesignForm');
            const previewContainer = $('#designPreviewContainer');
            const previewElement = $('#designPreview');
            const countElement = $('#selectedDesignsCount');
            const progressBar = $('#designUploadProgress');
            
            // Обработчик выбора файлов
            $('#designInput').on('change', function() {
                const files = this.files;
                previewElement.html('');
                
                if (files.length > 0) {
                    previewContainer.show();
                    countElement.text(files.length);
                    
                    // Генерация предпросмотра
                    Array.from(files).forEach(function(file, index) {
                        const extension = file.name.split('.').pop().toLowerCase();
                        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
                        
                        if (isImage) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewElement.append(`
                                    <div class="file-preview" data-index="${index}">
                                        <img src="${e.target.result}" class="img-thumbnail">
                                        <span class="remove-file" data-index="${index}">&times;</span>
                                        <div class="file-meta">${file.name}</div>
                                    </div>
                                `);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            let icon;
                            
                            // Определяем иконку в зависимости от типа файла
                            switch (extension) {
                                case 'pdf':
                                    icon = 'bi-file-pdf';
                                    break;
                                case 'dwg':
                                case 'dxf':
                                    icon = 'bi-file-earmark-richtext';
                                    break;
                                case 'psd':
                                    icon = 'bi-file-earmark-image';
                                    break;
                                default:
                                    icon = 'bi-file-earmark';
                            }
                            
                            previewElement.append(`
                                <div class="file-preview" data-index="${index}">
                                    <div class="p-3 text-center">
                                        <i class="bi ${icon} fs-1"></i>
                                    </div>
                                    <span class="remove-file" data-index="${index}">&times;</span>
                                    <div class="file-meta">${file.name}</div>
                                </div>
                            `);
                        }
                    });
                } else {
                    previewContainer.hide();
                }
            });
            
            // Обработчик удаления файла из предпросмотра
            previewElement.on('click', '.remove-file', function() {
                const index = $(this).data('index');
                $(`.file-preview[data-index="${index}"]`).remove();
                
                // Обновляем счетчик
                const count = previewElement.find('.file-preview').length;
                countElement.text(count);
                
                if (count === 0) {
                    previewContainer.hide();
                }
            });
            
            // Обработчик отправки формы
            form.on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = form.find('button[type="submit"]');
                
                // Деактивация кнопки отправки
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...');
                
                // Показываем прогресс-бар
                progressBar.parent().show();
                progressBar.css('width', '0%');
                
                // Добавляем CSRF-токен
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                // Отправка данных через AJAX
                $.ajax({
                    url: '/api/projects/' + modalManager.projectId + '/design',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhrFields: {
                        withCredentials: true
                    },
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        
                        // Отслеживание прогресса загрузки
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBar.css('width', percent + '%');
                                progressBar.attr('aria-valuenow', percent);
                            }
                        });
                        
                        return xhr;
                    },
                    success: function(response) {
                        // Закрываем модальное окно
                        modalManager.activeModal.hide();
                        
                        // Показываем уведомление об успешной загрузке
                        modalManager.showToast('Успешно', 'Файлы дизайна успешно загружены', 'success');
                        
                        // Обновляем список файлов
                        if (window.fileManager) {
                            window.fileManager.loadFiles('design');
                        }
                    },
                    error: function(xhr) {
                        // Активация кнопки отправки
                        submitBtn.prop('disabled', false).html('Загрузить');
                        
                        // Скрываем прогресс-бар
                        progressBar.parent().hide();
                        
                        // Показываем сообщение об ошибке
                        try {
                            const response = JSON.parse(xhr.responseText);
                            modalManager.showToast('Ошибка загрузки', response.message || 'Произошла ошибка при загрузке файлов', 'danger');
                        } catch (e) {
                            modalManager.showToast('Ошибка загрузки', 'Произошла ошибка при загрузке файлов', 'danger');
                        }
                        
                        console.error('Ошибка загрузки файлов:', xhr);
                    }
                });
            });
        },
        
        // Обработчики для модального окна схем
        initSchemeHandlers: function() {
            const modal = $('#uploadSchemeModal');
            const form = $('#uploadSchemeForm');
            const previewContainer = $('#schemePreviewContainer');
            const previewElement = $('#schemePreview');
            const countElement = $('#selectedSchemesCount');
            const progressBar = $('#schemeUploadProgress');
            
            // Обработчик выбора файлов
            $('#schemeInput').on('change', function() {
                const files = this.files;
                previewElement.html('');
                
                if (files.length > 0) {
                    previewContainer.show();
                    countElement.text(files.length);
                    
                    // Генерация предпросмотра
                    Array.from(files).forEach(function(file, index) {
                        const extension = file.name.split('.').pop().toLowerCase();
                        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
                        
                        if (isImage) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewElement.append(`
                                    <div class="file-preview" data-index="${index}">
                                        <img src="${e.target.result}" class="img-thumbnail">
                                        <span class="remove-file" data-index="${index}">&times;</span>
                                        <div class="file-meta">${file.name}</div>
                                    </div>
                                `);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            let icon;
                            
                            // Определяем иконку в зависимости от типа файла
                            switch (extension) {
                                case 'pdf':
                                    icon = 'bi-file-pdf';
                                    break;
                                case 'dwg':
                                case 'dxf':
                                    icon = 'bi-file-earmark-richtext';
                                    break;
                                case 'cad':
                                    icon = 'bi-vector-pen';
                                    break;
                                default:
                                    icon = 'bi-file-earmark';
                            }
                            
                            previewElement.append(`
                                <div class="file-preview" data-index="${index}">
                                    <div class="p-3 text-center">
                                        <i class="bi ${icon} fs-1"></i>
                                    </div>
                                    <span class="remove-file" data-index="${index}">&times;</span>
                                    <div class="file-meta">${file.name}</div>
                                </div>
                            `);
                        }
                    });
                } else {
                    previewContainer.hide();
                }
            });
            
            // Обработчик удаления файла из предпросмотра
            previewElement.on('click', '.remove-file', function() {
                const index = $(this).data('index');
                $(`.file-preview[data-index="${index}"]`).remove();
                
                // Обновляем счетчик
                const count = previewElement.find('.file-preview').length;
                countElement.text(count);
                
                if (count === 0) {
                    previewContainer.hide();
                }
            });
            
            // Обработчик отправки формы
            form.on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = form.find('button[type="submit"]');
                
                // Деактивация кнопки отправки
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...');
                
                // Показываем прогресс-бар
                progressBar.parent().show();
                progressBar.css('width', '0%');
                
                // Добавляем CSRF-токен
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                // Отправка данных через AJAX
                $.ajax({
                    url: '/api/projects/' + modalManager.projectId + '/schemes',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhrFields: {
                        withCredentials: true
                    },
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        
                        // Отслеживание прогресса загрузки
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBar.css('width', percent + '%');
                                progressBar.attr('aria-valuenow', percent);
                            }
                        });
                        
                        return xhr;
                    },
                    success: function(response) {
                        // Закрываем модальное окно
                        modalManager.activeModal.hide();
                        
                        // Показываем уведомление об успешной загрузке
                        modalManager.showToast('Успешно', 'Схемы успешно загружены', 'success');
                        
                        // Обновляем список файлов
                        if (window.fileManager) {
                            window.fileManager.loadFiles('schemes');
                        }
                    },
                    error: function(xhr) {
                        // Активация кнопки отправки
                        submitBtn.prop('disabled', false).html('Загрузить');
                        
                        // Скрываем прогресс-бар
                        progressBar.parent().hide();
                        
                        // Показываем сообщение об ошибке
                        try {
                            const response = JSON.parse(xhr.responseText);
                            modalManager.showToast('Ошибка загрузки', response.message || 'Произошла ошибка при загрузке файлов', 'danger');
                        } catch (e) {
                            modalManager.showToast('Ошибка загрузки', 'Произошла ошибка при загрузке файлов', 'danger');
                        }
                        
                        console.error('Ошибка загрузки файлов:', xhr);
                    }
                });
            });
        },
        
        // Показ уведомления
        showToast: function(title, message, type = 'info') {
            const toastId = 'toast-' + Date.now();
            const toastHTML = `
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
                    <div class="toast-header bg-${type} text-white">
                        <strong class="me-auto">${title}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Закрыть"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            $('.toast-container').append(toastHTML);
            const toastElement = $('#' + toastId);
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
            toast.show();
            
            // Удаляем элемент после скрытия
            toastElement.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        },
        
        // Обработчики для модального окна событий
        initEventHandlers: function() {
            console.log('Инициализация обработчиков событий - логика в event-modal.blade.php');
            
            // Проверяем, что функция инициализации событий доступна
            if (typeof window.initEventFormHandler === 'function') {
                window.initEventFormHandler();
            } else {
                console.log('initEventFormHandler не найден, обработчики событий инициализируются в самом модальном окне');
            }
        },
        
        // Обработчики для модального окна этапов
        initStageHandlers: function() {
            console.log('Инициализация обработчиков этапов - логика в stage-modal.blade.php');
            
            // Проверяем, что функция инициализации этапов доступна
            if (typeof window.initStageFormHandlers === 'function') {
                window.initStageFormHandlers();
            } else {
                console.log('initStageFormHandlers не найден, обработчики этапов инициализируются в самом модальном окне');
            }
        }
    };
    
    // Удаляем существующие обработчики перед добавлением новых, чтобы избежать дублирования
    $(document).off('click.modalManager', '[data-modal-type]');
    
    // Глобальные обработчики для всех кнопок, открывающих модальные окна через AJAX
    $(document).on('click.modalManager', '[data-modal-type]', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Предотвращаем всплытие события
        
        // Проверяем атрибуты data-bs-toggle, если они есть, удаляем их
        if ($(this).attr('data-bs-toggle')) {
            $(this).removeAttr('data-bs-toggle');
        }
        if ($(this).attr('data-bs-target')) {
            $(this).removeAttr('data-bs-target');
        }
        
        // Проверяем, нет ли уже открытого модального окна
        const openModals = $('.modal.show');
        if (openModals.length > 0) {
            console.warn('Уже есть открытое модальное окно, закрываем его перед открытием нового');
            openModals.modal('hide');
        }
        
        const modalType = $(this).data('modal-type');
        const params = $(this).data('modal-params') || {};
        
        // Предотвращаем двойной вызов, если модальное окно уже загружается
        if (!window.modalLoading) {
            window.modalLoading = true;
            
            // Очищаем контейнер модальных окон перед загрузкой нового
            $('#modalContainer').empty();
            
            window.modalManager.loadModal(modalType, params);
            
            // Сбрасываем флаг после задержки
            setTimeout(() => {
                window.modalLoading = false;
            }, 800); // Увеличиваем задержку для предотвращения двойного клика
        }
    });
    
    // Устанавливаем флаг инициализации
    window.modalManagerInitialized = true;
    
    // Дополнительная проверка и восстановление projectId при загрузке страницы
    if (window.modalManager) {
        console.log('=== ПРОВЕРКА PROJECT ID ПОСЛЕ ИНИЦИАЛИЗАЦИИ ===');
        window.modalManager.ensureProjectId();
    }
    
    console.log('Система модальных окон полностью инициализирована');
});
</script>
