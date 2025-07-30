/**
 * Основные вспомогательные функции для системы документов
 * Объединяет функциональность из ajax-helper.js, notifications.js, phone-mask.js, tab-filters-fix.js
 * Версия: 2.0
 */

// ==================== AJAX HELPER ====================
/**
 * Универсальная функция для выполнения AJAX запросов с обработкой ошибок
 */
window.ajaxRequest = async function(url, options = {}) {
    try {
        // Добавляем CSRF токен по умолчанию
        const defaultHeaders = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };
        
        // Объединяем заголовки
        const headers = { ...defaultHeaders, ...options.headers };
        
        // Выполняем запрос
        const response = await fetch(url, { ...options, headers });
        
        // Проверяем статус ответа
        if (!response.ok) {
            const errorText = await response.text();
            console.error(`HTTP ${response.status} Error:`, errorText);
            
            // Пытаемся извлечь сообщение об ошибке из HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(errorText, 'text/html');
            const errorMessage = doc.querySelector('.exception-message')?.textContent || 
                               doc.querySelector('h1')?.textContent || 
                               'Неизвестная ошибка сервера';
            
            throw new Error(`${response.status}: ${errorMessage}`);
        }
        
        // Проверяем тип контента
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        } else {
            return await response.text();
        }
        
    } catch (error) {
        console.error('AJAX Error:', error);
        
        // Улучшенные сообщения об ошибках
        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            throw new Error('Ошибка сети. Проверьте подключение к интернету.');
        } else if (error.message.includes('500')) {
            throw new Error('Внутренняя ошибка сервера. Попробуйте позже.');
        } else if (error.message.includes('404')) {
            throw new Error('Запрашиваемый ресурс не найден.');
        } else {
            throw error;
        }
    }
};

// ==================== NOTIFICATIONS ====================
/**
 * Отображает сообщение в верхней части экрана
 * @param {string} message - текст сообщения
 * @param {string} type - тип сообщения (success, error, warning, info)
 */
function showMessage(message, type = 'success') {
    // Удаляем существующие сообщения с тем же типом
    $('.toast-message.' + type).remove();
    
    // Определяем цвет и иконку в зависимости от типа
    let bgColor, icon;
    switch(type) {
        case 'success':
            bgColor = 'bg-success';
            icon = 'bi-check-circle';
            break;
        case 'error':
            bgColor = 'bg-danger';
            icon = 'bi-exclamation-triangle';
            break;
        case 'warning':
            bgColor = 'bg-warning';
            icon = 'bi-exclamation-circle';
            break;
        case 'info':
            bgColor = 'bg-info';
            icon = 'bi-info-circle';
            break;
        default:
            bgColor = 'bg-primary';
            icon = 'bi-bell';
    }
    
    // Создаем элемент сообщения
    const toast = $(`
        <div class="toast-message ${type} position-fixed top-0 start-50 translate-middle-x p-3 mt-3" style="z-index: 9999;">
            <div class="toast show align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icon} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрыть"></button>
                </div>
            </div>
        </div>
    `);
    
    // Добавляем в DOM
    $('body').append(toast);
    
    // Удаляем через 3 секунды
    setTimeout(function() {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

/**
 * Функция для показа/скрытия спиннера загрузки
 */
window.toggleLoading = function(show = true, target = null) {
    if (target) {
        const button = typeof target === 'string' ? document.querySelector(target) : target;
        if (button) {
            if (show) {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.dataset.originalText = originalText;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Загрузка...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalText || button.innerHTML;
            }
        }
    }
};

// ==================== PHONE MASK ====================
/**
 * Маска для номера телефона в формате +7 (XXX) XXX-XX-XX
 */
function initPhoneMask(inputSelector) {
    const phoneInputs = document.querySelectorAll(inputSelector);
    
    phoneInputs.forEach(phoneInput => {
        function formatPhoneNumber(value) {
            // Удаляем все символы кроме цифр
            const cleaned = value.replace(/\D/g, '');
            
            // Если начинается с 8, заменяем на 7
            let numbers = cleaned;
            if (numbers.startsWith('8')) {
                numbers = '7' + numbers.slice(1);
            }
            
            // Если не начинается с 7, добавляем 7
            if (!numbers.startsWith('7')) {
                numbers = '7' + numbers;
            }
            
            // Ограничиваем длину до 11 цифр
            numbers = numbers.slice(0, 11);
            
            // Форматируем в маску +7 (XXX) XXX-XX-XX
            if (numbers.length >= 1) {
                let formatted = '+7';
                if (numbers.length > 1) {
                    formatted += ' (' + numbers.slice(1, 4);
                    if (numbers.length >= 4) {
                        formatted += ')';
                        if (numbers.length > 4) {
                            formatted += ' ' + numbers.slice(4, 7);
                            if (numbers.length > 7) {
                                formatted += '-' + numbers.slice(7, 9);
                                if (numbers.length > 9) {
                                    formatted += '-' + numbers.slice(9, 11);
                                }
                            }
                        }
                    }
                }
                return formatted;
            }
            
            return '+7 (';
        }
        
        phoneInput.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const formatted = formatPhoneNumber(e.target.value);
            
            e.target.value = formatted;
            
            // Корректируем позицию курсора
            if (formatted.length < oldValue.length) {
                e.target.setSelectionRange(cursorPosition - 1, cursorPosition - 1);
            } else {
                e.target.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
            }
        });
        
        phoneInput.addEventListener('keydown', function(e) {
            // Разрешаем удаление и навигацию
            if (e.key === 'Backspace' || e.key === 'Delete' || 
                e.key === 'ArrowLeft' || e.key === 'ArrowRight' || 
                e.key === 'Tab' || e.key === 'Home' || e.key === 'End') {
                return;
            }
            
            // Разрешаем Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            if (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'v' || e.key === 'x')) {
                return;
            }
            
            // Разрешаем только цифры
            if (!/\d/.test(e.key)) {
                e.preventDefault();
            }
        });
        
        phoneInput.addEventListener('focus', function(e) {
            if (e.target.value === '') {
                e.target.value = '+7 (';
            }
        });
        
        phoneInput.addEventListener('blur', function(e) {
            if (e.target.value === '+7 (') {
                e.target.value = '';
            }
        });
        
        // Инициализация маски, если поле уже имеет значение
        if (phoneInput.value && phoneInput.value !== '') {
            phoneInput.value = formatPhoneNumber(phoneInput.value);
        }
    });
}

// ==================== TAB FILTERS ====================
/**
 * Универсальные исправления для фильтров во всех вкладках
 */
function initTabFilters() {
    console.log('Инициализация универсальных фильтров для вкладок');
    
    // Переключение видимости фильтров
    $(document).on('click', '[id^="toggle"][id*="FiltersBtn"]', function() {
        const btnId = $(this).attr('id');
        const tabType = btnId.replace('toggle', '').replace('FiltersBtn', '').toLowerCase();
        const filtersBody = $(`#${tabType}FiltersBody`);
        const icon = $(this).find('i');
        
        if (filtersBody.is(':visible')) {
            filtersBody.slideUp();
            icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
        } else {
            filtersBody.slideDown();
            icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
        }
    });
    
    // Переключение расширенных фильтров
    $(document).on('click', '[id^="toggleAdvanced"][id*="Filters"]', function() {
        const btnId = $(this).attr('id');
        const tabType = btnId.replace('toggleAdvanced', '').replace('Filters', '').toLowerCase();
        const advancedFilters = $(`#advanced${tabType.charAt(0).toUpperCase() + tabType.slice(1)}Filters`);
        const button = $(this);
        const icon = button.find('i');
        const text = button.find('span');
        
        if (advancedFilters.is(':visible')) {
            advancedFilters.slideUp();
            icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
            text.text('Расширенные фильтры');
        } else {
            advancedFilters.slideDown();
            icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
            text.text('Скрыть фильтры');
        }
    });
    
    // Очистка поиска
    $(document).on('click', '[id^="clear"][id*="SearchBtn"]', function() {
        const btnId = $(this).attr('id');
        const tabType = btnId.replace('clear', '').replace('SearchBtn', '').toLowerCase();
        const searchInput = $(`#${tabType}SearchFilter`);
        
        searchInput.val('');
        searchInput.trigger('input');
    });
    
    // Подсчет активных фильтров
    function updateActiveFiltersCount(tabType) {
        const form = $(`#${tabType}FilterForm`);
        const filledInputs = form.find('input, select').filter(function() {
            return $(this).val() && $(this).val() !== '';
        });
        
        const count = filledInputs.length;
        const badge = $(`#${tabType}FiltersBadge`);
        
        if (count > 0) {
            badge.text(count).show();
        } else {
            badge.hide();
        }
    }
    
    // Обработчик изменения любых фильтров для обновления счетчика
    $(document).on('change input', '[id*="Filter"]', function() {
        const id = $(this).attr('id');
        if (id) {
            const tabType = id.replace(/Filter.*/, '').replace(/.*Filter/, '').toLowerCase();
            if (tabType) {
                updateActiveFiltersCount(tabType);
            }
        }
    });
    
    console.log('Универсальные фильтры инициализированы');
}

// ==================== EXPORTS ====================
// Экспортируем функции в глобальную область
window.showMessage = showMessage;
window.showNotification = showMessage; // Алиас для совместимости
window.initPhoneMask = initPhoneMask;
window.initTabFilters = initTabFilters;

// ==================== AUTO INITIALIZATION ====================
// Автоматическая инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем маску для всех полей с типом tel или классом phone-mask
    initPhoneMask('input[type="tel"], input.phone-mask');
    
    // Инициализируем фильтры вкладок
    initTabFilters();
    
    console.log('Core Helpers инициализированы');
});

// jQuery версия инициализации для совместимости
$(document).ready(function() {
    initTabFilters();
});
