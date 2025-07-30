<!-- Схемы проекта -->
<div id="schemes-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Схемы проекта (<span id="schemeCount">0</span>)</h5>
        <?php if(\App\Helpers\UserRoleHelper::canSeeActionButtons()): ?>
        <button class="btn btn-primary" data-modal-type="scheme">
            <i class="bi bi-plus-lg me-2"></i>Загрузить схему
        </button>
        <?php endif; ?>
    </div>

    <!-- Улучшенные фильтры с AJAX -->
    <div class="card mb-4" id="schemeFiltersCard">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Фильтры и сортировка</h6>
            <small class="text-muted" id="activeSchemeFiltersText">Активных фильтров: 0</small>
        </div>
        <div class="card-body">
            <form id="schemeFilterForm" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="schemeTypeFilter" class="form-label">Тип схемы</label>
                        <select class="form-select" id="schemeTypeFilter" name="scheme_type">
                            <option value="">Все типы</option>
                            <option value="electrical">Электрика</option>
                            <option value="plumbing">Сантехника</option>
                            <option value="ventilation">Вентиляция</option>
                            <option value="layout">Планировка</option>
                            <option value="structure">Конструкция</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="schemeRoomFilter" class="form-label">Помещение</label>
                        <select class="form-select" id="schemeRoomFilter" name="room">
                            <option value="">Все помещения</option>
                            <option value="living_room">Гостиная</option>
                            <option value="bedroom">Спальня</option>
                            <option value="kitchen">Кухня</option>
                            <option value="bathroom">Ванная</option>
                            <option value="hallway">Прихожая</option>
                            <option value="entire">Вся квартира/дом</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="schemeSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="schemeSortFilter" name="sort">
                            <option value="created_at_desc">Сначала новые</option>
                            <option value="created_at_asc">Сначала старые</option>
                            <option value="name_asc">По имени (А-Я)</option>
                            <option value="name_desc">По имени (Я-А)</option>
                            <option value="size_asc">По размеру (возрастание)</option>
                            <option value="size_desc">По размеру (убывание)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="clearSchemeFilters" title="Сбросить все фильтры">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="toggleSchemeFiltersBtn" title="Свернуть/развернуть фильтры">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Дополнительные опции (скрываемые) -->
                <div class="row g-3 mt-2" id="advancedSchemeFilters" style="display: none;">
                    <div class="col-md-4">
                        <label for="schemeDateFromFilter" class="form-label">Дата создания от</label>
                        <input type="date" class="form-control" id="schemeDateFromFilter" name="date_from">
                    </div>
                    <div class="col-md-4">
                        <label for="schemeDateToFilter" class="form-label">Дата создания до</label>
                        <input type="date" class="form-control" id="schemeDateToFilter" name="date_to">
                    </div>
                    <div class="col-md-4">
                        <label for="schemeSearchFilter" class="form-label">Поиск по названию</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="schemeSearchFilter" name="search" placeholder="Введите название...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSchemeSearchBtn">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Область для индикатора загрузки -->
    <div id="schemeLoadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
        <p class="mt-2 text-muted">Загрузка схем...</p>
    </div>

    <!-- Список схем -->
    <div id="schemeGallery" class="row g-3">
        <!-- Сюда будут загружены карточки схем через AJAX -->
    </div>

    <!-- Пустое состояние -->
    <div id="emptySchemeState" class="text-center py-5" style="display: none;">
        <i class="bi bi-bezier2 display-1 text-muted"></i>
        <h5 class="mt-3">Нет схем</h5>
        <p class="text-muted">Загрузите схемы проекта, нажав кнопку "Загрузить схему" вверху страницы</p>
    </div>
</div>

<style>
/* Улучшенные стили для карточек схем */
.scheme-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    height: 100%;
    border: 1px solid rgba(0,0,0,0.05);
}

.scheme-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.scheme-preview {
    height: 200px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.scheme-preview img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.scheme-card:hover .scheme-preview img {
    transform: scale(1.05);
}

.file-icon-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #6c757d;
}

.file-extension {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 8px;
    text-transform: uppercase;
}

.scheme-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(2px);
}

.scheme-card:hover .scheme-overlay {
    opacity: 1;
}

.scheme-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
}

.scheme-badge .badge {
    background: rgba(255, 255, 255, 0.95) !important;
    color: #333 !important;
    font-weight: 500;
    padding: 6px 10px;
    font-size: 0.75rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.scheme-actions {
    position: absolute;
    bottom: 12px;
    right: 12px;
    z-index: 3;
    opacity: 0;
    transition: all 0.3s ease;
    display: flex;
    gap: 8px;
}

.scheme-card:hover .scheme-actions {
    opacity: 1;
    transform: translateY(0);
}

.scheme-actions .btn {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.9) !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #333;
    border-radius: 8px;
    padding: 8px 12px;
    transition: all 0.2s ease;
}

.scheme-actions .btn:hover {
    background: rgba(255, 255, 255, 1) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.scheme-delete-btn:hover {
    background: rgba(220, 53, 69, 0.9) !important;
    color: white !important;
}

.scheme-delete-btn:hover i {
    color: white !important;
}

/* Улучшенные фильтры */
#schemeFiltersCard {
    border: none;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
}

#schemeFiltersCard .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 12px 12px 0 0;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
}

.form-select, .form-control {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.form-select:focus, .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.input-group-text {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #6c757d;
}

#clearSchemeSearchBtn {
    border-radius: 0 8px 8px 0;
}

/* Индикатор загрузки */
#schemeLoadingIndicator {
    background: rgba(248, 249, 250, 0.95);
    border-radius: 12px;
    margin: 20px 0;
}

.spinner-border {
    color: #0d6efd;
}

/* Пустое состояние */
#emptySchemeState {
    background: rgba(248, 249, 250, 0.5);
    border-radius: 12px;
    margin: 20px 0;
}

#emptySchemeState i {
    opacity: 0.6;
}

/* Адаптивность */
@media (max-width: 768px) {
    .scheme-card {
        margin-bottom: 16px;
    }
    
    .scheme-preview {
        height: 160px;
    }
    
    .scheme-badge {
        top: 8px;
        left: 8px;
    }
    
    .scheme-actions {
        bottom: 8px;
        right: 8px;
    }
    
    .scheme-actions .btn {
        padding: 6px 10px;
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .scheme-preview {
        height: 140px;
    }
    
    .form-select, .form-control {
        font-size: 0.85rem;
        padding: 8px 10px;
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

.scheme-card {
    animation: fadeInUp 0.6s ease-out;
}

.scheme-card:nth-child(1) { animation-delay: 0.1s; }
.scheme-card:nth-child(2) { animation-delay: 0.2s; }
.scheme-card:nth-child(3) { animation-delay: 0.3s; }
.scheme-card:nth-child(4) { animation-delay: 0.4s; }

/* Улучшенные tooltips */
.tooltip {
    font-size: 0.8rem;
}

.tooltip-inner {
    background: rgba(0, 0, 0, 0.9);
    padding: 6px 10px;
    border-radius: 6px;
}

/* Счетчик активных фильтров */
#activeSchemeFiltersCount {
    background: #0d6efd;
    color: white;
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
}

/* Улучшенные кнопки */
.btn-group .btn {
    border-radius: 0;
    border-right: none;
}

.btn-group .btn:first-child {
    border-radius: 8px 0 0 8px;
}

.btn-group .btn:last-child {
    border-radius: 0 8px 8px 0;
    border-right: 1px solid #dee2e6;
}
</style>

<script>
(function() {
    // Объект для работы со схемами
    const SchemeManager = {
        data: [],
        currentId: null,
        filters: {},
        currentPage: 1,
        perPage: 12,
        totalPages: 1,
        
        // Инициализация
        init: function() {
            console.log('=== ИНИЦИАЛИЗАЦИЯ SCHEME MANAGER ===');
            
            this.projectId = window.projectId;
            
            if (!this.projectId) {
                console.error('ID проекта не найден');
                return;
            }
            
            // Устанавливаем флаг инициализации
            window.SchemeManager.initialized = true;
            
            this.attachEventHandlers();
            this.loadFiles();
            
            console.log('SchemeManager инициализирован успешно для проекта:', this.projectId);
        },
        
        // Настройка обработчиков событий
        attachEventHandlers: function() {
            const self = this;
            
            // Обработчики фильтров - без отправки формы
            $('#schemeFilterForm').on('submit', function(e) {
                e.preventDefault();
            });
            
            // Быстрые фильтры - обработка изменений
            $(document).on('change', '#schemeTypeFilter, #schemeRoomFilter, #schemeSortFilter', function() {
                self.applyFilters();
            });
            
            // Расширенные фильтры
            $(document).on('change', '#schemeDateFromFilter, #schemeDateToFilter', function() {
                self.applyFilters();
            });
            
            // Поиск
            $(document).on('input', '#schemeSearchFilter', function() {
                clearTimeout(self.searchTimeout);
                self.searchTimeout = setTimeout(() => {
                    self.applyFilters();
                }, 500);
            });
            
            // Кнопка очистки поиска
            $('#clearSchemeSearchBtn').on('click', function() {
                $('#schemeSearchFilter').val('');
                self.applyFilters();
            });
            
            // Кнопка сброса фильтров
            $('#clearSchemeFilters').on('click', function() {
                self.resetFilters();
            });
            
            // Переключение расширенных фильтров
            $('#toggleSchemeFiltersBtn').on('click', function() {
                self.toggleAdvancedFilters();
            });
            
            // Обработчики для карточек
            $(document).on('click', '.scheme-card', function(e) {
                if (!$(e.target).closest('.scheme-actions').length) {
                    const schemeId = $(this).data('id');
                    self.viewFile(schemeId);
                }
            });
            
            console.log('SchemeManager: обработчики событий прикреплены');
        },
        
        // Переключение расширенных фильтров
        toggleAdvancedFilters: function() {
            const advancedFilters = $('#advancedSchemeFilters');
            const toggleBtn = $('#toggleSchemeFiltersBtn i');
            
            if (advancedFilters.is(':visible')) {
                advancedFilters.slideUp(300);
                toggleBtn.removeClass('bi-chevron-down').addClass('bi-chevron-up');
            } else {
                advancedFilters.slideDown(300);
                toggleBtn.removeClass('bi-chevron-up').addClass('bi-chevron-down');
            }
        },
        
        // Получение параметров фильтрации
        getFilterParams: function() {
            const params = {};
            
            // Основные фильтры
            const schemeType = $('#schemeTypeFilter').val();
            const room = $('#schemeRoomFilter').val();
            const sort = $('#schemeSortFilter').val();
            
            // Расширенные фильтры
            const dateFrom = $('#schemeDateFromFilter').val();
            const dateTo = $('#schemeDateToFilter').val();
            const search = $('#schemeSearchFilter').val();
            
            if (schemeType) params.scheme_type = schemeType;
            if (room) params.room = room;
            if (sort) params.sort = sort;
            if (dateFrom) params.date_from = dateFrom;
            if (dateTo) params.date_to = dateTo;
            if (search) params.search = search;
            
            // Пагинация
            params.page = this.currentPage;
            params.per_page = this.perPage;
            
            return params;
        },
        
        // Подсчет активных фильтров
        updateActiveFiltersCount: function() {
            const params = this.getFilterParams();
            let count = 0;
            
            // Исключаем параметры пагинации и сортировки из подсчета
            const excludeKeys = ['page', 'per_page', 'sort'];
            Object.keys(params).forEach(key => {
                if (!excludeKeys.includes(key) && params[key]) {
                    count++;
                }
            });
            
            $('#activeSchemeFiltersText').text(`Активных фильтров: ${count}`);
        },
        
        // Загрузка файлов с применением фильтров
        loadFiles: function() {
            if (!this.projectId) {
                console.error('ID проекта не найден');
                return;
            }
            
            this.showLoadingIndicator();
            this.hideEmptyState();
            
            // Формируем параметры запроса
            const params = this.getFilterParams();
            this.updateActiveFiltersCount();
            
            console.log('Загрузка схем с параметрами:', params);
            
            // AJAX запрос
            $.ajax({
                url: `/partner/projects/${this.projectId}/schemes`,
                method: 'GET',
                data: params,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: (response) => {
                    console.log('Ответ сервера:', response);
                    
                    // Приоритет для files, затем data для обратной совместимости
                    if (Array.isArray(response.files)) {
                        this.data = response.files;
                    } else if (response.data) {
                        this.data = response.data;
                    } else {
                        this.data = [];
                    }
                    
                    this.renderFiles();
                    this.updateCounter(this.data.length);
                    this.hideLoadingIndicator();
                    
                    if (this.data.length === 0) {
                        this.showEmptyState();
                    }
                },
                error: (xhr) => {
                    console.error('Ошибка при загрузке схем:', xhr);
                    this.hideLoadingIndicator();
                    this.showEmptyState(true);
                    
                    if (typeof showMessage === 'function') {
                        showMessage('Ошибка при загрузке схем', 'error');
                    }
                }
            });
        },
        
        // Применение фильтров
        applyFilters: function() {
            this.currentPage = 1; // Сброс на первую страницу
            this.loadFiles();
        },
        
        // Сброс фильтров
        resetFilters: function() {
            $('#schemeFilterForm')[0].reset();
            this.currentPage = 1;
            this.loadFiles();
        },
        
        // Показать индикатор загрузки
        showLoadingIndicator: function() {
            $('#schemeLoadingIndicator').show();
            $('#schemeGallery').hide();
        },
        
        // Скрыть индикатор загрузки
        hideLoadingIndicator: function() {
            $('#schemeLoadingIndicator').hide();
            $('#schemeGallery').show();
        },
        
        // Показать пустое состояние
        showEmptyState: function(isError = false) {
            const emptyState = $('#emptySchemeState');
            if (isError) {
                emptyState.html(`
                    <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                    <h5 class="mt-3 text-danger">Ошибка загрузки</h5>
                    <p class="text-muted">Произошла ошибка при загрузке схем</p>
                    <button class="btn btn-outline-primary" onclick="SchemeManager.loadFiles()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Попробовать снова
                    </button>
                `);
            } else {
                emptyState.html(`
                    <i class="bi bi-bezier2 display-1 text-muted"></i>
                    <h5 class="mt-3">Нет схем</h5>
                    <p class="text-muted">Схемы не найдены или еще не загружены</p>
                    <button class="btn btn-primary" data-modal-type="scheme">
                        <i class="bi bi-plus-lg me-1"></i>Загрузить схему
                    </button>
                `);
            }
            emptyState.show();
        },
        
        // Скрыть пустое состояние
        hideEmptyState: function() {
            $('#emptySchemeState').hide();
        },
        
        // Обновить счетчик
        updateCounter: function(count) {
            $('#schemeCount').text(count);
        },
        
        // Отрисовка файлов
        renderFiles: function() {
            const gallery = $('#schemeGallery');
            gallery.empty();
            
            if (this.data.length === 0) {
                return;
            }
            
            this.data.forEach(file => {
                gallery.append(this.createFileCard(file));
            });
        },
        
        // Создание карточки файла
        createFileCard: function(file) {
            const isImage = file.mime_type && (file.mime_type.startsWith('image/') || /\.(?:jpe?g|gif|png|svg|bmp|webp)$/i.test(file.name));
            const schemeTypeLabel = this.getSchemeTypeName(file.scheme_type || file.type);
            const roomLabel = file.room ? this.getSchemeRoomName(file.room) : '';
            const fileExtension = this.getFileExtension(file.name);
            
            // Проверяем роль пользователя для показа кнопки удаления
            const isClient = <?php echo json_encode(!(App\Helpers\UserRoleHelper::canSeeActionButtons()), 15, 512) ?>;
            const deleteButton = isClient ? '' : `
                <button class="btn btn-sm scheme-delete-btn" onclick="SchemeManager.confirmDelete(${file.id}, event)" title="Удалить">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            return `
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card scheme-card h-100" data-id="${file.id}">
                        <div class="scheme-badge">
                            <span class="badge">${schemeTypeLabel}</span>
                            ${roomLabel ? `<span class="badge ms-1">${roomLabel}</span>` : ''}
                        </div>
                        <div class="scheme-preview">
                            ${isImage ? 
                                `<img src="${file.url}" alt="${file.name}" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                 <div class="file-icon-wrapper" style="display: none;">
                                     <i class="bi bi-file-earmark display-1"></i>
                                     <span class="file-extension">${fileExtension}</span>
                                 </div>` : 
                                `<div class="file-icon-wrapper">
                                     <i class="bi bi-file-earmark display-1"></i>
                                     <span class="file-extension">${fileExtension}</span>
                                 </div>`
                            }
                        </div>
                        <div class="scheme-overlay"></div>
                        <div class="scheme-actions">
                            <button class="btn btn-sm" onclick="SchemeManager.viewFile(${file.id})" title="Просмотр">
                                <i class="bi bi-eye"></i>
                            </button>
                            ${deleteButton}
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title text-truncate mb-1" title="${file.name}">${file.name}</h6>
                            <p class="card-text small text-muted mb-0">
                                ${this.formatFileSize(file.file_size || file.size)} • ${this.formatDate(file.created_at)}
                            </p>
                            ${file.description ? `<p class="card-text small text-muted mt-1" title="${file.description}">${file.description.substring(0, 50)}${file.description.length > 50 ? '...' : ''}</p>` : ''}
                        </div>
                    </div>
                </div>
            `;
        },
        
        // Просмотр файла
        viewFile: function(fileId) {
            const file = this.data.find(f => f.id == fileId);
            if (file && file.url) {
                window.open(file.url, '_blank');
            }
        },
        
        // Подтверждение удаления
        confirmDelete: function(fileId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            if (confirm('Вы уверены, что хотите удалить эту схему? Это действие нельзя отменить.')) {
                this.deleteFile(fileId);
            }
        },
        
        // Удаление файла
        deleteFile: function(fileId) {
            if (!this.projectId || !fileId) {
                console.error('ID проекта или файла не найден');
                return;
            }
            
            $.ajax({
                url: `/partner/projects/${this.projectId}/schemes/${fileId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (typeof showMessage === 'function') {
                        showMessage('Схема успешно удалена', 'success');
                    }
                    this.loadFiles();
                },
                error: (xhr) => {
                    console.error('Ошибка при удалении схемы:', xhr);
                    if (typeof showMessage === 'function') {
                        showMessage('Ошибка при удалении схемы', 'error');
                    }
                }
            });
        },
        
        // Вспомогательные функции
        getSchemeTypeName: function(type) {
            const types = {
                'electrical': 'Электрика',
                'plumbing': 'Сантехника',
                'ventilation': 'Вентиляция',
                'layout': 'Планировка',
                'structure': 'Конструкция',
                'other': 'Другое'
            };
            return types[type] || 'Не указано';
        },
        
        getSchemeRoomName: function(room) {
            const rooms = {
                'living_room': 'Гостиная',
                'bedroom': 'Спальня',
                'kitchen': 'Кухня',
                'bathroom': 'Ванная',
                'hallway': 'Прихожая',
                'entire': 'Вся квартира/дом',
                'other': 'Другое'
            };
            return rooms[room] || room;
        },
        
        getFileExtension: function(filename) {
            return filename.split('.').pop().toUpperCase();
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
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        }
    };

    // Экспорт объекта SchemeManager в глобальную область
    window.SchemeManager = SchemeManager;
    
    // Устанавливаем флаг инициализации
    window.SchemeManager.initialized = false;
    
    // Обратная совместимость со старым кодом
    window.loadSchemes = function() {
        SchemeManager.loadFiles();
    };
    
    window.confirmDeleteScheme = function(schemeId, event) {
        SchemeManager.confirmDelete(schemeId, event);
    };
    
    // Загрузка схем при инициализации вкладки
    $(document).ready(function() {
        console.log('SchemeManager DOM ready');
    });
})();
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/schemes.blade.php ENDPATH**/ ?>