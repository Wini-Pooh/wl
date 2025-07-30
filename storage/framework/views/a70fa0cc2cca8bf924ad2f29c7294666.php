<!-- Дизайн проекта -->
<div id="design-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-paint-bucket me-2"></i>Дизайн проекта (<span id="designCount">0</span>)</h5>
        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <button class="btn btn-primary" data-modal-type="design">
            <i class="bi bi-plus-lg me-2"></i>Загрузить дизайн
        </button>
        <?php endif; ?>
    </div>

    <!-- Улучшенные фильтры с AJAX -->
    <div class="card mb-4" id="designFiltersCard">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Фильтры и поиск
                <small class="text-muted ms-2" id="activeFiltersCount"></small>
            </h6>
        </div>
        <div class="card-body">
            <form id="designFilterForm" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Поиск по названию -->
                    <div class="col-md-12">
                        <label for="designSearchFilter" class="form-label">Поиск по названию</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="designSearchFilter" name="search" 
                                   placeholder="Введите название файла дизайна..." autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" style="display: none;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Фильтры в одной строке -->
                    <div class="col-md-3">
                        <label for="designTypeFilter" class="form-label">Тип дизайна</label>
                        <select class="form-select" id="designTypeFilter" name="design_type">
                            <option value="">Все типы</option>
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
                    <div class="col-md-3">
                        <label for="designRoomFilter" class="form-label">Помещение</label>
                        <select class="form-select" id="designRoomFilter" name="room">
                            <option value="">Все помещения</option>
                            <option value="living_room">Гостиная</option>
                            <option value="bedroom">Спальня</option>
                            <option value="kitchen">Кухня</option>
                            <option value="bathroom">Ванная</option>
                            <option value="hallway">Прихожая</option>
                            <option value="office">Кабинет</option>
                            <option value="balcony">Балкон</option>
                            <option value="children">Детская</option>
                            <option value="storage">Кладовая</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="designSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="designSortFilter" name="sort">
                            <option value="newest">Сначала новые</option>
                            <option value="oldest">Сначала старые</option>
                            <option value="name_asc">По имени (А-Я)</option>
                            <option value="name_desc">По имени (Я-А)</option>
                            <option value="size_asc">По размеру (возрастание)</option>
                            <option value="size_desc">По размеру (убывание)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="clearDesignFilters" title="Сбросить все фильтры">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="toggleFiltersBtn" title="Свернуть/развернуть фильтры">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Дополнительные опции (скрываемые) -->
                <div class="row g-3 mt-2" id="advancedFilters" style="display: none;">
                    <div class="col-md-4">
                        <label for="designDateFromFilter" class="form-label">Дата создания от</label>
                        <input type="date" class="form-control" id="designDateFromFilter" name="date_from">
                    </div>
                    <div class="col-md-4">
                        <label for="designDateToFilter" class="form-label">Дата создания до</label>
                        <input type="date" class="form-control" id="designDateToFilter" name="date_to">
                    </div>
                    <div class="col-md-4">
                        <label for="designSizeFilter" class="form-label">Размер файла</label>
                        <select class="form-select" id="designSizeFilter" name="file_size">
                            <option value="">Любой размер</option>
                            <option value="small">Маленькие (до 1 МБ)</option>
                            <option value="medium">Средние (1-10 МБ)</option>
                            <option value="large">Большие (больше 10 МБ)</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Область для индикатора загрузки -->
    <div id="designLoadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
        <p class="mt-2 text-muted">Загрузка файлов дизайна...</p>
    </div>

    <!-- Список файлов дизайна -->
    <div id="designGallery" class="row g-3">
        <!-- Сюда будут загружены карточки файлов дизайна через AJAX -->
    </div>

    <!-- Пустое состояние -->
    <div id="emptyDesignState" class="text-center py-5" style="display: none;">
        <i class="bi bi-folder2-open display-1 text-muted"></i>
        <h5 class="mt-3">Нет файлов дизайна</h5>
        <p class="text-muted">Загрузите файлы дизайна проекта, нажав кнопку "Загрузить дизайн" вверху страницы</p>
    </div>
</div>

<style>
/* Улучшенные стили для дизайн-файлов */
.design-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    height: 100%;
    border: 1px solid rgba(0,0,0,0.05);
}

.design-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: rgba(0,123,255,0.2);
}

.design-preview {
    height: 220px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.design-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.design-card:hover .design-preview img {
    transform: scale(1.05);
}

.file-icon-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.file-extension {
    background: rgba(0,123,255,0.1);
    color: #0d6efd;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
    letter-spacing: 0.5px;
}

.design-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
    padding: 15px;
}

.design-card:hover .design-overlay {
    opacity: 1;
}

.design-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
}

.design-badge .badge {
    backdrop-filter: blur(10px);
    font-size: 0.7rem;
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 500;
}

.design-actions {
    z-index: 4;
    display: flex;
    gap: 8px;
}

.design-actions .btn {
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.design-actions .btn:hover {
    background-color: rgba(255, 255, 255, 1);
    transform: scale(1.1);
}

.design-delete-btn:hover {
    background-color: rgba(220, 53, 69, 0.9) !important;
    border-color: rgba(220, 53, 69, 0.5) !important;
}

.design-delete-btn:hover i {
    color: white !important;
}

/* Улучшенные фильтры */
#designFiltersCard {
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border-radius: 12px;
}

#designFiltersCard .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    border-radius: 12px 12px 0 0;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
    background-color: #fff;
}

.form-select:focus, .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    transform: translateY(-1px);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    border-radius: 8px 0 0 8px;
}

#clearSearchBtn {
    border-radius: 0 8px 8px 0;
}

/* Индикатор загрузки */
#designLoadingIndicator {
    background: rgba(248, 249, 250, 0.8);
    border-radius: 12px;
    padding: 3rem;
    margin: 2rem 0;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Пустое состояние */
#emptyDesignState {
    background: rgba(248, 249, 250, 0.5);
    border-radius: 12px;
    padding: 4rem 2rem;
    margin: 2rem 0;
}

#emptyDesignState i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

/* Адаптивность */
@media (max-width: 768px) {
    .design-preview {
        height: 180px;
    }
    
    .design-card {
        margin-bottom: 1rem;
    }
    
    #designFiltersCard .card-body .row {
        gap: 1rem;
    }
    
    .col-md-3 {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .design-preview {
        height: 160px;
    }
    
    .design-badge {
        top: 8px;
        left: 8px;
    }
    
    .design-badge .badge {
        font-size: 0.65rem;
        padding: 4px 8px;
    }
    
    .design-overlay {
        padding: 10px;
    }
    
    .design-actions .btn {
        width: 32px;
        height: 32px;
    }
}

/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.design-card {
    animation: fadeInUp 0.5s ease-out;
}

.design-card:nth-child(1) { animation-delay: 0.1s; }
.design-card:nth-child(2) { animation-delay: 0.2s; }
.design-card:nth-child(3) { animation-delay: 0.3s; }
.design-card:nth-child(4) { animation-delay: 0.4s; }

/* Улучшенные tooltips */
.tooltip {
    font-size: 0.875rem;
}

.tooltip-inner {
    background-color: rgba(0, 0, 0, 0.9);
    border-radius: 6px;
    padding: 8px 12px;
}

/* Счетчик активных фильтров */
#activeFiltersCount {
    color: #0d6efd;
    font-weight: 600;
}

/* Улучшенные кнопки */
.btn-group .btn {
    border-radius: 8px;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>

<script>
(function() {
    'use strict';
    
    // Улучшенный объект для работы с дизайн-файлами
    const DesignManagerFixed = {
        data: [],
        currentFilters: {},
        isLoading: false,
        searchTimeout: null,
        initialized: false,
        
        // Инициализация
        init: function() {
            if (this.initialized) {
                console.log('DesignManagerFixed уже инициализирован');
                return;
            }
            
            console.log('Инициализация улучшенного менеджера дизайн-файлов');
            
            this.initialized = true;
            this.setupEventListeners();
            this.loadFiles();
            this.updateFiltersUI();
        },
        
        // Настройка обработчиков событий
        setupEventListeners: function() {
            const self = this;
            
            // Поиск в реальном времени с задержкой
            $('#designSearchFilter').off('input.designManager').on('input.designManager', function() {
                const value = $(this).val();
                $('#clearSearchBtn').toggle(value.length > 0);
                
                clearTimeout(self.searchTimeout);
                self.searchTimeout = setTimeout(() => {
                    self.applyFilters();
                }, 500);
            });
            
            // Очистка поиска
            $('#clearSearchBtn').off('click.designManager').on('click.designManager', function() {
                $('#designSearchFilter').val('').trigger('input');
            });
            
            // Изменение фильтров
            $('#designTypeFilter, #designRoomFilter, #designSortFilter, #designDateFromFilter, #designDateToFilter, #designSizeFilter')
                .off('change.designManager').on('change.designManager', function() {
                    self.applyFilters();
                });
            
            // Кнопка сброса фильтров
            $('#clearDesignFilters').off('click.designManager').on('click.designManager', function() {
                self.resetFilters();
            });
            
            // Переключение расширенных фильтров
            $('#toggleFiltersBtn').off('click.designManager').on('click.designManager', function() {
                const $advanced = $('#advancedFilters');
                const $icon = $(this).find('i');
                
                $advanced.slideToggle(300);
                $icon.toggleClass('bi-chevron-up bi-chevron-down');
            });
            
            // Обработчики для динамически создаваемых элементов
            $(document).off('click.designManager', '.design-card').on('click.designManager', '.design-card', function(e) {
                if (!$(e.target).closest('.design-actions').length) {
                    const designId = $(this).data('id');
                    self.viewFile(designId);
                }
            });
            
            $(document).off('click.designManager', '.design-delete-btn').on('click.designManager', '.design-delete-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const designId = $(this).data('id');
                self.confirmDelete(designId);
            });
        },
        
        // Загрузка файлов с применением фильтров
        loadFiles: function() {
            if (this.isLoading) {
                console.log('Загрузка уже в процессе');
                return;
            }
            
            const projectId = window.projectId;
            if (!projectId) {
                console.error('ID проекта не найден');
                this.showError('ID проекта не найден');
                return;
            }
            
            this.isLoading = true;
            
            // Показываем индикатор загрузки
            this.showLoading();
            
            // Формируем параметры запроса из фильтров
            const params = this.getFilterParams();
            
            console.log('Загрузка файлов дизайна с параметрами:', params);
            
            // AJAX запрос
            $.ajax({
                url: `/partner/projects/${projectId}/design`,
                method: 'GET',
                data: params,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                timeout: 30000,
                success: (response) => {
                    console.log('Ответ сервера:', response);
                    this.handleLoadSuccess(response);
                },
                error: (xhr, status, error) => {
                    console.error('Ошибка при загрузке файлов дизайна:', {xhr, status, error});
                    this.handleLoadError(xhr, status, error);
                },
                complete: () => {
                    this.isLoading = false;
                }
            });
        },
        
        // Обработка успешной загрузки
        handleLoadSuccess: function(response) {
            try {
                // Приоритет для files, затем data для обратной совместимости
                if (Array.isArray(response.files)) {
                    this.data = response.files;
                } else if (Array.isArray(response.data)) {
                    this.data = response.data;
                } else if (Array.isArray(response)) {
                    this.data = response;
                } else {
                    this.data = [];
                }
                
                console.log('Загружено файлов:', this.data.length);
                
                this.renderFiles();
                this.updateCounter();
                this.hideLoading();
                
                if (this.data.length === 0) {
                    this.showEmptyState();
                } else {
                    this.hideEmptyState();
                }
                
            } catch (error) {
                console.error('Ошибка при обработке ответа:', error);
                this.showError('Ошибка при обработке данных');
            }
        },
        
        // Обработка ошибки загрузки
        handleLoadError: function(xhr, status, error) {
            this.hideLoading();
            
            let errorMessage = 'Ошибка при загрузке файлов дизайна';
            
            if (status === 'timeout') {
                errorMessage = 'Превышено время ожидания запроса';
            } else if (xhr.status === 403) {
                errorMessage = 'Недостаточно прав для просмотра файлов';
            } else if (xhr.status === 404) {
                errorMessage = 'Проект не найден';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            this.showError(errorMessage);
            
            if (typeof showMessage === 'function') {
                showMessage('error', errorMessage);
            }
        },
        
        // Получение параметров фильтрации
        getFilterParams: function() {
            const form = $('#designFilterForm');
            const formData = form.serializeArray();
            const params = {};
            
            // Преобразуем массив в объект и фильтруем пустые значения
            formData.forEach(item => {
                if (item.value !== '' && item.value !== null) {
                    params[item.name] = item.value.trim();
                }
            });
            
            // Сохраняем текущие фильтры
            this.currentFilters = {...params};
            
            return params;
        },
        
        // Применение фильтров
        applyFilters: function() {
            this.updateFiltersUI();
            this.loadFiles();
        },
        
        // Сброс фильтров
        resetFilters: function() {
            $('#designFilterForm')[0].reset();
            $('#clearSearchBtn').hide();
            this.currentFilters = {};
            this.updateFiltersUI();
            this.loadFiles();
        },
        
        // Обновление UI фильтров
        updateFiltersUI: function() {
            const activeFiltersCount = Object.keys(this.currentFilters).length;
            const $counter = $('#activeFiltersCount');
            
            if (activeFiltersCount > 0) {
                $counter.text(`(${activeFiltersCount} активных)`).show();
            } else {
                $counter.hide();
            }
        },
        
        // Отрисовка файлов
        renderFiles: function() {
            const gallery = $('#designGallery');
            gallery.empty();
            
            if (this.data.length === 0) {
                return;
            }
            
            this.data.forEach(file => {
                gallery.append(this.createFileCard(file));
            });
            
            // Инициализируем tooltips для новых элементов
            this.initTooltips();
        },
        
        // Создание карточки файла
        createFileCard: function(file) {
            const isImage = file.mime_type && file.mime_type.startsWith('image/');
            const designTypeLabel = file.design_type_name || this.getDesignTypeName(file.design_type || file.type);
            const roomLabel = file.room_name || (file.room ? this.getDesignRoomName(file.room) : '');
            
            // Формируем описание категории
            let categoryInfo = designTypeLabel;
            if (roomLabel) {
                categoryInfo += ' - ' + roomLabel;
            }
            
            // Проверяем роль пользователя для показа кнопки удаления
            const isClient = <?php echo json_encode(!(App\Helpers\UserRoleHelper::canSeeActionButtons()), 15, 512) ?>;
            const deleteButton = isClient ? '' : `
                <button class="btn btn-sm btn-light design-delete-btn" data-id="${file.id}" 
                        data-bs-toggle="tooltip" title="Удалить файл">
                    <i class="bi bi-trash text-danger"></i>
                </button>
            `;
            
            return `
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card design-card h-100" data-id="${file.id}" 
                         data-bs-toggle="tooltip" data-bs-placement="top" title="Нажмите для просмотра">
                        <div class="design-badge">
                            <span class="badge bg-primary">${designTypeLabel}</span>
                            ${roomLabel ? `<span class="badge bg-secondary ms-1">${roomLabel}</span>` : ''}
                        </div>
                        <div class="design-preview">
                            ${isImage ? 
                                `<img src="${file.url}" alt="${file.original_name || file.name}" 
                                      onerror="this.src='/img/file-error.svg'" loading="lazy">` : 
                                `<div class="file-icon-wrapper">
                                    <i class="bi bi-file-earmark display-1 text-secondary"></i>
                                    <small class="file-extension">${this.getFileExtension(file.original_name || file.name)}</small>
                                 </div>`
                            }
                        </div>
                        <div class="design-overlay">
                            <div class="design-actions">
                                ${deleteButton}
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title text-truncate mb-2" title="${file.original_name || file.name}">
                                ${file.original_name || file.name}
                            </h6>
                            ${file.description ? `<p class="card-text small text-muted mb-2">${file.description}</p>` : ''}
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">${this.formatFileSize(file.file_size || file.size)}</small>
                                <small class="text-muted">${this.formatDate(file.created_at)}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        
        // Подтверждение удаления
        confirmDelete: function(fileId) {
            if (!fileId) {
                console.error('ID файла не передан');
                return;
            }
            
            const file = this.data.find(f => f.id == fileId);
            const fileName = file ? (file.original_name || file.name) : 'файл';
            
            if (confirm(`Вы уверены, что хотите удалить файл "${fileName}"? Это действие нельзя отменить.`)) {
                this.deleteFile(fileId);
            }
        },
        
        // Удаление файла
        deleteFile: function(fileId) {
            const projectId = window.projectId;
            
            if (!projectId || !fileId) {
                console.error('ID проекта или файла не найден');
                return;
            }
            
            // Показываем индикатор загрузки на кнопке
            const $btn = $(`.design-delete-btn[data-id="${fileId}"]`);
            const originalHtml = $btn.html();
            $btn.html('<i class="spinner-border spinner-border-sm"></i>').prop('disabled', true);
            
            $.ajax({
                url: `/partner/projects/${projectId}/design/${fileId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                timeout: 15000,
                success: (response) => {
                    console.log('Файл успешно удален:', response);
                    
                    if (typeof showMessage === 'function') {
                        showMessage('success', 'Файл дизайна успешно удален');
                    }
                    
                    // Перезагружаем список файлов
                    this.loadFiles();
                },
                error: (xhr, status, error) => {
                    console.error('Ошибка при удалении файла:', {xhr, status, error});
                    
                    let errorMessage = 'Ошибка при удалении файла дизайна';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (status === 'timeout') {
                        errorMessage = 'Превышено время ожидания запроса';
                    }
                    
                    if (typeof showMessage === 'function') {
                        showMessage('error', errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                    
                    // Восстанавливаем кнопку
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        },
        
        // Просмотр файла
        viewFile: function(fileId) {
            const file = this.data.find(f => f.id == fileId);
            if (!file) {
                console.error('Файл не найден');
                return;
            }
            
            // Здесь можно добавить логику для открытия модального окна с просмотром файла
            console.log('Просмотр файла:', file);
            
            // Пример: открытие файла в новой вкладке
            if (file.url) {
                window.open(file.url, '_blank');
            }
        },
        
        // Показать индикатор загрузки
        showLoading: function() {
            $('#designLoadingIndicator').show();
            $('#designGallery').hide();
            $('#emptyDesignState').hide();
        },
        
        // Скрыть индикатор загрузки
        hideLoading: function() {
            $('#designLoadingIndicator').hide();
            $('#designGallery').show();
        },
        
        // Показать состояние "нет файлов"
        showEmptyState: function() {
            $('#designGallery').hide();
            $('#emptyDesignState').show();
        },
        
        // Скрыть состояние "нет файлов"
        hideEmptyState: function() {
            $('#emptyDesignState').hide();
        },
        
        // Показать ошибку
        showError: function(message) {
            $('#designGallery').html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${message}
                        <button type="button" class="btn btn-sm btn-outline-danger ms-3" onclick="DesignManagerFixed.loadFiles()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Повторить
                        </button>
                    </div>
                </div>
            `).show();
        },
        
        // Обновить счетчик
        updateCounter: function() {
            $('#designCount').text(this.data.length);
        },
        
        // Инициализация tooltips
        initTooltips: function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        },
        
        // Получение расширения файла
        getFileExtension: function(filename) {
            if (!filename) return '';
            const parts = filename.split('.');
            return parts.length > 1 ? parts.pop().toUpperCase() : '';
        },
        
        // Вспомогательные функции для отображения информации
        getDesignTypeName: function(type) {
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
            return types[type] || type || 'Не указано';
        },
        
        getDesignRoomName: function(room) {
            const rooms = {
                'living_room': 'Гостиная',
                'bedroom': 'Спальня',
                'kitchen': 'Кухня',
                'bathroom': 'Ванная',
                'hallway': 'Прихожая',
                'office': 'Кабинет',
                'balcony': 'Балкон',
                'children': 'Детская',
                'storage': 'Кладовая',
                'living': 'Гостиная', // для обратной совместимости
                'other': 'Другое'
            };
            return rooms[room] || room || 'Не указано';
        },
        
        formatFileSize: function(bytes) {
            if (!bytes) return '0 Б';
            
            const sizes = ['Б', 'КБ', 'МБ', 'ГБ', 'ТБ'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        formatDate: function(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(date);
        },
        
        // Публичные методы для обратной совместимости
        reload: function() {
            this.loadFiles();
        },
        
        refresh: function() {
            this.loadFiles();
        }
    };

    // Экспорт объекта в глобальную область
    window.DesignManagerFixed = DesignManagerFixed;
    
    // Обратная совместимость со старым кодом
    window.DesignManager = DesignManagerFixed;
    
    window.loadDesign = function() {
        DesignManagerFixed.loadFiles();
    };
    
    window.confirmDeleteDesign = function(designId) {
        DesignManagerFixed.confirmDelete(designId);
    };
    
    // Инициализация при готовности документа
    $(document).ready(function() {
        console.log('🎨 Документ готов, проверяем состояние вкладки дизайна');
        
        // Функция для инициализации менеджера
        function initDesignManager() {
            if (!DesignManagerFixed.initialized) {
                console.log('🚀 Инициализируем DesignManagerFixed');
                DesignManagerFixed.init();
                // Автозагрузка файлов дизайна
                setTimeout(() => {
                    console.log('📁 Автозагрузка файлов дизайна');
                    DesignManagerFixed.loadFiles();
                }, 100);
            } else {
                console.log('✅ DesignManagerFixed уже инициализирован');
                // Если уже инициализирован, но файлы не загружены - загружаем
                if (!DesignManagerFixed.data || DesignManagerFixed.data.length === 0) {
                    console.log('📁 Перезагрузка файлов дизайна');
                    DesignManagerFixed.loadFiles();
                }
            }
        }
        
        // Проверяем, активна ли вкладка дизайна
        if ($('#design-tab-content').is(':visible') || 
            $('#design-tab').hasClass('active') || 
            $('[href="#design"]').hasClass('active') ||
            window.location.hash === '#design' ||
            window.location.pathname.includes('design')) {
            console.log('🎨 Вкладка дизайна активна при загрузке');
            initDesignManager();
        }
        
        // Обработчик переключения на вкладку дизайна
        $('a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).attr('href') || $(e.target).data('bs-target');
            if (target === '#design-tab' || target === '#design-tab-content' || target === '#design' || target?.includes('design')) {
                console.log('🔄 Переключились на вкладку дизайна:', target);
                initDesignManager();
            }
        });
        
        // Альтернативный обработчик для разных версий Bootstrap
        $(document).on('shown.bs.tab', function(e) {
            const target = $(e.target).attr('href') || $(e.target).data('bs-target');
            if (target && (target.includes('design') || target === '#design-tab' || target === '#design-tab-content' || target === '#design')) {
                console.log('🔄 Альтернативный обработчик: переключились на вкладку дизайна:', target);
                initDesignManager();
            }
        });
        
        // Дополнительная проверка через 500мс
        setTimeout(() => {
            if ($('#design-tab-content').is(':visible') && !DesignManagerFixed.initialized) {
                console.log('🔄 Дополнительная инициализация дизайна');
                initDesignManager();
            }
        }, 500);
    });
})();
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/design.blade.php ENDPATH**/ ?>