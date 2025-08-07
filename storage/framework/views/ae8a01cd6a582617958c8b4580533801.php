<!-- Модальное окно для загрузки документов -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Загрузить документы
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('partner.projects.documents.upload', $project ?? ['id' => 'PROJECT_ID'])); ?>" method="POST" enctype="multipart/form-data" id="uploadDocumentForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="documentProjectId" name="project_id" value="<?php echo e($project->id ?? ''); ?>">
                    
                    <!-- Зона загрузки файлов -->
                    <div class="upload-zone mb-4" id="documentUploadZone">
                        <div class="upload-content text-center p-4 border border-dashed rounded">
                            <i class="bi bi-file-earmark-text display-4 text-muted mb-3"></i>
                            <h5>Выберите документы для загрузки</h5>
                            <p class="text-muted mb-3">Поддерживаемые форматы: PDF, DOC, DOCX, XLS, XLSX, TXT, RTF</p>
                            <button type="button" class="btn btn-outline-primary" id="selectDocumentFilesBtn">
                                <i class="bi bi-folder2-open me-1"></i>Выбрать файлы
                            </button>
                            <input type="file" id="documentFileInput" name="documents[]" multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.rtf" class="d-none" required>
                            <small class="text-muted d-block mt-2">Максимальный размер файла: 50 МБ</small>
                        </div>
                    </div>
                    
                    <!-- Список выбранных файлов -->
                    <div id="documentFileList" class="d-none">
                        <h6 class="fw-bold mb-3">Выбранные файлы:</h6>
                        <div id="documentFileItems" class="file-list mb-3"></div>
                    </div>
                    
                    <!-- Дополнительные параметры -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="documentTypeSelect" class="form-label">Тип документа</label>
                            <div class="input-group">
                                <select class="form-select" id="documentTypeSelect" name="document_type" onchange="handleDocumentTypeChange()">
                                    <option value="">Выберите тип</option>
                                    <option value="contract">Договор</option>
                                    <option value="specification">Спецификация</option>
                                    <option value="blueprint">Чертеж</option>
                                    <option value="estimate">Смета</option>
                                    <option value="permit">Разрешение</option>
                                    <option value="certificate">Сертификат</option>
                                    <option value="invoice">Счет-фактура</option>
                                    <option value="report">Отчет</option>
                                    <option value="other">Другое</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="customDocumentTypeBtn" onclick="toggleCustomDocumentType()" style="display: none;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2 d-none" id="customDocumentType" name="custom_document_type" placeholder="Укажите тип документа">
                        </div>
                        <div class="col-md-6">
                            <label for="documentCategorySelect" class="form-label">Категория</label>
                            <div class="input-group">
                                <select class="form-select" id="documentCategorySelect" name="category" onchange="handleDocumentCategoryChange()">
                                    <option value="">Выберите категорию</option>
                                    <option value="legal">Юридические</option>
                                    <option value="technical">Технические</option>
                                    <option value="financial">Финансовые</option>
                                    <option value="administrative">Административные</option>
                                    <option value="quality">Качество</option>
                                    <option value="safety">Безопасность</option>
                                    <option value="other">Другое</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="customDocumentCategoryBtn" onclick="toggleCustomDocumentCategory()" style="display: none;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2 d-none" id="customDocumentCategory" name="custom_category" placeholder="Укажите категорию">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="documentDescription" class="form-label">Описание (необязательно)</label>
                            <textarea class="form-control" id="documentDescription" name="description" rows="3" 
                                    placeholder="Добавьте описание или комментарии к документам"></textarea>
                        </div>
                    </div>
                    
                    <!-- Прогресс загрузки -->
                    <div id="documentUploadProgress" class="d-none mt-3">
                        <label class="form-label">Загрузка файлов:</label>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted" id="documentProgressText">Подготовка к загрузке...</small>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" id="uploadDocumentBtn" disabled>
                            <i class="bi bi-cloud-upload me-1"></i>Загрузить документы
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Предотвращаем множественную инициализацию
if (!window.documentModalInitialized) {
    window.documentModalInitialized = true;

    // Оптимизированная инициализация через ProjectManager
    $(document).ready(function() {
        if (window.projectManager) {
            window.projectManager.initModal('uploadDocumentModal', 'document', function() {
                initDocumentModalHandlers();
            });
        } else {
            // Fallback для совместимости
            console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
            initDocumentModalHandlers();
        }
    });

    // Обработчик закрытия модального окна для очистки обработчиков
    $('#uploadDocumentModal').on('hidden.bs.modal', function () {
        console.log('🔄 Сброс формы документов');
        $('#uploadDocumentForm')[0].reset();
        $('#documentFileList').addClass('d-none');
        $('#documentFileItems').empty();
        $('#uploadDocumentBtn').prop('disabled', true);
        hideDocumentUploadProgress();
    });
}

function initDocumentModalHandlers() {
    console.log('📄 Инициализация обработчиков модала документов...');
    
    // Проверяем, не были ли уже инициализированы обработчики
    if (window.documentUploadHandlersInitialized) {
        console.log('⚠️ Обработчики документов уже инициализированы');
        return;
    }
    
    // Проверяем projectId в форме
    const formProjectId = $('#documentProjectId').val();
    console.log('Project ID в форме документов:', formProjectId);
    
    // Если в форме нет projectId, попытаемся восстановить его
    if (!formProjectId || formProjectId === '') {
        const globalProjectId = window.projectId;
        if (globalProjectId) {
            $('#documentProjectId').val(globalProjectId);
            console.log('✅ Project ID восстановлен в форме документов:', globalProjectId);
        } else {
            console.error('❌ Не удалось найти Project ID для формы документов');
        }
    }
    
    initDocumentUploadHandlers();
}

function initDocumentUploadHandlers() {
    // Предотвращаем повторную инициализацию
    if (window.documentUploadHandlersInitialized) {
        console.log('⚠️ Обработчики загрузки документов уже инициализированы');
        return;
    }
    
    console.log('📄 Инициализация обработчиков загрузки документов...');
    window.documentUploadHandlersInitialized = true;
    
    const uploadZone = document.getElementById('documentUploadZone');
    const fileInput = document.getElementById('documentFileInput');
    const fileList = document.getElementById('documentFileList');
    const fileItems = document.getElementById('documentFileItems');
    const uploadBtn = document.getElementById('uploadDocumentBtn');
    let selectedFiles = [];

    // Удаляем старые обработчики если они есть
    if (uploadZone._documentHandlersAttached) {
        console.log('🧹 Удаляем старые обработчики документов...');
        uploadZone.replaceWith(uploadZone.cloneNode(true));
    }

    uploadZone._documentHandlersAttached = true;

    // Обработчики drag & drop
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', function(e) {
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
    const selectButton = document.getElementById('selectDocumentFilesBtn');
    if (selectButton && !selectButton._documentClickHandlerAttached) {
        selectButton._documentClickHandlerAttached = true;
        selectButton.addEventListener('click', function() {
            fileInput.click();
        });
    }

    // Обработчик загрузки
    if (uploadBtn && !uploadBtn._documentUploadHandlerAttached) {
        uploadBtn._documentUploadHandlerAttached = true;
        uploadBtn.addEventListener('click', function() {
            uploadDocuments();
        });
    }

    // Обработка выбранных файлов
    function handleFileSelection(files) {
        console.log('📄 Выбрано файлов документов:', files.length);
        
        // Фильтруем только допустимые типы файлов
        const allowedTypes = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.txt', '.rtf'];
        const validFiles = files.filter(file => {
            const extension = '.' + file.name.split('.').pop().toLowerCase();
            return allowedTypes.includes(extension);
        });

        if (validFiles.length !== files.length) {
            alert('Некоторые файлы имеют недопустимый формат и были исключены.');
        }

        selectedFiles = validFiles;
        displaySelectedFiles();
    }

    // Отображение выбранных файлов
    function displaySelectedFiles() {
        if (selectedFiles.length === 0) {
            fileList.classList.add('d-none');
            uploadBtn.disabled = true;
            return;
        }

        fileItems.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-icon">
                    <i class="${getDocumentIcon(file.type, file.name)} text-primary"></i>
                </div>
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size">${formatFileSize(file.size)}</div>
                </div>
                <div class="file-actions">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDocumentFile(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            fileItems.appendChild(fileItem);
        });

        fileList.classList.remove('d-none');
        uploadBtn.disabled = false;
    }

    // Получение иконки для файла документа
    function getDocumentIcon(fileType, fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        
        switch (extension) {
            case 'pdf':
                return 'bi bi-file-earmark-pdf';
            case 'doc':
            case 'docx':
                return 'bi bi-file-earmark-word';
            case 'xls':
            case 'xlsx':
                return 'bi bi-file-earmark-excel';
            case 'txt':
                return 'bi bi-file-earmark-text';
            case 'rtf':
                return 'bi bi-file-earmark-richtext';
            default:
                return 'bi bi-file-earmark';
        }
    }

    // Удаление файла из списка
    window.removeDocumentFile = function(index) {
        selectedFiles.splice(index, 1);
        displaySelectedFiles();
    };

    // Форматирование размера файла
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Загрузка документов
    function uploadDocuments() {
        if (selectedFiles.length === 0) {
            alert('Выберите файлы для загрузки');
            return;
        }

        const projectId = $('#documentProjectId').val();
        if (!projectId) {
            alert('Ошибка: не найден ID проекта');
            return;
        }

        const formData = new FormData();
        formData.append('project_id', projectId);
        formData.append('document_type', $('#documentTypeSelect').val() || $('#customDocumentType').val());
        formData.append('category', $('#documentCategorySelect').val() || $('#customDocumentCategory').val());
        formData.append('description', $('#documentDescription').val());

        selectedFiles.forEach(file => {
            formData.append('documents[]', file);
        });

        showDocumentUploadProgress();
        uploadBtn.disabled = true;

        $.ajax({
            url: `/partner/projects/${projectId}/documents/upload`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        updateDocumentProgress(percentComplete);
                    }
                });
                return xhr;
            },
            success: function(response) {
                console.log('✅ Документы успешно загружены:', response);
                hideDocumentUploadProgress();
                
                // Закрываем модальное окно
                $('#uploadDocumentModal').modal('hide');
                
                // Показываем уведомление об успехе
                if (typeof showNotification === 'function') {
                    showNotification('Документы успешно загружены', 'success');
                } else {
                    alert('Документы успешно загружены');
                }
                
                // Обновляем страницу или раздел с документами
                if (typeof refreshDocuments === 'function') {
                    refreshDocuments();
                } else {
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Ошибка при загрузке документов:', error);
                hideDocumentUploadProgress();
                uploadBtn.disabled = false;
                
                const errorMessage = xhr.responseJSON?.message || 'Произошла ошибка при загрузке документов';
                if (typeof showNotification === 'function') {
                    showNotification(errorMessage, 'error');
                } else {
                    alert(errorMessage);
                }
            }
        });
    }

    console.log('✅ Обработчики загрузки документов инициализированы');
}

// Функции для управления кастомными полями типа документа
function handleDocumentTypeChange() {
    const select = document.getElementById('documentTypeSelect');
    const customBtn = document.getElementById('customDocumentTypeBtn');
    const customInput = document.getElementById('customDocumentType');
    
    if (select.value === 'other') {
        customBtn.style.display = 'block';
        customInput.classList.remove('d-none');
        customInput.required = true;
    } else {
        customBtn.style.display = 'none';
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = '';
    }
}

function toggleCustomDocumentType() {
    const select = document.getElementById('documentTypeSelect');
    const customInput = document.getElementById('customDocumentType');
    
    if (customInput.classList.contains('d-none')) {
        select.value = 'other';
        customInput.classList.remove('d-none');
        customInput.focus();
        customInput.required = true;
    } else {
        select.value = '';
        customInput.classList.add('d-none');
        customInput.value = '';
        customInput.required = false;
    }
}

// Функции для управления кастомными полями категории
function handleDocumentCategoryChange() {
    const select = document.getElementById('documentCategorySelect');
    const customBtn = document.getElementById('customDocumentCategoryBtn');
    const customInput = document.getElementById('customDocumentCategory');
    
    if (select.value === 'other') {
        customBtn.style.display = 'block';
        customInput.classList.remove('d-none');
        customInput.required = true;
    } else {
        customBtn.style.display = 'none';
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = '';
    }
}

function toggleCustomDocumentCategory() {
    const select = document.getElementById('documentCategorySelect');
    const customInput = document.getElementById('customDocumentCategory');
    
    if (customInput.classList.contains('d-none')) {
        select.value = 'other';
        customInput.classList.remove('d-none');
        customInput.focus();
        customInput.required = true;
    } else {
        select.value = '';
        customInput.classList.add('d-none');
        customInput.value = '';
        customInput.required = false;
    }
}

// Функции для управления прогрессом загрузки
function showDocumentUploadProgress() {
    const progressContainer = document.getElementById('documentUploadProgress');
    const progressBar = progressContainer.querySelector('.progress-bar');
    const progressText = document.getElementById('documentProgressText');
    
    progressContainer.classList.remove('d-none');
    progressBar.style.width = '0%';
    progressText.textContent = 'Начало загрузки...';
}

function updateDocumentProgress(percent) {
    const progressBar = document.querySelector('#documentUploadProgress .progress-bar');
    const progressText = document.getElementById('documentProgressText');
    
    progressBar.style.width = percent + '%';
    progressText.textContent = `Загружено ${Math.round(percent)}%`;
}

function hideDocumentUploadProgress() {
    const progressContainer = document.getElementById('documentUploadProgress');
    progressContainer.classList.add('d-none');
}
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/modals/document-modal.blade.php ENDPATH**/ ?>