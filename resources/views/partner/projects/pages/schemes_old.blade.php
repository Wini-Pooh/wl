@extends('partner.projects.layouts.project-base')

@section('page-content')
    @include('partner.projects.tabs.schemes')
    
    <!-- Модальное окно для схем -->
    @include('partner.projects.tabs.modals.scheme-modal')
@endsection

@section('styles')
    @parent
    <!-- Дополнительные стили для страницы схем -->
    <style>
        /* Специфичные стили для страницы схем */
        .schemes-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .scheme-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
        }
        
        .scheme-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #28a745;
        }
        
        .scheme-preview {
            height: 220px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        .scheme-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .scheme-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .scheme-card:hover .scheme-actions {
            opacity: 1;
        }
        
        .scheme-type-badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .scheme-upload-zone {
            border: 2px dashed #28a745;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f8fff9;
            transition: all 0.3s ease;
        }
        
        .scheme-upload-zone:hover {
            border-color: #1e7e34;
            background: #f1f8f4;
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            console.log('=== СТРАНИЦА СХЕМ ПРОЕКТА ===');
            
            // Инициализация через ProjectManager
            if (window.projectManager) {
                window.projectManager.initPage('schemes', function() {
                    console.log('✅ Страница схем инициализирована через ProjectManager');
                    loadSchemes();
                    initSchemesHandlers();
                });
            } else {
                console.warn('⚠️ ProjectManager не найден, используем прямую инициализацию');
                initSchemesPage();
            }
        });
        
        function initSchemesPage() {
            console.log('Инициализация страницы схем...');
            
            // Загружаем схемы
            loadSchemes();
            
            // Инициализируем обработчики
            initSchemesHandlers();
        }
        
        function loadSchemes() {
            console.log('� Загрузка схем...');
            
            $('#schemeLoadingIndicator').show();
            $('#schemeGallery').hide();
            
            // Имитируем задержку и показываем тестовые данные
            setTimeout(() => {
                const testSchemes = [
                    {
                        id: 1,
                        name: 'Планировочная схема 1 этаж',
                        original_name: 'plan_floor_1.dwg',
                        file_type: 'application/dwg',
                        file_size: 2048000,
                        url: '/storage/schemes/plan_floor_1.dwg',
                        uploaded_at: '2024-01-15 10:30:00',
                        scheme_type: 'Планировка',
                        scale: '1:100'
                    },
                    {
                        id: 2,
                        name: 'Схема электроснабжения',
                        original_name: 'electrical_scheme.pdf',
                        file_type: 'application/pdf',
                        file_size: 1536000,
                        url: '/storage/schemes/electrical_scheme.pdf',
                        uploaded_at: '2024-01-14 14:45:00',
                        scheme_type: 'Электрика',
                        scale: '1:50'
                    },
                    {
                        id: 3,
                        name: 'Схема водоснабжения',
                        original_name: 'water_supply.jpg',
                        file_type: 'image/jpeg',
                        file_size: 3072000,
                        url: '/storage/schemes/water_supply.jpg',
                        uploaded_at: '2024-01-13 16:20:00',
                        scheme_type: 'Сантехника',
                        scale: '1:75'
                    }
                ];
                
                $('#schemeLoadingIndicator').hide();
                displaySchemes(testSchemes);
            }, 1000);
        }
        
        function displaySchemes(schemes) {
            const gallery = $('#schemeGallery');
            const emptyState = $('#emptySchemeState');
            const loading = $('#schemeLoadingIndicator');
            
            // Скрываем загрузку
            loading.hide();
            
            if (schemes.length === 0) {
                // Показываем пустое состояние
                gallery.hide();
                emptyState.show();
                $('#schemeCount').text('0');
                return;
            }
            
            // Показываем галерею
            emptyState.hide();
            gallery.show().empty();
            
            // Создаем карточки схем
            schemes.forEach(scheme => {
                const schemeCard = createSchemeCard(scheme);
                gallery.append(schemeCard);
            });
            
            // Обновляем счетчик
            $('#schemeCount').text(schemes.length);
            
            // Переинициализируем обработчики
            initSchemesHandlers();
        }
        
        function createSchemeCard(scheme) {
            console.log('🎨 Создание карточки схемы:', scheme);
            
            // Обрабатываем кастомные значения
            let schemeType = scheme.scheme_type || scheme.type;
            let room = scheme.room || scheme.location;
            
            // Если это кастомные значения (не в базовом списке), используем их как есть
            const typeName = getSchemeTypeName(schemeType) || schemeType || 'Схема';
            const roomName = getRoomName(room) || room || '';
            
            const fileSize = formatFileSize(scheme.file_size || scheme.size);
            const date = formatDate(scheme.created_at);
            const fileExtension = getFileExtension(scheme.name || scheme.file_name || scheme.original_name);
            const isImage = isImageFile(fileExtension);
            
            // Проверяем наличие URL файла
            const fileUrl = scheme.url || scheme.thumbnail_url || '/storage/' + scheme.path;
            
            return `
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card scheme-card h-100" data-id="${scheme.id}" data-type="${schemeType}" data-room="${room}">
                        <div class="scheme-preview position-relative">
                            ${isImage ? 
                                `<img src="${fileUrl}" alt="${scheme.name || scheme.file_name || scheme.original_name}" 
                                     class="img-fluid w-100" style="height: 200px; object-fit: cover;"
                                     onerror="this.src='/images/no-image-placeholder.png'">` :
                                `<div class="d-flex align-items-center justify-content-center h-100" style="height: 200px;">
                                     <i class="bi bi-file-earmark-${getFileIcon(fileExtension)} display-3 text-success"></i>
                                 </div>`
                            }
                            <div class="scheme-badges position-absolute top-0 start-0 p-2">
                                <span class="badge bg-success mb-1" title="Тип: ${typeName}">${typeName}</span>
                                ${roomName ? `<br><span class="badge bg-secondary" title="Помещение: ${roomName}">${roomName}</span>` : ''}
                                ${scheme.scale ? `<br><span class="badge bg-info text-dark" title="Масштаб: ${scheme.scale}">${scheme.scale}</span>` : ''}
                            </div>
                            <div class="scheme-actions position-absolute top-0 end-0 p-2">
                                <button class="btn btn-sm btn-light view-scheme-btn me-1" data-scheme-id="${scheme.id}" title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light download-scheme-btn me-1" data-scheme-id="${scheme.id}" title="Скачать">
                                    <i class="bi bi-download"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-scheme-btn" data-scheme-id="${scheme.id}" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title text-truncate" title="${scheme.name || scheme.file_name || scheme.original_name}">
                                ${scheme.name || scheme.file_name || scheme.original_name}
                            </h6>
                            <small class="text-muted">
                                ${fileSize} • ${date}
                                ${scheme.version ? ` • ${scheme.version}` : ''}
                            </small>
                            ${scheme.description || scheme.comment ? `<br><small class="text-muted">${scheme.description || scheme.comment}</small>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
        
        function initSchemesHandlers() {
            console.log('🎯 Инициализация обработчиков схем...');
            
            // Обработчик просмотра схемы
            $(document).off('click', '.view-scheme-btn').on('click', '.view-scheme-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const schemeId = $(this).data('scheme-id');
                viewScheme(schemeId);
            });
            
            // Обработчик скачивания схемы
            $(document).off('click', '.download-scheme-btn').on('click', '.download-scheme-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const schemeId = $(this).data('scheme-id');
                downloadScheme(schemeId);
            });
            
            // Обработчик удаления схемы
            $(document).off('click', '.delete-scheme-btn').on('click', '.delete-scheme-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const schemeId = $(this).data('scheme-id');
                confirmDeleteScheme(schemeId);
            });
            
            // Клик по карточке для просмотра
            $(document).off('click', '.scheme-card').on('click', '.scheme-card', function(e) {
                if (!$(e.target).closest('.scheme-actions').length) {
                    const schemeId = $(this).data('id');
                    viewScheme(schemeId);
                }
            });
        }
        
        function initSchemesFilters() {
            console.log('🔍 Инициализация фильтров схем...');
            
            // Загружаем кастомные опции для фильтров
            loadCustomSchemeFilterOptions();
            
            // Обработчики фильтров
            $('.scheme-filter').off('change input').on('change input', function() {
                applySchemeFilters();
            });
            
            // Поиск с задержкой
            let searchTimeout;
            $('#schemeSearchFilter').off('input').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applySchemeFilters();
                }, 300);
            });
            
            // Сброс фильтров
            $('#clearSchemeFilters').off('click').on('click', function() {
                $('.scheme-filter').val('');
                $('#schemeSortFilter').val('created_at_desc');
                applySchemeFilters();
                showMessage('Фильтры сброшены', 'success');
            });
            
            // Переключение расширенных фильтров
            $('#toggleSchemeFiltersBtn').off('click').on('click', function() {
                const advancedFilters = $('#advancedSchemeFilters');
                const icon = $(this).find('i');
                
                if (advancedFilters.is(':visible')) {
                    advancedFilters.slideUp();
                    icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                } else {
                    advancedFilters.slideDown();
                    icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                }
            });
            
            // Очистка поиска
            $('#clearSchemeSearchBtn').off('click').on('click', function() {
                $('#schemeSearchFilter').val('');
                applySchemeFilters();
            });
        }
        
        function viewScheme(schemeId) {
            const projectId = window.projectId;
            if (!projectId || !schemeId) {
                console.error('❌ Missing projectId or schemeId:', { projectId, schemeId });
                return;
            }
            
            console.log('👁️ Просмотр схемы:', { projectId, schemeId });
            
            // Создаем модальное окно для просмотра схемы
            const modalHtml = `
                <div class="modal fade" id="schemeViewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Просмотр схемы</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Добавляем модальное окно в DOM
            $('body').append(modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('schemeViewModal'));
            modal.show();
            
            // Загружаем данные схемы (отключено)
            console.log('👁️ Функция просмотра схем временно отключена');
            
            setTimeout(() => {
                $('#schemeViewModal .modal-body').html(`
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Функция просмотра схем временно отключена
                    </div>
                `);
            }, 500);
            
            // Удаляем модальное окно после закрытия
            $('#schemeViewModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
        
        function downloadScheme(schemeId) {
            console.log('⬇️ Скачивание схемы:', schemeId);
            
            const projectId = window.projectId;
            if (!projectId || !schemeId) return;
            
            // Создаем скрытую ссылку для скачивания
            const link = document.createElement('a');
            link.href = `/partner/projects/${projectId}/schemes/${schemeId}/download`;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        function confirmDeleteScheme(schemeId) {
            console.log('🗑️ Удаление схемы (без AJAX):', schemeId);
            
            // Простое уведомление вместо AJAX запроса
            if (confirm('Функция удаления схем временно отключена. Продолжить?')) {
                showMessage('Функция удаления схем временно отключена', 'info');
            }
        }
        
        function applySchemeFilters() {
            // Простая фильтрация на клиенте
            const schemeType = $('#schemeTypeFilter').val();
            const room = $('#schemeRoomFilter').val();
            const search = $('#schemeSearchFilter').val().toLowerCase();
            const sort = $('#schemeSortFilter').val();
            const dateFrom = $('#schemeDateFromFilter').val();
            const dateTo = $('#schemeDateToFilter').val();
            
            const cards = $('.scheme-card').parent();
            let visibleCount = 0;
            
            cards.each(function() {
                const card = $(this);
                const cardType = card.find('.badge.bg-success').text();
                const cardRoom = card.find('.badge.bg-secondary').text();
                const cardTitle = card.find('.card-title').text().toLowerCase();
                const cardDate = card.find('.text-muted').text();
                
                let show = true;
                
                if (schemeType && !cardType.includes(getSchemeTypeName(schemeType))) show = false;
                if (room && !cardRoom.includes(getRoomName(room))) show = false;
                if (search && !cardTitle.includes(search)) show = false;
                
                // Фильтрация по дате (упрощенная)
                if (dateFrom || dateTo) {
                    // Здесь можно добавить более сложную логику фильтрации по дате
                }
                
                if (show) {
                    card.show();
                    visibleCount++;
                } else {
                    card.hide();
                }
            });
            
            updateSchemeCount(visibleCount);
            updateActiveFiltersText();
        }
        
        function updateSchemeCount(count) {
            $('#schemeCount').text(count);
        }
        
        function updateActiveFiltersText() {
            let activeFilters = 0;
            
            if ($('#schemeTypeFilter').val()) activeFilters++;
            if ($('#schemeRoomFilter').val()) activeFilters++;
            if ($('#schemeSearchFilter').val()) activeFilters++;
            if ($('#schemeDateFromFilter').val()) activeFilters++;
            if ($('#schemeDateToFilter').val()) activeFilters++;
            
            $('#activeSchemeFiltersText').text(`Активных фильтров: ${activeFilters}`);
        }
        
        function loadCustomSchemeFilterOptions() {
            const projectId = window.projectId;
            if (!projectId) {
                console.warn('❌ Project ID не найден для загрузки кастомных опций');
                // Если API недоступен, извлекаем опции из уже отображенных схем
                extractOptionsFromCurrentSchemes();
                return;
            }
            
            console.log('📋 Загрузка кастомных опций схем отключена (без AJAX)');
            console.log('🆔 Project ID:', projectId);
            
            // Простое извлечение опций из уже отображенных схем вместо AJAX
            extractOptionsFromCurrentSchemes();
        }
        
        function updateSchemeFilterOptions(options) {
            // Обновляем тип схемы
            if (options.scheme_types && options.scheme_types.length > 0) {
                console.log('🔄 Обновляем типы схем:', options.scheme_types);
                updateSchemeTypeFilter(options.scheme_types);
            }
            
            // Обновляем помещения
            if (options.rooms && options.rooms.length > 0) {
                console.log('🔄 Обновляем помещения схем:', options.rooms);
                updateSchemeRoomFilter(options.rooms);
            }
        }
        
        function extractOptionsFromCurrentSchemes() {
            // Если API недоступен, извлекаем опции из уже отображенных схем
            const types = new Set();
            const rooms = new Set();
            
            $('#schemeGallery .scheme-card').each(function() {
                const typeText = $(this).find('.badge.bg-success').text().trim();
                const roomText = $(this).find('.badge.bg-secondary').text().trim();
                
                if (typeText && typeof typeText === 'string' && typeText.trim()) types.add(typeText.trim());
                if (roomText && typeof roomText === 'string' && roomText.trim()) rooms.add(roomText.trim());
            });
            
            updateSchemeFilterOptions({
                scheme_types: Array.from(types),
                rooms: Array.from(rooms)
            });
        }
        
        function updateFiltersWithData(schemes) {
            console.log('🔄 Обновляем фильтры с данными из схем...');
            
            // Собираем уникальные типы и помещения
            const types = new Set();
            const rooms = new Set();
            
            schemes.forEach(scheme => {
                if (scheme.scheme_type || scheme.type) {
                    types.add(scheme.scheme_type || scheme.type);
                }
                if (scheme.room || scheme.location) {
                    rooms.add(scheme.room || scheme.location);
                }
            });
            
            // Обновляем фильтры типов
            updateSchemeTypeFilter(Array.from(types));
            
            // Обновляем фильтры помещений
            updateSchemeRoomFilter(Array.from(rooms));
        }
        
        function updateSchemeTypeFilter(dynamicTypes) {
            const typeSelect = $('#schemeTypeFilter');
            
            // Базовые предустановленные типы
            const baseTypes = [
                { value: 'electrical', label: 'Электрика' },
                { value: 'plumbing', label: 'Сантехника' },
                { value: 'ventilation', label: 'Вентиляция' },
                { value: 'layout', label: 'Планировка' },
                { value: 'structure', label: 'Конструкция' },
                { value: 'heating', label: 'Отопление' },
                { value: 'flooring', label: 'Напольные покрытия' },
                { value: 'ceiling', label: 'Потолки' },
                { value: 'walls', label: 'Стены' },
                { value: 'doors', label: 'Двери' },
                { value: 'windows', label: 'Окна' },
                { value: 'furniture', label: 'Мебель' },
                { value: 'lighting', label: 'Освещение' },
                { value: 'security', label: 'Безопасность' },
                { value: 'automation', label: 'Автоматизация' },
                { value: 'other', label: 'Другое' }
            ];
            
            const currentValue = typeSelect.val();
            
            // Очищаем и добавляем базовую опцию
            typeSelect.empty().append('<option value="">Все типы</option>');
            
            // Добавляем базовые типы
            baseTypes.forEach(type => {
                typeSelect.append(`<option value="${type.value}">${type.label}</option>`);
            });
            
            // Добавляем динамические (кастомные) типы, которых нет в базовых
            const baseValues = baseTypes.map(type => type.value);
            dynamicTypes.forEach(type => {
                if (type && !baseValues.includes(type)) {
                    // Проверяем, является ли это кастомным значением
                    const displayLabel = getSchemeTypeName(type);
                    const optionValue = type;
                    
                    // Добавляем пометку для кастомных значений
                    const isCustom = !baseValues.includes(type);
                    const finalLabel = isCustom ? `${displayLabel} (кастомный)` : displayLabel;
                    
                    typeSelect.append(`<option value="${optionValue}">${finalLabel}</option>`);
                }
            });
            
            // Восстанавливаем выбранное значение
            if (currentValue) {
                typeSelect.val(currentValue);
            }
        }
        
        function updateSchemeRoomFilter(dynamicRooms) {
            const roomSelect = $('#schemeRoomFilter');
            
            // Базовые предустановленные помещения
            const baseRooms = [
                { value: 'living_room', label: 'Гостиная' },
                { value: 'bedroom', label: 'Спальня' },
                { value: 'kitchen', label: 'Кухня' },
                { value: 'bathroom', label: 'Ванная' },
                { value: 'toilet', label: 'Туалет' },
                { value: 'hallway', label: 'Прихожая' },
                { value: 'balcony', label: 'Балкон' },
                { value: 'corridor', label: 'Коридор' },
                { value: 'pantry', label: 'Кладовая' },
                { value: 'garage', label: 'Гараж' },
                { value: 'basement', label: 'Подвал' },
                { value: 'attic', label: 'Чердак' },
                { value: 'terrace', label: 'Терраса' },
                { value: 'entire', label: 'Вся квартира/дом' },
                { value: 'other', label: 'Другое' }
            ];
            
            const currentValue = roomSelect.val();
            
            // Очищаем и добавляем базовую опцию
            roomSelect.empty().append('<option value="">Все помещения</option>');
            
            // Добавляем базовые помещения
            baseRooms.forEach(room => {
                roomSelect.append(`<option value="${room.value}">${room.label}</option>`);
            });
            
            // Добавляем динамические (кастомные) помещения, которых нет в базовых
            const baseValues = baseRooms.map(room => room.value);
            dynamicRooms.forEach(room => {
                if (room && !baseValues.includes(room)) {
                    // Проверяем, является ли это кастомным значением
                    const displayLabel = getRoomName(room) || room;
                    const optionValue = room;
                    
                    // Добавляем пометку для кастомных значений
                    const isCustom = !baseValues.includes(room);
                    const finalLabel = isCustom ? `${displayLabel} (кастомное)` : displayLabel;
                    
                    roomSelect.append(`<option value="${optionValue}">${finalLabel}</option>`);
                }
            });
            
            // Восстанавливаем выбранное значение
            if (currentValue) {
                roomSelect.val(currentValue);
            }
        }
        
        // Вспомогательные функции
        function getSchemeTypeName(type) {
            const types = {
                'electrical': 'Электрика',
                'plumbing': 'Сантехника',
                'ventilation': 'Вентиляция',
                'layout': 'Планировка',
                'structure': 'Конструкция',
                'heating': 'Отопление',
                'flooring': 'Напольные покрытия',
                'ceiling': 'Потолки',
                'walls': 'Стены',
                'doors': 'Двери',
                'windows': 'Окна',
                'furniture': 'Мебель',
                'lighting': 'Освещение',
                'security': 'Безопасность',
                'automation': 'Автоматизация',
                'other': 'Другое',
                'general': 'Общая'
            };
            
            // Если есть готовый перевод, используем его
            if (types[type]) {
                return types[type];
            }
            
            // Если это кастомное значение, возвращаем как есть, но с большой буквы
            if (type && typeof type === 'string') {
                return type.charAt(0).toUpperCase() + type.slice(1);
            }
            
            return 'Схема';
        }
        
        function getRoomName(room) {
            const rooms = {
                'living_room': 'Гостиная',
                'bedroom': 'Спальня',
                'kitchen': 'Кухня',
                'bathroom': 'Ванная',
                'toilet': 'Туалет',
                'hallway': 'Прихожая',
                'balcony': 'Балкон',
                'corridor': 'Коридор',
                'pantry': 'Кладовая',
                'garage': 'Гараж',
                'basement': 'Подвал',
                'attic': 'Чердак',
                'terrace': 'Терраса',
                'entire': 'Вся квартира/дом',
                'other': 'Другое'
            };
            
            // Если есть готовый перевод, используем его
            if (rooms[room]) {
                return rooms[room];
            }
            
            // Если это кастомное значение, возвращаем как есть, но с большой буквы
            if (room && typeof room === 'string') {
                return room.charAt(0).toUpperCase() + room.slice(1);
            }
            
            return '';
        }
        
        function getFileExtension(filename) {
            if (!filename) return '';
            return filename.split('.').pop().toLowerCase() || '';
        }
        
        function isImageFile(extension) {
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff'];
            return imageExtensions.includes(extension.toLowerCase());
        }
        
        function getFileIcon(extension) {
            const icons = {
                'pdf': 'pdf',
                'dwg': 'image', 'dxf': 'image',
                'ai': 'image', 'svg': 'image',
                'cad': 'image', 'autocad': 'image',
                'doc': 'word', 'docx': 'word',
                'xls': 'excel', 'xlsx': 'excel',
                'ppt': 'powerpoint', 'pptx': 'powerpoint',
                'txt': 'text',
                'zip': 'archive', 'rar': 'archive', '7z': 'archive'
            };
            return icons[extension.toLowerCase()] || 'text';
        }
        
        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 Б';
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
        
        // Функция для обновления схем после изменений
        window.reloadSchemes = function() {
            console.log('🔄 Обновление схем...');
            loadSchemes();
        };
    </script>
@endsection

{{-- Подключение системы модальных окон --}}
@include('partner.projects.tabs.modals.init-modals')
