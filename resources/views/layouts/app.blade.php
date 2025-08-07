<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Preconnect для оптимизации загрузки шрифтов -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Основной шрифт Inter для единого дизайна -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- ЕДИНАЯ ДИЗАЙН-СИСТЕМА - основные стили -->
   
    <!-- Дополнительные стили для специфических компонентов -->
    @stack('styles')
    @yield('styles')
    
    <!-- Vite Assets для JS -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- jQuery Mask Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"></script>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Select2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js"></script>
    
    <!-- Маски ввода -->
    <script src="{{ asset('js/input-masks.js') }}"></script>
    
    <!-- Проверка масок (только для отладки) -->
    <script src="{{ asset('js/mask-validation-check.js') }}"></script>
    
    <!-- Клиентская валидация -->
    <script src="{{ asset('js/client-validation.js') }}"></script>
    
   
    @yield('head')
</head>
<body>
    <!-- Loader overlay -->
    <div id="page-loader-overlay">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
    </div>
    <div id="app">

       

        <!-- Контейнер для flexbox размещения боковой панели и основного содержимого -->
        <div class="app-layout">
            <!-- Подключение боковой панели -->
            @auth
                @include('layouts.sidebar')
            @endauth

            <!-- Основной контейнер контента -->
            <div class="content-wrapper">
                <main class="content-container">
                    <!-- Предупреждения о лимитах подписки -->
                    @include('components.subscription-alerts')
                    
                    @yield('content')
                </main>
            </div>
        </div>
     
     
        <!-- Глобальный AJAX обработчик и утилиты -->
        <script>
            // Глобальные настройки jQuery для AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                timeout: 30000, // Глобальный таймаут 30 секунд
                beforeSend: function(xhr, settings) {
                    console.log('AJAX beforeSend for', settings.url);
                },
                complete: function(xhr, status) {
                    console.log('AJAX complete: status:', status, 'for url:', this.url);
                },
                success: function(data, textStatus, xhr) {
                    console.log('AJAX success for:', this.url, 'Status:', textStatus);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error: status:', status, 'error:', error, 'for url:', this.url);
                    handleGlobalAjaxError(xhr, status, error);
                }
            });

            // Глобальные функции для работы с AJAX
            window.AjaxHelper = {
                // Обработка форм с AJAX
                submitForm: function(form, options = {}) {
                    const $form = $(form);
                    const url = options.url || $form.attr('action');
                    const method = options.method || $form.attr('method') || 'POST';
                    const formData = new FormData($form[0]);
                    
                    // Добавляем CSRF токен если его нет
                    if (!formData.has('_token')) {
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    }

                    return $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (options.onSuccess) {
                                options.onSuccess(response);
                            } else {
                                handleFormSuccess(response, $form);
                            }
                        },
                        error: function(xhr) {
                            if (options.onError) {
                                options.onError(xhr);
                            } else {
                                handleFormError(xhr, $form);
                            }
                        }
                    });
                },

                // Простой AJAX запрос
                request: function(url, options = {}) {
                    return $.ajax($.extend({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        timeout: 30000 // Таймаут 30 секунд
                    }, options));
                },

                // Удаление элемента
                delete: function(url, options = {}) {
                    return this.request(url, $.extend({
                        method: 'DELETE',
                        success: function(response) {
                            showMessage(response.message || 'Элемент успешно удален', 'success');
                            if (options.onSuccess) options.onSuccess(response);
                        }
                    }, options));
                }
            };
            
            // Глобальные утилиты для работы с модальными окнами
            window.ModalUtils = {
                // Открывает модальное окно с правильным backdrop
                open: function(modalSelector) {
                    const modal = document.querySelector(modalSelector);
                    if (modal) {
                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();
                        console.log('🔓 Модальное окно открыто:', modalSelector);
                        return bsModal;
                    } else {
                        console.error('❌ Модальное окно не найдено:', modalSelector);
                        return null;
                    }
                },
                
                // Закрывает модальное окно с очисткой backdrop
                close: function(modalSelector) {
                    const modal = document.querySelector(modalSelector);
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        } else {
                            modal.classList.remove('show');
                            modal.style.display = 'none';
                        }
                        
                        // Принудительная очистка backdrop
                        setTimeout(() => {
                            window.ModalBackdropManager.removeBackdrop(modal);
                            window.ModalBackdropManager.clearOrphanedBackdrops();
                        }, 350);
                        
                        console.log('🔒 Модальное окно закрыто:', modalSelector);
                    } else {
                        console.error('❌ Модальное окно не найдено:', modalSelector);
                    }
                },
                
                // Принудительно закрывает все модальные окна
                closeAll: function() {
                    const modals = document.querySelectorAll('.modal.show');
                    modals.forEach(modal => {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        } else {
                            modal.classList.remove('show');
                            modal.style.display = 'none';
                        }
                    });
                    
                    // Очищаем все backdrop'ы
                    setTimeout(() => {
                        window.ModalBackdropManager.clearAll();
                    }, 350);
                    
                    console.log('🔒 Все модальные окна закрыты');
                },
                
                // Проверяет состояние модальных окон и backdrop'ов
                diagnose: function() {
                    const modals = document.querySelectorAll('.modal');
                    const visibleModals = document.querySelectorAll('.modal.show');
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    
                    console.log('🔍 Диагностика модальных окон:');
                    console.log('  Всего модальных окон:', modals.length);
                    console.log('  Видимых модальных окон:', visibleModals.length);
                    console.log('  Backdrop\'ов:', backdrops.length);
                    console.log('  Body имеет класс modal-open:', document.body.classList.contains('modal-open'));
                    
                    return {
                        total: modals.length,
                        visible: visibleModals.length,
                        backdrops: backdrops.length,
                        bodyHasModalOpen: document.body.classList.contains('modal-open')
                    };
                }
            };

            // Обработка успешного ответа формы
            function handleFormSuccess(response, $form) {
                // Показываем сообщение
                if (response.message) {
                    showMessage(response.message, 'success');
                }

                // Закрываем модальное окно если форма внутри модального окна
                const $modal = $form.closest('.modal');
                if ($modal.length) {
                    const modal = $modal[0];
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    
                    if (bsModal) {
                        bsModal.hide();
                    } else {
                        $modal.modal('hide');
                    }
                    
                    // Используем новую систему управления backdrop
                    setTimeout(() => {
                        window.ModalBackdropManager.removeBackdrop(modal);
                        window.ModalBackdropManager.clearOrphanedBackdrops();
                    }, 350);
                }

                // Очищаем форму
                if (response.reset_form !== false) {
                    $form[0].reset();
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $form.find('.invalid-feedback').remove();
                }

                // Обновляем данные на странице
                if (typeof refreshCurrentTabData === 'function') {
                    refreshCurrentTabData();
                }
            }

            // Обработка ошибок формы
            function handleFormError(xhr, $form) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    
                    // Очищаем предыдущие ошибки
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $form.find('.invalid-feedback').remove();
                    
                    // Показываем новые ошибки
                    $.each(errors, function(field, messages) {
                        const $field = $form.find(`[name="${field}"]`);
                        $field.addClass('is-invalid');
                        $field.after(`<div class="invalid-feedback">${messages[0]}</div>`);
                    });
                } else {
                    // Другие ошибки
                    const message = xhr.responseJSON?.message || 'Произошла ошибка при отправке формы';
                    showMessage(message, 'error');
                }
            }

            // Глобальная обработка AJAX ошибок
            function handleGlobalAjaxError(xhr, status, error) {
                if (xhr.status === 403) {
                    showMessage('У вас нет прав для выполнения этого действия', 'error');
                } else if (xhr.status === 404) {
                    showMessage('Запрашиваемый ресурс не найден', 'error');
                } else if (xhr.status === 500) {
                    showMessage('Внутренняя ошибка сервера', 'error');
                } else if (status === 'timeout') {
                    showMessage('Превышено время ожидания ответа сервера', 'error');
                } else if (xhr.status !== 422) { // 422 обрабатывается в handleFormError
                    const message = xhr.responseJSON?.message || 'Произошла непредвиденная ошибка';
                    showMessage(message, 'error');
                }
            }

            // Универсальная функция показа сообщений
            function showMessage(message, type = 'info') {
                const alertClass = {
                    'success': 'alert-success',
                    'error': 'alert-danger',
                    'warning': 'alert-warning',
                    'info': 'alert-info'
                }[type] || 'alert-info';

                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9998; min-width: 300px;" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                $('body').append(alertHtml);

                // Автоматически скрываем через 5 секунд
                setTimeout(() => {
                    $('.alert:last').alert('close');
                }, 5000);
            }
                setTimeout(() => {
                    $('.alert:last').alert('close');
                }, 5000);
            }
            
            // Глобальные функции для работы с Select2
            window.Select2Utils = {
                // Переинициализация всех Select2 на странице
                reinitializeAll: function() {
                    console.log('🔄 Принудительная переинициализация всех Select2');
                    if (window.select2TabsManager) {
                        window.select2TabsManager.forceReinitializeAll();
                    }
                },
                
                // Инициализация Select2 в определенном контейнере
                initializeInContainer: function(container) {
                    console.log('🎯 Инициализация Select2 в контейнере:', container);
                    if (window.select2TabsManager) {
                        window.select2TabsManager.reinitializeInContainer(container);
                    }
                },
                
                // Диагностика Select2
                diagnose: function() {
                    if (typeof Select2Diagnostic !== 'undefined') {
                        Select2Diagnostic.printReport();
                    } else {
                        console.log('❌ Select2Diagnostic не доступен');
                    }
                },
                
                // Принудительная инициализация всех необработанных select
                forceInitializeUnprocessed: function() {
                    if (window.select2TabsManager) {
                        window.select2TabsManager.checkAndInitializeUnprocessedSelects();
                    }
                }
            };

            // Дополнительный скрипт для мобильного меню
                    $('.alert:not(.permanent)').alert('close');
                }, 5000);
            }

            // Подтверждение удаления
            function confirmDelete(message = 'Вы уверены, что хотите удалить этот элемент?') {
                return confirm(message);
            }

            // Дополнительный скрипт для мобильного меню
            document.addEventListener('DOMContentLoaded', function() {
                // Инициализация Select2 Manager
                console.log('🚀 Инициализация Select2 в глобальном шаблоне');
                
                // Ждем полной загрузки всех ресурсов
                setTimeout(function() {
                    // Инициализируем Select2TabsManager если он доступен
                    if (typeof Select2TabsManager !== 'undefined' && !window.select2TabsManager) {
                        console.log('📋 Создание глобального экземпляра Select2TabsManager');
                        window.select2TabsManager = new Select2TabsManager();
                        window.select2TabsManager.init();
                    }
                    
                    // Принудительная инициализация всех select элементов
                    if (window.select2TabsManager) {
                        window.select2TabsManager.initializeAllSelects();
                    }
                    
                    // Диагностика если доступна
                    if (typeof Select2Diagnostic !== 'undefined') {
                        console.log('🔍 Запуск диагностики Select2');
                        Select2Diagnostic.printReport();
                    }
                }, 500);

                const mobileMenuToggle = document.getElementById('mobileMenuToggle');
                const body = document.body;
                
                if (mobileMenuToggle) {
                    mobileMenuToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Переключаем состояние мобильного меню
                        body.classList.toggle('sidebar-mobile-open');
                        
                        // Обновляем иконку
                        const icon = this.querySelector('i');
                        if (body.classList.contains('sidebar-mobile-open')) {
                            icon.className = 'bi bi-x-lg';
                        } else {
                            icon.className = 'bi bi-list';
                        }
                    });
                }
                
               

                // Глобальная обработка форм с классом ajax-form
                $(document).on('submit', '.ajax-form', function(e) {
                    e.preventDefault();
                    AjaxHelper.submitForm(this);
                });

                // Глобальная обработка кнопок удаления
                $(document).on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    const $btn = $(this);
                    const url = $btn.data('url') || $btn.attr('href');
                    const message = $btn.data('confirm') || 'Вы уверены, что хотите удалить этот элемент?';
                    
                    if (confirmDelete(message)) {
                        AjaxHelper.delete(url, {
                            onSuccess: function(response) {
                                // Удаляем элемент из DOM если указан селектор
                                const removeSelector = $btn.data('remove');
                                if (removeSelector) {
                                    $(removeSelector).remove();
                                }
                                
                                // Обновляем счетчики
                                if (typeof updateTabBadges === 'function') {
                                    updateTabBadges();
                                }
                            }
                        });
                    }
                });
                
                console.log('✅ Глобальная инициализация завершена успешно');
            });

            // Глобальные обработчики для переинициализации Select2 при изменении контента
            // Обработчик для табов Bootstrap
            $(document).on('shown.bs.tab', function (e) {
                console.log('🔄 Таб переключен, переинициализация Select2...');
                setTimeout(function() {
                    if (window.select2TabsManager) {
                        window.select2TabsManager.initializeAllSelects();
                    }
                }, 100);
            });
            
            // Обработчик для модальных окон
            $(document).on('shown.bs.modal', function (e) {
                console.log('📋 Модальное окно открыто, инициализация Select2...');
                const modal = e.target;
                setTimeout(function() {
                    if (window.select2TabsManager) {
                        window.select2TabsManager.reinitializeInContainer(modal);
                    }
                }, 100);
            });
            
            // Обработчик для успешных AJAX запросов
            $(document).ajaxSuccess(function(event, xhr, settings) {
                // Переинициализируем Select2 после успешных AJAX запросов
                setTimeout(function() {
                    if (window.select2TabsManager) {
                        window.select2TabsManager.checkAndInitializeUnprocessedSelects();
                    }
                }, 200);
            });

            
            // Улучшенная система управления modal-backdrop
            window.ModalBackdropManager = {
                // Создает backdrop точно после модального окна
                createBackdrop: function(modal) {
                    // Проверяем, есть ли уже backdrop для этого модального окна
                    const existingBackdrop = modal.nextElementSibling;
                    if (existingBackdrop && existingBackdrop.classList.contains('modal-backdrop')) {
                        return existingBackdrop;
                    }
                    
                    // Создаем новый backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.setAttribute('data-modal-id', modal.id || 'unknown');
                    
                    // Вставляем backdrop сразу после модального окна
                    modal.parentNode.insertBefore(backdrop, modal.nextSibling);
                    
                    console.log('✅ Backdrop создан для модального окна:', modal.id);
                    return backdrop;
                },
                
                // Удаляет backdrop для конкретного модального окна
                removeBackdrop: function(modal) {
                    const modalId = modal.id || 'unknown';
                    
                    // Ищем backdrop сразу после модального окна
                    let backdrop = modal.nextElementSibling;
                    if (backdrop && backdrop.classList.contains('modal-backdrop')) {
                        backdrop.remove();
                        console.log('🗑️ Backdrop удален для модального окна:', modalId);
                        return true;
                    }
                    
                    // Если не найден рядом, ищем по data-modal-id
                    const backdrops = document.querySelectorAll(`[data-modal-id="${modalId}"]`);
                    backdrops.forEach(b => {
                        b.remove();
                        console.log('🗑️ Backdrop удален по ID для модального окна:', modalId);
                    });
                    
                    return backdrops.length > 0;
                },
                
                // Очищает все "потерянные" backdrop'ы
                clearOrphanedBackdrops: function() {
                    const allBackdrops = document.querySelectorAll('.modal-backdrop');
                    const visibleModals = document.querySelectorAll('.modal.show');
                    
                    if (allBackdrops.length > visibleModals.length) {
                        console.log('🧹 Найдены потерянные backdrop\'ы, очищаем...');
                        
                        allBackdrops.forEach(backdrop => {
                            const modalId = backdrop.getAttribute('data-modal-id');
                            const correspondingModal = modalId ? document.getElementById(modalId) : null;
                            
                            // Удаляем backdrop если соответствующее модальное окно не отображается
                            if (!correspondingModal || !correspondingModal.classList.contains('show')) {
                                backdrop.remove();
                                console.log('🗑️ Удален потерянный backdrop для:', modalId);
                            }
                        });
                        
                        // Очищаем стили body если нет видимых модальных окон
                        if (visibleModals.length === 0) {
                            document.body.classList.remove('modal-open');
                            document.body.style.removeProperty('overflow');
                            document.body.style.removeProperty('padding-right');
                        }
                    }
                },
                
                // Полная очистка всех backdrop'ов
                clearAll: function() {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    console.log('🧹 Очистка всех backdrop\'ов, найдено:', backdrops.length);
                    
                    backdrops.forEach(backdrop => backdrop.remove());
                    
                    // Восстанавливаем стили body
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                    
                    console.log('✅ Все backdrop\'ы очищены');
                }
            };
            
            // Обратная совместимость
            window.clearModalBackdrop = function() {
                window.ModalBackdropManager.clearAll();
            };
            
            // Улучшенная функция для инициализации обработчиков модальных окон
            function initModalHandlers() {
                console.log('🔧 Инициализация обработчиков модальных окон...');
                
                // Находим все модальные окна
                const modals = document.querySelectorAll('.modal');
                console.log('📋 Найдено модальных окон:', modals.length);
                
                modals.forEach(function(modal, index) {
                    const modalId = modal.id || `modal-${index}`;
                    console.log(`🎯 Настройка обработчиков для модального окна: ${modalId}`);
                    
                    // Удаляем старые обработчики чтобы избежать дублирования
                    modal.removeEventListener('show.bs.modal', modal._showHandler);
                    modal.removeEventListener('shown.bs.modal', modal._shownHandler);
                    modal.removeEventListener('hide.bs.modal', modal._hideHandler);
                    modal.removeEventListener('hidden.bs.modal', modal._hiddenHandler);
                    
                    // Обработчик начала показа модального окна
                    modal._showHandler = function(e) {
                        console.log('📤 Модальное окно начинает показываться:', modalId);
                        
                        // Убираем потерянные backdrop'ы перед показом нового
                        window.ModalBackdropManager.clearOrphanedBackdrops();
                    };
                    
                    // Обработчик завершения показа модального окна
                    modal._shownHandler = function(e) {
                        console.log('✅ Модальное окно полностью показано:', modalId);
                        
                        // Создаем backdrop сразу после модального окна
                        setTimeout(() => {
                            window.ModalBackdropManager.createBackdrop(modal);
                        }, 50);
                    };
                    
                    // Обработчик начала скрытия модального окна
                    modal._hideHandler = function(e) {
                        console.log('📥 Модальное окно начинает скрываться:', modalId);
                        
                        // Удаляем backdrop для этого модального окна
                        window.ModalBackdropManager.removeBackdrop(modal);
                    };
                    
                    // Обработчик завершения скрытия модального окна
                    modal._hiddenHandler = function(e) {
                        console.log('❌ Модальное окно полностью скрыто:', modalId);
                        
                        // Дополнительная очистка и проверка
                        setTimeout(() => {
                            window.ModalBackdropManager.removeBackdrop(modal);
                            window.ModalBackdropManager.clearOrphanedBackdrops();
                        }, 100);
                    };
                    
                    // Добавляем обработчики
                    modal.addEventListener('show.bs.modal', modal._showHandler);
                    modal.addEventListener('shown.bs.modal', modal._shownHandler);
                    modal.addEventListener('hide.bs.modal', modal._hideHandler);
                    modal.addEventListener('hidden.bs.modal', modal._hiddenHandler);
                    
                    // Обработчики для кнопок закрытия
                    const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"], .btn-close');
                    closeButtons.forEach(function(button) {
                        // Удаляем старый обработчик если есть
                        button.removeEventListener('click', button._closeHandler);
                        
                        button._closeHandler = function(e) {
                            console.log('🔘 Кнопка закрытия нажата для модального окна:', modalId);
                            
                            // Задержка для корректного срабатывания Bootstrap событий
                            setTimeout(() => {
                                window.ModalBackdropManager.removeBackdrop(modal);
                                window.ModalBackdropManager.clearOrphanedBackdrops();
                            }, 350);
                        };
                        
                        button.addEventListener('click', button._closeHandler);
                    });
                    
                    // Обработчик клика по backdrop для закрытия
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            console.log('🖱️ Клик по backdrop модального окна:', modalId);
                            setTimeout(() => {
                                window.ModalBackdropManager.removeBackdrop(modal);
                                window.ModalBackdropManager.clearOrphanedBackdrops();
                            }, 350);
                        }
                    });
                });
                
                console.log('✅ Инициализация обработчиков модальных окон завершена');
            }
            
            // Инициализируем обработчики при загрузке DOM
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 DOM загружен, инициализируем обработчики модальных окон...');
                initModalHandlers();
            });
            
            // Переинициализируем обработчики при изменении DOM (для динамически добавляемых модальных окон)
            const modalObserver = new MutationObserver(function(mutations) {
                let shouldReinit = false;
                
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && (node.classList.contains('modal') || node.querySelector('.modal'))) {
                                console.log('🆕 Обнаружено новое модальное окно, переинициализируем обработчики');
                                shouldReinit = true;
                            }
                        });
                    }
                });
                
                if (shouldReinit) {
                    setTimeout(initModalHandlers, 100);
                }
            });
            
            modalObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
            
            // Обработчик для очистки backdrop при нажатии Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    console.log('⌨️ Нажата клавиша Escape, очищаем backdrop через 300мс');
                    setTimeout(() => {
                        window.ModalBackdropManager.clearOrphanedBackdrops();
                    }, 300);
                }
            });
            
            // Обработчик для клика по backdrop (если он все же появился не в нужном месте)
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-backdrop')) {
                    console.log('🖱️ Клик по backdrop, очищаем через 300мс');
                    setTimeout(() => {
                        window.ModalBackdropManager.clearAll();
                    }, 300);
                }
            });
            
            // Периодическая проверка и очистка "потерянных" backdrop каждые 3 секунды
            setInterval(function() {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                const visibleModals = document.querySelectorAll('.modal.show');
                
                if (backdrops.length > 0 && visibleModals.length === 0) {
                    console.log('🔍 Периодическая проверка: найдены потерянные backdrop\'ы');
                    window.ModalBackdropManager.clearAll();
                } else if (backdrops.length > visibleModals.length) {
                    console.log('🔍 Периодическая проверка: несоответствие количества backdrop\'ов и модальных окон');
                    window.ModalBackdropManager.clearOrphanedBackdrops();
                }
            }, 3000);
        </script>
        
     
        <!-- Диагностический скрипт для поиска JavaScript ошибок (только для отладки) -->
      
        
        <!-- JavaScript для мобильного меню в navbar -->
        <script>
        // Предотвращаем мерцание при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            const appLayout = document.querySelector('.app-layout');
            if (appLayout) {
                // Добавляем класс loaded для включения анимаций после загрузки
                setTimeout(() => {
                    appLayout.classList.add('loaded');
                }, 50);
            }
            
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const body = document.body;
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Переключаем класс для показа/скрытия sidebar на мобильных
                    body.classList.toggle('sidebar-mobile-open');
                    
                    // Обновляем иконку
                    const icon = this.querySelector('i');
                    if (body.classList.contains('sidebar-mobile-open')) {
                        icon.className = 'bi bi-x-lg';
                    } else {
                        icon.className = 'bi bi-list';
                    }
                });
            }
        });
        </script>
        
        <!-- Секция для дополнительных скриптов -->
        @yield('scripts')
        @stack('scripts')
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const loader = document.getElementById('page-loader-overlay');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 300);
            }
        }, 900);
    });
    </script>
</body>
</html>
