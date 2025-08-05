/**
 * Инициализация фильтров для страниц проектов
 * Версия: 1.0
 * Дата: 2 августа 2025
 */
(function() {
    'use strict';
    
    console.log('🚀 Загружен project-filters-init.js');
    
    // Функция для инициализации фильтров конкретной страницы
    function initializePageFilters() {
        const currentPage = getCurrentPageType();
        console.log(`📄 Текущая страница: ${currentPage}`);
        
        // Задержка для полной загрузки DOM
        setTimeout(() => {
            switch (currentPage) {
                case 'photos':
                    initializePhotoFilters();
                    break;
                case 'schemes':
                    initializeSchemeFilters();
                    break;
                case 'documents':
                    initializeDocumentFilters();
                    break;
                case 'design':
                    initializeDesignFilters();
                    break;
                default:
                    initializeAllFilters();
            }
        }, 500);
    }
    
    // Определение типа текущей страницы
    function getCurrentPageType() {
        const path = window.location.pathname;
        
        if (path.includes('/photos')) return 'photos';
        if (path.includes('/schemes')) return 'schemes';
        if (path.includes('/documents')) return 'documents';
        if (path.includes('/design')) return 'design';
        
        // Определение по наличию элементов на странице
        if (document.getElementById('photos-tab-content')) return 'photos';
        if (document.getElementById('schemes-tab-content')) return 'schemes';
        if (document.getElementById('documents-tab-content')) return 'documents';
        if (document.getElementById('design-tab-content')) return 'design';
        
        return 'unknown';
    }
    
    // Инициализация фильтров фотографий
    function initializePhotoFilters() {
        console.log('📸 Инициализация фильтров фотографий...');
        
        const photoFilters = [
            '#photoTypeFilter',
            '#photoLocationFilter',
            '#photoSortFilter',
            '#photoTypeFilterMobile',
            '#photoLocationFilterMobile',
            '#photoSortFilterMobile'
        ];
        
        initializeFiltersList(photoFilters, 'photo');
        setupPhotoFilterHandlers();
    }
    
    // Инициализация фильтров схем
    function initializeSchemeFilters() {
        console.log('📐 Инициализация фильтров схем...');
        
        const schemeFilters = [
            '#schemeTypeFilter',
            '#schemeRoomFilter',
            '#schemeSortFilter'
        ];
        
        initializeFiltersList(schemeFilters, 'scheme');
        setupSchemeFilterHandlers();
    }
    
    // Инициализация фильтров документов
    function initializeDocumentFilters() {
        console.log('📄 Инициализация фильтров документов...');
        
        const documentFilters = [
            '#documentTypeFilter',
            '#documentStatusFilter',
            '#documentSortFilter'
        ];
        
        initializeFiltersList(documentFilters, 'document');
        setupDocumentFilterHandlers();
    }
    
    // Инициализация фильтров дизайна
    function initializeDesignFilters() {
        console.log('🎨 Инициализация фильтров дизайна...');
        
        const designFilters = [
            '#designTypeFilter',
            '#designRoomFilter',
            '#designStyleFilter',
            '#designSortFilter'
        ];
        
        initializeFiltersList(designFilters, 'design');
        setupDesignFilterHandlers();
    }
    
    // Инициализация всех фильтров (для основной страницы проекта)
    function initializeAllFilters() {
        console.log('🔄 Инициализация всех фильтров...');
        
        initializePhotoFilters();
        initializeSchemeFilters();
        initializeDocumentFilters();
        initializeDesignFilters();
    }
    
    // Инициализация списка фильтров
    function initializeFiltersList(filters, type) {
        filters.forEach(selector => {
            const $select = $(selector);
            if ($select.length > 0) {
                try {
                    // Деинициализируем если уже инициализирован
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                    
                    // Создаем конфигурацию
                    const config = createFilterConfig($select, type);
                    
                    // Инициализируем Select2
                    $select.select2(config);
                    
                    console.log(`✅ Инициализирован фильтр: ${selector}`);
                } catch (error) {
                    console.error(`❌ Ошибка инициализации ${selector}:`, error);
                }
            }
        });
    }
    
    // Создание конфигурации для фильтра
    function createFilterConfig($select, type) {
        const config = {
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            minimumResultsForSearch: 0,
            language: {
                noResults: function() {
                    return "Ничего не найдено";
                },
                searching: function() {
                    return "Поиск...";
                }
            }
        };
        
        // Устанавливаем placeholder
        const label = $(`label[for="${$select.attr('id')}"]`).text().trim();
        if (label) {
            config.placeholder = `Все (${label.toLowerCase()})`;
        } else {
            config.placeholder = 'Выберите...';
        }
        
        // Специальные настройки для разных типов
        switch (type) {
            case 'photo':
                config.dropdownCssClass = 'photo-filter-dropdown';
                break;
            case 'scheme':
                config.dropdownCssClass = 'scheme-filter-dropdown';
                break;
            case 'document':
                config.dropdownCssClass = 'document-filter-dropdown';
                break;
            case 'design':
                config.dropdownCssClass = 'design-filter-dropdown';
                break;
        }
        
        return config;
    }
    
    // Настройка обработчиков для фильтров фотографий
    function setupPhotoFilterHandlers() {
        $('#clearAllFilters, #clearAllFiltersMobile').off('click').on('click', function() {
            clearFilters(['#photoTypeFilter', '#photoLocationFilter', '#photoSortFilter', 
                         '#photoTypeFilterMobile', '#photoLocationFilterMobile', '#photoSortFilterMobile']);
        });
        
        $('.photo-filter').off('change.photoFilter').on('change.photoFilter', function() {
            console.log('📸 Изменен фильтр фотографий:', this.id, '=', this.value);
            // Здесь можно добавить логику фильтрации
        });
    }
    
    // Настройка обработчиков для фильтров схем
    function setupSchemeFilterHandlers() {
        $('#clearSchemeFilters').off('click').on('click', function() {
            clearFilters(['#schemeTypeFilter', '#schemeRoomFilter', '#schemeSortFilter']);
        });
        
        $('.scheme-filter').off('change.schemeFilter').on('change.schemeFilter', function() {
            console.log('📐 Изменен фильтр схем:', this.id, '=', this.value);
            // Здесь можно добавить логику фильтрации
        });
    }
    
    // Настройка обработчиков для фильтров документов
    function setupDocumentFilterHandlers() {
        $('#clearDocumentFilters').off('click').on('click', function() {
            clearFilters(['#documentTypeFilter', '#documentStatusFilter', '#documentSortFilter']);
        });
        
        $('[id*="documentFilter"]').off('change.documentFilter').on('change.documentFilter', function() {
            console.log('📄 Изменен фильтр документов:', this.id, '=', this.value);
            // Здесь можно добавить логику фильтрации
        });
    }
    
    // Настройка обработчиков для фильтров дизайна
    function setupDesignFilterHandlers() {
        $('#clearDesignFilters').off('click').on('click', function() {
            clearFilters(['#designTypeFilter', '#designRoomFilter', '#designStyleFilter', '#designSortFilter']);
        });
        
        $('[id*="designFilter"]').off('change.designFilter').on('change.designFilter', function() {
            console.log('🎨 Изменен фильтр дизайна:', this.id, '=', this.value);
            // Здесь можно добавить логику фильтрации
        });
    }
    
    // Очистка фильтров
    function clearFilters(selectors) {
        selectors.forEach(selector => {
            const $select = $(selector);
            if ($select.length > 0) {
                $select.val('').trigger('change');
                console.log(`🧹 Очищен фильтр: ${selector}`);
            }
        });
    }
    
    // Переинициализация при изменении DOM
    function setupDOMObserver() {
        const observer = new MutationObserver(function(mutations) {
            let shouldReinitialize = false;
            
            mutations.forEach(mutation => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1 && (
                            node.classList?.contains('form-select') ||
                            node.querySelector?.('.form-select')
                        )) {
                            shouldReinitialize = true;
                        }
                    });
                }
            });
            
            if (shouldReinitialize) {
                console.log('🔄 Обнаружены изменения DOM, переинициализация...');
                setTimeout(initializePageFilters, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Инициализация при загрузке DOM
    $(document).ready(function() {
        console.log('📋 DOM готов, инициализация фильтров проекта...');
        
        initializePageFilters();
        setupDOMObserver();
        
        // Дополнительная проверка через 2 секунды
        setTimeout(() => {
            console.log('🔍 Дополнительная проверка фильтров...');
            if (window.Select2FiltersFix) {
                const report = window.Select2FiltersFix.diagnoseFilters();
                if (report.brokenFilters > 0) {
                    console.log('🔧 Исправление неинициализированных фильтров...');
                    window.Select2FiltersFix.forceInitializeAllFilters();
                }
            }
        }, 2000);
    });
    
    // Экспорт функций
    window.ProjectFilters = {
        initializePageFilters,
        initializePhotoFilters,
        initializeSchemeFilters,
        initializeDocumentFilters,
        initializeDesignFilters,
        clearFilters
    };
    
    console.log('✅ project-filters-init.js готов к работе');
    
})();
