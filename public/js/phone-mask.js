/**
 * Маска для поля ввода телефона
 * Формат: +7 (999) 123-45-67
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('📞 Инициализация маски телефона...');
    
    // Находим все поля с типом tel или с id/name phone
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name="phone"], input[id="phone"]');
    
    phoneInputs.forEach(function(input) {
        initPhoneMask(input);
    });
});

function initPhoneMask(input) {
    if (!input) return;
    
    console.log('🎭 Применение маски к полю:', input.id || input.name);
    
    // Установка placeholder если его нет
    if (!input.placeholder) {
        input.placeholder = '+7 (999) 123-45-67';
    }
    
    // Обработчик ввода
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Только цифры
        
        // Если начинается с 8, заменяем на 7
        if (value.startsWith('8')) {
            value = '7' + value.slice(1);
        }
        
        // Если не начинается с 7, добавляем 7
        if (value.length > 0 && !value.startsWith('7')) {
            value = '7' + value;
        }
        
        // Применяем маску
        let formatted = '';
        if (value.length > 0) {
            formatted = '+7';
            if (value.length > 1) {
                formatted += ' (' + value.slice(1, 4);
                if (value.length > 4) {
                    formatted += ') ' + value.slice(4, 7);
                    if (value.length > 7) {
                        formatted += '-' + value.slice(7, 9);
                        if (value.length > 9) {
                            formatted += '-' + value.slice(9, 11);
                        }
                    }
                }
            }
        }
        
        // Устанавливаем отформатированное значение
        e.target.value = formatted;
        
        // Сохраняем позицию курсора
        const cursorPosition = e.target.selectionStart;
        setTimeout(() => {
            e.target.setSelectionRange(cursorPosition, cursorPosition);
        }, 0);
    });
    
    // Обработчик focus - если поле пустое, добавляем +7
    input.addEventListener('focus', function(e) {
        if (!e.target.value) {
            e.target.value = '+7 ';
            setTimeout(() => {
                e.target.setSelectionRange(3, 3);
            }, 0);
        }
    });
    
    // Обработчик для вставки из буфера
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const digits = paste.replace(/\D/g, '');
        
        if (digits.length >= 10) {
            let phoneNumber = digits;
            // Если начинается с 8, заменяем на 7
            if (phoneNumber.startsWith('8')) {
                phoneNumber = '7' + phoneNumber.slice(1);
            }
            // Если не начинается с 7, добавляем 7
            if (!phoneNumber.startsWith('7')) {
                phoneNumber = '7' + phoneNumber;
            }
            
            // Форматируем
            const formatted = '+7 (' + phoneNumber.slice(1, 4) + ') ' + 
                             phoneNumber.slice(4, 7) + '-' + 
                             phoneNumber.slice(7, 9) + '-' + 
                             phoneNumber.slice(9, 11);
            
            e.target.value = formatted;
        }
    });
    
    // Обработчик keydown для управления курсором
    input.addEventListener('keydown', function(e) {
        const cursorPosition = e.target.selectionStart;
        const value = e.target.value;
        
        // Backspace
        if (e.key === 'Backspace') {
            // Если курсор в начале или сразу после +7, не даем удалить
            if (cursorPosition <= 3) {
                e.preventDefault();
                e.target.setSelectionRange(3, 3);
                return;
            }
            
            // Если курсор на символе форматирования, перемещаем на предыдущую цифру
            const formatChars = [' ', '(', ')', '-'];
            if (formatChars.includes(value[cursorPosition - 1])) {
                e.preventDefault();
                let newPos = cursorPosition - 1;
                while (newPos > 3 && formatChars.includes(value[newPos - 1])) {
                    newPos--;
                }
                if (newPos > 3) {
                    const newValue = value.slice(0, newPos - 1) + value.slice(newPos);
                    e.target.value = newValue;
                    e.target.setSelectionRange(newPos - 1, newPos - 1);
                    // Запускаем событие input для переформатирования
                    e.target.dispatchEvent(new Event('input'));
                }
            }
        }
        
        // Delete
        if (e.key === 'Delete') {
            const formatChars = [' ', '(', ')', '-'];
            if (formatChars.includes(value[cursorPosition])) {
                e.preventDefault();
                let newPos = cursorPosition;
                while (newPos < value.length && formatChars.includes(value[newPos])) {
                    newPos++;
                }
                if (newPos < value.length) {
                    const newValue = value.slice(0, newPos) + value.slice(newPos + 1);
                    e.target.value = newValue;
                    e.target.setSelectionRange(cursorPosition, cursorPosition);
                    // Запускаем событие input для переформатирования
                    e.target.dispatchEvent(new Event('input'));
                }
            }
        }
    });
    
    // Инициализация с текущим значением, если оно есть
    if (input.value) {
        input.dispatchEvent(new Event('input'));
    }
}

// Экспорт функции для использования в других скриптах
window.initPhoneMask = initPhoneMask;

console.log('📞 Модуль маски телефона загружен');
