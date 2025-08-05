/**
 * AJAX обработчики для финансовых операций
 * Версия: 2.3 - Исправлено заполнение данных в модальные окна
 * Дата: 2 августа 2025
 */

console.log('💰 Загружен finance-ajax.js v2.3');

// Глобальные переменные
window.financeAjaxInitialized = false;
window.currentEditingRecord = null;
window.actionButtonHandlersInitialized = false;
window.globalAjaxHandlersInitialized = false;


function initFinanceAjax() {
    if (window.financeAjaxInitialized) {
        console.log('⚠️ AJAX обработчики уже инициализированы');
        return;
    }
    
    console.log('🚀 Инициализация AJAX обработчиков для финансов v2.3...');
    
    // Отмечаем как инициализированные в начале
    window.financeAjaxInitialized = true;
    
    // Настройка CSRF токена для всех AJAX запросов
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Проверяем доступность jQuery
    if (typeof $ === 'undefined') {
        console.error('❌ jQuery не загружен!');
        return;
    }
    
    // Проверяем наличие проекта ID
    if (typeof window.projectId === 'undefined') {
        console.error('❌ Project ID не определен!');
    } else {
        console.log('✅ Project ID:', window.projectId);
    }
    
    // Инициализируем обработчики для каждого типа формы
    initWorkFormAjax();
    initMaterialFormAjax();
    initTransportFormAjax();
    initFinanceModalAjax();
    
    // Настраиваем общие обработчики
    setupGlobalEventHandlers();
    
    console.log('✅ AJAX обработчики для финансов v2.3 инициализированы');
}

/**
 * Настройка глобальных обработчиков событий
 */
function setupGlobalEventHandlers() {
    console.log('🔧 Настройка глобальных обработчиков...');
    
    // Проверяем, не настроены ли уже глобальные обработчики
    if (window.globalAjaxHandlersInitialized) {
        console.log('⚠️ Глобальные AJAX обработчики уже настроены');
        return;
    }
    
    // Отмечаем как настроенные
    window.globalAjaxHandlersInitialized = true;
    
    // Настройка обработчиков кнопок действий
    setupActionButtonHandlers();
    
    // Глобальные обработчики ошибок AJAX
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        // Игнорируем прерванные запросы
        if (xhr.statusText === 'abort') {
            return;
        }
        
        console.error('AJAX Error:', {
            url: settings.url,
            status: xhr.status,
            error: thrownError,
            response: xhr.responseText
        });
        
        let errorMessage = 'Произошла ошибка при выполнении операции';
        
        if (xhr.status === 422) {
            // Ошибки валидации
            try {
                const errors = JSON.parse(xhr.responseText);
                if (errors.errors) {
                    const errorList = Object.values(errors.errors).flat();
                    errorMessage = errorList.join('<br>');
                }
            } catch (e) {
                console.error('Ошибка парсинга ответа валидации:', e);
            }
        } else if (xhr.status === 500) {
            errorMessage = 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.';
        } else if (xhr.status === 404) {
            errorMessage = 'Ресурс не найден';
        } else if (xhr.status === 403) {
            errorMessage = 'Недостаточно прав доступа';
        } else if (xhr.status === 0) {
            errorMessage = 'Ошибка соединения с сервером';
        }
        
        showNotification(errorMessage, 'error');
    });
    
    // Успешные AJAX запросы
    $(document).ajaxSuccess(function(event, xhr, settings) {
        console.log('✅ AJAX Success:', settings.url);
    });
    
    // Начало AJAX запроса
    $(document).ajaxStart(function() {
        showPageLoader(true);
    });
    
    // Завершение всех AJAX запросов
    $(document).ajaxStop(function() {
        showPageLoader(false);
    });
}

/**
 * Настройка обработчиков кнопок действий
 */
function setupActionButtonHandlers() {
    console.log('🔧 Настройка обработчиков кнопок действий...');
    
    // Проверяем, не настроены ли уже обработчики кнопок действий
    if (window.actionButtonHandlersInitialized) {
        console.log('⚠️ Обработчики кнопок действий уже настроены');
        return;
    }
    
    // Отмечаем как настроенные
    window.actionButtonHandlersInitialized = true;
    
    // Делегирование событий для всех кнопок с data-action
    $(document).off('click.action-buttons').on('click.action-buttons', '[data-action]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const action = $(this).data('action');
        const id = $(this).data('id');
        
        console.log(`🎯 Обработка действия: ${action}`, { id, element: this });
        
        switch(action) {
            case 'edit-work':
                if (id && typeof editWork === 'function') {
                    editWork(id);
                } else {
                    console.error('❌ Функция editWork не найдена или ID не указан', { id, editWorkExists: typeof editWork });
                }
                break;
                
            case 'delete-work':
                if (id) {
                    deleteFinanceRecord('work', id);
                } else {
                    console.error('❌ ID не указан для удаления работы');
                }
                break;
                
            case 'edit-material':
                if (id && typeof editMaterial === 'function') {
                    editMaterial(id);
                } else {
                    console.error('❌ Функция editMaterial не найдена или ID не указан');
                }
                break;
                
            case 'delete-material':
                if (id) {
                    deleteFinanceRecord('material', id);
                } else {
                    console.error('❌ ID не указан для удаления материала');
                }
                break;
                
            case 'edit-transport':
                if (id && typeof editTransport === 'function') {
                    editTransport(id);
                } else {
                    console.error('❌ Функция editTransport не найдена или ID не указан');
                }
                break;
                
            case 'delete-transport':
                if (id) {
                    deleteFinanceRecord('transport', id);
                } else {
                    console.error('❌ ID не указан для удаления транспорта');
                }
                break;
                
            case 'add-work':
                // Открытие модального окна для добавления работы
                console.log('🏗️ Открытие модального окна для добавления работы...');
                const workModalElement = document.getElementById('workModal');
                if (!workModalElement) {
                    console.error('❌ Модальное окно workModal не найдено в DOM');
                    return;
                }
                if (typeof bootstrap === 'undefined') {
                    console.error('❌ Bootstrap не загружен');
                    return;
                }
                const workModal = new bootstrap.Modal(workModalElement);
                workModal.show();
                break;
                
            case 'add-material':
                // Открытие модального окна для добавления материала
                console.log('📦 Открытие модального окна для добавления материала...');
                const materialModalElement = document.getElementById('materialModal');
                if (!materialModalElement) {
                    console.error('❌ Модальное окно materialModal не найдено в DOM');
                    return;
                }
                if (typeof bootstrap === 'undefined') {
                    console.error('❌ Bootstrap не загружен');
                    return;
                }
                const materialModal = new bootstrap.Modal(materialModalElement);
                materialModal.show();
                break;
                
            case 'add-transport':
                // Открытие модального окна для добавления транспорта
                console.log('🚛 Открытие модального окна для добавления транспорта...');
                const transportModalElement = document.getElementById('transportModal');
                if (!transportModalElement) {
                    console.error('❌ Модальное окно transportModal не найдено в DOM');
                    return;
                }
                if (typeof bootstrap === 'undefined') {
                    console.error('❌ Bootstrap не загружен');
                    return;
                }
                const transportModal = new bootstrap.Modal(transportModalElement);
                transportModal.show();
                break;
                
            default:
                console.warn(`⚠️ Неизвестное действие: ${action}`);
        }
    });
    
    console.log('✅ Обработчики кнопок действий настроены');
    
    // Добавляем функцию для отладки кнопок
    window.testActionButtons = function() {
        console.log('🔍 Тестирование кнопок действий...');
        const buttons = document.querySelectorAll('[data-action]');
        console.log(`Найдено кнопок с data-action: ${buttons.length}`);
        buttons.forEach((btn, index) => {
            console.log(`Кнопка ${index + 1}:`, {
                action: btn.getAttribute('data-action'),
                id: btn.getAttribute('data-id'),
                element: btn
            });
        });
    };
    
    // Добавляем функцию для диагностики системы
    window.debugFinanceSystem = function() {
        console.log('🔧 Диагностика финансовой системы:');
        console.log('- financeAjaxInitialized:', window.financeAjaxInitialized);
        console.log('- actionButtonHandlersInitialized:', window.actionButtonHandlersInitialized);
        console.log('- globalAjaxHandlersInitialized:', window.globalAjaxHandlersInitialized);
        console.log('- projectId:', window.projectId);
        console.log('- jQuery загружен:', typeof $ !== 'undefined');
        console.log('- Bootstrap загружен:', typeof bootstrap !== 'undefined');
        
        // Проверяем модальные окна
        const modals = ['workModal', 'materialModal', 'transportModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            console.log(`- ${modalId} существует:`, !!modal);
        });
        
        // Проверяем функции
        const functions = ['editWork', 'editMaterial', 'editTransport', 'deleteFinanceRecord'];
        functions.forEach(funcName => {
            console.log(`- ${funcName} функция существует:`, typeof window[funcName] === 'function');
        });
        
        // Тестируем кнопки
        window.testActionButtons();
        
        // Проверяем наличие форм
        const forms = ['workForm', 'materialForm', 'transportForm'];
        forms.forEach(formId => {
            const form = document.getElementById(formId);
            console.log(`- ${formId} форма существует:`, !!form);
            if (form) {
                const fields = form.querySelectorAll('input, select, textarea');
                console.log(`  - поля в ${formId}:`, fields.length);
            }
        });
    };
}

/**
 * Инициализация AJAX для формы работ
 */
function initWorkFormAjax() {
    console.log('🔧 Настройка AJAX для формы работ...');
    
    const form = document.getElementById('workForm');
    if (!form) {
        console.warn('⚠️ Форма работ не найдена');
        return;
    }
    
    // Проверяем, не инициализирована ли уже форма
    if ($(form).data('ajax-initialized')) {
        console.log('⚠️ Форма работ уже инициализирована');
        return;
    }
    
    // Отмечаем форму как инициализированную
    $(form).data('ajax-initialized', true);
    
    $(form).on('submit.ajax', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('📝 Отправка формы работ через AJAX...');
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const isEditing = formData.get('work_id') && formData.get('work_id') !== '';
        
        // Показываем состояние загрузки
        submitBtn.prop('disabled', true)
                 .html('<i class="bi bi-arrow-clockwise spin me-1"></i>Сохранение...');
        
        // Очищаем предыдущие ошибки
        clearFormErrors(this);
        
        // Определяем URL и метод на основе режима
        let url = this.action;
        let method = 'POST';
        
        if (isEditing) {
            // Для редактирования добавляем _method=PUT
            formData.append('_method', 'PUT');
            console.log('✏️ Режим редактирования работы');
        } else {
            console.log('➕ Режим добавления работы');
        }
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                console.log('📤 Отправка данных работы...', {
                    url: url,
                    isEditing: isEditing,
                    formData: Object.fromEntries(formData.entries())
                });
            },
            success: function(response) {
                console.log('✅ Работа успешно сохранена:', response);
                
                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('workModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Показываем уведомление
                showNotification(
                    response.message || (isEditing ? 'Работа успешно обновлена' : 'Работа успешно добавлена'), 
                    'success'
                );
                
                // Обновляем финансовые данные
                refreshFinanceData();
                
                // Сбрасываем форму
                resetForm(form);
            },
            error: function(xhr) {
                console.error('❌ Ошибка сохранения работы:', xhr);
                
                if (xhr.status === 422) {
                    // Показываем ошибки валидации
                    const errors = xhr.responseJSON?.errors || {};
                    showFormErrors(form, errors);
                    showNotification('Проверьте правильность заполнения полей', 'warning');
                } else {
                    showNotification('Ошибка при сохранении работы', 'error');
                }
            },
            complete: function() {
                // Восстанавливаем кнопку
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    console.log('✅ AJAX для формы работ настроен');
}

/**
 * Инициализация AJAX для формы материалов
 */
function initMaterialFormAjax() {
    console.log('📦 Настройка AJAX для формы материалов...');
    
    const form = document.getElementById('materialForm');
    if (!form) {
        console.warn('⚠️ Форма материалов не найдена');
        return;
    }
    
    // Проверяем, не инициализирована ли уже форма
    if ($(form).data('ajax-initialized')) {
        console.log('⚠️ Форма материалов уже инициализирована');
        return;
    }
    
    // Отмечаем форму как инициализированную
    $(form).data('ajax-initialized', true);
    
    $(form).on('submit.ajax', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('📦 Отправка формы материалов через AJAX...');
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const isEditing = formData.get('material_id') && formData.get('material_id') !== '';
        
        // Показываем состояние загрузки
        submitBtn.prop('disabled', true)
                 .html('<i class="bi bi-arrow-clockwise spin me-1"></i>Сохранение...');
        
        // Очищаем предыдущие ошибки
        clearFormErrors(this);
        
        // Определяем URL и метод на основе режима
        let url = this.action;
        let method = 'POST';
        
        if (isEditing) {
            // Для редактирования добавляем _method=PUT
            formData.append('_method', 'PUT');
            console.log('✏️ Режим редактирования материала');
        } else {
            console.log('➕ Режим добавления материала');
        }
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                console.log('📤 Отправка данных материала...', {
                    url: url,
                    isEditing: isEditing,
                    formData: Object.fromEntries(formData.entries())
                });
            },
            success: function(response) {
                console.log('✅ Материал успешно сохранен:', response);
                
                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('materialModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Показываем уведомление
                showNotification(
                    response.message || (isEditing ? 'Материал успешно обновлен' : 'Материал успешно добавлен'), 
                    'success'
                );
                
                // Обновляем финансовые данные
                refreshFinanceData();
                
                // Сбрасываем форму
                resetForm(form);
            },
            error: function(xhr) {
                console.error('❌ Ошибка сохранения материала:', xhr);
                
                if (xhr.status === 422) {
                    // Показываем ошибки валидации
                    const errors = xhr.responseJSON?.errors || {};
                    showFormErrors(form, errors);
                    showNotification('Проверьте правильность заполнения полей', 'warning');
                } else {
                    showNotification('Ошибка при сохранении материала', 'error');
                }
            },
            complete: function() {
                // Восстанавливаем кнопку
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    console.log('✅ AJAX для формы материалов настроен');
}

/**
 * Инициализация AJAX для формы транспорта
 */
function initTransportFormAjax() {
    console.log('🚛 Настройка AJAX для формы транспорта...');
    
    const form = document.getElementById('transportForm');
    if (!form) {
        console.warn('⚠️ Форма транспорта не найдена');
        return;
    }
    
    // Проверяем, не инициализирована ли уже форма
    if ($(form).data('ajax-initialized')) {
        console.log('⚠️ Форма транспорта уже инициализирована');
        return;
    }
    
    // Отмечаем форму как инициализированную
    $(form).data('ajax-initialized', true);
    
    $(form).on('submit.ajax', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('🚛 Отправка формы транспорта через AJAX...');
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const isEditing = formData.get('transport_id') && formData.get('transport_id') !== '';
        
        // Показываем состояние загрузки
        submitBtn.prop('disabled', true)
                 .html('<i class="bi bi-arrow-clockwise spin me-1"></i>Сохранение...');
        
        // Очищаем предыдущие ошибки
        clearFormErrors(this);
        
        // Определяем URL и метод на основе режима
        let url = this.action;
        let method = 'POST';
        
        if (isEditing) {
            // Для редактирования добавляем _method=PUT
            formData.append('_method', 'PUT');
            console.log('✏️ Режим редактирования транспорта');
        } else {
            console.log('➕ Режим добавления транспорта');
        }
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                console.log('📤 Отправка данных транспорта...', {
                    url: url,
                    isEditing: isEditing,
                    formData: Object.fromEntries(formData.entries())
                });
            },
            success: function(response) {
                console.log('✅ Транспорт успешно сохранен:', response);
                
                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('transportModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Показываем уведомление
                showNotification(
                    response.message || (isEditing ? 'Транспорт успешно обновлен' : 'Транспорт успешно добавлен'), 
                    'success'
                );
                
                // Обновляем финансовые данные
                refreshFinanceData();
                
                // Сбрасываем форму
                resetForm(form);
            },
            error: function(xhr) {
                console.error('❌ Ошибка сохранения транспорта:', xhr);
                
                if (xhr.status === 422) {
                    // Показываем ошибки валидации
                    const errors = xhr.responseJSON?.errors || {};
                    showFormErrors(form, errors);
                    showNotification('Проверьте правильность заполнения полей', 'warning');
                } else {
                    showNotification('Ошибка при сохранении транспорта', 'error');
                }  
            },
            complete: function() {
                // Восстанавливаем кнопку
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    console.log('✅ AJAX для формы транспорта настроен');
}

/**
 * Инициализация AJAX для общего финансового модального окна
 */
function initFinanceModalAjax() {
    console.log('💰 Настройка AJAX для финансового модального окна...');
    
    const form = document.getElementById('financeForm');
    if (!form) {
        console.warn('⚠️ Общая финансовая форма не найдена - это нормально, если используются отдельные формы');
        return;
    }
    
    // Проверяем, не инициализирована ли уже форма
    if ($(form).data('ajax-initialized')) {
        console.log('⚠️ Финансовая форма уже инициализирована');
        return;
    }
    
    // Отмечаем форму как инициализированную
    $(form).data('ajax-initialized', true);
    
    $(form).on('submit.ajax', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('💰 Отправка общей финансовой формы через AJAX...');
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const financeType = formData.get('type');
        const isEditing = formData.get('finance_id') && formData.get('finance_id') !== '';
        
        // Показываем состояние загрузки
        submitBtn.prop('disabled', true)
                 .html('<i class="bi bi-arrow-clockwise spin me-1"></i>Сохранение...');
        
        // Очищаем предыдущие ошибки
        clearFormErrors(this);
        
        // Определяем URL на основе типа финансовой записи
        let url = this.action;
        let method = 'POST';
        
        if (isEditing) {
            formData.append('_method', 'PUT');
            console.log(`✏️ Режим редактирования ${financeType}`);
        } else {
            console.log(`➕ Режим добавления ${financeType}`);
        }
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                console.log('📤 Отправка финансовых данных...', {
                    url: url,
                    type: financeType,
                    isEditing: isEditing,
                    formData: Object.fromEntries(formData.entries())
                });
            },
            success: function(response) {
                console.log(`✅ ${financeType} успешно сохранен:`, response);
                
                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('financeModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Показываем уведомление
                const actionText = isEditing ? 'обновлена' : 'добавлена';
                showNotification(
                    response.message || `Финансовая запись успешно ${actionText}`, 
                    'success'
                );
                
                // Обновляем финансовые данные
                refreshFinanceData();
                
                // Сбрасываем форму
                resetForm(form);
            },
            error: function(xhr) {
                console.error(`❌ Ошибка сохранения ${financeType}:`, xhr);
                
                if (xhr.status === 422) {
                    // Показываем ошибки валидации
                    const errors = xhr.responseJSON?.errors || {};
                    showFormErrors(form, errors);
                    showNotification('Проверьте правильность заполнения полей', 'warning');
                } else {
                    showNotification(`Ошибка при сохранении ${financeType}`, 'error');
                }
            },
            complete: function() {
                // Восстанавливаем кнопку
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    console.log('✅ AJAX для финансового модального окна настроен');
}

/**
 * Функции редактирования записей
 */
function editWork(id, workData = null) {
    console.log(`✏️ Редактирование работы #${id}...`);
    
    // Проверяем наличие модального окна
    const modalElement = document.getElementById('workModal');
    if (!modalElement) {
        console.error('❌ Модальное окно workModal не найдено в DOM');
        showNotification('Модальное окно не найдено', 'error');
        return;
    }
    
    // Проверяем наличие Bootstrap
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap не загружен');
        showNotification('Bootstrap не загружен', 'error');
        return;
    }
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(modalElement);
    
    if (workData) {
        // Если данные переданы, заполняем форму
        fillWorkForm(workData);
        modal.show();
    } else {
        // Загружаем данные через AJAX
        console.log(`🔄 Загрузка данных работы через AJAX: /partner/projects/${window.projectId}/works/${id}`);
        $.ajax({
            url: `/partner/projects/${window.projectId}/works/${id}`,
            method: 'GET',
            success: function(response) {
                console.log('✅ Данные работы получены с сервера:', response);
                
                // Проверяем структуру ответа
                let workData = null;
                if (response && response.data) {
                    workData = response.data;
                } else if (response && response.work) {
                    // Новый формат ответа: {success: true, work: {...}}
                    workData = response.work;
                } else if (response && response.id) {
                    workData = response;
                } else {
                    console.error('❌ Неожиданная структура ответа:', response);
                    showNotification('Ошибка структуры данных', 'error');
                    return;
                }
                
                fillWorkForm(workData);
                modal.show();
            },
            error: function(xhr) {
                console.error('❌ Ошибка загрузки данных работы:', xhr);
                let errorMessage = 'Ошибка загрузки данных работы';
                
                if (xhr.status === 404) {
                    errorMessage = 'Запись работы не найдена';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showNotification(errorMessage, 'error');
            }
        });
    }
}

function editMaterial(id, materialData = null) {
    console.log(`✏️ Редактирование материала #${id}...`);
    
    // Проверяем наличие модального окна
    const modalElement = document.getElementById('materialModal');
    if (!modalElement) {
        console.error('❌ Модальное окно materialModal не найдено в DOM');
        showNotification('Модальное окно не найдено', 'error');
        return;
    }
    
    // Проверяем наличие Bootstrap
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap не загружен');
        showNotification('Bootstrap не загружен', 'error');
        return;
    }
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(modalElement);
    
    if (materialData) {
        // Если данные переданы, заполняем форму
        fillMaterialForm(materialData);
        modal.show();
    } else {
        // Загружаем данные через AJAX
        console.log(`🔄 Загрузка данных материала через AJAX: /partner/projects/${window.projectId}/materials/${id}`);
        $.ajax({
            url: `/partner/projects/${window.projectId}/materials/${id}`,
            method: 'GET',
            success: function(response) {
                console.log('✅ Данные материала получены с сервера:', response);
                
                // Проверяем структуру ответа
                let materialData = null;
                if (response && response.data) {
                    materialData = response.data;
                } else if (response && response.material) {
                    // Новый формат ответа: {success: true, material: {...}}
                    materialData = response.material;
                } else if (response && response.id) {
                    materialData = response;
                } else {
                    console.error('❌ Неожиданная структура ответа:', response);
                    showNotification('Ошибка структуры данных', 'error');
                    return;
                }
                
                fillMaterialForm(materialData);
                modal.show();
            },
            error: function(xhr) {
                console.error('❌ Ошибка загрузки данных материала:', xhr);
                let errorMessage = 'Ошибка загрузки данных материала';
                
                if (xhr.status === 404) {
                    errorMessage = 'Запись материала не найдена';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showNotification(errorMessage, 'error');
            }
        });
    }
}

function editTransport(id, transportData = null) {
    console.log(`✏️ Редактирование транспорта #${id}...`);
    
    // Проверяем наличие модального окна
    const modalElement = document.getElementById('transportModal');
    if (!modalElement) {
        console.error('❌ Модальное окно transportModal не найдено в DOM');
        showNotification('Модальное окно не найдено', 'error');
        return;
    }
    
    // Проверяем наличие Bootstrap
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap не загружен');
        showNotification('Bootstrap не загружен', 'error');
        return;
    }
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(modalElement);
    
    if (transportData) {
        // Если данные переданы, заполняем форму
        fillTransportForm(transportData);
        modal.show();
    } else {
        // Загружаем данные через AJAX
        console.log(`🔄 Загрузка данных транспорта через AJAX: /partner/projects/${window.projectId}/transports/${id}`);
        $.ajax({
            url: `/partner/projects/${window.projectId}/transports/${id}`,
            method: 'GET',
            success: function(response) {
                console.log('✅ Данные транспорта получены с сервера:', response);
                
                // Проверяем структуру ответа
                let transportData = null;
                if (response && response.data) {
                    transportData = response.data;
                } else if (response && response.transport) {
                    // Новый формат ответа: {success: true, transport: {...}}
                    transportData = response.transport;
                } else if (response && response.id) {
                    transportData = response;
                } else {
                    console.error('❌ Неожиданная структура ответа:', response);
                    showNotification('Ошибка структуры данных', 'error');
                    return;
                }
                
                fillTransportForm(transportData);
                modal.show();
            },
            error: function(xhr) {
                console.error('❌ Ошибка загрузки данных транспорта:', xhr);
                let errorMessage = 'Ошибка загрузки данных транспорта';
                
                if (xhr.status === 404) {
                    errorMessage = 'Запись транспорта не найдена';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showNotification(errorMessage, 'error');
            }
        });
    }
}

/**
 * Функции заполнения форм
 */
function fillWorkForm(data) {
    console.log('📝 Заполнение формы работы данными:', data);
    
    const form = document.getElementById('workForm');
    if (!form) {
        console.error('❌ Форма workForm не найдена');
        return;
    }
    
    // Заполняем скрытое поле ID
    const workIdField = form.querySelector('[name="work_id"]');
    if (workIdField) {
        workIdField.value = data.id || '';
    }
    
    // Заполняем основные поля
    const fieldMappings = {
        'name': data.name || '',
        'description': data.description || '',
        'quantity': data.quantity || '',
        'unit': data.unit || 'шт',
        'price': data.price || ''
    };
    
    // Заполняем поля по имени
    Object.keys(fieldMappings).forEach(fieldName => {
        const input = form.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = fieldMappings[fieldName];
            console.log(`✅ Заполнено поле ${fieldName}:`, fieldMappings[fieldName]);
        } else {
            console.warn(`⚠️ Поле ${fieldName} не найдено в форме`);
        }
    });
    
    // Обновляем заголовок модального окна
    const titleElement = document.querySelector('#workModalTitle');
    if (titleElement) {
        titleElement.textContent = 'Редактировать работу';
    }
    
    // Пересчитываем общую стоимость
    if (typeof updateWorkTotal === 'function') {
        updateWorkTotal();
    }
    
    console.log('✅ Форма работы заполнена');
}

function fillMaterialForm(data) {
    console.log('📦 Заполнение формы материала данными:', data);
    
    const form = document.getElementById('materialForm');
    if (!form) {
        console.error('❌ Форма materialForm не найдена');
        return;
    }
    
    // Заполняем скрытое поле ID
    const materialIdField = form.querySelector('[name="material_id"]');
    if (materialIdField) {
        materialIdField.value = data.id || '';
    }
    
    // Заполняем основные поля с учетом правильного имени поля цены
    const fieldMappings = {
        'name': data.name || '',
        'description': data.description || '',
        'quantity': data.quantity || '',
        'unit': data.unit || 'шт',
        'unit_price': data.unit_price || data.price || '' // unit_price для материалов
    };
    
    // Заполняем поля по имени
    Object.keys(fieldMappings).forEach(fieldName => {
        const input = form.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = fieldMappings[fieldName];
            console.log(`✅ Заполнено поле ${fieldName}:`, fieldMappings[fieldName]);
        } else {
            console.warn(`⚠️ Поле ${fieldName} не найдено в форме`);
        }
    });
    
    // Обновляем заголовок модального окна
    const titleElement = document.querySelector('#materialModalTitle');
    if (titleElement) {
        titleElement.textContent = 'Редактировать материал';
    }
    
    // Пересчитываем общую стоимость
    if (typeof updateMaterialTotal === 'function') {
        updateMaterialTotal();
    }
    
    console.log('✅ Форма материала заполнена');
}

function fillTransportForm(data) {
    console.log('🚛 Заполнение формы транспорта данными:', data);
    
    const form = document.getElementById('transportForm');
    if (!form) {
        console.error('❌ Форма transportForm не найдена');
        return;
    }
    
    // Заполняем скрытое поле ID
    const transportIdField = form.querySelector('[name="transport_id"]');
    if (transportIdField) {
        transportIdField.value = data.id || '';
    }
    
    // Заполняем основные поля
    const fieldMappings = {
        'name': data.name || '',
        'description': data.description || '',
        'quantity': data.quantity || '',
        'unit': data.unit || 'шт',
        'price': data.price || ''
    };
    
    // Заполняем поля по имени
    Object.keys(fieldMappings).forEach(fieldName => {
        const input = form.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = fieldMappings[fieldName];
            console.log(`✅ Заполнено поле ${fieldName}:`, fieldMappings[fieldName]);
        } else {
            console.warn(`⚠️ Поле ${fieldName} не найдено в форме`);
        }
    });
    
    // Обновляем заголовок модального окна
    const titleElement = document.querySelector('#transportModalTitle');
    if (titleElement) {
        titleElement.textContent = 'Редактировать транспорт';
    }
    
    // Пересчитываем общую стоимость
    if (typeof updateTransportTotal === 'function') {
        updateTransportTotal();
    }
    
    console.log('✅ Форма транспорта заполнена');
}

/**
 * AJAX удаление записи
 */
function deleteFinanceRecord(type, id, confirmMessage = null) {
    console.log(`🗑️ Удаление ${type} #${id}...`);
    
    const typeLabel = getTypeLabel(type);
    const message = confirmMessage || `Вы уверены, что хотите удалить ${typeLabel}?`;
    
    if (!confirm(message)) {
        return;
    }
    
    const url = getDeleteUrl(type, id);
    
    // Показываем индикатор загрузки
    showPageLoader(true);
    
    $.ajax({
        url: url,
        method: 'DELETE',
        beforeSend: function() {
            console.log('📤 Отправка запроса на удаление...', { type, id, url });
        },
        success: function(response) {
            console.log(`✅ ${typeLabel} успешно удален:`, response);
            
            showNotification(
                response.message || `${typeLabel} успешно удален`, 
                'success'
            );
            
            // Обновляем финансовые данные
            refreshFinanceData();
        },
        error: function(xhr) {
            console.error(`❌ Ошибка удаления ${type}:`, xhr);
            
            let errorMessage = `Ошибка удаления ${typeLabel}`;
            if (xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = `${typeLabel} не найден`;
            } else if (xhr.status === 403) {
                errorMessage = `Недостаточно прав для удаления ${typeLabel}`;
            }
            
            showNotification(errorMessage, 'error');
        },
        complete: function() {
            showPageLoader(false);
        }
    });
}

/**
 * Обновление финансовых данных
 */
function refreshFinanceData() {
    console.log('🔄 Обновление финансовых данных...');
    
    if (!window.projectId) {
        console.error('❌ ProjectId не определен');
        return;
    }
    
    // Простая перезагрузка страницы для обновления данных
    // Это избегает сложных AJAX запросов для обновления
    console.log('🔄 Перезагрузка страницы для обновления данных...');
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

/**
 * Показ уведомлений
 */
function showNotification(message, type = 'info', duration = null) {
    console.log(`📢 Уведомление [${type}]:`, message);
    
    // Удаляем предыдущие уведомления того же типа
    $(`.toast.${getToastClass(type)}`).remove();
    
    const toastClass = getToastClass(type);
    const toastIcon = getToastIcon(type);
    const toastTitle = getToastTitle(type);
    const autohide = duration !== null ? duration > 0 : type !== 'error';
    const delay = duration || (type === 'error' ? 7000 : type === 'warning' ? 5000 : 3000);
    
    const toastHtml = `
        <div class="toast ${toastClass} text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi ${toastIcon} me-2"></i>
                <strong class="me-auto">${toastTitle}</strong>
                <small class="text-muted">сейчас</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    // Добавляем контейнер для toast если его нет
    if (!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
    }
    
    const $toast = $(toastHtml);
    $('.toast-container').append($toast);
    
    const toast = new bootstrap.Toast($toast[0], {
        autohide: autohide,
        delay: delay
    });
    
    toast.show();
    
    // Автоматическое удаление после показа
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

/**
 * Показ ошибок валидации в форме
 */
function showFormErrors(form, errors) {
    console.log('❌ Показ ошибок валидации:', errors);
    
    // Очищаем предыдущие ошибки
    clearFormErrors(form);
    
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            
            // Добавляем анимацию встряхивания
            field.classList.add('shake');
            setTimeout(() => field.classList.remove('shake'), 500);
            
            // Ищем или создаем элемент для ошибки
            let errorElement = field.parentNode.querySelector('.invalid-feedback');
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'invalid-feedback';
                field.parentNode.appendChild(errorElement);
            }
            
            // Показываем первую ошибку с иконкой
            errorElement.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>${errors[fieldName][0]}`;
            
            // Фокусируемся на первом поле с ошибкой
            if (Object.keys(errors)[0] === fieldName) {
                field.focus();
            }
        }
    });
    
    // Прокручиваем к первому полю с ошибкой
    const firstError = form.querySelector('.is-invalid');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

/**
 * Очистка ошибок валидации в форме
 */
function clearFormErrors(form) {
    const invalidFields = form.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => {
        field.classList.remove('is-invalid', 'shake');
    });
    
    const errorElements = form.querySelectorAll('.invalid-feedback');
    errorElements.forEach(element => {
        element.remove();
    });
}

/**
 * Сброс формы с очисткой всех данных
 */
function resetForm(form) {
    form.reset();
    clearFormErrors(form);
    
    // Очищаем скрытые поля ID
    const idFields = form.querySelectorAll('input[name$="_id"]');
    idFields.forEach(field => field.value = '');
    
    // Сбрасываем method field
    const methodField = form.querySelector('input[name="_method"]');
    if (methodField) {
        methodField.remove();
    }
    
    // Пересчитываем итоговые суммы
    updateFormTotals(form);
}

/**
 * Обновление итоговых сумм в формах
 */
function updateFormTotals(form) {
    const formId = form.id;
    
    if (formId === 'workForm') {
        updateWorkTotal();
    } else if (formId === 'materialForm') {
        updateMaterialTotal();
    } else if (formId === 'transportForm') {
        updateTransportTotal();
    }
}

/**
 * Показ/скрытие индикатора загрузки страницы
 */
function showPageLoader(show) {
    if (show) {
        if (!$('#page-loader-overlay').length) {
            $('body').append(`
                <div id="page-loader-overlay" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255,255,255,0.8);
                    z-index: 9998;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                </div>
            `);
        }
        $('#page-loader-overlay').fadeIn(200);
    } else {
        $('#page-loader-overlay').fadeOut(200, function() {
            $(this).remove();
        });
    }
}

/**
 * Показ/скрытие индикатора загрузки контента
 */
function showContentLoader(show) {
    const containers = [
        '#worksContainer', 
        '#materialsContainer', 
        '#transportContainer',
        '.finance-summary'
    ];
    
    if (show) {
        containers.forEach(selector => {
            const $container = $(selector);
            if ($container.length && !$container.find('.content-loader').length) {
                $container.append(`
                    <div class="content-loader text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Обновление...</span>
                        </div>
                        <div class="mt-2 text-muted small">Обновление данных...</div>
                    </div>
                `);
            }
        });
    } else {
        $('.content-loader').remove();
    }
}

/**
 * Обновление счетчиков в навигационных вкладках
 */
function updateTabCounters(counts) {
    if (counts.works !== undefined) {
        $('[data-counter="works"]').text(counts.works);
    }
    if (counts.materials !== undefined) {
        $('[data-counter="materials"]').text(counts.materials);
    }
    if (counts.transports !== undefined) {
        $('[data-counter="transport"]').text(counts.transports);
    }
}

/**
 * Инициализация tooltips для новых элементов
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        if (!bootstrap.Tooltip.getInstance(tooltipTriggerEl)) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        }
    });
}

/**
 * Инициализация обработчиков для новых элементов
 */
function initNewElementHandlers() {
    // Переинициализируем обработчики кнопок удаления
    $('[data-delete-type][data-delete-id]').off('click.delete').on('click.delete', function(e) {
        e.preventDefault();
        const type = $(this).data('delete-type');
        const id = $(this).data('delete-id');
        deleteFinanceRecord(type, id);
    });
    
    // Переинициализируем обработчики кнопок редактирования
    $('[data-edit-type][data-edit-id]').off('click.edit').on('click.edit', function(e) {
        e.preventDefault();
        const type = $(this).data('edit-type');
        const id = $(this).data('edit-id');
        const data = $(this).data('edit-data');
        
        if (type === 'work' && typeof editWork === 'function') {
            editWork(id, data);
        } else if (type === 'material' && typeof editMaterial === 'function') {
            editMaterial(id, data);
        } else if (type === 'transport' && typeof editTransport === 'function') {
            editTransport(id, data);
        }
    });
}

/**
 * Вспомогательные функции для уведомлений
 */
function getToastClass(type) {
    switch(type) {
        case 'success': return 'bg-success';
        case 'error': return 'bg-danger';
        case 'warning': return 'bg-warning';
        default: return 'bg-info';
    }
}

function getToastIcon(type) {
    switch(type) {
        case 'success': return 'bi-check-circle-fill';
        case 'error': return 'bi-exclamation-triangle-fill';
        case 'warning': return 'bi-exclamation-circle-fill';
        default: return 'bi-info-circle-fill';
    }
}

function getToastTitle(type) {
    switch(type) {
        case 'success': return 'Успешно';
        case 'error': return 'Ошибка';
        case 'warning': return 'Предупреждение';
        default: return 'Информация';
    }
}

/**
 * Остальные вспомогательные функции
 */
function getTypeLabel(type) {
    switch(type) {
        case 'work': return 'работу';
        case 'material': return 'материал';
        case 'transport': return 'транспорт';
        case 'finance': return 'финансовую запись';
        default: return 'запись';
    }
}

function getDeleteUrl(type, id) {
    const baseUrl = `/partner/projects/${window.projectId}`;
    switch(type) {
        case 'work': return `${baseUrl}/works/${id}`;
        case 'material': return `${baseUrl}/materials/${id}`;
        case 'transport': return `${baseUrl}/transports/${id}`;
        case 'finance': return `${baseUrl}/finances/${id}`;
        default: return '#';
    }
}

/**
 * Функции для расчета итоговых сумм
 */
function updateWorkTotal() {
    calculateTotal('workPrice', 'workQuantity', 'workTotalCost');
}

function updateMaterialTotal() {
    calculateTotal('materialPrice', 'materialQuantity', 'materialTotalCost');
}

function updateTransportTotal() {
    calculateTotal('transportPrice', 'transportQuantity', 'transportTotalCost');
}

function calculateTotal(priceId, quantityId, totalId) {
    const priceInput = document.getElementById(priceId);
    const quantityInput = document.getElementById(quantityId);
    const totalElement = document.getElementById(totalId);
    
    if (priceInput && quantityInput && totalElement) {
        const price = parseFloat(priceInput.value) || 0;
        const quantity = parseFloat(quantityInput.value) || 0;
        const total = price * quantity;
        
        totalElement.textContent = total.toLocaleString('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ₽';
        
        // Обновляем скрытое поле amount если существует
        const amountField = priceInput.form?.querySelector(`input[name="amount"]`);
        if (amountField) {
            amountField.value = total.toFixed(2);
        }
    }
}

/**
 * Глобальные функции для обратной совместимости
 */
window.deleteWork = function(id) {
    deleteFinanceRecord('work', id);
};

window.deleteMaterial = function(id) {
    deleteFinanceRecord('material', id);
};

window.deleteTransport = function(id) {
    deleteFinanceRecord('transport', id);
};

window.deleteFinance = function(id) {
    deleteFinanceRecord('finance', id);
};

// Экспорт основных функций
window.initFinanceAjax = initFinanceAjax;
window.refreshFinanceData = refreshFinanceData;
window.showNotification = showNotification;
window.deleteFinanceRecord = deleteFinanceRecord;
window.clearFormErrors = clearFormErrors;
window.showFormErrors = showFormErrors;
window.resetForm = resetForm;
window.updateWorkTotal = updateWorkTotal;
window.updateMaterialTotal = updateMaterialTotal;
window.updateTransportTotal = updateTransportTotal;
window.calculateTotal = calculateTotal;
window.showPageLoader = showPageLoader;
window.editWork = editWork;
window.editMaterial = editMaterial;
window.editTransport = editTransport;
window.fillWorkForm = fillWorkForm;
window.fillMaterialForm = fillMaterialForm;
window.fillTransportForm = fillTransportForm;

// CSS для анимаций
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .shake {
        animation: shake 0.5s;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .content-loader {
        position: relative;
        background: rgba(255,255,255,0.9);
        border-radius: 0.375rem;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .fade-out {
        animation: fadeOut 0.3s ease-out;
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(style);

// Автоматическая инициализация при загрузке
$(document).ready(function() {
    // Ждем немного для полной загрузки страницы
    setTimeout(function() {
        if (!window.financeAjaxInitialized) {
            console.log('🔄 Автоматическая инициализация AJAX...');
            initFinanceAjax();
        } else {
            console.log('ℹ️ AJAX уже инициализирован, пропускаем автоматическую инициализацию');
        }
    }, 1000);
});

console.log('✅ finance-ajax.js v2.3 полностью загружен');
