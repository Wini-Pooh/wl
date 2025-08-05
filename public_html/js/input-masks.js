/**
 * Система масок ввода для проекта REM
 * Версия: 1.0
 * Автор: Project Manager
 */

class InputMaskManager {
    constructor() {
        this.masks = {
            currency: {
                mask: '000000000000,00',
                placeholder: '',
                translation: {
                    '0': { pattern: /[0-9]/, optional: true }
                },
                options: {
                    reverse: true,
                    selectOnFocus: true
                }
            },
            decimal: {
                mask: '000000000000,000',
                placeholder: '',
                translation: {
                    '0': { pattern: /[0-9]/, optional: true }
                },
                options: {
                    reverse: true,
                    selectOnFocus: true
                }
            },
            percentage: {
                mask: '000,00',
                placeholder: '',
                translation: {
                    '0': { pattern: /[0-9]/, optional: true }
                },
                options: {
                    reverse: true,
                    selectOnFocus: true
                }
            },
            phone: {
                mask: '+7 (999) 999-99-99',
                placeholder: '+7 (___) ___-__-__',
                options: {
                    selectOnFocus: true
                }
            },
            date: {
                mask: '99.99.9999',
                placeholder: 'дд.мм.гггг',
                options: {
                    selectOnFocus: true
                }
            }
        };
        
        this.init();
        this.setupGlobalAjaxIntercept();
    }

    init() {
        console.log('🎭 Инициализация системы масок ввода...');
        
        // Автоматическое применение масок при загрузке страницы
        document.addEventListener('DOMContentLoaded', () => {
            this.applyMasks();
        });

        // Переинициализация при открытии модальных окон
        $(document).on('shown.bs.modal', (e) => {
            setTimeout(() => {
                this.applyMasks(e.target);
            }, 100);
        });

        // Переинициализация при переключении табов
        $(document).on('shown.bs.tab', () => {
            setTimeout(() => {
                this.applyMasks();
            }, 100);
        });

        // Переинициализация после AJAX-запросов
        $(document).ajaxSuccess(() => {
            setTimeout(() => {
                this.applyMasks();
            }, 200);
        });

        // Применение масок для динамически добавленных элементов
        document.addEventListener('focus', (e) => {
            if (e.target.matches('[data-mask]') && !e.target.masked) {
                this.applyMaskToElement(e.target);
            }
        }, true);
    }

    applyMasks() {
        // Маски для валютных полей
        this.applyMaskToElements('.price-mask, [data-mask="currency"]', 'currency');
        
        // Маски для decimal полей (количество)
        this.applyMaskToElements('.quantity-mask, [data-mask="decimal"]', 'decimal');
        
        // Маски для процентов
        this.applyMaskToElements('.progress-mask, [data-mask="percentage"]', 'percentage');
        
        // Маски для телефонов
        this.applyMaskToElements('.phone-mask, [data-mask="phone"]', 'phone');
        
        // Маски для дат
        this.applyMaskToElements('.date-mask, [data-mask="date"]', 'date');

        console.log('✅ Маски применены ко всем элементам');
    }

    applyMaskToElements(selector, maskType) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            this.applyMaskToElement(element, maskType);
        });
    }

    applyMaskToElement(element, maskType = null) {
        if (element.masked) return; // Уже замаскирован

        // Определяем тип маски
        if (!maskType) {
            maskType = element.getAttribute('data-mask') || this.detectMaskType(element);
        }

        if (!this.masks[maskType]) {
            console.warn(`Неизвестный тип маски: ${maskType}`);
            return;
        }

        const maskConfig = this.masks[maskType];
        
        try {
            // Используем jQuery Mask Plugin если доступен
            if (typeof $ !== 'undefined' && $.fn.mask) {
                const options = {
                    placeholder: maskConfig.placeholder,
                    translation: maskConfig.translation || {},
                    ...maskConfig.options
                };
                
                // Удаляем предыдущую маску если есть
                $(element).unmask();
                
                $(element).mask(maskConfig.mask, options);
                element.masked = true;
                
                // Добавляем специальную обработку для валютных полей
                if (maskType === 'currency' || maskType === 'decimal') {
                    this.setupNumericBehavior(element);
                }
                
                // Добавляем валидацию для всех полей
                this.setupValidation(element, maskType);
                
                // Добавляем защиту от некорректного ввода
                this.setupInputProtection(element, maskType);
                
                console.log(`✅ Маска "${maskType}" применена к элементу:`, element.id || element.name || 'unnamed');
            } else {
                // Fallback: простая маска без плагина
                this.applySimpleMask(element, maskType);
            }
        } catch (error) {
            console.error('Ошибка применения маски:', error);
        }
    }

    detectMaskType(element) {
        const classList = element.classList;
        
        if (classList.contains('price-mask')) return 'currency';
        if (classList.contains('quantity-mask')) return 'decimal';
        if (classList.contains('progress-mask')) return 'percentage';
        if (classList.contains('phone-mask')) return 'phone';
        if (classList.contains('date-mask')) return 'date';
        
        return null;
    }

    setupNumericBehavior(element) {
        // Дополнительная обработка для числовых полей
        element.addEventListener('blur', () => {
            let value = element.value.replace(/[^\d,]/g, '');
            if (value && !value.includes(',') && value !== '0') {
                value += ',00';
                element.value = value;
            }
            // Убираем лишние нули в начале
            if (value.startsWith('0') && value.length > 1 && value[1] !== ',') {
                element.value = value.substring(1);
            }
        });

        // Конвертация для отправки на сервер
        element.addEventListener('change', () => {
            this.triggerCalculation(element);
        });

        // Обработка вставки из буфера обмена
        element.addEventListener('paste', (e) => {
            setTimeout(() => {
                let value = element.value.replace(/[^\d,]/g, '');
                element.value = value;
                this.triggerCalculation(element);
            }, 10);
        });

    
        this.setupFormSubmitHandler(element);
    }

    // Новый метод для обработки отправки форм
    setupFormSubmitHandler(element) {
        // Находим родительскую форму
        const form = element.closest('form');
        if (!form) return;

        // Добавляем обработчик только один раз
        if (form.hasAttribute('data-mask-handler-added')) return;
        form.setAttribute('data-mask-handler-added', 'true');

        // Обработчик отправки формы
        form.addEventListener('submit', (e) => {
            console.log('🎭 Обработка отправки формы с масками...');
            
            // Находим все поля с масками в форме
            const maskedFields = form.querySelectorAll('[data-mask="currency"], [data-mask="decimal"], [data-mask="percentage"], .price-mask, .quantity-mask, .progress-mask');
            
            maskedFields.forEach(field => {
                if (field.value && field.value.trim() !== '') {
                    const originalValue = field.value;
                    
                    // Проверяем на наличие некорректных символов
                    if (/[{}\[\]\\|]/.test(originalValue)) {
                        console.error(`❌ Обнаружены некорректные символы в поле ${field.name}: "${originalValue}"`);
                        
                        // Очищаем некорректные символы
                        const cleanedValue = originalValue.replace(/[{}\[\]\\|]/g, '');
                        field.value = cleanedValue;
                    }
                    
                    // Конвертируем в числовое значение
                    const numericValue = this.getNumericValue(field);
                    
                    // Заменяем значение на числовое
                    field.value = numericValue.toString();
                    
                    console.log(`✅ Конвертация поля ${field.name}: "${originalValue}" → "${field.value}"`);
                    
                    // Планируем восстановление значения после отправки
                    setTimeout(() => {
                        if (originalValue && !originalValue.includes('{') && !originalValue.includes('[')) {
                            field.value = originalValue;
                        }
                    }, 1000);
                }
            });
        });
    }

    // Метод для конвертации замаскированных полей в числовые значения
    convertMaskedFieldsToNumbers(form) {
        const maskedFields = form.querySelectorAll('[data-mask="currency"], [data-mask="decimal"], [data-mask="percentage"], .price-mask, .quantity-mask, .progress-mask');
        
        maskedFields.forEach(field => {
            if (field.value && field.value.trim() !== '') {
                // Получаем числовое значение без маски
                const numericValue = this.getNumericValue(field);
                
                // Сохраняем оригинальное значение
                const originalValue = field.value;
                
                // Временно заменяем значение поля на числовое
                field.value = numericValue.toString();
                
                // Устанавливаем атрибут для восстановления после отправки
                field.setAttribute('data-original-value', originalValue);
                
                console.log(`✅ Конвертация поля ${field.name}: "${originalValue}" → ${numericValue}`);
            }
        });

        // Восстанавливаем значения после отправки формы
        setTimeout(() => {
            maskedFields.forEach(field => {
                const originalValue = field.getAttribute('data-original-value');
                if (originalValue) {
                    field.value = originalValue;
                    field.removeAttribute('data-original-value');
                }
            });
        }, 500);
    }

    setupValidation(element, maskType) {
        element.addEventListener('invalid', (e) => {
            e.preventDefault();
            
            // Добавляем класс для визуального отображения ошибки
            element.classList.add('is-invalid');
            
            // Создаем или обновляем сообщение об ошибке
            let feedback = element.parentNode.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                element.parentNode.appendChild(feedback);
            }
            
            // Устанавливаем сообщение в зависимости от типа маски
            switch(maskType) {
                case 'currency':
                    feedback.textContent = 'Введите корректную сумму (например: 1500,00)';
                    break;
                case 'decimal':
                    feedback.textContent = 'Введите корректное количество (например: 2,5)';
                    break;
                case 'phone':
                    feedback.textContent = 'Введите корректный номер телефона';
                    break;
                case 'date':
                    feedback.textContent = 'Введите корректную дату (дд.мм.гггг)';
                    break;
                default:
                    feedback.textContent = 'Некорректное значение';
            }
        });

        // Убираем ошибку при правильном вводе
        element.addEventListener('input', () => {
            if (element.classList.contains('is-invalid')) {
                element.classList.remove('is-invalid');
                const feedback = element.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
            }
        });
    }

    setupInputProtection(element, maskType) {
        // Защита от некорректного ввода специальных символов
        element.addEventListener('input', (e) => {
            const value = e.target.value;
            
            // Проверяем на наличие некорректных символов для числовых полей
            if ((maskType === 'currency' || maskType === 'decimal' || maskType === 'percentage') && 
                /[{}\[\]\\|]/.test(value)) {
                
                // Удаляем все некорректные символы
                e.target.value = value.replace(/[{}\[\]\\|]/g, '');
                console.warn(`Удалены некорректные символы из поля ${e.target.name}`);
            }
        });

        // Дополнительная защита при потере фокуса
        element.addEventListener('blur', (e) => {
            const value = e.target.value;
            
            if ((maskType === 'currency' || maskType === 'decimal' || maskType === 'percentage') && 
                /[{}\[\]\\|]/.test(value)) {
                
                // Очищаем поле и показываем предупреждение
                e.target.value = '';
                this.showValidationError(e.target, 'Введите корректное числовое значение');
            }
        });
    }

    triggerCalculation(element) {
        // Триггер для пересчета сумм
        const event = new Event('input', { bubbles: true });
        element.dispatchEvent(event);
    }

    applySimpleMask(element, maskType) {
        // Простая реализация без внешних зависимостей
        console.log(`Применение простой маски ${maskType} к элементу`);
        
        switch(maskType) {
            case 'currency':
            case 'decimal':
                this.setupNumericInput(element);
                break;
            case 'percentage':
                this.setupPercentageInput(element);
                break;
            case 'phone':
                this.setupPhoneInput(element);
                break;
        }
        
        element.masked = true;
    }

    setupNumericInput(element) {
        element.addEventListener('input', (e) => {
            let value = e.target.value.replace(/[^\d,]/g, '');
            
            // Ограничиваем количество знаков после запятой
            const parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }
            if (parts[1] && parts[1].length > 2) {
                value = parts[0] + ',' + parts[1].substring(0, 2);
            }
            
            e.target.value = value;
        });
    }

    setupPercentageInput(element) {
        element.addEventListener('input', (e) => {
            let value = e.target.value.replace(/[^\d,]/g, '');
            const numValue = parseFloat(value.replace(',', '.')) || 0;
            
            if (numValue > 100) {
                e.target.value = '100';
            } else {
                e.target.value = value;
            }
        });
    }

    setupPhoneInput(element) {
        element.addEventListener('input', (e) => {
            let value = e.target.value.replace(/[^\d]/g, '');
            
            if (value.length >= 1 && value[0] !== '7') {
                value = '7' + value;
            }
            
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            // Форматирование
            let formatted = '+7';
            if (value.length > 1) {
                formatted += ' (' + value.substring(1, 4);
                if (value.length > 4) {
                    formatted += ') ' + value.substring(4, 7);
                    if (value.length > 7) {
                        formatted += '-' + value.substring(7, 9);
                        if (value.length > 9) {
                            formatted += '-' + value.substring(9, 11);
                        }
                    }
                }
            }
            
            e.target.value = formatted;
        });
    }

    // Утилитарные методы для работы с замаскированными значениями
    getNumericValue(element) {
        if (!element) return 0;
        
        // Удаляем все некорректные символы, включая символы масок
        let value = element.value;
        
        // Удаляем символы масок типа {}, [], \, |
        value = value.replace(/[{}\[\]\\|]/g, '');
        
        // Оставляем только цифры и запятые
        value = value.replace(/[^\d,]/g, '');
        
        // Заменяем запятую на точку для parseFloat
        value = value.replace(',', '.');
        
        const result = parseFloat(value) || 0;
        
        console.log(`🔢 getNumericValue: "${element.value}" → "${value}" → ${result}`);
        
        return result;
    }

    setNumericValue(element, value) {
        if (!element) return;
        
        const formatted = value.toFixed(2).replace('.', ',');
        element.value = formatted;
        
        // Триггер события для пересчета
        this.triggerCalculation(element);
    }

    // Метод для получения чистого значения для отправки на сервер  
    getCleanValue(element) {
        const numericValue = this.getNumericValue(element);
        return numericValue.toString();
    }

    // Метод для валидации поля
    validateField(element, rules) {
        if (!element || !rules) return true;
        
        const value = this.getNumericValue(element);
        
        // Проверка обязательности
        if (rules.required && (!element.value || value <= 0)) {
            this.showValidationError(element, 'Поле обязательно для заполнения');
            return false;
        }
        
        // Проверка минимального значения
        if (rules.min !== undefined && value < rules.min) {
            this.showValidationError(element, `Минимальное значение: ${rules.min}`);
            return false;
        }
        
        // Проверка максимального значения  
        if (rules.max !== undefined && value > rules.max) {
            this.showValidationError(element, `Максимальное значение: ${rules.max}`);
            return false;
        }
        
        this.hideValidationError(element);
        return true;
    }

    showValidationError(element, message) {
        element.classList.add('is-invalid');
        
        let feedback = element.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            element.parentNode.appendChild(feedback);
        }
        
        feedback.textContent = message;
        feedback.style.display = 'block';
    }

    hideValidationError(element) {
        element.classList.remove('is-invalid');
        const feedback = element.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }

    // Метод для реинициализации масок (для динамического контента)
    reinitialize(container = document) {
        console.log('🔄 Реинициализация масок в контейнере:', container);
        
        const elements = container.querySelectorAll('[data-mask], .price-mask, .quantity-mask, .progress-mask, .phone-mask, .date-mask');
        elements.forEach(element => {
            element.masked = false; // Сбрасываем флаг
            this.applyMaskToElement(element);
        });
    }

    // Глобальный интерсептор для AJAX запросов
    setupGlobalAjaxIntercept() {
        // Интерсептор для jQuery AJAX
        if (typeof $ !== 'undefined') {
            const originalAjax = $.ajaxSettings.xhr;
            
            $.ajaxSettings.xhr = function() {
                const xhr = originalAjax.call(this);
                
                // Перехватываем перед отправкой
                const originalSend = xhr.send;
                xhr.send = function(data) {
                    if (data instanceof FormData) {
                        // Конвертируем замаскированные поля в FormData
                        const maskManager = window.inputMaskManager;
                        if (maskManager) {
                            maskManager.convertFormDataMasks(data);
                        }
                    }
                    originalSend.call(this, data);
                };
                
                return xhr;
            };
        }

        // Интерсептор для fetch API
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (options.body instanceof FormData) {
                const maskManager = window.inputMaskManager;
                if (maskManager) {
                    maskManager.convertFormDataMasks(options.body);
                }
            }
            return originalFetch.call(this, url, options);
        };
    }

    // Метод для конвертации FormData с замаскированными значениями
    convertFormDataMasks(formData) {
        const entries = Array.from(formData.entries());
        
        entries.forEach(([key, value]) => {
            if (typeof value === 'string' && this.isMaskedNumericValue(value)) {
                const numericValue = this.convertMaskedToNumeric(value);
                formData.set(key, numericValue.toString());
                console.log(`🔄 FormData конвертация: ${key} = "${value}" → ${numericValue}`);
            }
        });
    }

    // Проверяет, является ли строка замаскированным числовым значением
    isMaskedNumericValue(value) {
        // Проверяем на наличие характерных символов масок
        return /[\{\}\[\]\\|]/.test(value) || /^\d+,\d+$/.test(value);
    }

    // Конвертирует замаскированное значение в числовое
    convertMaskedToNumeric(value) {
        // Удаляем все символы кроме цифр и запятых
        const cleaned = value.replace(/[^\d,]/g, '');
        // Заменяем запятую на точку и конвертируем в число
        return parseFloat(cleaned.replace(',', '.')) || 0;
    }
}

// Создаем глобальный экземпляр
window.inputMaskManager = new InputMaskManager();

// Экспортируем для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = InputMaskManager;
}

console.log('🎭 Input Mask Manager загружен и готов к работе');
