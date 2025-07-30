/**
 * Исправленный DesignManager - сохранение типа дизайна, помещения и описания при загрузке
 * Версия: 2.0 - исправление потери метаданных при загрузке
 */

window.DesignManagerFixed = {
    projectId: null,
    data: [],
    filters: {},
    currentPage: 1,
    perPage: 12,
    totalPages: 1,
    
    init: function() {
        console.log('=== ИНИЦИАЛИЗАЦИЯ DESIGN MANAGER FIXED ===');
        
        this.projectId = window.projectId;
        
        if (!this.projectId) {
            console.warn('Project ID не найден для DesignManagerFixed');
            return;
        }
        
        // Проверяем наличие модального окна
        const modalExists = $('#uploadDesignModal').length > 0;
        const formExists = $('#uploadDesignForm').length > 0;
        console.log('Элементы формы:', {
            modal: modalExists,
            form: formExists,
            designType: $('#designType').length > 0,
            designRoom: $('#designRoom').length > 0,
            designDescription: $('#designDescription').length > 0
        });
        
        this.attachEventHandlers();
        this.loadFiles();
        
        console.log('DesignManagerFixed инициализирован успешно для проекта:', this.projectId);
    },
    
    attachEventHandlers: function() {
        console.log('Подключение обработчиков событий для DesignManagerFixed...');
        
        // Обработчик фильтров
        $('#designFilterForm').off('submit.designManagerFixed').on('submit.designManagerFixed', (e) => {
            e.preventDefault();
            this.applyFilters();
        });
        
        $('#clearDesignFilters').off('click.designManagerFixed').on('click.designManagerFixed', (e) => {
            e.preventDefault();
            this.resetFilters();
        });
        
        // Обработчики для карточек дизайна
        $(document).off('click.designManagerFixed', '.view-design').on('click.designManagerFixed', '.view-design', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const designId = $(e.currentTarget).data('id');
            this.viewDesign(designId);
        });
        
        $(document).off('click.designManagerFixed', '.delete-design').on('click.designManagerFixed', '.delete-design', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const designId = $(e.currentTarget).data('id');
            this.confirmDelete(designId);
        });
        
        // Обработчик клика по карточке
        $(document).off('click.designManagerFixed', '.design-card').on('click.designManagerFixed', '.design-card', (e) => {
            if (!$(e.target).closest('.design-actions').length) {
                const designId = $(e.currentTarget).data('id');
                this.viewDesign(designId);
            }
        });
        
        // Обработчик загрузки файлов
        this.attachUploadHandlers();
        
        console.log('Обработчики событий подключены для DesignManagerFixed');
    },
    
    attachUploadHandlers: function() {
        console.log('Подключение обработчиков загрузки...');
        
        // ПОЛНОСТЬЮ отключаем все старые обработчики
        $('#uploadDesignForm').off();
        $(document).off('click', 'button[type="submit"][form="uploadDesignForm"]');
        $(document).off('submit', '#uploadDesignForm');
        
        // Ждем немного и подключаем наши обработчики
        setTimeout(() => {
            // Подключаем новый обработчик формы
            $('#uploadDesignForm').on('submit.designManagerFixed', (e) => {
                console.log('=== ОБРАБОТЧИК ФОРМЫ ДИЗАЙНА СРАБОТАЛ ===');
                e.preventDefault();
                e.stopPropagation();
                this.uploadDesignFiles();
                return false;
            });
            
            // Подключаем обработчик кнопки отправки
            $(document).on('click.designManagerFixed', 'button[type="submit"][form="uploadDesignForm"]', (e) => {
                console.log('=== ОБРАБОТЧИК КНОПКИ ДИЗАЙНА СРАБОТАЛ ===');
                e.preventDefault();
                e.stopPropagation();
                this.uploadDesignFiles();
                return false;
            });
            
            console.log('Обработчики загрузки подключены');
        }, 100);
    },
    
    uploadDesignFiles: function() {
        console.log('=== НАЧАЛО ЗАГРУЗКИ ФАЙЛОВ ДИЗАЙНА (FIXED) ===');
        
        if (!this.projectId) {
            console.error('Project ID не найден');
            return;
        }
        
        // Проверка выбранных файлов
        const files = $('#designFiles').prop('files');
        if (!files || files.length === 0) {
            alert('Пожалуйста, выберите файлы дизайна для загрузки');
            return;
        }
        
        // Получаем метаданные из формы - ИСПРАВЛЕННАЯ ВЕРСИЯ
        const form = $('#uploadDesignForm');
        const designType = form.find('#designType').val() || form.find('select[name="type"]').val() || '';
        const designRoom = form.find('#designRoom').val() || form.find('select[name="room"]').val() || '';
        const description = form.find('#designDescription').val() || form.find('textarea[name="description"]').val() || '';
        
        console.log('Элементы формы найдены:', {
            designTypeElement: form.find('#designType').length || form.find('select[name="type"]').length,
            designRoomElement: form.find('#designRoom').length || form.find('select[name="room"]').length,
            descriptionElement: form.find('#designDescription').length || form.find('textarea[name="description"]').length
        });
        
        console.log('Метаданные формы дизайна:', {
            type: designType,
            room: designRoom,
            description: description,
            filesCount: files.length
        });
        
        // Создаем FormData правильно
        const formData = new FormData();
        
        // Добавляем файлы
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
            console.log(`Добавлен файл ${i + 1}:`, files[i].name);
        }
        
        // Добавляем метаданные - ВАЖНО: всегда добавляем, даже если пустые
        formData.append('type', designType.trim());
        formData.append('room', designRoom.trim());
        formData.append('description', description.trim());
        
        console.log('✓ Добавлен тип дизайна:', designType || '(пусто)');
        console.log('✓ Добавлено помещение:', designRoom || '(пусто)');
        console.log('✓ Добавлено описание:', description || '(пусто)');
        
        // Выводим все данные FormData для отладки
        console.log('=== СОДЕРЖИМОЕ FORMDATA ===');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(pair[0] + ': FILE - ' + pair[1].name);
            } else {
                console.log(pair[0] + ': ' + pair[1]);
            }
        }
        console.log('=== КОНЕЦ FORMDATA ===');
        
        // Отключаем кнопку отправки
        const submitBtn = $('button[type="submit"][form="uploadDesignForm"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Загрузка...');
        
        // Отправляем AJAX запрос
        $.ajax({
            url: `/partner/projects/${this.projectId}/design`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                console.log('✓ Ответ сервера:', response);
                
                // Закрываем модальное окно
                $('#uploadDesignModal').modal('hide');
                $('#uploadDesignForm')[0].reset();
                $('#designPreview').empty();
                submitBtn.prop('disabled', false).html(originalText);
                
                // Обновляем список файлов
                this.loadFiles();
                
                // Показываем сообщение об успехе
                this.showMessage('Файлы дизайна успешно загружены', 'success');
                
                console.log('✓ Загрузка завершена успешно');
            },
            error: (xhr, status, error) => {
                console.error('✗ Ошибка при загрузке файлов дизайна:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                submitBtn.prop('disabled', false).html(originalText);
                
                let errorMessage = 'Ошибка при загрузке файлов дизайна';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(', ');
                }
                
                this.showMessage(errorMessage, 'error');
            }
        });
    },
    
    loadFiles: function() {
        console.log('Загрузка файлов дизайна...');
        
        if (!this.projectId) {
            console.error('ID проекта не найден');
            return;
        }
        
        this.showLoadingIndicator();
        
        // Формируем параметры запроса
        const params = this.getFilterParams();
        
        $.ajax({
            url: `/partner/projects/${this.projectId}/design`,
            method: 'GET',
            data: params,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                console.log('Получены файлы дизайна:', response);
                
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
                } else {
                    this.hideEmptyState();
                }
            },
            error: (xhr) => {
                console.error('Ошибка загрузки файлов дизайна:', xhr);
                this.hideLoadingIndicator();
                this.showEmptyState(true);
            }
        });
    },
    
    renderFiles: function() {
        const gallery = $('#designGallery');
        gallery.empty();
        
        if (!this.data || this.data.length === 0) {
            return;
        }
        
        this.data.forEach(file => {
            const card = this.createDesignCard(file);
            gallery.append(card);
        });
    },
    
    createDesignCard: function(design) {
        const isImage = design.mime_type && design.mime_type.startsWith('image/');
        const designTypeName = design.design_type_name || this.getDesignTypeName(design.design_type || design.type);
        const roomName = design.room_name || this.getDesignRoomName(design.room);
        
        return `
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card design-card h-100" data-id="${design.id}">
                    <div class="design-badge">
                        <span class="badge bg-info">${designTypeName}</span>
                        ${roomName ? `<span class="badge bg-secondary ms-1">${roomName}</span>` : ''}
                    </div>
                    <div class="design-preview">
                        ${isImage ? 
                            `<img src="${design.url}" alt="${design.original_name || design.name}" onerror="this.src='/img/file-error.svg'">` : 
                            `<i class="bi bi-file-earmark display-1 text-secondary"></i>`
                        }
                    </div>
                    <div class="design-overlay"></div>
                    <div class="design-actions">
                        <button class="btn btn-sm btn-light view-design" data-id="${design.id}">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-design" data-id="${design.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title text-truncate mb-1" title="${design.original_name || design.name}">
                            ${design.original_name || design.name}
                        </h6>
                        ${design.description ? `<p class="card-text small text-muted mb-1">${design.description}</p>` : ''}
                        <p class="card-text small text-muted mb-0">
                            ${this.formatFileSize(design.file_size || design.size)} • ${this.formatDate(design.created_at)}
                        </p>
                    </div>
                </div>
            </div>
        `;
    },
    
    getDesignTypeName: function(type) {
        const types = {
            '3d': '3D визуализация',
            'layout': 'Планировка',
            'sketch': 'Эскиз',
            'render': 'Рендер',
            'draft': 'Черновик',
            'concept': 'Концепт',
            'mood_board': 'Мудборд',
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
            'other': 'Другое'
        };
        return rooms[room] || room || '';
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
    },
    
    formatFileSize: function(bytes) {
        if (!bytes) return '0 Б';
        
        const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    getFilterParams: function() {
        const params = {};
        
        const typeFilter = $('#designTypeFilter').val();
        if (typeFilter) {
            params.type = typeFilter;
        }
        
        const roomFilter = $('#designRoomFilter').val();
        if (roomFilter) {
            params.room = roomFilter;
        }
        
        const sortFilter = $('#designSortFilter').val();
        if (sortFilter) {
            params.sort = sortFilter;
        }
        
        return params;
    },
    
    applyFilters: function() {
        console.log('Применение фильтров дизайна...');
        this.loadFiles();
    },
    
    resetFilters: function() {
        console.log('Сброс фильтров дизайна...');
        $('#designFilterForm')[0].reset();
        this.loadFiles();
    },
    
    showLoadingIndicator: function() {
        $('#designLoadingIndicator').show();
        $('#designGallery').hide();
        $('#emptyDesignState').hide();
    },
    
    hideLoadingIndicator: function() {
        $('#designLoadingIndicator').hide();
        $('#designGallery').show();
    },
    
    showEmptyState: function(isError = false) {
        const emptyState = $('#emptyDesignState');
        
        if (isError) {
            emptyState.html(`
                <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                <h5 class="mt-3">Ошибка загрузки</h5>
                <p class="text-muted">Произошла ошибка при загрузке файлов дизайна</p>
                <button class="btn btn-primary" onclick="DesignManagerFixed.loadFiles()">Повторить</button>
            `);
        } else {
            emptyState.html(`
                <i class="bi bi-folder2-open display-1 text-muted"></i>
                <h5 class="mt-3">Нет файлов дизайна</h5>
                <p class="text-muted">Загрузите файлы дизайна проекта, нажав кнопку "Загрузить дизайн" вверху страницы</p>
            `);
        }
        
        emptyState.show();
    },
    
    hideEmptyState: function() {
        $('#emptyDesignState').hide();
    },
    
    updateCounter: function(count) {
        $('#designCount').text(count);
    },
    
    viewDesign: function(designId) {
        console.log('Просмотр дизайна:', designId);
        // Реализация просмотра файла
    },
    
    confirmDelete: function(designId) {
        if (confirm('Вы уверены, что хотите удалить этот файл дизайна? Это действие нельзя отменить.')) {
            this.deleteFile(designId);
        }
    },
    
    deleteFile: function(designId) {
        console.log('Удаление файла дизайна:', designId);
        
        $.ajax({
            url: `/partner/projects/${this.projectId}/design/${designId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.showMessage('Файл дизайна успешно удален', 'success');
                this.loadFiles();
            },
            error: (xhr) => {
                console.error('Ошибка при удалении файла дизайна:', xhr);
                this.showMessage('Ошибка при удалении файла дизайна', 'error');
            }
        });
    },
    
    showMessage: function(message, type = 'info') {
        if (typeof window.showMessage === 'function') {
            window.showMessage(message, type);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
            alert(message);
        }
    }
};

// Добавляем обработчики событий для карточек дизайна
$(document).on('click', '.view-design', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const designId = $(this).data('id');
    window.DesignManagerFixed.viewDesign(designId);
});

$(document).on('click', '.delete-design', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const designId = $(this).data('id');
    window.DesignManagerFixed.confirmDelete(designId);
});

console.log('Design Manager Fixed loaded successfully - v2.0');
