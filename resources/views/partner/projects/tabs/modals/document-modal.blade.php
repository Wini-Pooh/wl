<!-- Модальное окно для загрузки документов (уникальное для страницы документов) -->
<div class="modal fade" id="documentPageModal" tabindex="-1" aria-labelledby="documentPageModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentPageModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Загрузить документы
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDocumentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="documentProjectId" value="{{ $project->id }}">
                    
                    <!-- Зона загрузки файлов -->
                    <div class="upload-zone" id="documentUploadZone">
                        <div class="upload-content">
                            <i class="bi bi-file-earmark-arrow-up display-4 text-muted mb-3"></i>
                            <h5>Перетащите документы сюда</h5>
                            <p class="text-muted mb-3">или нажмите для выбора файлов</p>
                            <input type="file" id="documentFileInput" name="documents[]" multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.rtf,.odt,.ods" class="d-none">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('documentFileInput').click()">
                                <i class="bi bi-plus-lg me-1"></i>Выбрать документы
                            </button>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Поддерживаемые форматы: PDF, DOC, DOCX, XLS, XLSX, TXT, RTF, ODT, ODS
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Список выбранных файлов -->
                    <div id="documentFileList" class="file-list mt-4" style="display: none;">
                        <h6>Выбранные документы:</h6>
                        <div id="documentFileItems"></div>
                    </div>
                    
                    <!-- Дополнительные параметры -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="documentType" class="form-label">Тип документа</label>
                            <div class="input-group">
                                <select class="form-select" id="documentTypeSelect" onchange="handleDocumentTypeChange()">
                                    <option value="">Выберите тип</option>
                                    <option value="contract">Договор</option>
                                    <option value="estimate">Смета</option>
                                    <option value="plan">План/чертеж</option>
                                    <option value="permit">Разрешение</option>
                                    <option value="technical">Техническая документация</option>
                                    <option value="invoice">Счет</option>
                                    <option value="act">Акт</option>
                                    <option value="certificate">Сертификат</option>
                                    <option value="photo_report">Фотоотчет</option>
                                    <option value="correspondence">Переписка</option>
                                    <option value="other">Другое</option>
                                    <option value="custom">Свой тип</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleCustomDocumentType()" title="Ввести свой тип документа">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="documentType" name="document_type" 
                                   placeholder="Введите свой тип документа..." style="display: none;">
                        </div>
                        <div class="col-md-6">
                            <label for="documentImportance" class="form-label">Важность</label>
                            <select class="form-select" id="documentImportance" name="importance">
                                <option value="normal">Обычная</option>
                                <option value="high">Высокая</option>
                                <option value="urgent">Срочная</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="documentDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="documentDescription" name="description" rows="3" 
                                  placeholder="Добавьте описание к документам..."></textarea>
                    </div>
                    
                    <!-- Прогресс загрузки -->
                    <div id="documentUploadProgress" class="mt-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Загрузка документов...</span>
                            <span id="documentProgressText">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="documentProgressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="uploadDocumentBtn" disabled>
                    <i class="bi bi-upload me-1"></i>Загрузить документы
                </button>
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
        console.log('📄 Инициализация модального окна документов...');
        
        // Проверяем и устанавливаем project ID
        const projectId = window.projectId || 
                         $('meta[name="project-id"]').attr('content') || 
                         $('#projectId').val() ||
                         $('[data-project-id]').data('project-id') ||
                         {{ $project->id ?? 'null' }};
        
        if (projectId && projectId !== 'null') {
            $('#documentProjectId').val(projectId);
            console.log('📄 Project ID установлен:', projectId);
        } else {
            console.error('❌ Project ID не найден для модального окна документов');
        }
        
        if (window.projectManager) {
            // Используем унифицированную систему инициализации модалов
            window.projectManager.initModal('documentPageModal', 'document', function() {
                console.log('✅ Модал документов инициализирован через ProjectManager');
                initDocumentModalHandlers();
            });
        } else {
            console.warn('⚠️ ProjectManager не найден, используем fallback инициализацию');
            initDocumentModalHandlers();
        }
    });
}

function initDocumentModalHandlers() {
    console.log('📄 Инициализация обработчиков документов...');
    
    // Проверяем, не были ли уже инициализированы обработчики
    if (window.documentUploadHandlersInitialized) {
        console.log('ℹ️ Обработчики документов уже инициализированы, пропускаем');
        return;
    }
    
    initDocumentUploadHandlers();
    
    // Отмечаем, что обработчики инициализированы
    window.documentUploadHandlersInitialized = true;
    console.log('✅ Обработчики модала документов инициализированы');
}

function initDocumentUploadHandlers() {
    console.log('📄 Инициализация обработчиков загрузки документов...');
    
    const uploadZone = document.getElementById('documentUploadZone');
    const fileInput = document.getElementById('documentFileInput');
    const fileList = document.getElementById('documentFileList');
    const fileItems = document.getElementById('documentFileItems');
    const uploadBtn = document.getElementById('uploadDocumentBtn');
    
    if (!uploadZone || !fileInput || !fileList || !fileItems || !uploadBtn) {
        console.error('❌ Не найдены необходимые элементы для инициализации загрузки документов');
        return;
    }
    
    let selectedFiles = [];

    // ПОЛНАЯ ОЧИСТКА старых обработчиков с заменой элементов
    console.log('🧹 Полная очистка обработчиков файлов документов...');
    
    // Клонируем элементы для полной очистки обработчиков
    const cleanUploadZone = uploadZone.cloneNode(true);
    const cleanFileInput = fileInput.cloneNode(true);
    const cleanUploadBtn = uploadBtn.cloneNode(true);
    
    uploadZone.parentNode.replaceChild(cleanUploadZone, uploadZone);
    fileInput.parentNode.replaceChild(cleanFileInput, fileInput);
    uploadBtn.parentNode.replaceChild(cleanUploadBtn, uploadBtn);

    // Получаем ссылки на новые элементы
    const newUploadZone = document.getElementById('documentUploadZone');
    const newFileInput = document.getElementById('documentFileInput');
    const newUploadBtn = document.getElementById('uploadDocumentBtn');

    console.log('✅ Элементы документов очищены и заменены');

    // Drag & Drop обработчики
    newUploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        newUploadZone.classList.add('dragover');
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
        
        const files = Array.from(e.dataTransfer.files).filter(file => isValidDocumentFile(file));
        console.log('📂 Document files dropped:', files.length);
        handleFileSelection(files);
    });

    // Обработчик выбора файлов через input
    newFileInput.addEventListener('change', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const files = Array.from(e.target.files);
        console.log('📂 Document files selected via input:', files.length);
        handleFileSelection(files);
    });

    // Обработчик клика по зоне загрузки
    newUploadZone.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('📂 Document upload zone clicked');
        newFileInput.click();
    });

    // Обработчик кнопки "Выбрать документы"
    const selectBtn = newUploadZone.querySelector('button');
    if (selectBtn) {
        selectBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('📂 Document select button clicked');
            newFileInput.click();
        });
    }

    function isValidDocumentFile(file) {
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'application/rtf',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet'
        ];
        return allowedTypes.includes(file.type);
    }

    function handleFileSelection(files) {
        console.log('📄 Обработка выбранных документов:', files.length);
        
        if (files.length === 0) {
            console.log('ℹ️ Документы не выбраны');
            return;
        }
        
        selectedFiles = files;
        displaySelectedFiles();
        newUploadBtn.disabled = false;
        
        console.log('✅ Документы обработаны:', selectedFiles.length);
    }

    function displaySelectedFiles() {
        console.log('📋 Отображение выбранных документов...');
        
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
                    <i class="bi bi-file-earmark me-2"></i>
                    <div>
                        <div class="file-name">${file.name}</div>
                        <div class="file-size text-muted">${formatFileSize(file.size)}</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDocumentFile(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            fileItems.appendChild(fileItem);
        });
    }

    // Глобальная функция для удаления документа
    window.removeDocumentFile = function(index) {
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

    // Обработчик загрузки документов
    newUploadBtn.addEventListener('click', function() {
        if (selectedFiles.length === 0) {
            console.log('❌ Нет документов для загрузки');
            return;
        }
        
        console.log('🚀 Начинаем загрузку документов:', selectedFiles.length);
        uploadDocuments();
    });

    function uploadDocuments() {
        const projectId = $('#documentProjectId').val();
        
        if (!projectId) {
            console.error('❌ Project ID не найден для документов');
            alert('Ошибка: ID проекта не найден');
            return;
        }
        
        console.log('📤 Загружаем документы для проекта:', projectId);
        
        const formData = new FormData();
        formData.append('project_id', projectId);
        formData.append('document_type', $('#documentType').val() || $('#documentTypeSelect').val());
        formData.append('importance', $('#documentImportance').val());
        formData.append('description', $('#documentDescription').val());
        
        selectedFiles.forEach(file => {
            formData.append('documents[]', file);
        });
        
        // Показываем прогресс (имитация)
        const progressContainer = document.getElementById('documentUploadProgress');
        const progressBar = document.getElementById('documentProgressBar');
        const progressText = document.getElementById('documentProgressText');
        
        progressContainer.style.display = 'block';
        newUploadBtn.disabled = true;
        
        // Простое уведомление вместо AJAX запроса
        setTimeout(() => {
            console.log('✅ Загрузка документов временно отключена');
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('documentPageModal'));
            if (modal) {
                modal.hide();
            }
            
            // Показываем уведомление
            alert('Функция загрузки документов временно отключена');
            
            // Перезагружаем список документов
            if (window.loadDocuments) {
                window.loadDocuments();
            } else if (window.location.pathname.includes('/documents')) {
                // window.location.reload(); // Отключаем перезагрузку страницы
            }
            
            // Очищаем форму
            selectedFiles = [];
            displaySelectedFiles();
            document.getElementById('uploadDocumentForm').reset();
            
            // Скрываем прогресс
            progressContainer.style.display = 'none';
            newUploadBtn.disabled = false;
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
        }, 1000);
    }
    
    console.log('✅ Обработчики загрузки документов инициализированы');
}
        displaySelectedFiles();
        uploadBtn.disabled = files.length === 0;
    }

    function displaySelectedFiles() {
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
                    <i class="bi bi-${getDocumentIcon(file.type)} text-primary"></i>
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
    }

    function getDocumentIcon(fileType) {
        switch (fileType) {
            case 'application/pdf':
                return 'file-earmark-pdf';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'file-earmark-word';
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return 'file-earmark-excel';
            case 'text/plain':
                return 'file-earmark-text';
            default:
                return 'file-earmark';
        }
    }

    window.removeDocumentFile = function(index) {
        selectedFiles.splice(index, 1);
        displaySelectedFiles();
        uploadBtn.disabled = selectedFiles.length === 0;
        
        // Обновляем input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    };

    // Обработчик загрузки
    uploadBtn.addEventListener('click', function() {
        if (selectedFiles.length === 0) {
            return;
        }

        uploadDocuments();
    });

    function uploadDocuments() {
        console.log('📤 Начинаем загрузку документов...');
        
        const formData = new FormData();
        const projectId = $('#documentProjectId').val();
        
        if (!projectId) {
            console.error('❌ Project ID не найден');
            if (window.modalManager) {
                window.modalManager.showErrorToast('Ошибка: ID проекта не найден');
            }
            return;
        }
        
        // Добавляем все данные в FormData
        formData.append('project_id', projectId);
        
        // Получаем тип документа (кастомный или из select)
        const documentTypeInput = $('#documentType');
        const documentTypeSelect = $('#documentTypeSelect');
        let documentType = '';
        
        if (documentTypeSelect.val() === 'custom' && documentTypeInput.val()) {
            documentType = documentTypeInput.val();
        } else if (documentTypeSelect.val() && documentTypeSelect.val() !== 'custom') {
            documentType = documentTypeSelect.val();
        }
        
        formData.append('document_type', documentType);
        formData.append('importance', $('#documentImportance').val());
        formData.append('description', $('#documentDescription').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Добавляем файлы
        selectedFiles.forEach((file, index) => {
            formData.append('documents[]', file);
        });

        // Показываем прогресс (имитация)
        showDocumentUploadProgress();
        
        // Отключаем кнопку загрузки
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Загрузка...';

        // Простое уведомление вместо AJAX запроса
        setTimeout(() => {
            console.log('✅ Загрузка документов временно отключена');
            
            hideDocumentUploadProgress();
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i>Загрузить документы';
            
            if (window.modalManager) {
                window.modalManager.closeActiveModal();
                window.modalManager.showToast('Функция загрузки документов временно отключена', 'info');
            } else {
                alert('Функция загрузки документов временно отключена');
            }
            
            // Перезагружаем документы на странице
            if (typeof window.reloadDocuments === 'function') {
                window.reloadDocuments();
            }
        }, 1500);
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (window.modalManager) {
                    window.modalManager.showErrorToast(errorMessage);
                }
            }
        });
    }

    function showDocumentUploadProgress() {
        document.getElementById('documentUploadProgress').style.display = 'block';
    }

    function hideDocumentUploadProgress() {
        document.getElementById('documentUploadProgress').style.display = 'none';
    }

    function updateDocumentUploadProgress(percent) {
        const progressBar = document.getElementById('documentProgressBar');
        const progressText = document.getElementById('documentProgressText');
        
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

// Функции для управления кастомными полями типа документа
function handleDocumentTypeChange() {
    const select = document.getElementById('documentTypeSelect');
    const input = document.getElementById('documentType');
    
    if (select.value === 'custom') {
        toggleCustomDocumentType();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function toggleCustomDocumentType() {
    const select = document.getElementById('documentTypeSelect');
    const input = document.getElementById('documentType');
    
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
