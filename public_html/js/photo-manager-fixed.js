/**
 * Исправленный PhotoManager - сохранение категорий и метаданных при загрузке
 * Версия: 2.0 - исправление потери категорий при загрузке
 */

window.PhotoManagerFixed = {
    projectId: null,
    data: [],
    filters: {},
    currentPage: 1,
    perPage: 12,
    totalPages: 1,
    
    init: function() {
        console.log('=== ИНИЦИАЛИЗАЦИЯ PHOTO MANAGER FIXED ===');
        
        this.projectId = window.projectId;
        
        if (!this.projectId) {
            console.warn('Project ID не найден для PhotoManagerFixed');
            return;
        }
        
        this.attachEventHandlers();
        this.loadFiles();
        
        console.log('PhotoManagerFixed инициализирован успешно для проекта:', this.projectId);
    },
    
    attachEventHandlers: function() {
        const self = this;
        
        // Обработчики фильтров
        $('#photoFilterForm').on('submit', function(e) {
            e.preventDefault();
            self.applyFilters();
        });
        
        // Быстрые фильтры
        $(document).on('change', '#photoTypeFilter, #photoRoomFilter', function() {
            self.applyFilters();
        });
        
        // Кнопка сброса фильтров
        $('#resetPhotoFilters').on('click', function() {
            self.resetFilters();
        });
        
        // Обработчик загрузки фотографий
        this.attachUploadHandlers();
        
        console.log('PhotoManagerFixed: обработчики событий прикреплены');
    },
    
    attachUploadHandlers: function() {
        const self = this;
        
        // Обработчик формы загрузки фотографий
        $('#uploadPhotoForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            self.uploadPhotos();
        });
        
        // Обработчик кнопки загрузки
        $(document).off('click', '#uploadPhotosBtn')
                   .on('click', '#uploadPhotosBtn', function(e) {
            e.preventDefault();
            self.uploadPhotos();
        });
    },
    
    uploadPhotos: function() {
        console.log('=== НАЧАЛО ЗАГРУЗКИ ФОТОГРАФИЙ ===');
        
        const form = $('#uploadPhotoForm')[0];
        const formData = new FormData();
        
        if (!this.projectId) {
            this.showMessage('Ошибка: ID проекта не найден', 'error');
            return;
        }
        
        // Проверка выбранных файлов
        const files = $('#photoInput').prop('files');
        if (!files || files.length === 0) {
            this.showMessage('Пожалуйста, выберите фотографии для загрузки', 'warning');
            return;
        }
        
        // Добавляем файлы
        for (let i = 0; i < files.length; i++) {
            formData.append('photos[]', files[i]);
        }
        
        // Получаем метаданные из формы
        const category = $('#photoCategory').val();
        const location = $('#photoLocation').val();
        const description = $('#photoDescription').val();
        
        // ВАЖНО: Добавляем метаданные в FormData
        if (category && category.trim() !== '') {
            formData.append('category', category.trim());
            console.log('Добавлена категория фотографии:', category);
        }
        
        if (location && location.trim() !== '') {
            formData.append('location', location.trim());
            console.log('Добавлено помещение фотографии:', location);
        }
        
        if (description && description.trim() !== '') {
            formData.append('description', description.trim());
            console.log('Добавлено описание фотографии:', description);
        }
        
        console.log('Отправляемые данные фотографий:', {
            category: category,
            location: location,
            description: description,
            filesCount: files.length,
            projectId: this.projectId
        });
        
        // Показываем индикатор загрузки
        const uploadButton = $('#uploadPhotosBtn');
        const originalButtonText = uploadButton.html();
        uploadButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Загрузка...');
        
        // Показываем прогресс
        $('#photoUploadProgress').show();
        $('#photoUploadProgress .progress-bar').css('width', '0%').attr('aria-valuenow', 0).text('0%');
        
        // Отправляем AJAX запрос
        $.ajax({
            url: `/partner/projects/${this.projectId}/photos`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: () => {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        $('#photoUploadProgress .progress-bar')
                            .css('width', percentComplete + '%')
                            .attr('aria-valuenow', percentComplete)
                            .text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                console.log('=== ФОТОГРАФИИ ЗАГРУЖЕНЫ УСПЕШНО ===');
                console.log('Ответ сервера:', response);
                
                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadPhotoModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Показываем сообщение об успехе
                this.showMessage('Фотографии успешно загружены', 'success');
                
                // Очищаем форму
                form.reset();
                $('#photoPreview').empty();
                $('#photoPreviewContainer').hide();
                
                // Восстанавливаем кнопку
                uploadButton.prop('disabled', false).html(originalButtonText);
                
                // Скрываем прогресс
                $('#photoUploadProgress').hide();
                $('#photoUploadProgress .progress-bar').css('width', '0%').attr('aria-valuenow', 0).text('0%');
                
                // Обновляем список фотографий
                this.loadFiles();
            },
            error: (xhr, status, error) => {
                console.error('=== ОШИБКА ЗАГРУЗКИ ФОТОГРАФИЙ ===');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                
                let message = 'Ошибка при загрузке фотографий';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message = errors.join(', ');
                    }
                }
                
                this.showMessage(message, 'error');
                
                // Восстанавливаем кнопку
                uploadButton.prop('disabled', false).html(originalButtonText);
                
                // Скрываем прогресс
                $('#photoUploadProgress').hide();
                $('#photoUploadProgress .progress-bar').css('width', '0%').attr('aria-valuenow', 0).text('0%');
            }
        });
    },
    
    loadFiles: function() {
        console.log('PhotoManagerFixed: загрузка файлов...');
        
        if (!this.projectId) {
            console.error('PhotoManagerFixed: Project ID не найден');
            return;
        }
        
        // Показываем индикатор загрузки
        this.showLoadingIndicator();
        
        const params = new URLSearchParams({
            page: this.currentPage,
            per_page: this.perPage,
            ...this.filters
        });
        
        $.ajax({
            url: `/partner/projects/${this.projectId}/photos?${params}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                console.log('PhotoManagerFixed: файлы загружены', response);
                
                if (response.photos) {
                    this.data = Array.isArray(response.photos.data) ? response.photos.data : response.photos;
                    this.totalPages = response.photos.last_page || 1;
                    this.currentPage = response.photos.current_page || 1;
                } else if (response.data) {
                    this.data = Array.isArray(response.data) ? response.data : [];
                    this.totalPages = response.last_page || 1;
                    this.currentPage = response.current_page || 1;
                } else {
                    this.data = Array.isArray(response) ? response : [];
                }
                
                this.renderFiles();
                this.updatePagination();
                this.hideLoadingIndicator();
                
                // Обновляем счетчик
                $('#photoCount').text(this.data.length);
                
                console.log(`PhotoManagerFixed: отображено ${this.data.length} фотографий`);
            },
            error: (xhr, status, error) => {
                console.error('PhotoManagerFixed: ошибка загрузки файлов', error);
                this.hideLoadingIndicator();
                this.showMessage('Ошибка загрузки фотографий', 'error');
            }
        });
    },
    
    renderFiles: function() {
        const gallery = $('#photosGrid, #photosGallery');
        gallery.empty();
        
        if (this.data.length === 0) {
            gallery.append(`
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-camera display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Фотографии не найдены</h5>
                        <p class="text-muted">Загрузите первую фотографию для проекта</p>
                    </div>
                </div>
            `);
            return;
        }
        
        this.data.forEach(photo => {
            gallery.append(this.createPhotoCard(photo));
        });
    },
    
    createPhotoCard: function(photo) {
        const photoCategory = this.getPhotoCategoryName(photo.category || photo.type);
        const photoLocation = this.getPhotoLocationName(photo.location || photo.room);
        const createdDate = this.formatDate(photo.created_at);
        const photoUrl = photo.thumbnail_url || photo.url || '#';
        
        return `
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="photo-card" data-photo-id="${photo.id}">
                    <div class="photo-preview">
                        <img src="${photoUrl}" alt="${photo.name || 'Фотография'}" loading="lazy" class="img-fluid">
                        <div class="photo-overlay">
                            <div class="d-flex gap-2">
                                <button class="btn btn-light btn-sm view-photo" data-photo-id="${photo.id}" title="Просмотреть">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="${photoUrl}" class="btn btn-light btn-sm" download title="Скачать">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button class="btn btn-danger btn-sm delete-photo" data-photo-id="${photo.id}" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="photo-info p-3">
                        <h6 class="photo-title mb-1">${photo.name || 'Фотография'}</h6>
                        <div class="photo-meta">
                            ${photoCategory ? `<span class="badge bg-primary me-2">${photoCategory}</span>` : ''}
                            ${photoLocation ? `<span class="badge bg-info me-2">${photoLocation}</span>` : ''}
                        </div>
                        <small class="text-muted d-block mt-2">
                            ${createdDate}
                            ${photo.description ? ` • ${photo.description}` : ''}
                        </small>
                    </div>
                </div>
            </div>
        `;
    },
    
    getPhotoCategoryName: function(category) {
        if (!category) return '';
        
        const categoryNames = {
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
        
        return categoryNames[category] || category;
    },
    
    getPhotoLocationName: function(location) {
        if (!location) return '';
        
        const locationNames = {
            'kitchen': 'Кухня',
            'living_room': 'Гостиная',
            'bedroom': 'Спальня',
            'bathroom': 'Ванная',
            'toilet': 'Туалет',
            'hallway': 'Прихожая',
            'balcony': 'Балкон',
            'other': 'Другое'
        };
        
        return locationNames[location] || location;
    },
    
    formatDate: function(dateString) {
        if (!dateString) return 'Дата неизвестна';
        
        const date = new Date(dateString);
        const options = { 
            day: '2-digit', 
            month: '2-digit',
            year: 'numeric'
        };
        
        return date.toLocaleDateString('ru-RU', options);
    },
    
    applyFilters: function() {
        this.filters = {
            type: $('#photoTypeFilter').val(),
            room: $('#photoRoomFilter').val(),
            category: $('#photoCategoryFilter').val()
        };
        
        // Убираем пустые фильтры
        Object.keys(this.filters).forEach(key => {
            if (!this.filters[key]) {
                delete this.filters[key];
            }
        });
        
        this.currentPage = 1;
        this.loadFiles();
    },
    
    resetFilters: function() {
        $('#photoFilterForm')[0].reset();
        this.filters = {};
        this.currentPage = 1;
        this.loadFiles();
    },
    
    showLoadingIndicator: function() {
        $('#photosSpinner, #photoLoadingIndicator').show();
        $('#photosGrid, #photosGallery').hide();
    },
    
    hideLoadingIndicator: function() {
        $('#photosSpinner, #photoLoadingIndicator').hide();
        $('#photosGrid, #photosGallery').show();
    },
    
    updatePagination: function() {
        // Здесь можно добавить логику пагинации если нужно
    },
    
    viewPhoto: function(photoId) {
        console.log('PhotoManagerFixed.viewPhoto() вызван с ID:', photoId);
        
        if (!photoId) {
            console.error('PhotoManagerFixed: ID фотографии не предоставлен');
            return;
        }
        
        // Находим фотографию в данных
        const photo = this.data.find(p => p.id == photoId);
        if (!photo) {
            console.error('PhotoManagerFixed: фотография с ID', photoId, 'не найдена');
            this.showMessage('Фотография не найдена', 'error');
            return;
        }
        
        // Открываем фотографию в новом окне
        if (photo.url) {
            window.open(photo.url, '_blank');
        } else {
            this.showMessage('URL фотографии не найден', 'error');
        }
    },
    
    confirmDelete: function(photoId) {
        console.log('PhotoManagerFixed.confirmDelete() вызван с ID:', photoId);
        
        if (!photoId) {
            console.error('PhotoManagerFixed: ID фотографии не предоставлен');
            return;
        }
        
        const photo = this.data.find(p => p.id == photoId);
        if (!photo) {
            console.error('PhotoManagerFixed: фотография с ID', photoId, 'не найдена');
            this.showMessage('Фотография не найдена', 'error');
            return;
        }
        
        if (confirm(`Вы уверены, что хотите удалить фотографию "${photo.name || 'без названия'}"?`)) {
            this.deleteFile(photoId);
        }
    },
    
    deleteFile: function(photoId) {
        console.log('PhotoManagerFixed: удаление фотографии с ID:', photoId);
        
        $.ajax({
            url: `/partner/projects/${this.projectId}/photos/${photoId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                console.log('PhotoManagerFixed: фотография удалена успешно');
                this.showMessage('Фотография успешно удалена', 'success');
                this.loadFiles();
            },
            error: (xhr, status, error) => {
                console.error('PhotoManagerFixed: ошибка удаления фотографии', error);
                this.showMessage('Ошибка при удалении фотографии', 'error');
            }
        });
    },
    
    showMessage: function(message, type = 'info') {
        if (typeof window.showMessage === 'function') {
            window.showMessage(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
            alert(message);
        }
    }
};

// Добавляем обработчики событий для карточек фотографий
$(document).on('click', '.view-photo', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const photoId = $(this).data('photo-id');
    window.PhotoManagerFixed.viewPhoto(photoId);
});

$(document).on('click', '.delete-photo', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const photoId = $(this).data('photo-id');
    window.PhotoManagerFixed.confirmDelete(photoId);
});

console.log('Photo Manager Fixed loaded successfully - v2.0');
