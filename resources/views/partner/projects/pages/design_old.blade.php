@php
$pageConfig = [
    'pageName' => 'СТРАНИЦА ДИЗАЙНА',
    'pageNameLower' => 'страницы дизайна',
    'pageNameFormatted' => 'Страница дизайна',
    'initIcon' => '🎨',
    'handlerIcon' => '🎨',
    'cssFile' => 'design-standard.css',
    'tabFile' => 'design',
    'modalType' => 'design',
    'modalId' => 'uploadDesignModal',
    'fileInputId' => 'designFiles',
    'fileIcon' => 'bi-paint-bucket',
    'tabContentId' => 'design-tab-content',
    'initFunction' => 'initDesignHandlers',
    'itemIdParam' => 'designId',
    'itemNameAccusative' => 'файл дизайна',
    'deleteRoute' => route('partner.projects.design.destroy', [$project, '__ID__']),
    'viewRoute' => route('partner.projects.design.view', [$project, '__ID__']),
    'openFunction' => 'openDesignView'
];
@endphp

@include('partner.projects.pages._template', compact('pageConfig'))

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            console.log('=== СТРАНИЦА ДИЗАЙНА ===');
            
            initPage();
            
            // Показываем сообщения если есть
            @if(session('success'))
                showMessage('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showMessage('{{ session('error') }}', 'error');
            @endif
        });
        
        function initPage() {
            console.log('🎨 Инициализация страницы дизайна...');
            
            // Инициализируем базовые обработчики
            initBaseHandlers();
            
            // Инициализируем фильтры
            initFilterHandlers();
            
            // Инициализируем специфичные обработчики для дизайна
            initDesignHandlers();
            
            console.log('✅ Страница дизайна инициализирована');
        }
        
        function initBaseHandlers() {
            console.log('🎯 Инициализация базовых обработчиков...');
            
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
                if ($(this).attr('name') === 'search') {
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => {
                        $(this).closest('form').submit();
                    }, 500);
                } else {
                    $(this).closest('form').submit();
                }
            });
        }
        
        function initFilterHandlers() {
            console.log('🔍 Инициализация обработчиков фильтров...');
            
            // Сброс фильтров
            $('.btn-reset-filters').on('click', function(e) {
                e.preventDefault();
                window.location.href = $(this).data('reset-url');
            });
        }
        
        function initDesignHandlers() {
            console.log('🎨 Инициализация обработчиков дизайна...');
            
            // Обработчик предварительного просмотра файлов
            $('#designFiles').on('change', function() {
                previewSelectedFiles();
            });
        }
        
        // Функция предварительного просмотра файлов
        function previewSelectedFiles() {
            const files = document.getElementById('designFiles').files;
            const preview = document.getElementById('filePreview');
            const previewList = document.getElementById('previewList');
            
            if (files.length > 0) {
                previewList.innerHTML = '';
                
                Array.from(files).forEach((file, index) => {
                    const listItem = document.createElement('div');
                    listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    listItem.innerHTML = `
                        <div>
                            <i class="bi bi-paint-bucket me-2"></i>
                            ${file.name}
                            <small class="text-muted">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">${index + 1}</span>
                    `;
                    previewList.appendChild(listItem);
                });
                
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }
        
        // Функция для показа сообщений
        function showMessage(message, type = 'info') {
            const alertClass = type === 'success' ? 'alert-success' : 
                             type === 'error' ? 'alert-danger' : 'alert-info';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Добавляем сообщение в начало контейнера
            $('#design-tab-content').prepend(alertHtml);
            
            // Автоматически скрываем через 5 секунд
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
        
        // Функция подтверждения удаления
        function confirmDelete(designId, filename) {
            if (confirm(`Вы уверены, что хотите удалить файл дизайна "${filename}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('partner.projects.design.destroy', [$project, '__ID__']) }}`.replace('__ID__', designId);
                
                // Добавляем CSRF токен
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                
                // Добавляем метод DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Функция открытия просмотра файла
        function openDesignView(designId) {
            window.open(`{{ route('partner.projects.design.view', [$project, '__ID__']) }}`.replace('__ID__', designId), '_blank');
        }
        
        console.log('✅ Скрипты страницы дизайна загружены');
    </script>
@endsection
         