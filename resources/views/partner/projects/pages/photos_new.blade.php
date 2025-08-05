@extends('partner.projects.layouts.project-base')

@section('page-content')
    @include('partner.projects.tabs.photos')
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            console.log('=== СТРАНИЦА ФОТОГРАФИЙ (БЕЗ AJAX) ===');
            
            // Простая инициализация без AJAX
            initPhotosPage();
        });
        
        function initPhotosPage() {
            console.log('📸 Инициализация страницы фотографий без AJAX...');
            
            // Инициализируем простые обработчики
            initSimpleHandlers();
            
            // Инициализируем фильтры
            initFilterHandlers();
            
            console.log('✅ Страница фотографий инициализирована (без AJAX)');
        }
        
        function initSimpleHandlers() {
            console.log('🎯 Инициализация простых обработчиков...');
            
            // Обработчик переключения фильтров
            $('#toggleFilters').off('click').on('click', function() {
                const content = $('#filtersContent');
                const icon = $('#toggleFiltersIcon');
                
                if (content.is(':visible')) {
                    content.slideUp();
                    icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                } else {
                    content.slideDown();
                    icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                }
            });
            
            // Автоотправка формы при изменении фильтров
            $('.form-select[name], .form-control[name]').on('change', function() {
                // Небольшая задержка для поиска
                if ($(this).attr('name') === 'search') {
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 500);
                } else {
                    $(this).closest('form').submit();
                }
            });
            
            // Обработчик очистки поиска
            $('button[onclick*="value="]').on('click', function() {
                const input = this.parentElement.querySelector('input');
                input.value = '';
                $(input).closest('form').submit();
            });
        }
        
        function initFilterHandlers() {
            console.log('🔍 Инициализация обработчиков фильтров...');
            
            // Синхронизация мобильных и десктопных фильтров
            $('#photoTypeFilter, #photoTypeFilterMobile').on('change', function() {
                const value = $(this).val();
                $('#photoTypeFilter, #photoTypeFilterMobile').not(this).val(value);
            });
            
            $('#photoLocationFilter, #photoLocationFilterMobile').on('change', function() {
                const value = $(this).val();
                $('#photoLocationFilter, #photoLocationFilterMobile').not(this).val(value);
            });
            
            $('#photoSortFilter, #photoSortFilterMobile').on('change', function() {
                const value = $(this).val();
                $('#photoSortFilter, #photoSortFilterMobile').not(this).val(value);
            });
            
            $('#photoSearchFilter, #photoSearchFilterMobile').on('input', function() {
                const value = $(this).val();
                $('#photoSearchFilter, #photoSearchFilterMobile').not(this).val(value);
            });
            
            console.log('✅ Обработчики фильтров инициализированы');
        }
        
        // Обработчик предварительного просмотра файлов
        $('#photoFiles').on('change', function() {
            const files = this.files;
            if (files.length > 0) {
                console.log(`📂 Выбрано файлов: ${files.length}`);
                
                // Простая валидация размера файлов
                let hasLargeFiles = false;
                Array.from(files).forEach(file => {
                    if (file.size > 10 * 1024 * 1024) { // 10MB
                        hasLargeFiles = true;
                    }
                });
                
                if (hasLargeFiles) {
                    alert('Внимание: некоторые файлы превышают 10 МБ и могут быть отклонены сервером.');
                }
            }
        });
        
        // Функция для показа сообщений
        function showMessage(message, type = 'info') {
            console.log(`📢 Сообщение (${type}):`, message);
            
            // Простой alert как fallback
            alert(message);
        }
        
        console.log('✅ Скрипты страницы фотографий загружены (без AJAX)');
    </script>
@endsection
