/**
 * Диагностический скрипт для проверки масок в модальных окнах
 * Автор: System Administrator
 * Версия: 1.0
 */

class ModalMaskDiagnostic {
    constructor() {
        this.init();
    }
    
    init() {
        console.log('🔍 Диагностический модуль для масок запущен');
        
        // Мониторинг открытия модальных окон
        $(document).on('shown.bs.modal', (e) => {
            this.diagnoseModal(e.target);
        });
        
        // Мониторинг отправки форм
        $(document).on('submit', 'form', (e) => {
            this.diagnoseFormSubmit(e.target);
        });
    }
    
    diagnoseModal(modal) {
        const modalId = modal.id;
        console.log(`🔍 Диагностика модального окна: ${modalId}`);
        
        // Ищем поля с масками
        const maskedFields = modal.querySelectorAll('[data-mask], .price-mask, .quantity-mask, .progress-mask');
        
        if (maskedFields.length === 0) {
            console.log('ℹ️ В модальном окне нет полей с масками');
            return;
        }
        
        console.log(`🎭 Найдено полей с масками: ${maskedFields.length}`);
        
        maskedFields.forEach((field, index) => {
            const maskType = field.getAttribute('data-mask') || this.detectMaskTypeByClass(field);
            const isMasked = !!field.masked;
            const hasJQueryMask = !!$(field).data('mask');
            
            console.log(`   ${index + 1}. Поле: ${field.id || field.name || 'unnamed'}`);
            console.log(`      - Тип маски: ${maskType}`);
            console.log(`      - Маска применена: ${isMasked}`);
            console.log(`      - jQuery mask: ${hasJQueryMask}`);
            console.log(`      - Текущее значение: "${field.value}"`);
            
            // Тестируем ввод
            this.testFieldInput(field, maskType);
        });
    }
    
    detectMaskTypeByClass(element) {
        if (element.classList.contains('price-mask')) return 'currency';
        if (element.classList.contains('quantity-mask')) return 'decimal';
        if (element.classList.contains('progress-mask')) return 'percentage';
        if (element.classList.contains('phone-mask')) return 'phone';
        if (element.classList.contains('date-mask')) return 'date';
        return 'unknown';
    }
    
    testFieldInput(field, maskType) {
        // Временно фокусируемся на поле для тестирования
        field.focus();
        
        // Тестируем различные вводы
        const testValues = {
            currency: ['123456', '1234.56', '1234,56'],
            decimal: ['123.456', '123,456', '12'],
            percentage: ['100', '99.5', '50,25'],
            phone: ['9123456789', '+79123456789'],
            date: ['01012023', '01.01.2023']
        };
        
        if (testValues[maskType]) {
            console.log(`      🧪 Тестирование значений для ${maskType}:`);
            
            testValues[maskType].forEach(testValue => {
                // Временно устанавливаем значение
                const originalValue = field.value;
                field.value = testValue;
                
                // Эмулируем событие input
                field.dispatchEvent(new Event('input', { bubbles: true }));
                
                console.log(`         "${testValue}" → "${field.value}"`);
                
                // Восстанавливаем значение
                field.value = originalValue;
            });
        }
        
        field.blur();
    }
    
    diagnoseFormSubmit(form) {
        console.log(`📋 Диагностика отправки формы: ${form.id || 'unnamed'}`);
        
        const formData = new FormData(form);
        const maskedFields = form.querySelectorAll('[data-mask], .price-mask, .quantity-mask, .progress-mask');
        
        console.log('📊 Данные формы:');
        for (let [key, value] of formData.entries()) {
            console.log(`   ${key}: "${value}"`);
        }
        
        console.log('🎭 Замаскированные поля:');
        maskedFields.forEach(field => {
            const numericValue = this.extractNumericValue(field.value);
            console.log(`   ${field.name}: "${field.value}" → ${numericValue}`);
        });
        
        // Проверяем наличие некорректных символов
        this.checkForInvalidCharacters(form);
    }
    
    extractNumericValue(value) {
        if (!value) return 0;
        
        // Удаляем все символы кроме цифр и запятых/точек
        const cleaned = value.replace(/[^\d,\.]/g, '');
        
        // Заменяем запятую на точку
        const normalized = cleaned.replace(',', '.');
        
        return parseFloat(normalized) || 0;
    }
    
    checkForInvalidCharacters(form) {
        const formData = new FormData(form);
        const invalidPatterns = [
            /[{}\[\]\\|]/,  // Символы масок
            /\d+{\d+,\d+}\[,\d+/  // Паттерн вида "2{1,12}[,2"
        ];
        
        console.log('🚨 Проверка на некорректные символы:');
        
        for (let [key, value] of formData.entries()) {
            if (typeof value === 'string') {
                invalidPatterns.forEach((pattern, index) => {
                    if (pattern.test(value)) {
                        console.error(`   ❌ Поле "${key}" содержит некорректные символы (паттерн ${index + 1}): "${value}"`);
                    }
                });
            }
        }
    }
    
    // Метод для принудительного исправления полей
    fixFieldValues(form) {
        console.log('🔧 Принудительное исправление значений полей...');
        
        const maskedFields = form.querySelectorAll('[data-mask], .price-mask, .quantity-mask, .progress-mask');
        
        maskedFields.forEach(field => {
            if (field.value) {
                const numericValue = this.extractNumericValue(field.value);
                const fixedValue = numericValue.toString();
                
                if (field.value !== fixedValue) {
                    console.log(`   🔧 Исправление ${field.name}: "${field.value}" → "${fixedValue}"`);
                    field.value = fixedValue;
                }
            }
        });
    }
}

// Создаем глобальный экземпляр
window.modalMaskDiagnostic = new ModalMaskDiagnostic();

console.log('🔍 Модуль диагностики масок инициализирован');
