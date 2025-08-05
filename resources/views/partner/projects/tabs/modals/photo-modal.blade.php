<!-- Модальное окно для загрузки фотографий -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotoModalLabel">
                    <i class="bi bi-camera me-2"></i>Загрузить фотографии
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadPhotoForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="photoProjectId" value="{{ $project->id }}">
                    
                    <!-- Зона загрузки файлов -->
                    <div class="upload-zone" id="photoUploadZone">
                        <div class="upload-content">
                            <i class="bi bi-cloud-upload display-4 text-muted mb-3"></i>
                            <h5>Перетащите фотографии сюда</h5>
                            <p class="text-muted mb-3">или нажмите для выбора файлов</p>
                            <input type="file" id="photoFileInput" name="files[]" multiple accept="image/*" class="d-none">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('photoFileInput').click()">
                                <i class="bi bi-plus-lg me-1"></i>Выбрать фотографии
                            </button>
                        </div>
                    </div>
                    
                    <!-- Список выбранных файлов -->
                    <div id="photoFileList" class="file-list mt-4" style="display: none;">
                        <h6>Выбранные фотографии:</h6>
                        <div id="photoFileItems"></div>
                    </div>
                    
                    <!-- Дополнительные параметры -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="photoCategory" class="form-label">Категория</label>
                            <div class="input-group">
                                <select class="form-select" id="photoCategorySelect" onchange="handleCategoryChange()">
                                    <option value="">Выберите категорию</option>
                                    <option value="progress">Ход работ</option>
                                    <option value="before">До начала работ</option>
                                    <option value="after">После завершения</option>
                                    <option value="materials">Материалы</option>
                                    <option value="process">Рабочий процесс</option>
                                    <option value="problems">Проблемы</option>
                                    <option value="documentation">Документация</option>
                                    <option value="demolition">Демонтаж</option>
                                    <option value="floors">Полы</option>
                                    <option value="walls">Стены</option>
                                    <option value="ceiling">Потолки</option>
                                    <option value="electrical">Электрика</option>
                                    <option value="plumbing">Сантехника</option>
                                    <option value="heating">Отопление</option>
                                    <option value="doors">Двери</option>
                                    <option value="windows">Окна</option>
                                    <option value="design">Дизайн</option>
                                    <option value="furniture">Мебель</option>
                                    <option value="decor">Декор</option>
                                    <option value="custom">Своя категория</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleCustomCategory()" title="Ввести свою категорию">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="photoCategory" name="category" 
                                   placeholder="Введите свою категорию..." style="display: none;">
                        </div>
                        <div class="col-md-6">
                            <label for="photoLocation" class="form-label">Место съемки</label>
                            <div class="input-group">
                                <select class="form-select" id="photoLocationSelect" onchange="handleLocationChange()">
                                    <option value="">Выберите помещение</option>
                                    <option value="kitchen">Кухня</option>
                                    <option value="living_room">Гостиная</option>
                                    <option value="bedroom">Спальня</option>
                                    <option value="bathroom">Ванная</option>
                                    <option value="toilet">Туалет</option>
                                    <option value="hallway">Прихожая</option>
                                    <option value="balcony">Балкон</option>
                                    <option value="corridor">Коридор</option>
                                    <option value="pantry">Кладовая</option>
                                    <option value="garage">Гараж</option>
                                    <option value="basement">Подвал</option>
                                    <option value="attic">Чердак</option>
                                    <option value="terrace">Терраса</option>
                                    <option value="custom">Свое помещение</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleCustomLocation()" title="Ввести свое помещение">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="photoLocation" name="location" 
                                   placeholder="Введите название помещения..." style="display: none;">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="photoDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="photoDescription" name="description" rows="3" 
                                  placeholder="Добавьте описание к фотографиям..."></textarea>
                    </div>
                    
                    <!-- Прогресс загрузки -->
                    <div id="photoUploadProgress" class="mt-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Загрузка фотографий...</span>
                            <span id="photoProgressText">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="photoProgressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="uploadPhotoBtn" disabled>
                    <i class="bi bi-upload me-1"></i>Загрузить фотографии
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Оптимизированная инициализация через ProjectManager
$(document).ready(function() {
    if (window.projectManager) {
        // Используем унифицированную систему инициализации модалов
        window.projectManager.initModal('uploadPhotoModal', 'photo', function() {
            console.log('✅ Модал фотографий инициализирован через ProjectManager');
            initPhotoModalHandlers();
        });
    } else {
        console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
        initPhotoModalHandlers();
    }
});

function initPhotoModalHandlers() {
    console.log('📸 Инициализация обработчиков модала фотографий...');
    console.log('=== ПРОВЕРКА PROJECT ID В PHOTO MODAL ===');
    
    // Проверяем projectId в форме
    const formProjectId = $('#photoProjectId').val();
    console.log('Project ID в форме фотографий:', formProjectId);
    
    // Проверяем глобальный projectId
    const globalProjectId = window.projectId;
    console.log('Глобальный Project ID:', globalProjectId);
    
    // Проверяем projectId в modalManager
    const modalManagerProjectId = window.modalManager ? window.modalManager.projectId : null;
    console.log('Project ID в modalManager:', modalManagerProjectId);
    
    // Если в форме нет projectId, попытаемся восстановить его
    if (!formProjectId || formProjectId === '') {
        console.warn('Project ID не установлен в форме фотографий, пытаемся восстановить...');
        
        if (globalProjectId) {
            $('#photoProjectId').val(globalProjectId);
            console.log('✅ Project ID восстановлен из глобальной переменной:', globalProjectId);
        } else if (modalManagerProjectId) {
            $('#photoProjectId').val(modalManagerProjectId);
            console.log('✅ Project ID восстановлен из modalManager:', modalManagerProjectId);
        } else {
            console.error('❌ Не удалось восстановить Project ID');
        }
    }
    
    initPhotoUploadHandlers();
});

function initPhotoUploadHandlers() {
    console.log('📸 Инициализация обработчиков загрузки фотографий...');
    
    // Проверяем, не были ли уже инициализированы обработчики
    if (window.photoUploadHandlersInitialized) {
        console.log('ℹ️ Обработчики загрузки фотографий уже инициализированы');
        return;
    }
    
    const uploadZone = document.getElementById('photoUploadZone');
    const fileInput = document.getElementById('photoFileInput');
    const fileList = document.getElementById('photoFileList');
    const fileItems = document.getElementById('photoFileItems');
    const uploadBtn = document.getElementById('uploadPhotoBtn');
    
    if (!uploadZone || !fileInput || !fileList || !fileItems || !uploadBtn) {
        console.error('❌ Не найдены необходимые элементы для инициализации загрузки фотографий');
        return;
    }
    
    let selectedFiles = [];

    // ПОЛНАЯ ОЧИСТКА старых обработчиков с заменой элементов
    console.log('🧹 Полная очистка обработчиков файлов...');
    
    // Клонируем элементы для полной очистки обработчиков
    const cleanUploadZone = uploadZone.cloneNode(true);
    const cleanFileInput = fileInput.cloneNode(true);
    const cleanUploadBtn = uploadBtn.cloneNode(true);
    
    uploadZone.parentNode.replaceChild(cleanUploadZone, uploadZone);
    fileInput.parentNode.replaceChild(cleanFileInput, fileInput);
    uploadBtn.parentNode.replaceChild(cleanUploadBtn, uploadBtn);

    // Получаем ссылки на новые элементы
    const newUploadZone = document.getElementById('photoUploadZone');
    const newFileInput = document.getElementById('photoFileInput');
    const newUploadBtn = document.getElementById('uploadPhotoBtn');

    console.log('✅ Элементы очищены и заменены');

    // Drag & Drop обработчики
    newUploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        newUploadZone.classList.add('dragover');
        console.log('📂 Drag over zone');
    });

    newUploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        newUploadZone.classList.remove('dragover');
    });

    newUploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        newUploadZone.classList.remove('dragover');
        
        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
        console.log('📂 Files dropped:', files.length);
        handleFileSelection(files);
    });

    // Обработчик выбора файлов через input
    newFileInput.addEventListener('change', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const files = Array.from(e.target.files);
        console.log('📂 Files selected via input:', files.length);
        handleFileSelection(files);
    });

    // Обработчик клика по зоне загрузки
    newUploadZone.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('📂 Upload zone clicked');
        newFileInput.click();
    });

    // Обработчик кнопки "Выбрать фотографии"
    const selectBtn = newUploadZone.querySelector('button');
    if (selectBtn) {
        selectBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('📂 Select button clicked');
            newFileInput.click();
        });
    }

    function handleFileSelection(files) {
        console.log('📸 Обработка выбранных файлов:', files.length);
        
        if (files.length === 0) {
            console.log('ℹ️ Файлы не выбраны');
            return;
        }
        
        selectedFiles = files;
        displaySelectedFiles();
        newUploadBtn.disabled = false;
        
        console.log('✅ Файлы обработаны:', selectedFiles.length);
    }

    function displaySelectedFiles() {
        console.log('📋 Отображение выбранных файлов...');
        
        fileItems.innerHTML = '';
        
        if (selectedFiles.length === 0) {
            fileList.style.display = 'none';
            return;
        }
        
        fileList.style.display = 'block';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex justify-content-between align-items-center p-2 border rounded mb-2';
            
            fileItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-image me-2"></i>
                    <div>
                        <div class="file-name">${file.name}</div>
                        <div class="file-size text-muted">${formatFileSize(file.size)}</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            fileItems.appendChild(fileItem);
        });
    }

    // Глобальная функция для удаления файла
    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        displaySelectedFiles();
        newUploadBtn.disabled = selectedFiles.length === 0;
    };

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Обработчик загрузки файлов
    newUploadBtn.addEventListener('click', function() {
        if (selectedFiles.length === 0) {
            console.log('❌ Нет файлов для загрузки');
            return;
        }
        
        console.log('🚀 Начинаем загрузку файлов:', selectedFiles.length);
        uploadFiles();
    });

    function uploadFiles() {
        const projectId = $('#photoProjectId').val();
        
        if (!projectId) {
            console.error('❌ Project ID не найден');
            alert('Ошибка: ID проекта не найден');
            return;
        }
        
        console.log('📤 Загружаем файлы для проекта:', projectId);
        
        const formData = new FormData();
        formData.append('project_id', projectId);
        formData.append('category', $('#photoCategory').val() || $('#photoCategorySelect').val());
        formData.append('location', $('#photoLocation').val() || $('#photoLocationSelect').val());
        formData.append('description', $('#photoDescription').val());
        
        selectedFiles.forEach(file => {
            formData.append('files[]', file);
        });
        
        // Показываем прогресс (имитация)
        const progressContainer = document.getElementById('photoUploadProgress');
        const progressBar = document.getElementById('photoProgressBar');
        const progressText = document.getElementById('photoProgressText');
        
        progressContainer.style.display = 'block';
        newUploadBtn.disabled = true;
        
        // Простое уведомление вместо AJAX запроса
        setTimeout(() => {
            console.log('✅ Загрузка фотографий временно отключена');
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('uploadPhotoModal'));
            if (modal) {
                modal.hide();
            }
            
            // Показываем уведомление
            alert('Функция загрузки фотографий временно отключена');
            
            // Перезагружаем список фотографий
            if (window.loadPhotos) {
                window.loadPhotos();
            } else if (window.location.pathname.includes('/photos')) {
                // window.location.reload(); // Отключаем перезагрузку страницы
            }
            
            // Очищаем форму
            selectedFiles = [];
            displaySelectedFiles();
            document.getElementById('uploadPhotoForm').reset();
            
            // Скрываем прогресс
            progressContainer.style.display = 'none';
            newUploadBtn.disabled = false;
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
        }, 1000);
    }
    
    // Отмечаем, что обработчики инициализированы
    window.photoUploadHandlersInitialized = true;
    console.log('✅ Обработчики загрузки фотографий инициализированы');
}
        if (selectedFiles.length === 0) {
            fileList.style.display = 'none';
            return;
        }

        fileList.style.display = 'block';
        fileItems.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-icon">
                    <i class="bi bi-image text-primary"></i>
                </div>
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size">${formatFileSize(file.size)}</div>
                </div>
                <div class="file-actions">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            fileItems.appendChild(fileItem);
        });
    }

    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        displaySelectedFiles();
        uploadBtn.disabled = selectedFiles.length === 0;
        
        // Обновляем input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    };

    // Обработчик загрузки
    newUploadBtn.addEventListener('click', function() {
        if (selectedFiles.length === 0) {
            return;
        }

        uploadPhotos();
    });

    // Отмечаем, что обработчики инициализированы
    window.photoUploadHandlersInitialized = true;

    function uploadPhotos() {
        console.log('📤 Начинаем загрузку фотографий...');
        
        const formData = new FormData();
        const projectId = $('#photoProjectId').val();
        
        console.log('🏗️ Параметры загрузки:', {
            projectId: projectId,
            filesCount: selectedFiles.length,
            category: $('#photoCategory').val(),
            location: $('#photoLocation').val(),
            description: $('#photoDescription').val()
        });
        
        if (!projectId) {
            console.error('❌ Project ID не найден');
            if (window.modalManager) {
                window.modalManager.showErrorToast('Ошибка: ID проекта не найден');
            }
            return;
        }
        
        // Добавляем все данные в FormData
        formData.append('project_id', projectId);
        
        // Получаем значение категории
        const categoryValue = $('#photoCategory').is(':visible') ? 
            $('#photoCategory').val() : $('#photoCategorySelect').val();
        formData.append('category', categoryValue);
        
        // Получаем значение локации
        const locationValue = $('#photoLocation').is(':visible') ? 
            $('#photoLocation').val() : $('#photoLocationSelect').val();
        formData.append('location', locationValue);
        
        formData.append('description', $('#photoDescription').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Добавляем файлы
        selectedFiles.forEach((file, index) => {
            formData.append('files[]', file);
            console.log(`📎 Добавлен файл ${index + 1}:`, file.name, `(${formatFileSize(file.size)})`);
        });

        console.log('🚀 Начинаем отправку фотографий на сервер...');

        // Показываем прогресс
        showUploadProgress();
        
        // Отключаем кнопку загрузки
        newUploadBtn.disabled = true;
        newUploadBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Загрузка...';

        // Отправляем AJAX запрос
        $.ajax({
            url: `/partner/projects/${projectId}/photos`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('✅ Фотографии загружены успешно:', response);
                
                hideUploadProgress();
                
                // Очищаем форму
                selectedFiles = [];
                displaySelectedFiles();
                newUploadBtn.disabled = true;
                newUploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Загрузить фотографии';
                
                // Сбрасываем поля формы
                $('#photoCategorySelect').val('');
                $('#photoLocationSelect').val('');
                $('#photoCategory').val('').hide();
                $('#photoLocation').val('').hide();
                $('#photoDescription').val('');
                
                // Очищаем input файла
                document.getElementById('photoFileInput').value = '';
                
                if (window.modalManager) {
                    window.modalManager.closeActiveModal();
                    window.modalManager.showSuccessToast('Фотографии успешно загружены');
                } else {
                    alert('Фотографии успешно загружены');
                }
                
                // Перезагружаем фотографии на странице
                if (typeof window.reloadPhotos === 'function') {
                    window.reloadPhotos();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Ошибка загрузки фотографий:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                hideUploadProgress();
                newUploadBtn.disabled = false;
                newUploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Загрузить фотографии';
                
                let errorMessage = 'Ошибка загрузки фотографий';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        // Обработка ошибок валидации
                        const errors = xhr.responseJSON.errors;
                        const errorMessages = [];
                        for (const field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                errorMessages.push(...errors[field]);
                            }
                        }
                        errorMessage = errorMessages.join(', ');
                    }
                } else if (xhr.status === 413) {
                    errorMessage = 'Файлы слишком большие. Максимальный размер: 10MB';
                } else if (xhr.status === 422) {
                    errorMessage = 'Ошибка валидации данных';
                } else if (xhr.status === 500) {
                    errorMessage = 'Внутренняя ошибка сервера';
                }
                
                if (window.modalManager) {
                    window.modalManager.showErrorToast(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    }

    function showUploadProgress() {
        document.getElementById('photoUploadProgress').style.display = 'block';
    }

    function hideUploadProgress() {
        document.getElementById('photoUploadProgress').style.display = 'none';
    }

    function updateUploadProgress(percent) {
        const progressBar = document.getElementById('photoProgressBar');
        const progressText = document.getElementById('photoProgressText');
        
        progressBar.style.width = percent + '%';
        progressText.textContent = Math.round(percent) + '%';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Функции для управления кастомными полями
function handleCategoryChange() {
    const select = document.getElementById('photoCategorySelect');
    const input = document.getElementById('photoCategory');
    
    if (select.value === 'custom') {
        toggleCustomCategory();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function handleLocationChange() {
    const select = document.getElementById('photoLocationSelect');
    const input = document.getElementById('photoLocation');
    
    if (select.value === 'custom') {
        toggleCustomLocation();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function toggleCustomCategory() {
    const select = document.getElementById('photoCategorySelect');
    const input = document.getElementById('photoCategory');
    
    if (input.style.display === 'none' || input.style.display === '') {
        input.style.display = 'block';
        input.focus();
        select.value = 'custom';
    } else {
        input.style.display = 'none';
        input.value = '';
        select.value = '';
    }
}

function toggleCustomLocation() {
    const select = document.getElementById('photoLocationSelect');
    const input = document.getElementById('photoLocation');
    
    if (input.style.display === 'none' || input.style.display === '') {
        input.style.display = 'block';
        input.focus();
        select.value = 'custom';
    } else {
        input.style.display = 'none';
        input.value = '';
        select.value = '';
    }
}
</script>
