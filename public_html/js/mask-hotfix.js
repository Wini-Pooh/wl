/**
 * Хотфикс для исправления проблемы с масками в модальных окнах
 * Перехватывает AJAX запросы и очищает данные от некорректных символов
 * Версия: 1.0
 */

(function() {
    'use strict';
    
    console.log('🔧 Загрузка хотфикса для исправления масок...');
    
    // Функция для очистки значения от некорректных символов
    function cleanMaskedValue(value) {
        if (typeof value !== 'string') return value;
        
        // Удаляем символы масок
        let cleaned = value.replace(/[{}\[\]\\|]/g, '');
        
        // Если это числовое значение с запятой
        if (/^\d+,\d+$/.test(cleaned)) {
            const numeric = parseFloat(cleaned.replace(',', '.'));
            return numeric.toString();
        }
        
        // Если есть некорректные паттерны типа "2{1,12}[,2"
        if (/\d+{\d+,\d+}\[,\d+/.test(value)) {
            // Извлекаем число до первого некорректного символа
            const match = value.match(/^(\d+(?:,\d+)?)/);
            if (match) {
                const numeric = parseFloat(match[1].replace(',', '.'));
                return numeric.toString();
            }
        }
        
        return cleaned;
    }
    
    // Перехватываем jQuery AJAX
    if (typeof $ !== 'undefined') {
        const originalAjax = $.ajax;
        
        $.ajax = function(options) {
            // Если есть данные формы
            if (options.data instanceof FormData) {
                console.log('🔧 Обработка FormData перед отправкой...');
                
                const entries = Array.from(options.data.entries());
                
                // Очищаем FormData
                options.data = new FormData();
                
                // Добавляем очищенные данные
                entries.forEach(([key, value]) => {
                    const cleanedValue = cleanMaskedValue(value);
                    options.data.append(key, cleanedValue);
                    
                    if (cleanedValue !== value) {
                        console.log(`🔧 Исправлено поле ${key}: "${value}" → "${cleanedValue}"`);
                    }
                });
            }
            
            return originalAjax.call(this, options);
        };
        
        // Сохраняем все свойства и методы
        Object.keys(originalAjax).forEach(key => {
            $.ajax[key] = originalAjax[key];
        });
    }
    
    // Перехватываем fetch API
    const originalFetch = window.fetch;
    
    window.fetch = function(url, options = {}) {
        if (options.body instanceof FormData) {
            console.log('🔧 Обработка FormData в fetch перед отправкой...');
            
            const entries = Array.from(options.body.entries());
            const newFormData = new FormData();
            
            entries.forEach(([key, value]) => {
                const cleanedValue = cleanMaskedValue(value);
                newFormData.append(key, cleanedValue);
                
                if (cleanedValue !== value) {
                    console.log(`🔧 Исправлено поле ${key}: "${value}" → "${cleanedValue}"`);
                }
            });
            
            options.body = newFormData;
        }
        
        return originalFetch.call(this, url, options);
    };
    
    // Добавляем обработчик для всех форм на странице
    $(document).on('submit', 'form', function(e) {
        console.log('🔧 Проверка формы перед отправкой...');
        
        const form = this;
        const inputs = form.querySelectorAll('input[type="text"], input[type="number"]');
        
        inputs.forEach(input => {
            if (input.value && /[{}\[\]\\|]/.test(input.value)) {
                const originalValue = input.value;
                const cleanedValue = cleanMaskedValue(input.value);
                input.value = cleanedValue;
                
                console.log(`🔧 Очищено поле ${input.name}: "${originalValue}" → "${cleanedValue}"`);
            }
        });
    });
    
    // Добавляем глобальный обработчик для модальных окон
    $(document).on('shown.bs.modal', function(e) {
        const modal = e.target;
        console.log('🔧 Модальное окно открыто, применяем защиту:', modal.id);
        
        // Находим все поля с масками
        const maskedFields = modal.querySelectorAll('[data-mask], .price-mask, .quantity-mask, .progress-mask');
        
        maskedFields.forEach(field => {
            // Добавляем обработчик для очистки при потере фокуса
            field.addEventListener('blur', function() {
                if (this.value && /[{}\[\]\\|]/.test(this.value)) {
                    const cleanedValue = cleanMaskedValue(this.value);
                    console.log(`🔧 Автоочистка поля ${this.name}: "${this.value}" → "${cleanedValue}"`);
                    this.value = cleanedValue;
                }
            });
        });
    });
    
    console.log('✅ Хотфикс для масок загружен успешно');
    
})();
