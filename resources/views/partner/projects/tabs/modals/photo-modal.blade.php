<!-- AJAX Модальное окно для загрузки фотографий (версия 2.0) -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotoModalLabel">
                    <i class="bi bi-cloud-upload me-2"></i>Загрузка фотографий
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form id="uploadPhotoForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="photoProjectId" value="{{ $project->id ?? '' }}">
                    
                    <div class="mb-4">
                        <label for="photoInput" class="form-label">Выберите фотографии</label>
                        <input type="file" id="photoInput" name="files[]" class="form-control" multiple accept="image/*">
                        <div class="form-text">Поддерживаемые форматы: JPEG, PNG, GIF, WebP</div>
                    </div>
                    
                    <div id="photoPreviewContainer" class="mb-4" style="display: none;">
                        <h6 class="mb-3">Выбранные фотографии: <span id="selectedPhotosCount">0</span></h6>
                        <div id="photoPreview" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="photoCategory" class="form-label">Категория</label>
                                <select class="form-select" id="photoCategory" name="category">
                                    <option value="">Без категории</option>
                                    <option value="before">До ремонта</option>
                                    <option value="after">После ремонта</option>
                                    <option value="process">Процесс работы</option>
                                    <option value="materials">Материалы</option>
                                    <option value="problems">Проблемы</option>
                                    <option value="design">Дизайн</option>
                                    <option value="furniture">Мебель</option>
                                    <option value="decor">Декор</option>
                                    <option value="demolition">Демонтаж</option>
                                    <option value="floors">Полы</option>
                                    <option value="walls">Стены</option>
                                    <option value="ceiling">Потолки</option>
                                    <option value="electrical">Электрика</option>
                                    <option value="plumbing">Сантехника</option>
                                    <option value="heating">Отопление</option>
                                    <option value="doors">Двери</option>
                                    <option value="windows">Окна</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="photoLocation" class="form-label">Помещение</label>
                                <select class="form-select" id="photoLocation" name="location">
                                    <option value="">Не выбрано</option>
                                    <option value="kitchen">Кухня</option>
                                    <option value="living_room">Гостиная</option>
                                    <option value="bedroom">Спальня</option>
                                    <option value="bathroom">Ванная</option>
                                    <option value="toilet">Туалет</option>
                                    <option value="hallway">Прихожая</option>
                                    <option value="balcony">Балкон</option>
                                    <option value="other">Другое</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photoDescription" class="form-label">Описание (необязательно)</label>
                        <textarea class="form-control" id="photoDescription" name="description" rows="3" placeholder="Добавьте описание фотографий"></textarea>
                    </div>
                </form>
                
                <div class="progress mb-3" id="photoUploadProgress" style="display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                
                <div class="alert alert-danger" id="photoUploadError" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="uploadPhotosBtn" disabled>
                    <i class="bi bi-cloud-upload me-2"></i>Загрузить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно просмотра фото -->
<div class="modal fade" id="viewPhotoModal" tabindex="-1" aria-labelledby="viewPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPhotoModalLabel">Просмотр фото</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" id="viewPhotoImg" class="img-fluid w-100" alt="Фото проекта">
                <div class="p-3">
                    <h5 id="viewPhotoTitle" class="mb-2"></h5>
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-primary" id="viewPhotoCategory"></span>
                        <span class="badge bg-secondary" id="viewPhotoLocation"></span>
                    </div>
                    <p id="viewPhotoDescription" class="text-muted"></p>
                    <div class="text-muted small">
                        <span id="viewPhotoDate"></span> • 
                        <span id="viewPhotoSize"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="downloadPhotoBtn">
                    <i class="bi bi-download me-2"></i>Скачать
                </a>
                <button type="button" class="btn btn-danger" id="deletePhotoBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="confirmDeletePhotoModal" tabindex="-1" aria-labelledby="confirmDeletePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeletePhotoModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить это фото?</p>
                <input type="hidden" id="photoToDeleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmPhotoDeleteBtn">
                    <i class="bi bi-trash me-2"></i>Удалить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Инициализация модальных окон для фото');
    
    // Очищаем все обработчики на случай повторной инициализации
    $('#photoInput').off('change');
    
    // Обработчик выбора файлов - используем one-time binding для предотвращения множественных вызовов
    $('#photoInput').off('change').one('change', function(e) {
        const files = this.files;
        console.log('Файлы выбраны:', files.length);
        
        if (files && files.length > 0) {
            $('#uploadPhotosBtn').prop('disabled', false);
            $('#photoPreviewContainer').show();
            // Отображаем превью
            showPhotosPreview(files);
        } else {
            $('#uploadPhotosBtn').prop('disabled', true);
            $('#photoPreviewContainer').hide();
            $('#selectedPhotosCount').text(0);
        }
        
        // Переинициализируем обработчик для следующего выбора файлов
        $(this).off('change').one('change', arguments.callee);
    });
    
    // Обработчик загрузки фото
    $('#uploadPhotosBtn').click(function() {
        uploadPhotos();
    });
    
    // Обработчик удаления фото
    $('#deletePhotoBtn').click(function() {
        const photoId = $(this).data('photo-id');
        $('#photoToDeleteId').val(photoId);
        $('#viewPhotoModal').modal('hide');
        $('#confirmDeletePhotoModal').modal('show');
    });
    
    // Подтверждение удаления
    $('#confirmPhotoDeleteBtn').click(function() {
        const photoId = $('#photoToDeleteId').val();
        if (photoId) {
            deletePhoto(photoId);
        }
    });
    
    // Сброс при закрытии модального окна
    $('#uploadPhotoModal').on('hidden.bs.modal', function() {
        const form = document.getElementById('uploadPhotoForm');
        if (form) form.reset();
        
        $('#photoPreview').empty();
        $('#photoPreviewContainer').hide();
        $('#selectedPhotosCount').text(0);
        $('#uploadPhotosBtn').prop('disabled', true);
        $('#photoUploadProgress').hide();
        $('#photoUploadError').hide();
        
        // Обновляем список фотографий при закрытии модального окна на случай, если обновление не произошло ранее
        setTimeout(function() {
            if (window.PhotoManager && typeof window.PhotoManager.loadPhotos === 'function') {
                console.log('Дополнительное обновление списка фотографий при закрытии модального окна');
                window.PhotoManager.loadPhotos();
            }
        }, 500);
    });
});

// Функция загрузки фотографий через AJAX
function uploadPhotos() {
    console.log('Запуск функции uploadPhotos');
    const form = $('#uploadPhotoForm')[0];
    const formData = new FormData(form);
    const projectId = $('#photoProjectId').val();
    
    console.log('Проект ID:', projectId);
    console.log('Количество файлов:', $('#photoInput')[0].files.length);
    
    $('#uploadPhotosBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...');
    $('#photoUploadProgress').show();
    $('#photoUploadError').hide();
    
    // Добавляем CSRF-токен
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    $.ajax({
        url: `/partner/projects/${projectId}/photos`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            withCredentials: true
        },
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    $('#photoUploadProgress .progress-bar')
                        .css('width', percent + '%')
                        .attr('aria-valuenow', percent)
                        .text(percent + '%');
                }
            });
            return xhr;
        },
        success: function(response) {
            console.log('Фото успешно загружены', response);
            $('#uploadPhotoModal').modal('hide');
            
            // Принудительно инициализируем PhotoManager если он не инициализирован
            if (!window.PhotoManager?.initialized) {
                console.log('PhotoManager не инициализирован, принудительно инициализируем...');
                if (window.PhotoManager && typeof window.PhotoManager.init === 'function') {
                    window.PhotoManager.init();
                }
            }
            
            // Обновляем список фотографий - используем правильный менеджер
            if (window.PhotoManager && typeof window.PhotoManager.loadPhotos === 'function') {
                console.log('Обновляем список фотографий через PhotoManager.loadPhotos()');
                window.PhotoManager.loadPhotos();
            } else if (typeof window.loadPhotos === 'function') {
                console.log('Обновляем список фотографий через window.loadPhotos()');
                window.loadPhotos();
            } else {
                console.warn('Менеджер фотографий не найден, пробуем перезагрузить вкладку');
                // Если менеджер не найден, попробуем перезагрузить содержимое вкладки
                if (typeof loadTabContent === 'function') {
                    loadTabContent('photos');
                } else {
                    // В качестве последнего средства - перезагружаем страницу
                    console.log('Перезагружаем страницу для обновления фотографий...');
                    location.reload();
                }
            }
            
            // Показываем сообщение
            showMessage('Фотографии успешно загружены', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при загрузке фотографий:', error);
            $('#uploadPhotosBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
            
            let errorMessage = 'Произошла ошибка при загрузке фотографий';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            $('#photoUploadError').text(errorMessage).show();
        },
        complete: function() {
            $('#uploadPhotosBtn').prop('disabled', false).html('<i class="bi bi-cloud-upload me-2"></i>Загрузить');
        }
    });
}

// Функция удаления фото через AJAX
function deletePhoto(photoId) {
    const projectId = $('#photoProjectId').val();
    
    $('#confirmPhotoDeleteBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Удаление...');
    
    $.ajax({
        url: `/partner/projects/${projectId}/photos/${photoId}`,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            withCredentials: true
        },
        success: function(response) {
            console.log('Фото успешно удалено', response);
            $('#confirmDeletePhotoModal').modal('hide');
            
            // Обновляем список фотографий - используем правильный менеджер
            if (window.PhotoManager && typeof window.PhotoManager.loadPhotos === 'function') {
                console.log('Обновляем список фотографий через PhotoManager.loadPhotos()');
                window.PhotoManager.loadPhotos();
            } else if (typeof window.loadPhotos === 'function') {
                console.log('Обновляем список фотографий через window.loadPhotos()');
                window.loadPhotos();
            } else {
                console.warn('Менеджер фотографий не найден, пробуем перезагрузить вкладку');
                // Если менеджер не найден, попробуем перезагрузить содержимое вкладки
                if (typeof loadTabContent === 'function') {
                    loadTabContent('photos');
                }
            }
            
            // Показываем сообщение
            showMessage('Фото успешно удалено', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при удалении фото:', error);
            
            let errorMessage = 'Произошла ошибка при удалении фото';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            showMessage(errorMessage, 'error');
        },
        complete: function() {
            $('#confirmPhotoDeleteBtn').prop('disabled', false).html('<i class="bi bi-trash me-2"></i>Удалить');
        }
    });
}

// Открытие модального окна просмотра фото
function viewPhoto(photoId) {
    const projectId = $('#photoProjectId').val();
    
    $.ajax({
        url: `/partner/projects/${projectId}/photos/${photoId}`,
        type: 'GET',
        success: function(response) {
            const photo = response.data;
            
            $('#viewPhotoImg').attr('src', photo.url);
            $('#viewPhotoTitle').text(photo.name || 'Фото без названия');
            $('#viewPhotoCategory').text(getPhotoCategoryName(photo.category));
            $('#viewPhotoLocation').text(getPhotoLocationName(photo.location));
            $('#viewPhotoDescription').text(photo.description || 'Нет описания');
            $('#viewPhotoDate').text(formatDate(photo.created_at));
            $('#viewPhotoSize').text(formatFileSize(photo.size));
            $('#downloadPhotoBtn').attr('href', photo.download_url);
            $('#deletePhotoBtn').data('photo-id', photo.id);
            
            $('#viewPhotoModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Ошибка при получении данных фото:', error);
            showMessage('Не удалось загрузить данные фото', 'error');
        }
    });
}

// Вспомогательные функции
function showPhotosPreview(files) {
    console.log('Вызов showPhotosPreview с', files.length, 'файлами');
    const container = $('#photoPreview');
    
    // Очищаем контейнер перед добавлением новых превью
    container.empty();
    
    if (!files || files.length === 0) return;
    
    // Счетчик для отслеживания загруженных файлов
    let loadedCount = 0;
    const totalFiles = Array.from(files).filter(file => file.type.startsWith('image/')).length;
    const alreadyProcessed = new Set(); // Для отслеживания уже обработанных файлов
    
    console.log('Всего файлов для обработки:', totalFiles);
    
    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            // Создаем уникальный идентификатор файла
            const fileId = file.name + '_' + file.size + '_' + index;
            
            // Пропускаем, если файл уже обработан
            if (alreadyProcessed.has(fileId)) {
                console.log('Файл уже обработан, пропускаем:', file.name);
                return;
            }
            
            alreadyProcessed.add(fileId);
            
            const reader = new FileReader();
            
            // Создаем только одного обработчика onload для каждого файла
            reader.onload = function(e) {
                // Проверяем, не был ли уже добавлен этот файл
                if (container.find(`[data-file-index="${index}"]`).length > 0) {
                    console.log('Превью уже существует, пропускаем:', file.name);
                    return;
                }
                
                // Создаем элемент превью
                const preview = $(`
                    <div class="position-relative d-inline-block me-2 mb-2" data-file-index="${index}" data-file-id="${fileId}">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="width: 20px; height: 20px; font-size: 10px;" onclick="removePreview(this, ${index})">
                            <i class="bi bi-x"></i>
                        </button>
                        <small class="d-block text-muted text-center mt-1" style="font-size: 10px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${file.name}">
                            ${file.name}
                        </small>
                    </div>
                `);
                
                // Добавляем превью в контейнер
                container.append(preview);
                
                loadedCount++;
                console.log('Обработано файлов:', loadedCount, 'из', totalFiles);
                
                // Обновляем текст количества выбранных файлов
                $('#selectedPhotosCount').text(loadedCount);
            };
            
            // Запускаем чтение файла только один раз
            reader.readAsDataURL(file);
        }
    });
}

function removePreview(button, index) {
    const photoInput = document.getElementById('photoInput');
    if (photoInput && photoInput.files) {
        // Создаем новый DataTransfer для переноса файлов без удаленного
        const dt = new DataTransfer();
        Array.from(photoInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        
        // Обновляем список файлов в инпуте
        photoInput.files = dt.files;
        
        // Удаляем превью из DOM
        $(button).closest('[data-file-index]').remove();
        
        // Обновляем счетчик
        const remainingCount = photoInput.files.length;
        $('#selectedPhotosCount').text(remainingCount);
        
        // Если файлов не осталось, скрываем контейнер и блокируем кнопку
        if (remainingCount === 0) {
            $('#photoPreviewContainer').hide();
            $('#uploadPhotosBtn').prop('disabled', true);
        }
        
        // Не запускаем change, так как это вызовет повторную перерисовку всех превью
        // и может привести к дублированию. Вместо этого мы обновляем UI напрямую.
    }
}

function getPhotoCategoryName(category) {
    const categories = {
        'before': 'До ремонта',
        'after': 'После ремонта',
        'process': 'Процесс работы',
        'materials': 'Материалы',
        'problems': 'Проблемы',
        'design': 'Дизайн',
        'furniture': 'Мебель',
        'decor': 'Декор',
        'demolition': 'Демонтаж',
        'floors': 'Полы',
        'walls': 'Стены',
        'ceiling': 'Потолки',
        'electrical': 'Электрика',
        'plumbing': 'Сантехника',
        'heating': 'Отопление',
        'doors': 'Двери',
        'windows': 'Окна',
    };
    
    return categories[category] || 'Без категории';
}

function getPhotoLocationName(location) {
    const locations = {
        'kitchen': 'Кухня',
        'living_room': 'Гостиная',
        'bedroom': 'Спальня',
        'bathroom': 'Ванная',
        'toilet': 'Туалет',
        'hallway': 'Прихожая',
        'balcony': 'Балкон',
        'other': 'Другое'
    };
    
    return locations[location] || 'Не указано';
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
    console.log('=== ПРОВЕРКА PROJECT ID В PHOTO MODAL ===');
    
    // Проверяем projectId в форме
    const formProjectId = $('#photoProjectId').val();
    console.log('Project ID в форме:', formProjectId);
    
    // Проверяем глобальный projectId
    const globalProjectId = window.projectId;
    console.log('Глобальный Project ID:', globalProjectId);
    
    // Проверяем projectId в modalManager
    const modalManagerProjectId = window.modalManager ? window.modalManager.projectId : null;
    console.log('Project ID в modalManager:', modalManagerProjectId);
    
    // Если в форме нет projectId, попытаемся восстановить его
    if (!formProjectId || formProjectId === '') {
        console.warn('Project ID не установлен в форме, пытаемся восстановить...');
        
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
            $('#photoProjectId').val(recoveredProjectId);
            console.log('Project ID установлен в форму:', recoveredProjectId);
            
            // Также обновляем PhotoManager если он существует
            if (window.PhotoManager) {
                window.PhotoManager.projectId = recoveredProjectId;
                console.log('Project ID обновлен в PhotoManager:', recoveredProjectId);
            }
        } else {
            console.error('НЕ УДАЛОСЬ ВОССТАНОВИТЬ PROJECT ID! Проверьте конфигурацию.');
        }
    } else {
        console.log('Project ID корректно установлен в форме:', formProjectId);
        
        // Убедимся, что PhotoManager также имеет правильный projectId
        if (window.PhotoManager && (!window.PhotoManager.projectId || window.PhotoManager.projectId === 'null')) {
            window.PhotoManager.projectId = formProjectId;
            console.log('Project ID синхронизирован с PhotoManager:', formProjectId);
        }
    }
    
    // Добавляем обработчик для принудительного обновления списка после закрытия модального окна
    $('#uploadPhotoModal').on('hidden.bs.modal', function() {
        console.log('Модальное окно закрыто, проверяем необходимость обновления списка фотографий...');
        
        // Небольшая задержка, чтобы убедиться, что все AJAX запросы завершились
        setTimeout(function() {
            if (window.PhotoManager && window.PhotoManager.initialized) {
                console.log('Принудительное обновление списка фотографий...');
                window.PhotoManager.loadPhotos();
            }
        }, 1000);
    });
});
</script>
