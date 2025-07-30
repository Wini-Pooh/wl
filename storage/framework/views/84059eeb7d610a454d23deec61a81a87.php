<!-- Документы проекта -->
<div id="documents-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Документы проекта (<span id="documentCount">0</span>)</h5>
        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <button class="btn btn-primary" data-modal-type="document">
            <i class="bi bi-plus-lg me-2"></i>Загрузить документ
        </button>
        <?php endif; ?>
    </div>

    <!-- Улучшенные фильтры с AJAX -->
    <div class="card mb-4" id="documentFiltersCard">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Фильтры
                <span id="activeDocumentFiltersCount" class="badge bg-primary ms-2" style="display: none;">0</span>
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleDocumentFiltersBtn" title="Свернуть/развернуть фильтры">
                <i class="bi bi-chevron-up" id="toggleDocumentFiltersIcon"></i>
            </button>
        </div>
        <div class="card-body" id="documentFiltersBody">
            <form id="documentFilterForm" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Поиск по названию -->
                    <div class="col-md-4">
                        <label for="documentSearchFilter" class="form-label">Поиск по названию</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="documentSearchFilter" name="search" placeholder="Введите название документа">
                            <button class="btn btn-outline-secondary" type="button" id="clearDocumentSearchBtn" title="Очистить поиск">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Тип документа -->
                    <div class="col-md-3">
                        <label for="documentTypeFilter" class="form-label">Тип документа</label>
                        <select class="form-select" id="documentTypeFilter" name="document_type">
                            <option value="">Все типы</option>
                            <option value="contract">Договор</option>
                            <option value="estimate">Смета</option>
                            <option value="invoice">Счет</option>
                            <option value="act">Акт</option>
                            <option value="technical">Технический документ</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    
                    <!-- Статус -->
                    <div class="col-md-3">
                        <label for="documentStatusFilter" class="form-label">Статус</label>
                        <select class="form-select" id="documentStatusFilter" name="status">
                            <option value="">Все статусы</option>
                            <option value="active">Активный</option>
                            <option value="draft">Черновик</option>
                            <option value="archived">Архивный</option>
                            <option value="signed">Подписан</option>
                        </select>
                    </div>
                    
                    <!-- Кнопки управления фильтрами -->
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="clearDocumentFilters" title="Сбросить все фильтры">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button type="submit" class="btn btn-primary" id="applyDocumentFilters" title="Применить фильтры">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Дополнительные фильтры (расширенные) -->
                <div class="row g-3 mt-2" id="advancedDocumentFilters" style="display: none;">
                    <!-- Сортировка -->
                    <div class="col-md-3">
                        <label for="documentSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="documentSortFilter" name="sort">
                            <option value="newest">Сначала новые</option>
                            <option value="oldest">Сначала старые</option>
                            <option value="name_asc">По имени (А-Я)</option>
                            <option value="name_desc">По имени (Я-А)</option>
                            <option value="size_asc">По размеру (возрастание)</option>
                            <option value="size_desc">По размеру (убывание)</option>
                        </select>
                    </div>
                    
                    <!-- Дата создания от -->
                    <div class="col-md-3">
                        <label for="documentDateFromFilter" class="form-label">Дата создания от</label>
                        <input type="date" class="form-control" id="documentDateFromFilter" name="date_from">
                    </div>
                    
                    <!-- Дата создания до -->
                    <div class="col-md-3">
                        <label for="documentDateToFilter" class="form-label">Дата создания до</label>
                        <input type="date" class="form-control" id="documentDateToFilter" name="date_to">
                    </div>
                    
                    <!-- Размер файла -->
                    <div class="col-md-3">
                        <label for="documentSizeFilter" class="form-label">Размер файла</label>
                        <select class="form-select" id="documentSizeFilter" name="size_filter">
                            <option value="">Любой размер</option>
                            <option value="small">Небольшие (< 1 МБ)</option>
                            <option value="medium">Средние (1-10 МБ)</option>
                            <option value="large">Крупные (> 10 МБ)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Кнопка для показа расширенных фильтров -->
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleAdvancedDocumentFilters">
                            <i class="bi bi-chevron-down me-1"></i>
                            <span>Дополнительные фильтры</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Область для индикатора загрузки -->
    <div id="documentLoadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
        <p class="mt-2 text-muted">Загрузка документов...</p>
    </div>

    <!-- Список документов -->
    <div id="documentsGallery" class="row">
        <!-- Сюда будут загружены карточки документов через AJAX -->
    </div>

    <!-- Пустое состояние -->
    <div id="emptyDocumentState" class="text-center py-5" style="display: none;">
        <i class="bi bi-file-earmark display-1 text-muted"></i>
        <h5 class="mt-3">Нет документов</h5>
        <p class="text-muted">Загрузите документы проекта, нажав кнопку "Загрузить документ" вверху страницы</p>
    </div>
</div>

<style>
/* Улучшенные стили для карточек документов */
.document-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    height: 100%;
    border: 1px solid rgba(0,0,0,0.05);
}

.document-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.document-preview {
    height: 200px;
    background-color: #f8f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.document-preview i {
    font-size: 3rem;
    color: #6c757d;
    transition: all 0.3s ease;
}

.document-card:hover .document-preview i {
    transform: scale(1.1);
    color: #495057;
}

.document-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.document-card:hover .document-overlay {
    opacity: 1;
}

.document-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
}

.document-badge .badge {
    font-size: 0.7rem;
    padding: 0.4em 0.6em;
    border-radius: 6px;
}

.document-actions {
    position: absolute;
    bottom: 10px;
    right: 10px;
    z-index: 2;
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    gap: 5px;
}

.document-card:hover .document-actions {
    opacity: 1;
}

.document-actions .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    backdrop-filter: blur(5px);
    background-color: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.2s ease;
}

.document-actions .btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.document-actions .btn-danger {
    background-color: rgba(220, 53, 69, 0.9);
    color: white;
    border-color: rgba(220, 53, 69, 0.3);
}

.document-actions .btn-danger:hover {
    background-color: rgba(220, 53, 69, 1);
    color: white;
}

/* Улучшенные стили для фильтров */
#documentFiltersCard {
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    border-radius: 12px;
    overflow: hidden;
}

#documentFiltersCard .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid rgba(0,0,0,0.08);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-select, .form-control {
    border: 1px solid #ced4da;
    border-radius: 8px;
    transition: all 0.2s ease;
    background-color: #fff;
}

.form-select:focus, .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    background-color: #fff;
}

.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 8px 0 0 8px;
}

#clearDocumentSearchBtn {
    border-radius: 0 8px 8px 0;
}

/* Индикатор загрузки */
#documentLoadingIndicator {
    background-color: rgba(248, 249, 250, 0.9);
    border-radius: 12px;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Пустое состояние */
#emptyDocumentState {
    background-color: #f8f9fa;
    border-radius: 12px;
    margin: 2rem 0;
}

#emptyDocumentState i {
    font-size: 4rem;
    color: #adb5bd;
}

/* Адаптивность */
@media (max-width: 768px) {
    .document-card {
        margin-bottom: 1rem;
    }
    
    .document-preview {
        height: 150px;
    }
    
    .form-label {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.375rem 0.5rem;
    }
}

@media (max-width: 576px) {
    .document-preview {
        height: 120px;
    }
    
    .document-preview i {
        font-size: 2rem;
    }
    
    .document-actions .btn {
        width: 28px;
        height: 28px;
    }
}

/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.document-card {
    animation: fadeInUp 0.6s ease forwards;
}

.document-card:nth-child(1) { animation-delay: 0.1s; }
.document-card:nth-child(2) { animation-delay: 0.2s; }
.document-card:nth-child(3) { animation-delay: 0.3s; }
.document-card:nth-child(4) { animation-delay: 0.4s; }

/* Улучшенные tooltips */
.tooltip {
    font-size: 0.875rem;
}

.tooltip-inner {
    background-color: rgba(0, 0, 0, 0.9);
    border-radius: 6px;
}

/* Счетчик активных фильтров */
#activeDocumentFiltersCount {
    font-size: 0.7rem;
    padding: 0.25em 0.5em;
}

/* Улучшенные кнопки */
.btn-group .btn {
    border-radius: 8px;
    margin-right: 2px;
}

.btn-group .btn:first-child {
    border-radius: 8px 0 0 8px;
    margin-right: 0;
}

.btn-group .btn:last-child {
    border-radius: 0 8px 8px 0;
    margin-right: 0;
}

/* Стили для расширенных фильтров */
#advancedDocumentFilters {
    border-top: 1px solid rgba(0,0,0,0.08);
    padding-top: 1rem;
    margin-top: 1rem !important;
}

#toggleAdvancedDocumentFilters {
    transition: all 0.2s ease;
}

#toggleAdvancedDocumentFilters:hover {
    transform: translateY(-1px);
}

#toggleAdvancedDocumentFilters i {
    transition: transform 0.2s ease;
}

#toggleAdvancedDocumentFilters.expanded i {
    transform: rotate(180deg);
}

/* Совместимость со старыми стилями */
.document-item {
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    padding: 15px;
}

.document-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
    border-left-color: #007bff;
}

.document-icon {
    width: 40px;
    height: 40px;
    background-color: #f8f9fa;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.document-item-contract .document-icon { background-color: #dff0d8; color: #3c763d; }
.document-item-estimate .document-icon { background-color: #d9edf7; color: #31708f; }
.document-item-invoice .document-icon { background-color: #fcf8e3; color: #8a6d3b; }
.document-item-act .document-icon { background-color: #f2dede; color: #a94442; }
.document-item-technical .document-icon { background-color: #e8eaf6; color: #3949ab; }
.document-item-other .document-icon { background-color: #f5f5f5; color: #777777; }

.document-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.document-status-active { background-color: #d4edda; color: #155724; }
.document-status-draft { background-color: #f8f9fa; color: #6c757d; }
.document-status-archived { background-color: #e2e3e5; color: #383d41; }
.document-status-signed { background-color: #cce5ff; color: #004085; }
</style>

<script>
(function() {
    'use strict';
    
    // Улучшенный менеджер документов с современным подходом к фильтрации
    const DocumentManagerFixed = {
        projectId: null,
        data: [],
        filteredData: [],
        filters: {},
        currentPage: 1,
        perPage: 12,
        totalPages: 1,
        initialized: false,
        isLoading: false,
        
        // Инициализация менеджера
        init: function() {
            console.log('=== ИНИЦИАЛИЗАЦИЯ DOCUMENT MANAGER FIXED ===');
            
            this.projectId = window.projectId;
            
            if (!this.projectId) {
                console.error('Project ID не найден');
                return;
            }
            
            if (this.initialized) {
                console.log('DocumentManagerFixed уже инициализирован, перезагружаем данные');
                this.loadFiles();
                return;
            }
            
            this.initialized = true;
            this.attachEventHandlers();
            this.loadFiles();
            
            console.log('DocumentManagerFixed инициализирован успешно для проекта:', this.projectId);
        },
        
        // Привязка обработчиков событий
        attachEventHandlers: function() {
            const self = this;
            
            // Обработчик формы фильтров
            $('#documentFilterForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                self.applyFilters();
            });
            
            // Быстрые фильтры (изменение select элементов)
            $(document).off('change', '#documentTypeFilter, #documentStatusFilter, #documentSortFilter')
                      .on('change', '#documentTypeFilter, #documentStatusFilter, #documentSortFilter', function() {
                self.applyFilters();
            });
            
            // Поиск в реальном времени
            let searchTimeout;
            $('#documentSearchFilter').off('input').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    self.applyFilters();
                }, 500);
            });
            
            // Очистка поиска
            $('#clearDocumentSearchBtn').off('click').on('click', function() {
                $('#documentSearchFilter').val('');
                self.applyFilters();
            });
            
            // Кнопка сброса всех фильтров
            $('#clearDocumentFilters').off('click').on('click', function() {
                self.resetFilters();
            });
            
            // Переключение видимости фильтров
            $('#toggleDocumentFiltersBtn').off('click').on('click', function() {
                self.toggleFiltersVisibility();
            });
            
            // Переключение расширенных фильтров
            $('#toggleAdvancedDocumentFilters').off('click').on('click', function() {
                self.toggleAdvancedFilters();
            });
            
            // Обработчики расширенных фильтров
            $(document).off('change', '#documentDateFromFilter, #documentDateToFilter, #documentSizeFilter')
                      .on('change', '#documentDateFromFilter, #documentDateToFilter, #documentSizeFilter', function() {
                self.applyFilters();
            });
            
            console.log('DocumentManagerFixed: обработчики событий прикреплены');
        },
        
        // Загрузка файлов с сервера
        loadFiles: function() {
            if (this.isLoading) {
                console.log('Загрузка уже в процессе, пропускаем');
                return;
            }
            
            this.isLoading = true;
            this.showLoadingIndicator();
            
            const params = this.getFilterParams();
            
            console.log('Загрузка документов с параметрами:', params);
            
            $.ajax({
                url: `/partner/projects/${this.projectId}/documents`,
                method: 'GET',
                data: params,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    console.log('Успешный ответ сервера:', response);
                    this.handleServerResponse(response);
                },
                error: (xhr) => {
                    console.error('Ошибка при загрузке документов:', xhr);
                    this.handleLoadError(xhr);
                },
                complete: () => {
                    this.isLoading = false;
                    this.hideLoadingIndicator();
                }
            });
        },
        
        // Обработка ответа сервера
        handleServerResponse: function(response) {
            let documents = [];
            
            // Обрабатываем различные форматы ответа
            if (response.success && Array.isArray(response.files)) {
                documents = response.files;
            } else if (response.success && Array.isArray(response.documents)) {
                documents = response.documents;
            } else if (response.success && Array.isArray(response.data)) {
                documents = response.data;
            } else if (Array.isArray(response)) {
                documents = response;
            } else if (response.files && response.files.data && Array.isArray(response.files.data)) {
                documents = response.files.data;
            } else {
                console.warn('Неожиданный формат ответа:', response);
                documents = [];
            }
            
            this.data = documents;
            this.filteredData = [...documents];
            
            console.log(`Загружено документов: ${documents.length}`);
            
            this.renderFiles();
            this.updateCounter();
            this.updateFilterStats();
        },
        
        // Обработка ошибки загрузки
        handleLoadError: function(xhr) {
            $('#documentsGallery').html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Ошибка при загрузке документов. Попробуйте обновить страницу.
                    </div>
                </div>
            `).show();
            
            this.showMessage('Ошибка при загрузке документов', 'error');
        },
        
        // Получение параметров фильтрации
        getFilterParams: function() {
            const params = {};
            
            // Основные фильтры
            const search = $('#documentSearchFilter').val();
            const type = $('#documentTypeFilter').val();
            const status = $('#documentStatusFilter').val();
            const sort = $('#documentSortFilter').val();
            
            // Расширенные фильтры
            const dateFrom = $('#documentDateFromFilter').val();
            const dateTo = $('#documentDateToFilter').val();
            const sizeFilter = $('#documentSizeFilter').val();
            
            if (search) params.search = search;
            if (type) params.document_type = type;
            if (status) params.status = status;
            if (sort) params.sort = sort;
            if (dateFrom) params.date_from = dateFrom;
            if (dateTo) params.date_to = dateTo;
            if (sizeFilter) params.size_filter = sizeFilter;
            
            return params;
        },
        
        // Применение фильтров
        applyFilters: function() {
            console.log('Применение фильтров документов');
            this.currentPage = 1;
            this.loadFiles();
        },
        
        // Сброс всех фильтров
        resetFilters: function() {
            console.log('Сброс фильтров документов');
            
            $('#documentFilterForm')[0].reset();
            $('#documentSearchFilter').val('');
            
            this.filters = {};
            this.currentPage = 1;
            this.loadFiles();
        },
        
        // Отрисовка файлов
        renderFiles: function() {
            const gallery = $('#documentsGallery');
            gallery.empty();
            
            if (!this.filteredData || this.filteredData.length === 0) {
                this.showEmptyState();
                return;
            }
            
            this.hideEmptyState();
            
            this.filteredData.forEach((document, index) => {
                const card = this.createDocumentCard(document);
                gallery.append(card);
            });
            
            console.log(`Отрисовано карточек документов: ${this.filteredData.length}`);
        },
        
        // Создание карточки документа
        createDocumentCard: function(document) {
            const documentType = this.getDocumentTypeName(document.document_type || document.category || document.type || 'other');
            const status = this.getDocumentStatusText(document.status || 'draft');
            const fileName = document.original_name || document.name || document.file_name || 'Документ';
            const fileUrl = document.url || document.download_url || '#';
            const fileSize = document.file_size || document.size || 0;
            const createdAt = document.created_at || document.upload_date || new Date().toISOString();
            
            // Проверяем роль пользователя для показа кнопки удаления
            const isClient = <?php echo json_encode(!(App\Helpers\UserRoleHelper::canSeeActionButtons()), 15, 512) ?>;
            const deleteButton = isClient ? '' : `
                <button class="btn btn-danger" onclick="DocumentManagerFixed.confirmDelete(${document.id})" title="Удалить">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            return `
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card document-card h-100" data-id="${document.id}">
                        <div class="document-badge">
                            <span class="badge bg-info">${documentType}</span>
                            <span class="badge bg-secondary ms-1">${status}</span>
                        </div>
                        <div class="document-preview">
                            <i class="bi ${this.getDocumentIcon(fileName)} display-1"></i>
                            <h6 class="mt-2 px-2 text-truncate" title="${fileName}">${fileName}</h6>
                        </div>
                        <div class="document-overlay"></div>
                        <div class="document-actions">
                            <a href="${fileUrl}" class="btn btn-primary" download="${fileName}" title="Скачать">
                                <i class="bi bi-download"></i>
                            </a>
                            ${deleteButton}
                        </div>
                        <div class="card-body p-2">
                            <small class="text-muted">
                                ${this.formatFileSize(fileSize)} • ${this.formatDate(createdAt)}
                            </small>
                        </div>
                    </div>
                </div>
            `;
        },
        
        // Получение иконки документа по типу файла
        getDocumentIcon: function(filename) {
            const ext = this.getFileExtension(filename).toLowerCase();
            
            const iconMap = {
                'pdf': 'bi-file-earmark-pdf',
                'doc': 'bi-file-earmark-word',
                'docx': 'bi-file-earmark-word',
                'xls': 'bi-file-earmark-excel',
                'xlsx': 'bi-file-earmark-excel',
                'ppt': 'bi-file-earmark-ppt',
                'pptx': 'bi-file-earmark-ppt',
                'txt': 'bi-file-earmark-text',
                'zip': 'bi-file-earmark-zip',
                'rar': 'bi-file-earmark-zip',
                '7z': 'bi-file-earmark-zip'
            };
            
            return iconMap[ext] || 'bi-file-earmark';
        },
        
        // Получение расширения файла
        getFileExtension: function(filename) {
            return filename.split('.').pop() || '';
        },
        
        // Получение названия типа документа
        getDocumentTypeName: function(type) {
            const types = {
                'contract': 'Договор',
                'estimate': 'Смета',
                'invoice': 'Счет',
                'act': 'Акт',
                'technical': 'Технический документ',
                'other': 'Другое'
            };
            return types[type] || 'Другое';
        },
        
        // Получение текста статуса
        getDocumentStatusText: function(status) {
            const statuses = {
                'active': 'Активный',
                'draft': 'Черновик',
                'archived': 'Архивный',
                'signed': 'Подписан'
            };
            return statuses[status] || 'Черновик';
        },
        
        // Форматирование размера файла
        formatFileSize: function(bytes) {
            if (!bytes) return '0 Б';
            
            const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        },
        
        // Форматирование даты
        formatDate: function(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(date);
        },
        
        // Переключение видимости фильтров
        toggleFiltersVisibility: function() {
            const filtersBody = $('#documentFiltersBody');
            const icon = $('#toggleDocumentFiltersIcon');
            
            if (filtersBody.is(':visible')) {
                filtersBody.slideUp(300);
                icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
            } else {
                filtersBody.slideDown(300);
                icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
            }
        },
        
        // Переключение расширенных фильтров
        toggleAdvancedFilters: function() {
            const advancedFilters = $('#advancedDocumentFilters');
            const button = $('#toggleAdvancedDocumentFilters');
            const icon = button.find('i');
            const text = button.find('span');
            
            if (advancedFilters.is(':visible')) {
                advancedFilters.slideUp(300);
                icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                text.text('Дополнительные фильтры');
                button.removeClass('expanded');
            } else {
                advancedFilters.slideDown(300);
                icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                text.text('Скрыть фильтры');
                button.addClass('expanded');
            }
        },
        
        // Обновление счетчика документов
        updateCounter: function() {
            const count = this.filteredData ? this.filteredData.length : 0;
            $('#documentCount').text(count);
        },
        
        // Обновление статистики фильтров
        updateFilterStats: function() {
            const activeFilters = Object.keys(this.getFilterParams()).length;
            const badge = $('#activeDocumentFiltersCount');
            
            if (activeFilters > 0) {
                badge.text(activeFilters).show();
            } else {
                badge.hide();
            }
        },
        
        // Показать индикатор загрузки
        showLoadingIndicator: function() {
            $('#documentLoadingIndicator').show();
            $('#documentsGallery').hide();
            $('#emptyDocumentState').hide();
        },
        
        // Скрыть индикатор загрузки
        hideLoadingIndicator: function() {
            $('#documentLoadingIndicator').hide();
            $('#documentsGallery').show();
        },
        
        // Показать пустое состояние
        showEmptyState: function(isError = false) {
            $('#documentsGallery').hide();
            $('#emptyDocumentState').show();
            
            if (isError) {
                $('#emptyDocumentState').html(`
                    <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                    <h5 class="mt-3">Ошибка загрузки</h5>
                    <p class="text-muted">Произошла ошибка при загрузке документов</p>
                `);
            } else {
                $('#emptyDocumentState').html(`
                    <i class="bi bi-file-earmark display-1 text-muted"></i>
                    <h5 class="mt-3">Нет документов</h5>
                    <p class="text-muted">Документы не найдены или еще не загружены</p>
                    <button class="btn btn-primary" data-modal-type="document">
                        <i class="bi bi-plus-lg me-1"></i>Загрузить документ
                    </button>
                `);
            }
        },
        
        // Скрыть пустое состояние
        hideEmptyState: function() {
            $('#emptyDocumentState').hide();
        },
        
        // Подтверждение удаления
        confirmDelete: function(documentId) {
            if (confirm('Вы уверены, что хотите удалить этот документ? Это действие нельзя отменить.')) {
                this.deleteFile(documentId);
            }
        },
        
        // Удаление файла
        deleteFile: function(documentId) {
            console.log('Удаление документа:', documentId);
            
            $.ajax({
                url: `/partner/projects/${this.projectId}/documents/${documentId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    this.showMessage('Документ успешно удален', 'success');
                    this.loadFiles();
                },
                error: (xhr) => {
                    console.error('Ошибка при удалении документа:', xhr);
                    this.showMessage('Ошибка при удалении документа', 'error');
                }
            });
        },
        
        // Показать сообщение
        showMessage: function(message, type = 'info') {
            if (typeof window.showMessage === 'function') {
                window.showMessage(message, type);
            } else {
                console.log(`${type.toUpperCase()}: ${message}`);
            }
        }
    };
    
    // Экспорт в глобальную область видимости
    window.DocumentManagerFixed = DocumentManagerFixed;
    
    // Обратная совместимость
    window.DocumentManager = DocumentManagerFixed;
    
    console.log('Document Manager Fixed loaded successfully - v2.0');
})();

// Инициализация при активации вкладки документов
$(document).ready(function() {
    console.log('=== ИНИЦИАЛИЗАЦИЯ ДОКУМЕНТОВ ===');
    
    // Функция инициализации документов
    function initDocuments() {
        console.log('🚀 Инициализация менеджера документов');
        if (typeof window.DocumentManagerFixed !== 'undefined') {
            window.DocumentManagerFixed.init();
            // Загружаем документы сразу после инициализации
            setTimeout(() => {
                if (window.DocumentManagerFixed.projectId) {
                    console.log('📁 Автозагрузка документов');
                    window.DocumentManagerFixed.loadFiles();
                }
            }, 100);
        }
    }
    
    // Обработчик переключения на вкладку документов
    $('button[data-bs-target="#documents-content"], a[data-bs-target="#documents-content"], a[href="#documents"], button[data-bs-target="#documents"]').on('shown.bs.tab', function(e) {
        console.log('🔄 Вкладка документов активирована');
        initDocuments();
    });
    
    // Универсальный обработчик для различных селекторов вкладок
    $(document).on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href') || $(e.target).data('bs-target');
        if (target && (target.includes('documents') || target === '#documents-content' || target === '#documents')) {
            console.log('🔄 Обнаружено переключение на вкладку документов:', target);
            initDocuments();
        }
    });
    
    // Проверяем, активна ли вкладка документов при загрузке страницы
    if ($('#documents-tab').hasClass('active') || 
        $('#documents-content').hasClass('active show') ||
        $('[href="#documents"]').hasClass('active') ||
        window.location.hash === '#documents' ||
        window.location.pathname.includes('documents')) {
        console.log('📄 Вкладка документов активна при загрузке, инициализируем');
        initDocuments();
    }
    
    // Дополнительная проверка через 500мс для случаев медленной инициализации
    setTimeout(() => {
        if ($('#documents-tab-content').is(':visible') && !window.DocumentManagerFixed?.initialized) {
            console.log('🔄 Дополнительная инициализация документов');
            initDocuments();
        }
    }, 500);
});
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/documents.blade.php ENDPATH**/ ?>