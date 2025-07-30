/**
 * Главный файл инициализации для системы управления документами
 * Объединяет инициализацию всех менеджеров файлов
 * Версия: 2.0
 */

(function() {
    'use strict';
    
    console.log('=== ИНИЦИАЛИЗАЦИЯ СИСТЕМЫ УПРАВЛЕНИЯ ФАЙЛАМИ ===');
    
    // Глобальные переменные
    window.fileManagersReady = false;
    
    /**
     * Функция для безопасной инициализации менеджера
     */
    function safeInitManager(managerName, initMethod = 'init') {
        try {
            const manager = window[managerName];
            
            if (manager && typeof manager[initMethod] === 'function') {
                console.log(`✅ Инициализация ${managerName}`);
                manager[initMethod]();
                return true;
            } else {
                console.warn(`⚠️ ${managerName} не найден или не имеет метода ${initMethod}`);
                return false;
            }
        } catch (error) {
            console.error(`❌ Ошибка инициализации ${managerName}:`, error);
            return false;
        }
    }
    
    /**
     * Инициализация всех файловых менеджеров
     */
    function initFileManagers() {
        console.log('Начало инициализации файловых менеджеров...');
        
        // Проверяем наличие Project ID
        if (!window.projectId) {
            console.error('❌ window.projectId не установлен');
            return;
        }
        
        console.log('✅ Project ID найден:', window.projectId);
        
        // Инициализируем менеджеры в правильном порядке
        const managers = [
            'DocumentManagerFixed',
            'DesignManagerFixed', 
            'PhotoManagerFixed'
        ];
        
        let successCount = 0;
        
        managers.forEach(managerName => {
            if (safeInitManager(managerName)) {
                successCount++;
            }
        });
        
        console.log(`Инициализировано менеджеров: ${successCount}/${managers.length}`);
        
        // Отмечаем что инициализация завершена
        window.fileManagersReady = true;
        
        // Диспатчим событие для других скриптов
        window.dispatchEvent(new CustomEvent('fileManagersReady', { 
            detail: { successCount, totalCount: managers.length } 
        }));
    }
    
    /**
     * Обработчик переключения вкладок Bootstrap
     */
    function handleTabSwitch() {
        $(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]', function(e) {
            const target = $(e.target).attr('href') || $(e.target).data('bs-target');
            const tabId = target ? target.replace('#', '') : '';
            
            console.log('Переключение на вкладку:', tabId);
            
            // Перезагружаем данные для активной вкладки
            switch(tabId) {
                case 'documents':
                case 'documentsTab':
                    if (window.DocumentManagerFixed && window.DocumentManagerFixed.loadFiles) {
                        setTimeout(() => window.DocumentManagerFixed.loadFiles(), 100);
                    }
                    break;
                    
                case 'design':
                case 'designTab':
                    if (window.DesignManagerFixed && window.DesignManagerFixed.loadFiles) {
                        setTimeout(() => window.DesignManagerFixed.loadFiles(), 100);
                    }
                    break;
                    
                case 'photos':
                case 'photosTab':
                    if (window.PhotoManagerFixed && window.PhotoManagerFixed.loadFiles) {
                        setTimeout(() => window.PhotoManagerFixed.loadFiles(), 100);
                    }
                    break;
            }
        });
    }
    
    /**
     * Глобальная функция для ручной инициализации
     */
    window.reinitFileManagers = function() {
        console.log('Ручная реинициализация файловых менеджеров...');
        initFileManagers();
    };
    
    /**
     * Функция проверки статуса менеджеров
     */
    window.checkManagersStatus = function() {
        const managers = ['DocumentManagerFixed', 'DesignManagerFixed', 'PhotoManagerFixed'];
        const status = {};
        
        managers.forEach(name => {
            status[name] = {
                exists: !!window[name],
                initialized: !!(window[name] && window[name].projectId),
                hasData: !!(window[name] && window[name].data && window[name].data.length > 0)
            };
        });
        
        console.table(status);
        return status;
    };
    
    // Инициализация при готовности DOM
    $(document).ready(function() {
        console.log('📄 DOM готов, запускаем инициализацию файловых менеджеров');
        
        // Небольшая задержка для загрузки всех скриптов
        setTimeout(() => {
            initFileManagers();
            handleTabSwitch();
        }, 100);
    });
    
    // Дополнительная инициализация при полной загрузке страницы
    $(window).on('load', function() {
        if (!window.fileManagersReady) {
            console.log('🔄 Повторная инициализация при полной загрузке страницы');
            setTimeout(initFileManagers, 200);
        }
    });
    
    console.log('FileManager Init система загружена');
    
})();
