/**
 * Унифицированный менеджер финансовых компонентов
 * Устраняет дублирование инициализации финансовых AJAX обработчиков
 * Версия: 1.0
 */

class FinanceUnifiedManager {
    constructor(projectId) {
        this.projectId = projectId;
        this.modalsInitialized = new Set();
        
        console.log('💰 FinanceUnifiedManager инициализирован для проекта:', projectId);
    }
    
    /**
     * Основная инициализация финансовых компонентов
     */
    init() {
        if (window.financeUnifiedInitialized) {
            console.log('⏭️ Финансовые компоненты уже инициализированы');
            return;
        }
        
        console.log('🚀 Инициализация финансовых компонентов...');
        
        // Инициализируем AJAX обработчики только один раз
        this.initAjaxHandlers();
        
        // Инициализируем маски ввода для финансовых полей
        this.initInputMasks();
        
        // Инициализируем обработчики модальных окон
        this.initModalHandlers();
        
        // Инициализируем автоматические расчеты
        this.initCalculations();
        
        window.financeUnifiedInitialized = true;
        console.log('✅ Финансовые компоненты успешно инициализированы');
    }
    
    /**
     * Инициализация AJAX обработчиков (только один раз)
     */
    initAjaxHandlers() {
        if (window.financeAjaxInitialized) {
            console.log('⏭️ AJAX обработчики уже инициализированы');
            return;
        }
        
        console.log('🌐 Инициализация AJAX обработчиков...');
        
        // Инициализируем внешнюю функцию если она существует
        if (typeof initFinanceAjax === 'function') {
            initFinanceAjax();
        }
        
        // Добавляем собственные обработчики
        this.setupFormSubmissionHandlers();
        this.setupDeleteHandlers();
        this.setupEditHandlers();
        
        window.financeAjaxInitialized = true;
    }
    
    /**
     * Настройка обработчиков отправки форм
     */
    setupFormSubmissionHandlers() {
        // Обработчик для всех финансовых форм
        $(document).off('submit.financeUnified', '.finance-form').on('submit.financeUnified', '.finance-form', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = form.find('[type="submit"]');
            const originalText = submitBtn.html();
            
            // Показываем состояние загрузки
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Сохранение...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method') || 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        window.projectManager?.showNotification('success', response.message || 'Данные успешно сохранены');
                        
                        // Закрываем модальное окно
                        form.closest('.modal').modal('hide');
                        
                        // Обновляем содержимое страницы если нужно
                        if (typeof refreshFinanceData === 'function') {
                            refreshFinanceData();
                        }
                    } else {
                        window.projectManager?.showNotification('error', response.message || 'Ошибка при сохранении данных');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Произошла ошибка при сохранении';
                    window.projectManager?.showNotification('error', message);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    }
    
    /**
     * Настройка обработчиков удаления
     */
    setupDeleteHandlers() {
        $(document).off('click.financeUnified', '.delete-finance-item').on('click.financeUnified', '.delete-finance-item', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const itemId = button.data('id');
            const itemType = button.data('type');
            const deleteUrl = button.data('url');
            
            if (confirm('Вы уверены, что хотите удалить эту запись?')) {
                $.ajax({
                    url: deleteUrl,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            window.projectManager?.showNotification('success', 'Запись успешно удалена');
                            
                            // Удаляем элемент из DOM
                            button.closest('.finance-item, tr').fadeOut(300, function() {
                                $(this).remove();
                            });
                            
                            // Обновляем итоги
                            if (typeof updateFinanceTotals === 'function') {
                                updateFinanceTotals();
                            }
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Ошибка при удалении записи';
                        window.projectManager?.showNotification('error', message);
                    }
                });
            }
        });
    }
    
    /**
     * Настройка обработчиков редактирования
     */
    setupEditHandlers() {
        $(document).off('click.financeUnified', '.edit-finance-item').on('click.financeUnified', '.edit-finance-item', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const itemId = button.data('id');
            const itemType = button.data('type');
            
            // Определяем, какое модальное окно нужно открыть
            const modalMap = {
                'work': '#workModal',
                'material': '#materialModal', 
                'transport': '#transportModal'
            };
            
            const modalId = modalMap[itemType];
            if (modalId) {
                this.loadItemDataForEdit(itemId, itemType, modalId);
            }
        }.bind(this));
    }
    
    /**
     * Загрузка данных для редактирования
     */
    loadItemDataForEdit(itemId, itemType, modalId) {
        const loadUrl = `/partner/projects/${this.projectId}/finance/${itemType}/${itemId}`;
        
        $.ajax({
            url: loadUrl,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    this.populateModalForm(modalId, response.data);
                    $(modalId).modal('show');
                }
            }.bind(this),
            error: function(xhr) {
                window.projectManager?.showNotification('error', 'Ошибка при загрузке данных для редактирования');
            }
        });
    }
    
    /**
     * Заполнение формы модального окна данными
     */
    populateModalForm(modalId, data) {
        const modal = $(modalId);
        
        // Заполняем поля формы
        Object.keys(data).forEach(key => {
            const field = modal.find(`[name="${key}"]`);
            if (field.length) {
                field.val(data[key]);
            }
        });
        
        // Устанавливаем режим редактирования
        modal.find('form').attr('data-edit-id', data.id);
        modal.find('.modal-title').text(`Редактирование записи #${data.id}`);
    }
    
    /**
     * Инициализация масок ввода для финансовых полей
     */
    initInputMasks() {
        console.log('🎭 Инициализация масок для финансовых полей...');
        
        // Ждем загрузки inputMaskManager
        const initMasks = () => {
            if (window.inputMaskManager) {
                // Применяем маски к финансовым полям
                window.inputMaskManager.applyMaskToElements('.currency-mask', 'currency');
                window.inputMaskManager.applyMaskToElements('.decimal-mask', 'decimal');
                window.inputMaskManager.applyMaskToElements('.percentage-mask', 'percentage');
                
                console.log('✅ Маски для финансовых полей применены');
            } else {
                setTimeout(initMasks, 100);
            }
        };
        
        initMasks();
    }
    
    /**
     * Инициализация обработчиков модальных окон
     */
    initModalHandlers() {
        console.log('🪟 Инициализация обработчиков модальных окон...');
        
        // Обработчик открытия модальных окон
        $(document).off('click.financeUnified', '[data-action^="add-"]').on('click.financeUnified', '[data-action^="add-"]', function(e) {
            e.preventDefault();
            
            const action = $(this).data('action');
            const modalMap = {
                'add-work': '#workModal',
                'add-material': '#materialModal',
                'add-transport': '#transportModal'
            };
            
            const modalId = modalMap[action];
            if (modalId) {
                this.resetModalForm(modalId);
                $(modalId).modal('show');
            }
        }.bind(this));
        
        // Обработчик закрытия модальных окон
        $('.modal').off('hidden.bs.modal.financeUnified').on('hidden.bs.modal.financeUnified', function() {
            const modalId = '#' + $(this).attr('id');
            this.resetModalForm(modalId);
        }.bind(this));
    }
    
    /**
     * Сброс формы модального окна
     */
    resetModalForm(modalId) {
        const modal = $(modalId);
        const form = modal.find('form')[0];
        
        if (form) {
            form.reset();
        }
        
        // Сбрасываем режим редактирования
        modal.find('form').removeAttr('data-edit-id');
        modal.find('.modal-title').text(modal.find('.modal-title').data('original-title') || 'Добавить запись');
        
        // Сбрасываем состояние кнопок
        modal.find('[type="submit"]').prop('disabled', false).text('Сохранить');
    }
    
    /**
     * Инициализация автоматических расчетов
     */
    initCalculations() {
        console.log('🧮 Инициализация автоматических расчетов...');
        
        // Обработчик для автоматического расчета общей стоимости
        $(document).off('input.financeCalculation', '.finance-price, .finance-quantity').on('input.financeCalculation', '.finance-price, .finance-quantity', function() {
            const container = $(this).closest('.modal, .finance-form');
            const priceField = container.find('.finance-price');
            const quantityField = container.find('.finance-quantity');
            const totalField = container.find('.finance-total');
            
            if (priceField.length && quantityField.length && totalField.length) {
                const price = parseFloat(priceField.val().replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                const quantity = parseFloat(quantityField.val().replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                const total = price * quantity;
                
                totalField.val(total.toLocaleString('ru-RU', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        });
    }
    
    /**
     * Получение статистики инициализации
     */
    getInitializationStatus() {
        return {
            projectId: this.projectId,
            ajaxInitialized: !!window.financeAjaxInitialized,
            unifiedInitialized: !!window.financeUnifiedInitialized,
            modalsInitialized: Array.from(this.modalsInitialized)
        };
    }
}

// Интеграция с ProjectInitializationManager
if (window.projectManager) {
    window.projectManager.registerPageHandler('finance', function() {
        if (!window.financeUnifiedManager) {
            window.financeUnifiedManager = new FinanceUnifiedManager(window.projectId);
        }
        window.financeUnifiedManager.init();
    });
}

// Автоматическая инициализация для финансовых страниц
$(document).ready(function() {
    if (window.location.pathname.includes('/finance') || $('.finance-content, .finance-form').length > 0) {
        setTimeout(() => {
            if (window.projectId && !window.financeUnifiedManager) {
                window.financeUnifiedManager = new FinanceUnifiedManager(window.projectId);
                window.financeUnifiedManager.init();
            }
        }, 100);
    }
});

console.log('💰 FinanceUnifiedManager загружен');
