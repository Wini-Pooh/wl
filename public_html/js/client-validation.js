/**
 * Клиентская валидация форм для модальных окон
 * Обеспечивает мгновенную обратную связь пользователю
 */

(function() {
    'use strict';

    /**
     * Правила валидации для разных типов полей
     */
    const validationRules = {
        required: {
            test: (value) => value && value.trim().length > 0,
            message: 'Это поле обязательно для заполнения'
        },
        currency: {
            test: (value) => {
                if (!value || value.trim() === '') return false;
                // Разрешаем числа с запятой или точкой как разделителем десятичных
                const cleanValue = value.replace(/\s/g, '').replace(',', '.');
                const num = parseFloat(cleanValue);
                return !isNaN(num) && num >= 0 && /^\d+([.,]\d{0,2})?$/.test(value.replace(/\s/g, ''));
            },
            message: 'Введите корректную сумму (например: 1234.56)'
        },
        quantity: {
            test: (value) => {
                if (!value || value.trim() === '') return false;
                // Разрешаем числа с запятой или точкой как разделителем десятичных
                const cleanValue = value.replace(/\s/g, '').replace(',', '.');
                const num = parseFloat(cleanValue);
                return !isNaN(num) && num > 0 && /^\d+([.,]\d{0,3})?$/.test(value.replace(/\s/g, ''));
            },
            message: 'Введите корректное количество больше 0'
        },
        phone: {
            test: (value) => /^\+7\s\(\d{3}\)\s\d{3}-\d{2}-\d{2}$/.test(value),
            message: 'Введите телефон в формате +7 (999) 999-99-99'
        },
        email: {
            test: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            message: 'Введите корректный email адрес'
        },
        date: {
            test: (value) => {
                if (!value) return true; // Для необязательных дат
                const date = new Date(value);
                return date instanceof Date && !isNaN(date);
            },
            message: 'Введите корректную дату'
        },
        percentage: {
            test: (value) => {
                const num = parseFloat(value);
                return !isNaN(num) && num >= 0 && num <= 100;
            },
            message: 'Введите процент от 0 до 100'
        }
    };

    /**
     * Показать ошибку валидации для поля
     */
    function showFieldError(field, message) {
        clearFieldError(field);
        
        field.classList.add('is-invalid');
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        
        field.parentNode.appendChild(feedback);
    }

    /**
     * Очистить ошибку валидации для поля
     */
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        field.classList.remove('is-valid');
        
        const existingFeedback = field.parentNode.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    }

    /**
     * Показать успешную валидацию для поля
     */
    function showFieldSuccess(field) {
        clearFieldError(field);
        field.classList.add('is-valid');
    }

    /**
     * Валидировать одно поле
     */
    function validateField(field) {
        const value = field.value;
        const rules = field.dataset.validate ? field.dataset.validate.split('|') : [];
        const isRequired = field.hasAttribute('required') || rules.includes('required');
        
        // Если поле пустое и не обязательное - валидация пройдена
        if (!value.trim() && !isRequired) {
            clearFieldError(field);
            return true;
        }

        // Проверка обязательности
        if (isRequired && !validationRules.required.test(value)) {
            showFieldError(field, validationRules.required.message);
            return false;
        }

        // Проверка специфичных правил
        for (const rule of rules) {
            if (validationRules[rule] && !validationRules[rule].test(value)) {
                showFieldError(field, validationRules[rule].message);
                return false;
            }
        }

        // Проверка на основе CSS классов и data-mask
        const dataMask = field.dataset.mask;
        if (dataMask && validationRules[dataMask] && !validationRules[dataMask].test(value)) {
            showFieldError(field, validationRules[dataMask].message);
            return false;
        }

        // Проверка на основе CSS классов
        if (field.classList.contains('price-mask') && value && !validationRules.currency.test(value)) {
            showFieldError(field, validationRules.currency.message);
            return false;
        }

        if (field.classList.contains('quantity-mask') && value && !validationRules.quantity.test(value)) {
            showFieldError(field, validationRules.quantity.message);
            return false;
        }

        if (field.classList.contains('phone-mask') && value && !validationRules.phone.test(value)) {
            showFieldError(field, validationRules.phone.message);
            return false;
        }

        // Специальная проверка для дат
        if (field.type === 'date' && value && !validationRules.date.test(value)) {
            showFieldError(field, validationRules.date.message);
            return false;
        }

        // Проверка минимальных и максимальных значений
        if (field.min && parseFloat(value) < parseFloat(field.min)) {
            showFieldError(field, `Минимальное значение: ${field.min}`);
            return false;
        }

        if (field.max && parseFloat(value) > parseFloat(field.max)) {
            showFieldError(field, `Максимальное значение: ${field.max}`);
            return false;
        }

        // Если все проверки пройдены
        showFieldSuccess(field);
        return true;
    }

    /**
     * Валидировать всю форму
     */
    function validateForm(form) {
        const fields = form.querySelectorAll('input, select, textarea');
        let isValid = true;

        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Добавить обработчики валидации к полю
     */
    function addFieldValidation(field) {
        // Валидация при потере фокуса
        field.addEventListener('blur', function() {
            validateField(this);
        });

        // Валидация при вводе (с задержкой)
        let timeout;
        field.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                validateField(this);
            }, 500);
        });
    }

    /**
     * Инициализация валидации для формы
     */
    function initFormValidation(form) {
        console.log('🔍 Инициализация валидации для формы:', form.id);

        // Добавляем валидацию к полям
        const fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(addFieldValidation);

        // Валидация при отправке формы
        form.addEventListener('submit', function(e) {
            console.log('📝 Валидация формы при отправке...');
            
            if (!validateForm(this)) {
                e.preventDefault();
                e.stopPropagation();
                
                // Фокус на первое поле с ошибкой
                const firstErrorField = this.querySelector('.is-invalid');
                if (firstErrorField) {
                    firstErrorField.focus();
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Показываем уведомление
                if (typeof showMessage === 'function') {
                    showMessage('Пожалуйста, исправьте ошибки в форме', 'warning');
                }
                
                return false;
            }
        });
    }

    /**
     * Инициализация валидации для всех форм
     */
    function initAllFormsValidation() {
        console.log('🚀 Инициализация клиентской валидации...');

        // Находим все формы в модальных окнах
        const modalForms = document.querySelectorAll('.modal form');
        modalForms.forEach(initFormValidation);

        // Находим все формы с классом ajax-form
        const ajaxForms = document.querySelectorAll('.ajax-form');
        ajaxForms.forEach(initFormValidation);
    }

    /**
     * Переинициализация валидации в контейнере
     */
    function reinitValidation(container) {
        if (!container) {
            initAllFormsValidation();
            return;
        }

        console.log('🔄 Переинициализация валидации в контейнере:', container);

        const forms = container.querySelectorAll('form');
        forms.forEach(initFormValidation);
    }

    /**
     * Очистка ошибок валидации в форме
     */
    function clearFormErrors(form) {
        const fields = form.querySelectorAll('.is-invalid, .is-valid');
        fields.forEach(field => {
            clearFieldError(field);
        });
    }

    // Глобальные функции
    window.ClientValidation = {
        init: initAllFormsValidation,
        reinit: reinitValidation,
        validateForm: validateForm,
        validateField: validateField,
        clearFormErrors: clearFormErrors,
        showFieldError: showFieldError,
        clearFieldError: clearFieldError
    };

    // Автоинициализация при загрузке DOM
    document.addEventListener('DOMContentLoaded', function() {
        initAllFormsValidation();
    });

    // Переинициализация при показе модальных окон
    $(document).on('shown.bs.modal', function(e) {
        setTimeout(function() {
            reinitValidation(e.target);
        }, 100);
    });

    // Интеграция с глобальной функцией clearFormErrors
    if (!window.clearFormErrors) {
        window.clearFormErrors = clearFormErrors;
    }

})();
