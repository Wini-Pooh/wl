/**
 * Скрипт для проверки применения масок в модальных окнах
 * Версия: 1.0
 */

class MaskValidationChecker {
    constructor() {
        this.requiredMasks = {
            // Work Modal
            '#workPrice': 'currency',
            '#workQuantity': 'decimal', 
            '#workPaidAmount': 'currency',
            '#workPaymentDate': 'date',
            
            // Material Modal
            '#materialPrice': 'currency',
            '#materialQuantity': 'decimal',
            '#materialPaidAmount': 'currency', 
            '#materialPaymentDate': 'date',
            
            // Transport Modal
            '#transportPrice': 'currency',
            '#transportQuantity': 'decimal',
            '#transportPaidAmount': 'currency',
            '#transportPaymentDate': 'date'
        };
        
        this.init();
    }
    
    init() {
        console.log('🔍 Инициализация проверки масок...');
        
        // Проверяем маски при открытии модальных окон
        $(document).on('shown.bs.modal', (e) => {
            setTimeout(() => {
                this.checkModalMasks(e.target);
            }, 200);
        });
        
        // Общая проверка при загрузке страницы
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                this.checkAllMasks();
            }, 1000);
        });
    }
    
    checkModalMasks(modal) {
        const modalId = modal.id;
        console.log(`🔍 Проверка масок в модальном окне: ${modalId}`);
        
        Object.keys(this.requiredMasks).forEach(selector => {
            const element = modal.querySelector(selector);
            if (element) {
                this.checkElementMask(element, selector);
            }
        });
    }
    
    checkAllMasks() {
        console.log('🔍 Проверка всех масок на странице...');
        
        Object.keys(this.requiredMasks).forEach(selector => {
            const element = document.querySelector(selector);
            if (element) {
                this.checkElementMask(element, selector);
            }
        });
    }
    
    checkElementMask(element, selector) {
        const expectedMask = this.requiredMasks[selector];
        const hasMask = element.masked || false;
        const hasClass = this.getMaskClassFromElement(element);
        const hasDataMask = element.getAttribute('data-mask');
        
        const status = {
            selector: selector,
            expectedMask: expectedMask,
            hasMask: hasMask,
            hasClass: hasClass,
            hasDataMask: hasDataMask,
            element: element
        };
        
        if (!hasMask) {
            console.warn(`❌ Маска не применена для ${selector}`, status);
            this.fixMask(element, expectedMask);
        } else {
            console.log(`✅ Маска корректно применена для ${selector}`, status);
        }
    }
    
    getMaskClassFromElement(element) {
        const classes = Array.from(element.classList);
        const maskClasses = classes.filter(cls => cls.includes('mask'));
        return maskClasses.length > 0 ? maskClasses : null;
    }
    
    fixMask(element, maskType) {
        console.log(`🔧 Попытка исправить маску для элемента: ${element.id || element.name}`);
        
        // Добавляем соответствующий класс если его нет
        switch(maskType) {
            case 'currency':
                if (!element.classList.contains('price-mask')) {
                    element.classList.add('price-mask');
                }
                if (!element.getAttribute('data-mask')) {
                    element.setAttribute('data-mask', 'currency');
                }
                break;
            case 'decimal':
                if (!element.classList.contains('quantity-mask')) {
                    element.classList.add('quantity-mask');
                }
                if (!element.getAttribute('data-mask')) {
                    element.setAttribute('data-mask', 'decimal');
                }
                break;
            case 'date':
                if (!element.classList.contains('date-mask')) {
                    element.classList.add('date-mask');
                }
                if (!element.getAttribute('data-mask')) {
                    element.setAttribute('data-mask', 'date');
                }
                break;
        }
        
        // Применяем маску через inputMaskManager
        if (window.inputMaskManager) {
            element.masked = false; // Сброс флага
            window.inputMaskManager.applyMaskToElement(element, maskType);
        }
    }
    
    // Метод для тестирования конкретного модального окна
    testModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error(`Модальное окно ${modalId} не найдено`);
            return;
        }
        
        console.log(`🧪 Тестирование модального окна: ${modalId}`);
        this.checkModalMasks(modal);
    }
    
    // Отчет о состоянии всех масок
    generateReport() {
        console.log('📊 Генерация отчета о состоянии масок...');
        
        const report = {
            total: 0,
            applied: 0,
            missing: 0,
            details: []
        };
        
        Object.keys(this.requiredMasks).forEach(selector => {
            const element = document.querySelector(selector);
            report.total++;
            
            if (element) {
                const hasMask = element.masked || false;
                if (hasMask) {
                    report.applied++;
                } else {
                    report.missing++;
                }
                
                report.details.push({
                    selector: selector,
                    exists: true,
                    hasMask: hasMask,
                    expectedMask: this.requiredMasks[selector]
                });
            } else {
                report.details.push({
                    selector: selector,
                    exists: false,
                    hasMask: false,
                    expectedMask: this.requiredMasks[selector]
                });
            }
        });
        
        console.table(report.details);
        console.log(`📈 Итого: ${report.applied}/${report.total} масок применено, ${report.missing} отсутствуют`);
        
        return report;
    }
}

// Создаем глобальный экземпляр
window.maskValidationChecker = new MaskValidationChecker();

// Добавляем в глобальное пространство для отладки
window.checkMasks = () => window.maskValidationChecker.generateReport();
window.testModal = (modalId) => window.maskValidationChecker.testModal(modalId);

console.log('🔍 Mask Validation Checker загружен');
console.log('💡 Используйте checkMasks() для проверки состояния масок');
console.log('💡 Используйте testModal("modalId") для тестирования конкретного модального окна');
