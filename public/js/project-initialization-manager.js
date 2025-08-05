/**
 * Менеджер инициализации проекта REM
 * Устраняет дублирование кода и конфликты инициализации
 * Версия: 1.0
 */

class ProjectInitializationManager {
    constructor(projectId) {
        this.projectId = projectId;
        this.initialized = new Set();
        this.dependencies = new Map();
        this.pageHandlers = new Map();
        
        console.log('🚀 ProjectInitializationManager инициализирован для проекта:', projectId);
        
        // Устанавливаем глобальный projectId один раз
        if (!window.projectId) {
            window.projectId = projectId;
        }
        
        this.setupGlobalErrorHandling();
    }
    
    /**
     * Инициализация функции только один раз
     */
    initOnce(key, initFunction) {
        if (!this.initialized.has(key)) {
            console.log(`✅ Инициализация: ${key}`);
            try {
                initFunction();
                this.initialized.add(key);
            } catch (error) {
                console.error(`❌ Ошибка инициализации ${key}:`, error);
            }
        } else {
            console.log(`⏭️ Пропуск повторной инициализации: ${key}`);
        }
    }
    
    /**
     * Инициализация с зависимостями
     */
    initWithDependencies(key, dependencies, initFunction) {
        const checkDependencies = () => {
            const missing = dependencies.filter(dep => !this.initialized.has(dep));
            if (missing.length === 0) {
                this.initOnce(key, initFunction);
                return true;
            }
            return false;
        };
        
        if (!checkDependencies()) {
            console.log(`⏳ Ожидание зависимостей для ${key}:`, dependencies);
            
            // Ждем загрузки зависимостей
            const interval = setInterval(() => {
                if (checkDependencies()) {
                    clearInterval(interval);
                }
            }, 100);
            
            // Таймаут на случай если зависимости не загрузятся
            setTimeout(() => {
                clearInterval(interval);
                console.warn(`⚠️ Таймаут ожидания зависимостей для ${key}:`, dependencies);
                // Попытаемся инициализировать без зависимостей
                this.initOnce(key, initFunction);
            }, 5000);
        }
    }
    
    /**
     * Регистрация обработчика для конкретной страницы
     */
    registerPageHandler(pageName, handler) {
        this.pageHandlers.set(pageName, handler);
        console.log(`📝 Зарегистрирован обработчик для страницы: ${pageName}`);
    }
    
    /**
     * Инициализация конкретной страницы
     */
    initPage(pageName, additionalInit = null) {
        const key = `page_${pageName}`;
        
        this.initOnce(key, () => {
            console.log(`📄 Инициализация страницы: ${pageName}`);
            
            // Выполняем базовую инициализацию для страницы
            this.initBasicsForPage(pageName);
            
            // Выполняем зарегистрированный обработчик
            const handler = this.pageHandlers.get(pageName);
            if (handler) {
                handler();
            }
            
            // Выполняем дополнительную инициализацию
            if (additionalInit) {
                additionalInit();
            }
        });
    }
    
    /**
     * Базовая инициализация для любой страницы проекта
     */
    initBasicsForPage(pageName) {
        // CSRF токен для AJAX
        this.initOnce('csrf_token', () => {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        
        // Базовые обработчики модальных окон
        this.initOnce('modal_basics', () => {
            // Очистка backdrop'ов при закрытии модальных окон
            $(document).on('hidden.bs.modal', '.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });
        });
        
        // Системные уведомления
        this.initOnce('notifications', () => {
            this.setupNotificationSystem();
        });
    }
    
    /**
     * Инициализация финансовых компонентов (только один раз для всего проекта)
     */
    initFinance() {
        this.initOnce('finance_ajax', () => {
            console.log('💰 Инициализация финансовых AJAX обработчиков...');
            
            if (typeof initFinanceAjax === 'function') {
                initFinanceAjax();
                window.financeAjaxInitialized = true;
            } else if (window.financeUnifiedManager) {
                window.financeUnifiedManager.init();
            } else {
                console.warn('⚠️ Ни initFinanceAjax, ни financeUnifiedManager не найдены');
            }
        });
        
        this.initOnce('finance_masks', () => {
            console.log('🎭 Инициализация масок для финансовых полей...');
            
            if (window.inputMaskManager) {
                window.inputMaskManager.init();
            }
        });
    }
    
    /**
     * Универсальная инициализация модального окна
     */
    initModal(modalId, modalType, initFunction) {
        const key = `modal_${modalType}_${modalId}`;
        
        this.initWithDependencies(key, ['modal_basics'], () => {
            console.log(`🔧 Инициализация модального окна: ${modalType} (${modalId})`);
            
            if (initFunction) {
                initFunction();
            }
            
            // Автоматическая очистка при закрытии модального окна
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', () => {
                    this.cleanupModal(modalId, modalType);
                });
            }
        });
    }
    
    /**
     * Очистка модального окна
     */
    cleanupModal(modalId, modalType) {
        console.log(`🧹 Очистка модального окна: ${modalType} (${modalId})`);
        
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            // Очищаем формы
            const forms = modalElement.querySelectorAll('form');
            forms.forEach(form => form.reset());
            
            // Убираем состояния валидации
            const invalidElements = modalElement.querySelectorAll('.is-invalid');
            invalidElements.forEach(el => el.classList.remove('is-invalid'));
            
            // Скрываем сообщения об ошибках
            const feedbacks = modalElement.querySelectorAll('.invalid-feedback');
            feedbacks.forEach(fb => fb.style.display = 'none');
        }
    }
    
    /**
     * Инициализация фото компонентов
     */
    initPhotos() {
        this.initWithDependencies('photos', ['modal_basics'], () => {
            console.log('📸 Инициализация фото компонентов...');
            
            // Инициализация загрузчика фотографий
            if (typeof initPhotoUploadHandlers === 'function') {
                initPhotoUploadHandlers();
            }
        });
    }
    
    /**
     * Инициализация систем уведомлений
     */
    setupNotificationSystem() {
        // Создаем контейнер для уведомлений если его нет
        if ($('.toast-container').length === 0) {
            $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
        }
        
        // Глобальный обработчик AJAX ошибок
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status !== 419) { // Игнорируем CSRF ошибки
                console.error('AJAX Error:', xhr.responseText);
                window.projectManager?.showNotification('error', 'Произошла ошибка при выполнении запроса');
            }
        });
    }
    
    /**
     * Показать уведомление
     */
    showNotification(type, message, autoHide = true) {
        const toastId = 'toast_' + Date.now();
        const toastClass = type === 'error' ? 'bg-danger' : 'bg-success';
        const icon = type === 'error' ? 'bi-exclamation-triangle' : 'bi-check-circle';
        
        const toastHtml = `
            <div id="${toastId}" class="toast ${toastClass} text-white" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icon} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        $('.toast-container').append(toastHtml);
        
        const toast = new bootstrap.Toast(document.getElementById(toastId), {
            autohide: autoHide,
            delay: autoHide ? 5000 : 0
        });
        
        toast.show();
        
        // Автоматическое удаление после скрытия
        document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    /**
     * Глобальная обработка ошибок
     */
    setupGlobalErrorHandling() {
        window.addEventListener('error', (event) => {
            console.error('Global error:', event.error);
        });
        
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
        });
    }
    
    /**
     * Проверка состояния инициализации
     */
    getInitializationStatus() {
        return {
            projectId: this.projectId,
            initialized: Array.from(this.initialized),
            pageHandlers: Array.from(this.pageHandlers.keys())
        };
    }
    
    /**
     * Детальная диагностика состояния
     */
    getDiagnostics() {
        const diagnostics = {
            projectManager: !!window.projectManager,
            projectId: this.projectId,
            windowProjectId: window.projectId,
            initialized: Array.from(this.initialized),
            pageHandlers: Array.from(this.pageHandlers.keys()),
            financeInitialized: this.initialized.has('finance_ajax'),
            masksInitialized: this.initialized.has('finance_masks'),
            modalBasicsInitialized: this.initialized.has('modal_basics'),
            notificationsInitialized: this.initialized.has('notifications'),
            globalObjects: {
                financeUnifiedManager: !!window.financeUnifiedManager,
                inputMaskManager: !!window.inputMaskManager,
                initFinanceAjax: typeof initFinanceAjax,
                maskValidationChecker: !!window.maskValidationChecker
            }
        };
        
        console.table(diagnostics);
        return diagnostics;
    }
    
    /**
     * Проверка готовности конкретного компонента
     */
    isReady(componentKey) {
        return this.initialized.has(componentKey);
    }
    
    /**
     * Сброс инициализации (для отладки)
     */
    reset() {
        this.initialized.clear();
        console.log('🔄 Инициализация сброшена');
    }
    
    /**
     * Принудительная повторная инициализация компонента
     */
    forceReinit(key, initFunction) {
        this.initialized.delete(key);
        this.initOnce(key, initFunction);
    }
}

// Глобальная инициализация
$(document).ready(function() {
    // Получаем projectId из разных возможных источников
    const projectId = window.projectId || 
                     $('meta[name="project-id"]').attr('content') || 
                     $('#projectId').val() ||
                     $('[data-project-id]').data('project-id');
    
    if (projectId) {
        // Создаем глобальный менеджер проекта
        window.projectManager = new ProjectInitializationManager(projectId);
        
        // Инициализируем базовые компоненты
        window.projectManager.initBasicsForPage('all');
        
        console.log('✅ ProjectInitializationManager готов к работе');
    } else {
        console.warn('⚠️ Project ID не найден. Некоторые функции могут не работать.');
    }
});

// Экспорт для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProjectInitializationManager;
}

console.log('📦 ProjectInitializationManager загружен');
