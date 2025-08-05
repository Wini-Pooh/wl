@extends('partner.projects.layouts.project-base')

@section('page-content')
    @include('partner.projects.tabs.photos')
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            console.log('=== СТРАНИЦА ФОТОГРАФИЙ ===');
            
            // Простая инициализация без AJAX
            initPhotosPage();
        });
        
        // Функция для перезагрузки фотографий (упрощена)
        window.reloadPhotos = function() {
            console.log('📸 Перезагрузка фотографий упрощена (без AJAX)');
            initPhotosPage();
        };
        
        function initPhotosPage() {
            console.log('📸 Инициализация страницы фотографий...');
            
            // Инициализируем обработчики
            initPhotosHandlers();
            
            // Инициализируем фильтры
            initPhotosFilters();
            
            // Загружаем тестовые данные
            loadPhotos();
        }
        
        function loadPhotos() {
            console.log('📸 Загрузка фотографий проекта...');
            
            // Показываем индикатор загрузки
            showPhotosLoading();
            
            const projectId = window.projectId;
            if (!projectId) {
                console.error('❌ Project ID не найден');
                showMessage('Ошибка: ID проекта не найден', 'error');
                return;
            }
            
            // Загружаем реальные фотографии с сервера
            $.ajax({
                url: `/partner/projects/${projectId}/photos`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('✅ Фотографии загружены:', response);
                    hidePhotosLoading();
                    
                    if (response.success && response.files) {
                        displayPhotos(response.files);
                        updateFiltersWithData(response.files);
                    } else {
                        displayPhotos([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Ошибка загрузки фотографий:', error);
                    hidePhotosLoading();
                    
                    // Показываем тестовые данные как fallback
                    const testPhotos = [
                        {
                            id: 1,
                            name: 'До ремонта - гостиная.jpg',
                            category: 'before',
                            location: 'living_room',
                            size: 2048576,
                            url: '#',
                            created_at: '2024-01-15'
                        },
                        {
                            id: 2,
                            name: 'Процесс работ - кухня.jpg',
                            category: 'process',
                            location: 'kitchen',
                            size: 3145728,
                            url: '#',
                            created_at: '2024-01-12'
                        },
                        {
                            id: 3,
                            name: 'После ремонта - спальня.jpg',
                            category: 'after',
                            location: 'bedroom',
                            size: 1572864,
                            url: '#',
                            created_at: '2024-01-10'
                        }
                    ];
                    
                    console.log('📸 Показываем тестовые данные как fallback');
                    displayPhotos(testPhotos);
                    showMessage('Не удалось загрузить фотографии с сервера, показаны тестовые данные', 'warning');
                }
            });
        }
        
        function displayPhotos(photos) {
            const gallery = $('#photoGallery');
            const emptyState = $('#emptyPhotoState');
            const loading = $('#photoLoadingIndicator');
            
            // Скрываем загрузку
            loading.hide();
            
            if (photos.length === 0) {
                // Показываем пустое состояние
                gallery.hide();
                emptyState.show();
                $('#photoCount').text('0');
                return;
            }
            
            // Показываем галерею
            emptyState.hide();
            gallery.show().empty();
            
            // Создаем карточки фотографий
            photos.forEach(photo => {
                const photoCard = createPhotoCard(photo);
                gallery.append(photoCard);
            });
            
            // Обновляем счетчик
            $('#photoCount').text(photos.length);
            
            // Переинициализируем обработчики
            initPhotosHandlers();
        }
        
        function createPhotoCard(photo) {
            const categoryName = getCategoryName(photo.category);
            const locationName = getLocationName(photo.location);
            
            // Генерируем URL для изображения
            let imageUrl = '#';
            if (photo.url) {
                imageUrl = photo.url;
            } else if (photo.path) {
                imageUrl = `/storage/${photo.path}`;
            } else if (photo.filename) {
                const projectId = window.projectId;
                imageUrl = `/storage/projects/${projectId}/photos/${photo.filename}`;
            }
            
            return `
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card photo-card" data-id="${photo.id}" data-category="${photo.category}" data-location="${photo.location}">
                        <div class="photo-preview position-relative">
                            <img src="${imageUrl}" alt="${photo.name || photo.original_name || photo.filename}" 
                                 class="card-img-top" style="height: 200px; object-fit: cover;"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGVlMmU2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPtCk0L7RgtC+PC90ZXh0Pjwvc3ZnPg=='">
                            <div class="photo-badges position-absolute top-0 start-0 p-2">
                                <span class="badge bg-primary me-1">${categoryName}</span>
                                <span class="badge bg-secondary">${locationName}</span>
                            </div>
                            <div class="photo-actions position-absolute top-0 end-0 p-2">
                                <button class="btn btn-sm btn-light view-photo-btn me-1" data-photo-id="${photo.id}" title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light delete-photo-btn" data-photo-id="${photo.id}" title="Удалить">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title text-truncate mb-2" title="${photo.name || photo.original_name || photo.filename}">
                                ${photo.name || photo.original_name || photo.filename}
                            </h6>
                            <div class="text-muted small">
                                ${photo.file_size ? formatFileSize(photo.file_size) : ''}
                                ${photo.created_at ? `<br>${formatDate(photo.created_at)}` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        }
        
        function initPhotosHandlers() {
            console.log('🎯 Инициализация обработчиков фотографий...');
            
            // Обработчик просмотра фото
            $(document).off('click', '.view-photo-btn').on('click', '.view-photo-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const photoId = $(this).data('photo-id');
                viewPhoto(photoId);
            });
            
            // Обработчик удаления фото
            $(document).off('click', '.delete-photo-btn').on('click', '.delete-photo-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const photoId = $(this).data('photo-id');
                confirmDeletePhoto(photoId);
            });
            
            // Клик по карточке для просмотра
            $(document).off('click', '.photo-card').on('click', '.photo-card', function(e) {
                if (!$(e.target).closest('.photo-actions').length) {
                    const photoId = $(this).data('id');
                    viewPhoto(photoId);
                }
            });
        }
        
        function initPhotosFilters() {
            console.log('🔍 Инициализация фильтров фотографий...');
            
            // Обработчики фильтров
            $('.photo-filter').off('change input').on('change input', function() {
                applyPhotoFilters();
            });
            
            // Поиск с задержкой
            let searchTimeout;
            $('#photoSearchFilter').off('input').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyPhotoFilters();
                }, 300);
            });
            
            // Сброс фильтров
            $('#clearAllFilters').off('click').on('click', function() {
                $('.photo-filter').val('');
                $('#photoSortFilter').val('newest');
                applyPhotoFilters();
                showMessage('Фильтры сброшены', 'success');
            });
        }
        
        function applyPhotoFilters() {
            // Простая фильтрация на клиенте
            const category = $('#photoTypeFilter').val();
            const location = $('#photoLocationFilter').val();
            const search = $('#photoSearchFilter').val().toLowerCase();
            const sort = $('#photoSortFilter').val();
            
            const cards = $('.photo-card').parent();
            let visibleCount = 0;
            
            cards.each(function() {
                const card = $(this);
                const cardCategory = card.find('.badge.bg-primary').text();
                const cardLocation = card.find('.badge.bg-secondary').text();
                const cardTitle = card.find('.card-title').text().toLowerCase();
                
                let show = true;
                
                if (category && !cardCategory.includes(category)) show = false;
                if (location && !cardLocation.includes(location)) show = false;
                if (search && !cardTitle.includes(search)) show = false;
                
                if (show) {
                    card.show();
                    visibleCount++;
                } else {
                    card.hide();
                }
            });
            
            updatePhotoCount(visibleCount);
        }
        
        function showPhotosLoading() {
            const loadingIndicator = $('#photoLoadingIndicator');
            const gallery = $('#photoGallery');
            const emptyState = $('#emptyPhotoState');
            
            // Показываем индикатор загрузки
            loadingIndicator.show();
            gallery.hide();
            emptyState.hide();
        }
        
        function hidePhotosLoading() {
            const loadingIndicator = $('#photoLoadingIndicator');
            loadingIndicator.hide();
        }
        
        function viewPhoto(photoId) {
            console.log('👁️ Просмотр фото:', photoId);
            
            const projectId = window.projectId;
            if (!projectId || !photoId) {
                showMessage('Ошибка: Не найдены параметры для просмотра фото', 'error');
                return;
            }
            
            // Открываем фото в новой вкладке
            const viewUrl = `/partner/projects/${projectId}/photos/${photoId}`;
            window.open(viewUrl, '_blank');
        }
        
        function confirmDeletePhoto(photoId) {
            console.log('🗑️ Подтверждение удаления фото:', photoId);
            
            if (!photoId) {
                showMessage('Ошибка: ID фотографии не найден', 'error');
                return;
            }
            
            if (confirm('Вы уверены, что хотите удалить эту фотографию? Это действие нельзя отменить.')) {
                deletePhoto(photoId);
            }
        }
        
        function deletePhoto(photoId) {
            console.log('🗑️ Удаление фото:', photoId);
            
            const projectId = window.projectId;
            if (!projectId || !photoId) {
                showMessage('Ошибка: Не найдены параметры для удаления фото', 'error');
                return;
            }
            
            // Отправляем запрос на удаление
            $.ajax({
                url: `/partner/projects/${projectId}/photos/${photoId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('✅ Фото удалено:', response);
                    
                    if (response.success) {
                        showMessage('Фотография успешно удалена', 'success');
                        // Перезагружаем список фотографий
                        loadPhotos();
                    } else {
                        showMessage(response.message || 'Ошибка удаления фотографии', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Ошибка удаления фото:', error);
                    
                    let errorMessage = 'Ошибка удаления фотографии';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showMessage(errorMessage, 'error');
                }
            });
        }
        
        function updatePhotoCount(count) {
            $('#photoCount').text(count);
        }
        
        function updateFiltersWithData(photos) {
            console.log('🔄 Обновляем фильтры с данными из фотографий...');
            
            // Собираем уникальные категории и локации
            const categories = new Set();
            const locations = new Set();
            
            photos.forEach(photo => {
                if (photo.category) {
                    categories.add(photo.category);
                }
                if (photo.location) {
                    locations.add(photo.location);
                }
            });
            
            // Обновляем фильтры категорий
            updateCategoryFilter(Array.from(categories));
            
            // Обновляем фильтры локаций
            updateLocationFilter(Array.from(locations));
        }
        
        function updateCategoryFilter(dynamicCategories) {
            const categorySelects = ['#photoTypeFilter', '#photoTypeFilterMobile'];
            
            // Базовые предустановленные категории
            const baseCategories = [
                { value: 'before', label: 'До ремонта' },
                { value: 'after', label: 'После ремонта' },
                { value: 'process', label: 'Процесс работы' },
                { value: 'progress', label: 'Ход работ' },
                { value: 'materials', label: 'Материалы' },
                { value: 'problems', label: 'Проблемы' },
                { value: 'design', label: 'Дизайн' },
                { value: 'furniture', label: 'Мебель' },
                { value: 'decor', label: 'Декор' },
                { value: 'demolition', label: 'Демонтаж' },
                { value: 'floors', label: 'Полы' },
                { value: 'walls', label: 'Стены' },
                { value: 'ceiling', label: 'Потолки' },
                { value: 'electrical', label: 'Электрика' },
                { value: 'plumbing', label: 'Сантехника' },
                { value: 'heating', label: 'Отопление' },
                { value: 'doors', label: 'Двери' },
                { value: 'windows', label: 'Окна' },
                { value: 'documentation', label: 'Документация' }
            ];
            
            categorySelects.forEach(selectId => {
                const select = $(selectId);
                if (select.length) {
                    const currentValue = select.val();
                    
                    // Очищаем и добавляем базовую опцию
                    select.empty().append('<option value="">Все типы</option>');
                    
                    // Добавляем базовые категории
                    baseCategories.forEach(cat => {
                        select.append(`<option value="${cat.value}">${cat.label}</option>`);
                    });
                    
                    // Добавляем динамические категории, которых нет в базовых
                    const baseValues = baseCategories.map(cat => cat.value);
                    dynamicCategories.forEach(category => {
                        if (category && !baseValues.includes(category)) {
                            const label = getCategoryName(category);
                            select.append(`<option value="${category}">${label}</option>`);
                        }
                    });
                    
                    // Восстанавливаем выбранное значение
                    if (currentValue) {
                        select.val(currentValue);
                    }
                }
            });
        }
        
        function updateLocationFilter(dynamicLocations) {
            const locationSelects = ['#photoLocationFilter', '#photoLocationFilterMobile'];
            
            // Базовые предустановленные локации
            const baseLocations = [
                { value: 'kitchen', label: 'Кухня' },
                { value: 'living_room', label: 'Гостиная' },
                { value: 'bedroom', label: 'Спальня' },
                { value: 'bathroom', label: 'Ванная' },
                { value: 'toilet', label: 'Туалет' },
                { value: 'hallway', label: 'Прихожая' },
                { value: 'balcony', label: 'Балкон' },
                { value: 'corridor', label: 'Коридор' },
                { value: 'pantry', label: 'Кладовая' },
                { value: 'garage', label: 'Гараж' },
                { value: 'basement', label: 'Подвал' },
                { value: 'attic', label: 'Чердак' },
                { value: 'terrace', label: 'Терраса' }
            ];
            
            locationSelects.forEach(selectId => {
                const select = $(selectId);
                if (select.length) {
                    const currentValue = select.val();
                    
                    // Очищаем и добавляем базовую опцию
                    select.empty().append('<option value="">Все помещения</option>');
                    
                    // Добавляем базовые локации
                    baseLocations.forEach(loc => {
                        select.append(`<option value="${loc.value}">${loc.label}</option>`);
                    });
                    
                    // Добавляем динамические локации, которых нет в базовых
                    const baseValues = baseLocations.map(loc => loc.value);
                    dynamicLocations.forEach(location => {
                        if (location && !baseValues.includes(location)) {
                            const label = getLocationName(location) || location;
                            select.append(`<option value="${location}">${label}</option>`);
                        }
                    });
                    
                    // Восстанавливаем выбранное значение
                    if (currentValue) {
                        select.val(currentValue);
                    }
                }
            });
        }
        
        function getCategoryName(category) {
            const categories = {
                'before': 'До ремонта',
                'after': 'После ремонта',
                'process': 'Процесс работы',
                'progress': 'Ход работ',
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
                'windows': 'Окна',
                'documentation': 'Документация'
            };
            
            // Если есть готовый перевод, используем его, иначе возвращаем как есть
            return categories[category] || category || 'Без категории';
        }
        
        function getLocationName(location) {
            const locations = {
                'kitchen': 'Кухня',
                'living_room': 'Гостиная',
                'bedroom': 'Спальня',
                'bathroom': 'Ванная',
                'toilet': 'Туалет',
                'hallway': 'Прихожая',
                'balcony': 'Балкон',
                'corridor': 'Коридор',
                'pantry': 'Кладовая',
                'garage': 'Гараж',
                'basement': 'Подвал',
                'attic': 'Чердак',
                'terrace': 'Терраса'
            };
            
            // Если есть готовый перевод, используем его, иначе возвращаем как есть
            return locations[location] || location || '';
        }
        
        function formatFileSize(bytes) {
            if (!bytes) return '0 Б';
            const sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(1)) + ' ' + sizes[i];
        }
        
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(date);
        }
        
        // Функция для перезагрузки фотографий (вызывается после загрузки)
        window.reloadPhotos = function() {
            console.log('🔄 Перезагрузка фотографий...');
            loadPhotos();
        };
        
        // Функция для показа сообщений
        function showMessage(message, type = 'info') {
            console.log(`📢 Сообщение (${type}):`, message);
            
            if (window.modalManager) {
                if (type === 'success') {
                    window.modalManager.showSuccessToast(message);
                } else if (type === 'error') {
                    window.modalManager.showErrorToast(message);
                } else {
                    window.modalManager.showToast(message);
                }
            } else {
                // Fallback через обычный alert
                alert(message);
            }
        }
    </script>
@endsection

{{-- Подключение модальных окон --}}
@include('partner.projects.tabs.modals.photo-modal')
@include('partner.projects.tabs.modals.init-modals')
