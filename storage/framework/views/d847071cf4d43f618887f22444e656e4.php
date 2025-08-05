<!-- Модальное окно для загрузки дизайн-файлов -->
<div class="modal fade" id="uploadDesignModal" tabindex="-1" aria-labelledby="uploadDesignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDesignModalLabel">
                    <i class="bi bi-paint-bucket me-2"></i>Загрузить дизайн-файлы
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDesignForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="project_id" id="designProjectId" value="<?php echo e($project->id); ?>">
                    
                    <!-- Зона загрузки файлов -->
                    <div class="upload-zone" id="designUploadZone">
                        <div class="upload-content">
                            <i class="bi bi-palette display-4 text-muted mb-3"></i>
                            <h5>Перетащите файлы дизайна сюда</h5>
                            <p class="text-muted mb-3">или нажмите для выбора файлов</p>
                            <input type="file" id="designFileInput" name="design_files[]" multiple 
                                   accept="image/*,.psd,.ai,.sketch,.fig,.dwg,.dxf,.3ds,.max" class="d-none">
                            <button type="button" class="btn btn-primary" id="selectDesignFilesBtn">
                                <i class="bi bi-plus-lg me-1"></i>Выбрать файлы
                            </button>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Поддерживаемые форматы: JPG, PNG, GIF, SVG, PSD, AI, Sketch, Figma, DWG, DXF, 3DS, MAX
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Список выбранных файлов -->
                    <div id="designFileList" class="file-list mt-4" style="display: none;">
                        <h6>Выбранные файлы:</h6>
                        <div id="designFileItems"></div>
                    </div>
                    
                    <!-- Дополнительные параметры -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="designType" class="form-label">Тип дизайна</label>
                            <div class="input-group">
                                <select class="form-select" id="designTypeSelect" onchange="handleDesignTypeChange()">
                                    <option value="">Выберите тип</option>
                                    <option value="3d">3D визуализация</option>
                                    <option value="layout">Планировка</option>
                                    <option value="sketch">Эскиз</option>
                                    <option value="render">Рендер</option>
                                    <option value="draft">Черновик</option>
                                    <option value="concept">Концепт</option>
                                    <option value="detail">Детализация</option>
                                    <option value="material">Материалы</option>
                                    <option value="elevation">Развертка</option>
                                    <option value="section">Разрез</option>
                                    <option value="specification">Спецификация</option>
                                    <option value="custom">Свой тип</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleCustomDesignType()" title="Ввести свой тип дизайна">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="designType" name="design_type" 
                                   placeholder="Введите свой тип дизайна..." style="display: none;">
                        </div>
                        <div class="col-md-6">
                            <label for="designRoom" class="form-label">Помещение</label>
                            <div class="input-group">
                                <select class="form-select" id="designRoomSelect" onchange="handleDesignRoomChange()">
                                    <option value="">Выберите помещение</option>
                                    <option value="kitchen">Кухня</option>
                                    <option value="living_room">Гостиная</option>
                                    <option value="bedroom">Спальня</option>
                                    <option value="bathroom">Ванная</option>
                                    <option value="toilet">Туалет</option>
                                    <option value="hallway">Прихожая</option>
                                    <option value="balcony">Балкон</option>
                                    <option value="corridor">Коридор</option>
                                    <option value="office">Кабинет</option>
                                    <option value="children">Детская</option>
                                    <option value="pantry">Кладовая</option>
                                    <option value="garage">Гараж</option>
                                    <option value="basement">Подвал</option>
                                    <option value="attic">Чердак</option>
                                    <option value="terrace">Терраса</option>
                                    <option value="custom">Свое помещение</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleCustomDesignRoom()" title="Ввести свое помещение">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="designRoom" name="room" 
                                   placeholder="Введите название помещения..." style="display: none;">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="designStyle" class="form-label">Стиль</label>
                            <div class="input-group">
                                <select class="form-select" id="designStyleSelect" onchange="handleDesignStyleChange()">
                                    <option value="">Выберите стиль</option>
                                    <option value="modern">Современный</option>
                                    <option value="classic">Классический</option>
                                    <option value="minimalism">Минимализм</option>
                                    <option value="loft">Лофт</option>
                                    <option value="scandinavian">Скандинавский</option>
                                    <option value="provence">Прованс</option>
                                    <option value="high_tech">Хай-тек</option>
                                    <option value="eco">Эко</option>
                                    <option value="art_deco">Арт-деко</option>
                                    <option value="neoclassic">Неоклассика</option>
                                    <option value="fusion">Фьюжн</option>
                                    <option value="industrial">Индустриальный</option>
                                    <option value="custom">Свой стиль</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleCustomDesignStyle()" title="Ввести свой стиль">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="designStyle" name="style" 
                                   placeholder="Введите название стиля..." style="display: none;">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="designDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="designDescription" name="description" rows="3" 
                                  placeholder="Добавьте описание к дизайн-файлам..."></textarea>
                    </div>
                    
                    <!-- Прогресс загрузки -->
                    <div id="designUploadProgress" class="mt-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Загрузка дизайн-файлов...</span>
                            <span id="designProgressText">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="designProgressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="uploadDesignBtn" disabled>
                    <i class="bi bi-upload me-1"></i>Загрузить файлы
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Предотвращаем множественную инициализацию
if (!window.designModalInitialized) {
    window.designModalInitialized = true;

    // Оптимизированная инициализация через ProjectManager
    $(document).ready(function() {
        console.log('🎨 Инициализация модального окна дизайна...');
        
        // Проверяем и устанавливаем project ID
        const projectId = window.projectId || 
                         $('meta[name="project-id"]').attr('content') || 
                         $('#projectId').val() ||
                         $('[data-project-id]').data('project-id');
        
        if (projectId) {
            $('#designProjectId').val(projectId);
            console.log('🎨 Project ID установлен:', projectId);
        } else {
            console.error('❌ Project ID не найден для модального окна дизайна');
        }
        
        if (window.projectManager) {
            // Используем унифицированную систему инициализации модалов
            window.projectManager.initModal('uploadDesignModal', 'design', function() {
                console.log('✅ Модал дизайна инициализирован через ProjectManager');
                initDesignModalHandlers();
            });
        } else {
            console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
            initDesignModalHandlers();
        }
    });

    // Обработчик закрытия модального окна для очистки обработчиков
    $('#uploadDesignModal').on('hidden.bs.modal', function () {
        console.log('🧹 Очистка состояния modal дизайна...');
        window.designUploadHandlersInitialized = false;
        
        // Очищаем флаги кнопок
        const selectButton = document.getElementById('selectDesignFilesBtn');
        if (selectButton) {
            selectButton._designClickHandlerAttached = false;
        }
    });
}

function initDesignModalHandlers() {
    console.log('🎨 Инициализация обработчиков модала дизайна...');
    
    // Проверяем, не были ли уже инициализированы обработчики
    if (window.designUploadHandlersInitialized) {
        console.log('ℹ️ Обработчики дизайна уже инициализированы, пропускаем');
        return;
    }
    
    // Инициализация при открытии modal
    $('#uploadDesignModal').on('shown.bs.modal', function () {
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
            
            if (globalProjectId) {
                $('#designProjectId').val(globalProjectId);
                console.log('✅ Project ID восстановлен из глобальной переменной:', globalProjectId);
            } else if (modalManagerProjectId) {
                $('#designProjectId').val(modalManagerProjectId);
                console.log('✅ Project ID восстановлен из modalManager:', modalManagerProjectId);
            } else {
                console.error('❌ Не удалось восстановить Project ID');
            }
        }
        
        // Инициализируем обработчики только при открытии
        initDesignUploadHandlers();
    });
    
    // Отмечаем, что обработчики инициализированы
    window.designUploadHandlersInitialized = true;
    console.log('✅ Обработчики модала дизайна инициализированы');
}
        
        // Если в форме нет projectId, попытаемся восстановить его
        if (!formProjectId || formProjectId === '') {
            console.warn('Project ID не установлен в форме дизайна, пытаемся восстановить...');
            
            if (globalProjectId) {
                $('#designProjectId').val(globalProjectId);
                console.log('✅ Project ID восстановлен из глобальной переменной:', globalProjectId);
            } else if (modalManagerProjectId) {
                $('#designProjectId').val(modalManagerProjectId);
                console.log('✅ Project ID восстановлен из modalManager:', modalManagerProjectId);
            } else {
                console.error('❌ Не удалось восстановить Project ID');
            }
        }
        
        initDesignUploadHandlers();
    });
}

function initDesignUploadHandlers() {
    // Предотвращаем повторную инициализацию
    if (window.designUploadHandlersInitialized) {
        console.log('🎨 Обработчики дизайна уже инициализированы, пропускаем...');
        return;
    }
    
    console.log('🎨 Инициализация обработчиков загрузки дизайн-файлов...');
    window.designUploadHandlersInitialized = true;
    
    const uploadZone = document.getElementById('designUploadZone');
    const fileInput = document.getElementById('designFileInput');
    const fileList = document.getElementById('designFileList');
    const fileItems = document.getElementById('designFileItems');
    const uploadBtn = document.getElementById('uploadDesignBtn');
    let selectedFiles = [];

    // Удаляем старые обработчики если они есть
    if (uploadZone._designHandlersAttached) {
        console.log('🧹 Удаляем старые обработчики...');
        return;
    }

    uploadZone._designHandlersAttached = true;

    // Обработчики drag & drop
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        
        const files = Array.from(e.dataTransfer.files);
        handleFileSelection(files);
    });

    // Обработчик выбора файлов
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleFileSelection(files);
    });

    // Обработчик клика по кнопке выбора файлов
    const selectButton = document.getElementById('selectDesignFilesBtn');
    if (selectButton && !selectButton._designClickHandlerAttached) {
        selectButton._designClickHandlerAttached = true;
        selectButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🖱️ Клик по кнопке выбора файлов дизайна');
            fileInput.click();
        });
    }

    // Обработка выбранных файлов
    function handleFileSelection(files) {
        console.log('📁 Обработка выбранных файлов:', files.length);
        
        if (files.length === 0) return;
        
        // Очищаем предыдущий выбор перед добавлением новых файлов
        selectedFiles = Array.from(files);
        console.log('📋 Обновлен список файлов:', selectedFiles.map(f => f.name));
        
        displaySelectedFiles();
        updateUploadButton();
    }

    // Отображение выбранных файлов
    function displaySelectedFiles() {
        if (selectedFiles.length === 0) {
            fileList.style.display = 'none';
            return;
        }

        fileItems.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex justify-content-between align-items-center p-3 border rounded mb-2';
            
            fileItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-${getDesignIcon(file.type, file.name)} me-3 text-primary"></i>
                    <div>
                        <div class="fw-medium">${file.name}</div>
                        <small class="text-muted">${formatFileSize(file.size)}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDesignFile(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            fileItems.appendChild(fileItem);
        });

        fileList.style.display = 'block';
    }

    // Получение иконки для файла дизайна
    function getDesignIcon(fileType, fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        
        if (fileType.startsWith('image/')) {
            return 'file-earmark-image';
        }
        
        switch (extension) {
            case 'psd':
                return 'file-earmark-image';
            case 'ai':
                return 'vector-pen';
            case 'sketch':
                return 'pencil-square';
            case 'fig':
                return 'file-earmark-binary';
            case 'dwg':
            case 'dxf':
                return 'blueprint';
            case '3ds':
            case 'max':
                return 'box';
            case 'pdf':
                return 'file-earmark-pdf';
            default:
                return 'file-earmark';
        }
    }

    // Удаление файла из списка
    window.removeDesignFile = function(index) {
        selectedFiles.splice(index, 1);
        displaySelectedFiles();
        updateUploadButton();
        
        if (selectedFiles.length === 0) {
            fileInput.value = '';
        }
    };

    // Обновление состояния кнопки загрузки
    function updateUploadButton() {
        uploadBtn.disabled = selectedFiles.length === 0;
    }

    // Обработчик кнопки загрузки
    if (uploadBtn && !uploadBtn._designClickHandlerAttached) {
        uploadBtn._designClickHandlerAttached = true;
        uploadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (selectedFiles.length === 0) return;
            uploadFiles();
        });
    }

    // Загрузка файлов
    function uploadFiles() {
        console.log('⬆️ Начинаем загрузку файлов дизайна...');
        
        const projectId = $('#designProjectId').val();
        if (!projectId) {
            console.error('❌ Project ID не найден');
            if (window.modalManager) {
                window.modalManager.showErrorToast('Ошибка: не найден ID проекта');
            }
            return;
        }

        const formData = new FormData();
        formData.append('project_id', projectId);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Добавляем файлы
        selectedFiles.forEach((file, index) => {
            formData.append('files[]', file);
        });

        // Добавляем метаданные
        const designType = getDesignTypeValue();
        const room = getRoomValue();
        const style = getStyleValue();
        const description = $('#designDescription').val();

        if (designType) formData.append('type', designType);
        if (room) formData.append('room', room);
        if (style) formData.append('style', style);
        if (description) formData.append('description', description);

        console.log('📦 Данные для отправки:', {
            projectId,
            filesCount: selectedFiles.length,
            designType,
            room,
            style,
            description
        });

        // Показываем прогресс (имитация)
        showDesignUploadProgress();
        uploadBtn.disabled = true;

        // Простое уведомление вместо AJAX запроса
        setTimeout(() => {
            console.log('✅ Загрузка дизайн-файлов временно отключена');
            
            hideDesignUploadProgress();
            
            // Очищаем выбранные файлы и состояние формы
            selectedFiles = [];
            if (fileInput) fileInput.value = '';
            updateSelectedFilesDisplay();
            
            if (window.modalManager) {
                window.modalManager.showToast('Функция загрузки дизайн-файлов временно отключена', 'info');
                window.modalManager.closeActiveModal();
            } else {
                alert('Функция загрузки дизайн-файлов временно отключена');
            }
            
            // Перезагружаем вкладку дизайна
            if (typeof loadDesignFiles === 'function') {
                loadDesignFiles();
            } else if (window.location.pathname.includes('/design')) {
                // location.reload(); // Отключаем перезагрузку страницы
            }
            
            uploadBtn.disabled = false;
        }, 1500);
    }

    // Получение значения типа дизайна (с учетом кастомного)
    function getDesignTypeValue() {
        const select = document.getElementById('designTypeSelect');
        const input = document.getElementById('designType');
        
        if (select.value === 'custom' && input.style.display !== 'none') {
            return input.value.trim();
        }
        return select.value;
    }

    // Получение значения помещения (с учетом кастомного)
    function getRoomValue() {
        const select = document.getElementById('designRoomSelect');
        const input = document.getElementById('designRoom');
        
        if (select.value === 'custom' && input.style.display !== 'none') {
            return input.value.trim();
        }
        return select.value;
    }

    // Получение значения стиля (с учетом кастомного)
    function getStyleValue() {
        const select = document.getElementById('designStyleSelect');
        const input = document.getElementById('designStyle');
        
        if (select.value === 'custom' && input.style.display !== 'none') {
            return input.value.trim();
        }
        return select.value;
    }

    function showDesignUploadProgress() {
        document.getElementById('designUploadProgress').style.display = 'block';
    }

    function hideDesignUploadProgress() {
        document.getElementById('designUploadProgress').style.display = 'none';
    }

    function updateDesignUploadProgress(percent) {
        const progressBar = document.getElementById('designProgressBar');
        const progressText = document.getElementById('designProgressText');
        
        progressBar.style.width = percent + '%';
        progressText.textContent = Math.round(percent) + '%';
    }

    // Очистка состояния при закрытии модального окна
    $('#uploadDesignModal').on('hidden.bs.modal', function() {
        console.log('🚪 Модальное окно дизайна закрыто, очищаем состояние...');
        
        // Очищаем выбранные файлы
        selectedFiles = [];
        fileInput.value = '';
        
        // Скрываем список файлов
        fileList.style.display = 'none';
        fileItems.innerHTML = '';
        
        // Сбрасываем форму
        document.getElementById('uploadDesignForm').reset();
        
        // Скрываем кастомные поля
        document.getElementById('designType').style.display = 'none';
        document.getElementById('designRoom').style.display = 'none';
        document.getElementById('designStyle').style.display = 'none';
        
        // Скрываем прогресс
        hideDesignUploadProgress();
        
        // Активируем кнопку загрузки
        updateUploadButton();
        
        console.log('✨ Состояние модального окна дизайна очищено');
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Функции для управления кастомными полями типа дизайна
function handleDesignTypeChange() {
    const select = document.getElementById('designTypeSelect');
    const input = document.getElementById('designType');
    
    if (select.value === 'custom') {
        toggleCustomDesignType();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function toggleCustomDesignType() {
    const select = document.getElementById('designTypeSelect');
    const input = document.getElementById('designType');
    
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

// Функции для управления кастомными полями помещения
function handleDesignRoomChange() {
    const select = document.getElementById('designRoomSelect');
    const input = document.getElementById('designRoom');
    
    if (select.value === 'custom') {
        toggleCustomDesignRoom();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function toggleCustomDesignRoom() {
    const select = document.getElementById('designRoomSelect');
    const input = document.getElementById('designRoom');
    
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

// Функции для управления кастомными полями стиля
function handleDesignStyleChange() {
    const select = document.getElementById('designStyleSelect');
    const input = document.getElementById('designStyle');
    
    if (select.value === 'custom') {
        toggleCustomDesignStyle();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function toggleCustomDesignStyle() {
    const select = document.getElementById('designStyleSelect');
    const input = document.getElementById('designStyle');
    
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
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/modals/design-modal.blade.php ENDPATH**/ ?>