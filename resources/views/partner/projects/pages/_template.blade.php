@extends('partner.projects.layouts.project-base')

@section('styles')
    @parent
    <link href="{{ asset('css/' . $pageConfig['cssFile']) }}" rel="stylesheet">
@endsection

@section('page-content')
    @include('partner.projects.tabs.' . $pageConfig['tabFile'])
    
    @if(isset($pageConfig['modalFile']))
        @include('partner.projects.tabs.modals.' . $pageConfig['modalFile'])
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            console.log('=== {{ $pageConfig['pageName'] }} ===');
            
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
            console.log('{{ $pageConfig['initIcon'] }} Инициализация {{ $pageConfig['pageNameLower'] }}...');
            
            // Инициализируем базовые обработчики
            initBaseHandlers();
            
            // Инициализируем фильтры
            initFilterHandlers();
            
            // Инициализируем специфичные обработчики
            {{ $pageConfig['initFunction'] }}();
            
            console.log('✅ {{ $pageConfig['pageNameFormatted'] }} инициализирована');
        }
        
        function initBaseHandlers() {
            console.log('🎯 Инициализация базовых обработчиков...');
            
            // Обработчик переключения фильтров
            $('#toggleFilters').off('click').on('click', function() {
                const content = $('#filtersContent');
                const icon = $(this).find('.bi');
                content.slideToggle(300);
                icon.toggleClass('bi-chevron-down bi-chevron-up');
            });
            
            // Обработчик модального окна
            $('[data-modal-type="{{ $pageConfig['modalType'] }}"]').off('click').on('click', function() {
                $('#{{ $pageConfig['modalId'] }}').modal('show');
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
        
        function {{ $pageConfig['initFunction'] }}() {
            console.log('{{ $pageConfig['handlerIcon'] }} Инициализация обработчиков {{ $pageConfig['pageNameLower'] }}...');
            
            // Обработчик предварительного просмотра файлов
            $('#{{ $pageConfig['fileInputId'] }}').on('change', function() {
                previewSelectedFiles();
            });
        }
        
        // Функция предварительного просмотра файлов
        function previewSelectedFiles() {
            const files = document.getElementById('{{ $pageConfig['fileInputId'] }}').files;
            const preview = document.getElementById('filePreview');
            const previewList = document.getElementById('previewList');
            
            if (files.length > 0) {
                previewList.innerHTML = '';
                
                Array.from(files).forEach((file, index) => {
                    const listItem = document.createElement('div');
                    listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    listItem.innerHTML = `
                        <div>
                            <i class="bi {{ $pageConfig['fileIcon'] }} me-2"></i>
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
            $('#{{ $pageConfig['tabContentId'] }}').prepend(alertHtml);
            
            // Автоматически скрываем через 5 секунд
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
        
        // Функция подтверждения удаления
        function confirmDelete({{ $pageConfig['itemIdParam'] }}, filename) {
            if (confirm(`Вы уверены, что хотите удалить {{ $pageConfig['itemNameAccusative'] }} "${filename}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ $pageConfig['deleteRoute'] }}`.replace('__ID__', {{ $pageConfig['itemIdParam'] }});
                
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
        
        @if(isset($pageConfig['viewRoute']))
        // Функция открытия просмотра
        function {{ $pageConfig['openFunction'] }}({{ $pageConfig['itemIdParam'] }}) {
            window.open(`{{ $pageConfig['viewRoute'] }}`.replace('__ID__', {{ $pageConfig['itemIdParam'] }}), '_blank');
        }
        @endif
        
        console.log('✅ Скрипты {{ $pageConfig['pageNameLower'] }} загружены');
    </script>
@endsection
