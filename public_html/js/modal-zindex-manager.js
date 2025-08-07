/**
 * Модуль для управления модальными окнами и исправления проблем с z-index
 * Решает проблему конфликта между modal-backdrop и content-container
 */

class ModalZIndexManager {
    constructor() {
        this.modalStack = [];
        this.isInitialized = false;
        this.debugMode = false;
        
        // Z-index константы
        this.Z_INDEX = {
            BASE: 1,
            SIDEBAR: 1000,
            NAVBAR: 1010,
            CONTENT: 1020,
            MODAL_BACKDROP: 1040,
            MODAL: 1050,
            MODAL_CONTENT: 1055,
            TOOLTIP: 1070,
            NOTIFICATION: 9999
        };
    }

    /**
     * Инициализация менеджера модальных окон
     */
    init() {
        if (this.isInitialized) {
            console.log('🔧 ModalZIndexManager уже инициализирован');
            return;
        }

        console.log('🚀 Инициализация ModalZIndexManager...');
        
        this.setupEventListeners();
        this.cleanupExistingBackdrops();
        this.isInitialized = true;
        
        console.log('✅ ModalZIndexManager инициализирован');
    }

    /**
     * Настройка обработчиков событий
     */
    setupEventListeners() {
        // Обработчик показа модального окна
        $(document).on('show.bs.modal', '.modal', (e) => {
            this.handleModalShow(e);
        });

        // Обработчик скрытия модального окна
        $(document).on('hide.bs.modal', '.modal', (e) => {
            this.handleModalHide(e);
        });

        // Обработчик полного скрытия модального окна
        $(document).on('hidden.bs.modal', '.modal', (e) => {
            this.handleModalHidden(e);
        });

        // Глобальная очистка при клике по backdrop
        $(document).on('click', '.modal-backdrop', (e) => {
            this.handleBackdropClick(e);
        });

        // Периодическая очистка потерянных backdrop'ов
        setInterval(() => {
            this.cleanupOrphanedBackdrops();
        }, 5000);
    }

    /**
     * Обработка показа модального окна
     */
    handleModalShow(event) {
        const modal = event.target;
        const $modal = $(modal);
        const modalId = modal.id || 'modal-' + Date.now();
        
        console.log(`🔵 Показ модального окна: ${modalId}`);
        
        // Добавляем в стек
        this.modalStack.push({
            id: modalId,
            element: modal,
            $element: $modal,
            timestamp: Date.now()
        });

        // Устанавливаем правильные z-index
        this.updateModalZIndex($modal);
        
        // Добавляем debug атрибуты если включен режим отладки
        if (this.debugMode) {
            $modal.attr('data-debug', 'true');
        }
    }

    /**
     * Обработка скрытия модального окна
     */
    handleModalHide(event) {
        const modal = event.target;
        const modalId = modal.id || modal.getAttribute('data-modal-id');
        
        console.log(`🔴 Скрытие модального окна: ${modalId}`);
        
        // Удаляем из стека
        this.modalStack = this.modalStack.filter(item => item.element !== modal);
    }

    /**
     * Обработка полного скрытия модального окна
     */
    handleModalHidden(event) {
        const modal = event.target;
        const modalId = modal.id || modal.getAttribute('data-modal-id');
        
        console.log(`⚫ Полное скрытие модального окна: ${modalId}`);
        
        // Принудительная очистка backdrop'ов
        setTimeout(() => {
            this.cleanupBackdrops();
        }, 100);
        
        // Если больше нет открытых модальных окон, восстанавливаем состояние body
        if (this.modalStack.length === 0) {
            this.restoreBodyState();
        }
    }

    /**
     * Обработка клика по backdrop
     */
    handleBackdropClick(event) {
        console.log('🖱️ Клик по backdrop, запуск очистки...');
        
        // Задержка для завершения анимации закрытия
        setTimeout(() => {
            this.cleanupBackdrops();
        }, 300);
    }

    /**
     * Установка правильного z-index для модального окна
     */
    updateModalZIndex($modal) {
        const baseZIndex = this.Z_INDEX.MODAL + (this.modalStack.length * 10);
        
        $modal.css('z-index', baseZIndex);
        
        // Ищем backdrop для этого модального окна и устанавливаем z-index
        setTimeout(() => {
            const $backdrop = $('.modal-backdrop').last();
            if ($backdrop.length) {
                $backdrop.css('z-index', baseZIndex - 5);
                
                if (this.debugMode) {
                    $backdrop.attr('data-debug', 'true');
                }
            }
        }, 50);
    }

    /**
     * Очистка всех backdrop'ов
     */
    cleanupBackdrops() {
        console.log('🧹 Очистка modal-backdrop элементов...');
        
        const backdrops = document.querySelectorAll('.modal-backdrop');
        const openModals = document.querySelectorAll('.modal.show');
        
        console.log(`Найдено backdrop'ов: ${backdrops.length}, открытых модальных окон: ${openModals.length}`);
        
        // Если нет открытых модальных окон, удаляем все backdrop'ы
        if (openModals.length === 0) {
            backdrops.forEach(backdrop => {
                console.log('🗑️ Удаление backdrop элемента');
                backdrop.remove();
            });
            
            this.restoreBodyState();
        } else {
            // Если есть открытые модальные окна, оставляем только нужное количество backdrop'ов
            const excessBackdrops = Array.from(backdrops).slice(openModals.length);
            excessBackdrops.forEach(backdrop => {
                console.log('🗑️ Удаление лишнего backdrop элемента');
                backdrop.remove();
            });
        }
    }

    /**
     * Очистка потерянных backdrop'ов
     */
    cleanupOrphanedBackdrops() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        const openModals = document.querySelectorAll('.modal.show, .modal.showing');
        
        if (backdrops.length > openModals.length) {
            console.log('🧹 Найдены потерянные backdrop элементы, очищаем...');
            this.cleanupBackdrops();
        }
    }

    /**
     * Принудительная очистка всех backdrop'ов
     */
    cleanupExistingBackdrops() {
        console.log('🧹 Принудительная очистка существующих backdrop элементов...');
        
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        this.restoreBodyState();
    }

    /**
     * Восстановление состояния body
     */
    restoreBodyState() {
        console.log('🔄 Восстановление состояния body...');
        
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Дополнительно очищаем стили с content-wrapper
        const contentWrapper = document.querySelector('.content-wrapper');
        if (contentWrapper) {
            contentWrapper.style.paddingRight = '';
        }
        
        const appLayout = document.querySelector('.app-layout');
        if (appLayout) {
            appLayout.style.paddingRight = '';
        }
    }

    /**
     * Включение режима отладки
     */
    enableDebugMode() {
        this.debugMode = true;
        console.log('🐛 Режим отладки модальных окон включен');
        
        // Добавляем debug стили
        const style = document.createElement('style');
        style.innerHTML = `
            .modal-backdrop[data-debug] {
                background-color: rgba(255, 0, 0, 0.3) !important;
                border: 2px solid red !important;
            }
            .modal[data-debug] {
                border: 2px solid green !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Получение информации о текущем состоянии
     */
    getStatus() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        const openModals = document.querySelectorAll('.modal.show');
        
        return {
            modalStack: this.modalStack.length,
            backdrops: backdrops.length,
            openModals: openModals.length,
            bodyModalOpen: document.body.classList.contains('modal-open'),
            isInitialized: this.isInitialized
        };
    }

    /**
     * Принудительное исправление состояния
     */
    forceRepair() {
        console.log('🔧 Принудительное исправление состояния модальных окон...');
        
        this.cleanupExistingBackdrops();
        this.modalStack = [];
        
        // Проверяем наличие открытых модальных окон
        const openModals = document.querySelectorAll('.modal.show');
        if (openModals.length === 0) {
            this.restoreBodyState();
        }
        
        console.log('✅ Принудительное исправление завершено');
    }
}

// Создаем глобальный экземпляр
window.modalZIndexManager = new ModalZIndexManager();

// Автоматическая инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    window.modalZIndexManager.init();
});

// Экспорт для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalZIndexManager;
}
