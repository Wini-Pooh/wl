/**
 * Улучшенная система модальных окон
 * Поддержка анимаций справа, правильного скроллинга и адаптивности
 * 
 * Автор: Система управления проектами
 * Версия: 2.0
 */

(function($) {
    'use strict';

    // Конфигурация по умолчанию
    const MODAL_CONFIG = {
        animationDuration: 400,
        backdropBlur: true,
        autoFocus: true,
        escapeKey: true,
        centerVertically: false,
        animationDirection: 'right', // right, left, top, bottom, scale
        modalClass: 'enhanced-modal',
        loadingTemplate: `
            <div class="modal-loading">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <div class="ms-3">Загрузка...</div>
            </div>
        `
    };

    /**
     * Класс для управления улучшенными модальными окнами
     */
    class EnhancedModal {
        constructor(element, options = {}) {
            this.element = $(element);
            this.options = { ...MODAL_CONFIG, ...options };
            this.isOpen = false;
            this.isAnimating = false;
            
            this.init();
        }

        init() {
            // Добавляем CSS классы для анимации
            this.element.addClass('fade enhanced-modal');
            
            // Устанавливаем направление анимации
            this.setAnimationDirection(this.options.animationDirection);
            
            // Привязываем события
            this.bindEvents();
            
            // Настраиваем автофокус
            if (this.options.autoFocus) {
                this.setupAutoFocus();
            }
            
            console.log('EnhancedModal инициализирован для:', this.element.attr('id'));
        }

        setAnimationDirection(direction) {
            // Удаляем предыдущие классы направления
            this.element.removeClass('modal-from-left modal-from-right modal-from-top modal-from-bottom modal-scale');
            
            // Добавляем новый класс направления
            switch (direction) {
                case 'left':
                    this.element.addClass('modal-from-left');
                    break;
                case 'top':
                    this.element.addClass('modal-from-top');
                    break;
                case 'bottom':
                    this.element.addClass('modal-from-bottom');
                    break;
                case 'scale':
                    this.element.addClass('modal-scale');
                    break;
                case 'right':
                default:
                    // По умолчанию справа (уже реализовано в CSS)
                    break;
            }
        }

        bindEvents() {
            const self = this;

            // События Bootstrap модального окна
            this.element.on('show.bs.modal', function(e) {
                self.onShow(e);
            });

            this.element.on('shown.bs.modal', function(e) {
                self.onShown(e);
            });

            this.element.on('hide.bs.modal', function(e) {
                self.onHide(e);
            });

            this.element.on('hidden.bs.modal', function(e) {
                self.onHidden(e);
            });

            // Закрытие по ESC
            if (this.options.escapeKey) {
                $(document).on('keydown.enhanced-modal', function(e) {
                    if (e.keyCode === 27 && self.isOpen) {
                        self.hide();
                    }
                });
            }

            // Клик по backdrop
            this.element.on('click', function(e) {
                if (e.target === this) {
                    self.hide();
                }
            });
        }

        onShow(e) {
            this.isAnimating = true;
            console.log('Модальное окно показывается:', this.element.attr('id'));
            
            // Добавляем blur к backdrop если включено
            if (this.options.backdropBlur) {
                $('.modal-backdrop').addClass('backdrop-blur');
            }

            // Блокируем скролл body
            $('body').addClass('modal-open');

            // Кастомное событие
            this.element.trigger('enhanced:modal:show');
        }

        onShown(e) {
            this.isOpen = true;
            this.isAnimating = false;
            console.log('Модальное окно показано:', this.element.attr('id'));

            // Автофокус на первом элементе
            if (this.options.autoFocus) {
                this.focusFirstElement();
            }

            // Кастомное событие
            this.element.trigger('enhanced:modal:shown');
        }

        onHide(e) {
            this.isAnimating = true;
            console.log('Модальное окно скрывается:', this.element.attr('id'));

            // Кастомное событие
            this.element.trigger('enhanced:modal:hide');
        }

        onHidden(e) {
            this.isOpen = false;
            this.isAnimating = false;
            console.log('Модальное окно скрыто:', this.element.attr('id'));

            // Убираем blur
            $('.modal-backdrop').removeClass('backdrop-blur');

            // Возвращаем скролл body
            if ($('.modal.show').length === 0) {
                $('body').removeClass('modal-open');
            }

            // Кастомное событие
            this.element.trigger('enhanced:modal:hidden');
        }

        show() {
            if (this.isAnimating) return;
            
            console.log('Показываем модальное окно:', this.element.attr('id'));
            this.element.modal('show');
        }

        hide() {
            if (this.isAnimating) return;
            
            console.log('Скрываем модальное окно:', this.element.attr('id'));
            this.element.modal('hide');
        }

        toggle() {
            if (this.isOpen) {
                this.hide();
            } else {
                this.show();
            }
        }

        setupAutoFocus() {
            const self = this;
            this.element.on('shown.bs.modal', function() {
                self.focusFirstElement();
            });
        }

        focusFirstElement() {
            // Ищем первый фокусируемый элемент
            const focusableElements = this.element.find('input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])');
            
            if (focusableElements.length > 0) {
                focusableElements.first().focus();
            } else {
                // Если нет фокусируемых элементов, фокусируемся на кнопке закрытия
                this.element.find('.btn-close').focus();
            }
        }

        showLoading(message = 'Загрузка...') {
            const loadingHtml = this.options.loadingTemplate.replace('Загрузка...', message);
            const modalBody = this.element.find('.modal-body');
            
            // Сохраняем оригинальный контент
            if (!modalBody.data('original-content')) {
                modalBody.data('original-content', modalBody.html());
            }
            
            modalBody.html(loadingHtml);
        }

        hideLoading() {
            const modalBody = this.element.find('.modal-body');
            const originalContent = modalBody.data('original-content');
            
            if (originalContent) {
                modalBody.html(originalContent);
                modalBody.removeData('original-content');
            }
        }

        updateContent(content) {
            this.element.find('.modal-body').html(content);
        }

        updateTitle(title) {
            this.element.find('.modal-title').html(title);
        }

        addShakeAnimation() {
            this.element.addClass('modal-shake');
            setTimeout(() => {
                this.element.removeClass('modal-shake');
            }, 600);
        }

        addPulseAnimation() {
            this.element.addClass('modal-pulse');
        }

        removePulseAnimation() {
            this.element.removeClass('modal-pulse');
        }

        destroy() {
            // Удаляем все события
            this.element.off('.enhanced-modal');
            $(document).off('keydown.enhanced-modal');
            
            // Удаляем CSS классы
            this.element.removeClass('enhanced-modal modal-from-left modal-from-right modal-from-top modal-from-bottom modal-scale');
            
            console.log('EnhancedModal уничтожен для:', this.element.attr('id'));
        }
    }

    /**
     * Менеджер модальных окон
     */
    class ModalManager {
        constructor() {
            this.modals = new Map();
            this.init();
        }

        init() {
            console.log('ModalManager инициализирован');
            
            // Автоматически инициализируем все модальные окна
            this.initializeExistingModals();
            
            // Наблюдаем за новыми модальными окнами
            this.observeNewModals();
        }

        initializeExistingModals() {
            $('.modal').each((index, element) => {
                this.createModal(element);
            });
        }

        observeNewModals() {
            // Используем MutationObserver для отслеживания новых модальных окон
            if (typeof MutationObserver !== 'undefined') {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                const modals = $(node).find('.modal').addBack('.modal');
                                modals.each((index, element) => {
                                    if (!this.modals.has(element)) {
                                        this.createModal(element);
                                    }
                                });
                            }
                        });
                    });
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        }

        createModal(element, options = {}) {
            const $element = $(element);
            const modalId = $element.attr('id') || 'modal-' + Date.now();
            
            if (!this.modals.has(element)) {
                const modal = new EnhancedModal(element, options);
                this.modals.set(element, modal);
                console.log('Создан EnhancedModal для:', modalId);
            }
            
            return this.modals.get(element);
        }

        getModal(selector) {
            const element = $(selector)[0];
            return this.modals.get(element);
        }

        showModal(selector, options = {}) {
            const modal = this.getModal(selector) || this.createModal($(selector)[0], options);
            modal.show();
            return modal;
        }

        hideModal(selector) {
            const modal = this.getModal(selector);
            if (modal) {
                modal.hide();
            }
        }

        destroyModal(selector) {
            const element = $(selector)[0];
            const modal = this.modals.get(element);
            if (modal) {
                modal.destroy();
                this.modals.delete(element);
            }
        }

        destroyAll() {
            this.modals.forEach((modal, element) => {
                modal.destroy();
            });
            this.modals.clear();
        }
    }

    // Создаем глобальный экземпляр менеджера
    window.modalManager = new ModalManager();

    // jQuery плагин для удобства
    $.fn.enhancedModal = function(options = {}) {
        return this.each(function() {
            window.modalManager.createModal(this, options);
        });
    };

    // Автоматическая инициализация при готовности документа
    $(document).ready(function() {
        console.log('Enhanced Modals готовы к работе');
        
        // Обрабатываем клики по кнопкам с data-modal-type
        $(document).on('click', '[data-modal-type]', function(e) {
            e.preventDefault();
            
            const modalType = $(this).data('modal-type');
            const modalId = getModalIdByType(modalType);
            
            if (modalId) {
                window.modalManager.showModal(modalId);
            } else {
                console.warn('Не найдено модальное окно для типа:', modalType);
            }
        });
    });

    // Утилитарная функция для получения ID модального окна по типу
    function getModalIdByType(type) {
        const modalMap = {
            'photo': '#uploadPhotoModal',
            'document': '#uploadDocumentModal',
            'design': '#uploadDesignModal',
            'scheme': '#uploadSchemeModal',
            'event': '#addEventModal',
            'stage': '#addStageModal',
            'material': '#addMaterialModal',
            'transport': '#addTransportModal',
            'work': '#addWorkModal'
        };
        
        return modalMap[type] || null;
    }

    // Экспортируем для глобального использования
    window.EnhancedModal = EnhancedModal;
    window.ModalManager = ModalManager;

    console.log('Enhanced Modals библиотека загружена');

})(jQuery);
