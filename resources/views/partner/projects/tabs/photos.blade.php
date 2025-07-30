<!-- Фотографии проекта -->
<div id="photos-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Фотографии проекта (<span id="photoCount">0</span>)</h5>
        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
        <button class="btn btn-primary" data-modal-type="photo">
            <i class="bi bi-plus-lg me-2"></i>Загрузить фото
        </button>
        @endif
    </div>

    <!-- Улучшенная система фильтров -->
    <div class="card mb-4" id="photoFiltersCard">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Фильтры фотографий</h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleFilters">
                <i class="bi bi-chevron-up" id="toggleFiltersIcon"></i>
            </button>
        </div>
        <div class="card-body" id="filtersContent">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="photoTypeFilter" class="form-label">Тип фото</label>
                    <select class="form-select photo-filter" id="photoTypeFilter" name="category" data-filter="category">
                        <option value="">Все типы</option>
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
                <div class="col-md-3">
                    <label for="photoLocationFilter" class="form-label">Помещение</label>
                    <select class="form-select photo-filter" id="photoLocationFilter" name="location" data-filter="location">
                        <option value="">Все помещения</option>
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
                <div class="col-md-3">
                    <label for="photoSortFilter" class="form-label">Сортировка</label>
                    <select class="form-select photo-filter" id="photoSortFilter" name="sort" data-filter="sort">
                        <option value="newest">Сначала новые</option>
                        <option value="oldest">Сначала старые</option>
                        <option value="name_asc">По имени (А-Я)</option>
                        <option value="name_desc">По имени (Я-А)</option>
                        <option value="size_asc">По размеру (меньше)</option>
                        <option value="size_desc">По размеру (больше)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="photoSearchFilter" class="form-label">Поиск</label>
                    <div class="input-group">
                        <input type="text" class="form-control photo-filter" id="photoSearchFilter" 
                               name="search" data-filter="search" placeholder="Поиск по названию...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearAllFilters">
                                <i class="bi bi-arrow-clockwise me-1"></i>Сбросить все
                            </button>
                            <span class="text-muted small align-self-center" id="activeFiltersText"></span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-info btn-sm" id="saveFiltersBtn">
                                <i class="bi bi-bookmark me-1"></i>Сохранить
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" id="loadFiltersBtn">
                                <i class="bi bi-bookmark-check me-1"></i>Загрузить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Область для индикатора загрузки -->
    <div id="photoLoadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
        <p class="mt-2 text-muted">Загрузка фотографий...</p>
    </div>

    <!-- Галерея фотографий -->
    <div id="photoGallery" class="row g-3">
        <!-- Сюда будут загружены фотографии через AJAX -->
    </div>

    <!-- Пустое состояние -->
    <div id="emptyPhotoState" class="text-center py-5" style="display: none;">
        <i class="bi bi-images display-1 text-muted"></i>
        <h5 class="mt-3">Нет фотографий</h5>
        <p class="text-muted">Загрузите фотографии проекта, нажав кнопку "Загрузить фото" вверху страницы</p>
    </div>
</div>

<style>
/* Улучшенные стили для фотогалереи */
.photo-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    height: 100%;
    background: white;
}

.photo-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.photo-preview {
    height: 220px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-card:hover .photo-preview img {
    transform: scale(1.05);
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.1) 0%, 
        rgba(0,0,0,0.0) 50%, 
        rgba(0,0,0,0.6) 100%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
    padding: 15px;
}

.photo-card:hover .photo-overlay {
    opacity: 1;
}

.photo-badges {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.photo-badges .badge {
    backdrop-filter: blur(10px);
    background-color: rgba(13, 110, 253, 0.8) !important;
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 0.75rem;
    padding: 4px 8px;
}

.photo-badges .badge.bg-secondary {
    background-color: rgba(108, 117, 125, 0.8) !important;
}

.photo-actions {
    display: flex;
    gap: 8px;
    z-index: 2;
}

.photo-actions .btn {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.3);
    transition: all 0.2s ease;
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.photo-actions .btn-light {
    background-color: rgba(255, 255, 255, 0.9);
    color: #495057;
}

.photo-actions .btn-light:hover {
    background-color: rgba(255, 255, 255, 1);
    transform: scale(1.1);
}

.photo-actions .btn-danger {
    background-color: rgba(220, 53, 69, 0.9);
    color: white;
}

.photo-actions .btn-danger:hover {
    background-color: rgba(220, 53, 69, 1);
    transform: scale(1.1);
}

/* Стили для фильтров */
#photoFiltersCard {
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

#photoFiltersCard .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 1px solid #e9ecef;
}

.photo-filter {
    transition: border-color 0.2s ease;
}

.photo-filter:focus {
    border-color: #86b7fe;
  
}

#activeFiltersText {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}

#toggleFilters {
    border: none;
    background: transparent;
    color: #6c757d;
    transition: all 0.2s ease;
}

#toggleFilters:hover {
    color: #495057;
  

#toggleFiltersIcon {
    transition: transform 0.3s ease;
}

/* Анимации загрузки */
#photoLoadingIndicator {
    padding: 3rem 0;
}

#photoLoadingIndicator .spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Пустое состояние */
#emptyPhotoState {
    padding: 4rem 0;
    text-align: center;
    color: #6c757d;
}

#emptyPhotoState .bi {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .photo-preview {
        height: 180px;
    }
    
    .photo-badges {
        top: 8px;
        left: 8px;
    }
    
    .photo-badges .badge {
        font-size: 0.7rem;
        padding: 3px 6px;
    }
    
    .photo-actions .btn {
        width: 32px;
        height: 32px;
    }
    
    .photo-overlay {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .photo-preview {
        height: 160px;
    }
}

/* Улучшенные стили для карточек */
.photo-card .card-body {
    padding: 1rem;
}

.photo-card .card-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.photo-card .text-muted {
    font-size: 0.8rem;
    line-height: 1.3;
}

/* Стили для кнопок фильтров */
.btn-sm {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
}

/* Hover эффекты для элементов управления */
.btn:hover {
    transform: translateY(-1px);
}

.form-select:hover {
    border-color: #b3d7ff;
}

.input-group .btn:hover {
    transform: none;
}

/* Анимация появления карточек */
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

.photo-card {
    animation: fadeInUp 0.3s ease-out;
}

/* Стили для drag and drop зоны */
.drag-over {
   
    border-color: #0d6efd;
}

/* Дополнительные стили для лучшего UX */
.card-header h6 {
    color: #495057;
    font-weight: 600;
}

.badge {
    font-weight: 500;
}

/* Стили для темной темы (если необходимо) */
@media (prefers-color-scheme: dark) {
    .photo-card {
        background: #2d3748;
        border-color: #4a5568;
    }
    
  
    .photo-card .card-title {
        color: #e2e8f0;
    }
    
    .photo-card .text-muted {
        color: #a0aec0 !important;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // Современный менеджер фотографий с улучшенной системой фильтрации
    const PhotoManager = {
        // Состояние
        data: [],
        filteredData: [],
        currentFilters: {},
        isLoading: false,
        
        // Инициализация
        init: function() {
            console.log('🚀 Инициализация PhotoManager v2.0');
            
            if (window.PhotoManager?.initialized) {
                console.log('📋 PhotoManager уже инициализирован');
                return;
            }
            
            this.setupEventListeners();
            this.loadSavedFilters();
            this.loadPhotos();
            
            window.PhotoManager.initialized = true;
        },
        
        // Настройка обработчиков событий
        setupEventListeners: function() {
            const self = this;
            
            // Обработчики фильтров - мгновенная фильтрация при изменении
            $('.photo-filter').off('change input').on('change input', function() {
                const filterType = $(this).data('filter');
                const value = $(this).val().trim();
                
                self.updateFilter(filterType, value);
                self.applyFilters();
                self.updateActiveFiltersText();
            });
            
            // Поиск с задержкой
            let searchTimeout;
            $('#photoSearchFilter').off('input').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const value = $(this).val().trim();
                    self.updateFilter('search', value);
                    self.applyFilters();
                    self.updateActiveFiltersText();
                }, 300);
            });
            
            // Очистка поиска
            $('#clearSearchBtn').off('click').on('click', function() {
                $('#photoSearchFilter').val('');
                self.updateFilter('search', '');
                self.applyFilters();
                self.updateActiveFiltersText();
            });
            
            // Сброс всех фильтров
            $('#clearAllFilters').off('click').on('click', function() {
                self.clearAllFilters();
            });
            
            // Сохранение/загрузка фильтров
            $('#saveFiltersBtn').off('click').on('click', function() {
                self.saveFilters();
            });
            
            $('#loadFiltersBtn').off('click').on('click', function() {
                self.loadSavedFilters();
            });
            
            // Переключение видимости фильтров
            $('#toggleFilters').off('click').on('click', function() {
                self.toggleFiltersVisibility();
            });
            
            // Клики по карточкам фотографий
            $(document).off('click', '.photo-card').on('click', '.photo-card', function(e) {
                if (!$(e.target).closest('.photo-actions').length) {
                    const photoId = $(this).data('id');
                    self.viewPhoto(photoId);
                }
            });
            
            // Удаление фотографий
            $(document).off('click', '.btn-delete-photo').on('click', '.btn-delete-photo', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const photoId = $(this).closest('.photo-card').data('id');
                self.confirmDelete(photoId);
            });
        },
        
        // Загрузка фотографий
        loadPhotos: function() {
            const projectId = window.projectId;
            if (!projectId) {
                console.error('❌ ID проекта не найден');
                this.showError('ID проекта не найден');
                return;
            }
            
            if (this.isLoading) {
                console.log('⏳ Загрузка уже выполняется...');
                return;
            }
            
            this.isLoading = true;
            this.showLoading(true);
            
            $.ajax({
                url: `/partner/projects/${projectId}/photos`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: (response) => {
                    console.log('✅ Фотографии загружены:', response);
                    
                    // Поддержка разных форматов ответа
                    if (Array.isArray(response.files)) {
                        this.data = response.files;
                    } else if (Array.isArray(response.data)) {
                        this.data = response.data;
                    } else if (Array.isArray(response)) {
                        this.data = response;
                    } else {
                        this.data = [];
                    }
                    
                    console.log(`Загружено фотографий: ${this.data.length}`);
                    
                    this.applyFilters();
                    this.updatePhotoCount();
                    this.isLoading = false;
                    this.showLoading(false);
                    
                    // Принудительное обновление галереи
                    if (this.data.length > 0) {
                        this.hideEmptyState();
                    } else {
                        this.showEmptyState();
                    }
                },
                error: (xhr) => {
                    console.error('❌ Ошибка загрузки фотографий:', xhr);
                    this.isLoading = false;
                    this.showLoading(false);
                    this.showError('Ошибка при загрузке фотографий');
                }
            });
        },
        
        // Применение фильтров
        applyFilters: function() {
            this.filteredData = [...this.data];
            
            // Применяем каждый активный фильтр
            Object.entries(this.currentFilters).forEach(([filterType, value]) => {
                if (value && value !== '') {
                    this.filteredData = this.filterData(this.filteredData, filterType, value);
                }
            });
            
            this.renderPhotos();
            this.updatePhotoCount();
            
            console.log('🔍 Фильтры применены:', {
                total: this.data.length,
                filtered: this.filteredData.length,
                filters: this.currentFilters
            });
        },
        
        // Фильтрация данных
        filterData: function(data, filterType, value) {
            switch (filterType) {
                case 'category':
                    return data.filter(photo => 
                        (photo.category || photo.photo_type || photo.type) === value
                    );
                    
                case 'location':
                    return data.filter(photo => 
                        (photo.location || photo.room) === value
                    );
                    
                case 'search':
                    const searchLower = value.toLowerCase();
                    return data.filter(photo => 
                        (photo.name || '').toLowerCase().includes(searchLower) ||
                        (photo.description || '').toLowerCase().includes(searchLower)
                    );
                    
                case 'sort':
                    return this.sortData([...data], value);
                    
                default:
                    return data;
            }
        },
        
        // Сортировка данных
        sortData: function(data, sortType) {
            switch (sortType) {
                case 'newest':
                    return data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                case 'oldest':
                    return data.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                case 'name_asc':
                    return data.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
                case 'name_desc':
                    return data.sort((a, b) => (b.name || '').localeCompare(a.name || ''));
                case 'size_asc':
                    return data.sort((a, b) => (a.file_size || a.size || 0) - (b.file_size || b.size || 0));
                case 'size_desc':
                    return data.sort((a, b) => (b.file_size || b.size || 0) - (a.file_size || a.size || 0));
                default:
                    return data;
            }
        },
        
        // Обновление фильтра
        updateFilter: function(filterType, value) {
            if (value && value !== '') {
                this.currentFilters[filterType] = value;
            } else {
                delete this.currentFilters[filterType];
            }
        },
        
        // Очистка всех фильтров
        clearAllFilters: function() {
            this.currentFilters = {};
            
            // Очищаем элементы формы
            $('.photo-filter').val('');
            $('#photoSortFilter').val('newest'); // Возвращаем сортировку по умолчанию
            
            this.applyFilters();
            this.updateActiveFiltersText();
            
            if (typeof showMessage === 'function') {
                showMessage('info', 'Фильтры сброшены');
            }
        },
        
        // Отображение фотографий
        renderPhotos: function() {
            console.log('🎨 Отображение фотографий...');
            console.log('Данные для отображения:', this.filteredData.length, 'фотографий');
            
            const gallery = $('#photoGallery');
            gallery.empty();
            
            if (this.filteredData.length === 0) {
                console.log('📭 Нет фотографий для отображения, показываем пустое состояние');
                this.showEmptyState();
                return;
            }
            
            console.log('✨ Отображаем', this.filteredData.length, 'фотографий');
            this.hideEmptyState();
            
            this.filteredData.forEach((photo, index) => {
                const card = this.createPhotoCard(photo);
                gallery.append(card);
                console.log(`📷 Добавлена фотография ${index + 1}: ${photo.name}`);
            });
            
            // Добавляем анимацию появления
            gallery.find('.photo-card').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).delay(index * 50).animate({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                }, 300);
            });
            
            console.log('✅ Галерея фотографий обновлена');
        },
        
        // Создание карточки фотографии
        createPhotoCard: function(photo) {
            const categoryName = this.getCategoryName(photo.category || photo.photo_type || photo.type);
            const locationName = this.getLocationName(photo.location || photo.room);
            const fileSize = this.formatFileSize(photo.file_size || photo.size);
            const date = this.formatDate(photo.created_at);
            
            // Проверяем роль пользователя для показа кнопки удаления
            const isClient = @json(!(App\Helpers\UserRoleHelper::canSeeActionButtons()));
            const deleteButton = isClient ? '' : `
                <button class="btn btn-sm btn-danger btn-delete-photo" title="Удалить">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            return `
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card photo-card h-100" data-id="${photo.id}">
                        <div class="photo-badges">
                            <span class="badge bg-primary">${categoryName}</span>
                            ${locationName && locationName !== 'Не указано' ? 
                                `<span class="badge bg-secondary ms-1">${locationName}</span>` : ''
                            }
                        </div>
                        <div class="photo-preview">
                            <img src="${photo.url}" alt="${photo.name}" 
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIxIDhWN0MyMSA2LjQ0NzcyIDIwLjU1MjMgNiAyMCA2SDE4VjRDMTggMy40NDc3MiAxNy41NTIzIDMgMTcgM0g3QzYuNDQ3NzIgMyA2IDMuNDQ3NzIgNiA0VjZINEM0LjQ0NzcyIDYgNCA2LjQ0NzcyIDQgN1Y4IiBzdHJva2U9IiM2Yjc2ODYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo='">
                        </div>
                        <div class="photo-overlay">
                            <div class="photo-actions">
                                <button class="btn btn-sm btn-light btn-view-photo" title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </button>
                                ${deleteButton}
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title text-truncate mb-2" title="${photo.name}">${photo.name}</h6>
                            <div class="text-muted small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>${fileSize}</span>
                                    <span>${date}</span>
                                </div>
                                ${photo.description ? 
                                    `<p class="mb-0 text-truncate" title="${photo.description}">${photo.description}</p>` : 
                                    ''
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        
        // Удаление фотографии
        confirmDelete: function(photoId) {
            if (!confirm('Вы уверены, что хотите удалить эту фотографию? Это действие нельзя отменить.')) {
                return;
            }
            
            const projectId = window.projectId;
            if (!projectId || !photoId) {
                console.error('❌ ID проекта или фотографии не найден');
                return;
            }
            
            $.ajax({
                url: `/partner/projects/${projectId}/photos/${photoId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (typeof showMessage === 'function') {
                        showMessage('success', 'Фотография успешно удалена');
                    }
                    
                    // Удаляем из локальных данных
                    this.data = this.data.filter(photo => photo.id != photoId);
                    this.applyFilters();
                },
                error: (xhr) => {
                    console.error('❌ Ошибка при удалении фотографии:', xhr);
                    if (typeof showMessage === 'function') {
                        showMessage('error', 'Ошибка при удалении фотографии');
                    }
                }
            });
        },
        
        // Просмотр фотографии
        viewPhoto: function(photoId) {
            const photo = this.data.find(p => p.id == photoId);
            if (!photo) return;
            
            // Здесь можно добавить модальное окно для просмотра
            window.open(photo.url, '_blank');
        },
        
        // Управление интерфейсом
        showLoading: function(show) {
            $('#photoLoadingIndicator').toggle(show);
            $('#photoGallery').toggle(!show);
        },
        
        showError: function(message) {
            $('#photoGallery').html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>${message}
                    </div>
                </div>
            `).show();
        },
        
        showEmptyState: function() {
            $('#emptyPhotoState').show();
            $('#photoGallery').hide();
        },
        
        hideEmptyState: function() {
            $('#emptyPhotoState').hide();
            $('#photoGallery').show();
        },
        
        updatePhotoCount: function() {
            $('#photoCount').text(this.filteredData.length);
        },
        
        updateActiveFiltersText: function() {
            const activeCount = Object.keys(this.currentFilters).length;
            const text = activeCount > 0 ? `Активных фильтров: ${activeCount}` : '';
            $('#activeFiltersText').text(text);
        },
        
        toggleFiltersVisibility: function() {
            const content = $('#filtersContent');
            const icon = $('#toggleFiltersIcon');
            
            content.slideToggle(300);
            icon.toggleClass('bi-chevron-up bi-chevron-down');
        },
        
        // Сохранение/загрузка фильтров
        saveFilters: function() {
            localStorage.setItem('photoFilters', JSON.stringify(this.currentFilters));
            if (typeof showMessage === 'function') {
                showMessage('success', 'Фильтры сохранены');
            }
        },
        
        loadSavedFilters: function() {
            try {
                const saved = localStorage.getItem('photoFilters');
                if (saved) {
                    this.currentFilters = JSON.parse(saved);
                    
                    // Применяем к элементам формы
                    Object.entries(this.currentFilters).forEach(([key, value]) => {
                        $(`[data-filter="${key}"]`).val(value);
                    });
                    
                    this.updateActiveFiltersText();
                    
                    if (typeof showMessage === 'function') {
                        showMessage('info', 'Фильтры загружены');
                    }
                }
            } catch (e) {
                console.error('Ошибка при загрузке сохраненных фильтров:', e);
            }
        },
        
        // Вспомогательные функции
        getCategoryName: function(category) {
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
                'windows': 'Окна'
            };
            return categories[category] || 'Без категории';
        },
        
        getLocationName: function(location) {
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
        },
        
        formatFileSize: function(bytes) {
            if (!bytes) return '0 Б';
            
            const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(1)) + ' ' + sizes[i];
        },
        
        formatDate: function(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(date);
        }
    };

    // Глобальный экспорт
    window.PhotoManager = PhotoManager;
    window.PhotoManager.initialized = false;
    
    // Обратная совместимость
    window.loadPhotos = function() {
        PhotoManager.loadPhotos();
    };
    
    window.confirmDeletePhoto = function(photoId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        PhotoManager.confirmDelete(photoId);
    };

    // Инициализация при загрузке страницы
    $(document).ready(function() {
        console.log('=== ФОТОГРАФИИ: ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ СТРАНИЦЫ ===');
        
        // Всегда инициализируем PhotoManager, если он еще не инициализирован
        if (!window.PhotoManager?.initialized) {
            console.log('Инициализируем PhotoManager...');
            PhotoManager.init();
        }
        
        // Обработчик переключения вкладок для всех возможных селекторов
        $('button[data-bs-target="#photos-tab-content"], a[href="#photos"], button[id="photos-tab"]').on('shown.bs.tab click', function(e) {
            console.log('Активирована вкладка фотографий');
            if (!window.PhotoManager?.initialized) {
                console.log('PhotoManager не инициализирован, инициализируем...');
                PhotoManager.init();
            } else {
                console.log('PhotoManager уже инициализирован, перезагружаем фотографии...');
                PhotoManager.loadPhotos();
            }
        });
        
        // Дополнительная проверка через несколько секунд для случаев медленной загрузки
        setTimeout(function() {
            console.log('=== ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА ФОТОГРАФИЙ ===');
            if (window.PhotoManager?.initialized) {
                const photoCount = window.PhotoManager.data ? window.PhotoManager.data.length : 0;
                console.log('Количество загруженных фотографий:', photoCount);
                
                // Если фотографий нет, попробуем загрузить еще раз
                if (photoCount === 0) {
                    console.log('Фотографии не найдены, пытаемся загрузить еще раз...');
                    window.PhotoManager.loadPhotos();
                }
            } else {
                console.warn('PhotoManager все еще не инициализирован, принудительно инициализируем...');
                PhotoManager.init();
            }
        }, 2000);
        
        // Обработчик загрузки фотографий
        $('#uploadPhotosBtn, #uploadPhotoForm').off('click submit').on('click submit', function(e) {
            e.preventDefault();
            if (typeof uploadPhotos === 'function') {
                uploadPhotos();
            }
        });
        
        // Обработчик выбора файлов
        $('#photoInput').off('change').on('change', function() {
            const files = this.files;
            const $previewContainer = $('#photoPreviewContainer');
            const $preview = $('#photoPreview');
            const $counter = $('#selectedPhotosCount');
            
            if (files.length > 0) {
                $previewContainer.show();
                $preview.empty();
                $counter.text(files.length);
                $('#uploadPhotosBtn').prop('disabled', false);
                
                Array.from(files).forEach(file => {
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $preview.append(`
                                <div class="position-relative">
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            `);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                $previewContainer.hide();
                $preview.empty();
                $counter.text(0);
                $('#uploadPhotosBtn').prop('disabled', true);
            }
        });
        
        // Кнопка выбора файлов
        $('#browsePhotosBtn').off('click').on('click', function() {
            $('#photoInput').click();
        });
        
        // Функция загрузки фотографий
        window.uploadPhotos = function() {
            const form = $('#uploadPhotoForm')[0];
            const formData = new FormData();
            const projectId = window.projectId;
            
            if (!projectId) {
                if (typeof showMessage === 'function') {
                    showMessage('ID проекта не найден', 'error');
                } else {
                    alert('Ошибка: ID проекта не найден');
                }
                return;
            }
            
            const files = $('#photoInput').prop('files');
            if (!files || files.length === 0) {
                if (typeof showMessage === 'function') {
                    showMessage('Выберите фотографии для загрузки', 'warning');
                } else {
                    alert('Выберите фотографии для загрузки');
                }
                return;
            }
            
            // Добавляем файлы
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            
            // Добавляем метаданные
            const category = $('#photoCategory').val() || '';
            const location = $('#photoLocation').val() || '';
            const description = $('#photoDescription').val() || '';
            
            formData.append('category', category);
            formData.append('location', location);
            formData.append('description', description);
            
            // Показываем прогресс
            $('#photoUploadProgress').show();
            
            $.ajax({
                url: `/partner/projects/${projectId}/photos`,
                type: 'POST',
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
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $('#photoUploadProgress .progress-bar')
                                .css('width', percent + '%')
                                .text(percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    // Закрываем модальное окно
                    const modal = bootstrap.Modal.getInstance(document.getElementById('uploadPhotoModal'));
                    if (modal) modal.hide();
                    
                    if (typeof showMessage === 'function') {
                        showMessage('Фотографии успешно загружены', 'success');
                    }
                    
                    // Очищаем форму
                    form.reset();
                    $('#photoPreviewContainer').hide();
                    $('#photoPreview').empty();
                    $('#selectedPhotosCount').text(0);
                    $('#photoUploadProgress').hide();
                    $('#photoUploadProgress .progress-bar').css('width', '0%').text('0%');
                    
                    // Обновляем список фотографий
                    if (window.PhotoManager?.initialized) {
                        window.PhotoManager.loadPhotos();
                    } else {
                        // Принудительно перезагружаем страницу если PhotoManager не инициализирован
                        console.log('PhotoManager не инициализирован, перезагружаем страницу...');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    console.error('Ошибка загрузки фотографий:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    
                    let errorMessage = 'Ошибка при загрузке фотографий';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                    
                    if (typeof showMessage === 'function') {
                        showMessage(errorMessage, 'error');
                    } else {
                        alert(errorMessage);
                    }
                    $('#photoUploadProgress').hide();
                }
            });
        };
    });
})();
</script>
