@extends('partner.projects.layouts.project-base')

@section('styles')
    @parent
    <link href="{{ asset('css/documents-standard.css') }}" rel="stylesheet">
@endsection

@section('page-content')
    @include('partner.projects.tabs.documents')
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            console.log('=== СТРАНИЦА ДОКУМЕНТОВ ===');
            
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
            console.log('📄 Инициализация страницы документов...');
            
            // Инициализируем базовые обработчики
            initBaseHandlers();
            
            // Инициализируем фильтры
            initFilterHandlers();
            
            // Инициализируем специфичные обработчики для документов
            initDocumentsHandlers();
            
            console.log('✅ Страница документов инициализирована');
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
        
        function initDocumentsHandlers() {
            console.log('📄 Инициализация обработчиков документов...');
            
            // Обработчик предварительного просмотра файлов
            $('#documentFiles').on('change', function() {
                previewSelectedFiles();
            });
        }
        
        // Функция предварительного просмотра файлов
        function previewSelectedFiles() {
            const files = document.getElementById('documentFiles').files;
            const preview = document.getElementById('filePreview');
            const previewList = document.getElementById('previewList');
            
            if (files.length > 0) {
                previewList.innerHTML = '';
                
                Array.from(files).forEach((file, index) => {
                    const listItem = document.createElement('div');
                    listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    listItem.innerHTML = `
                        <div>
                            <i class="bi bi-file-earmark-text me-2"></i>
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
            $('#documents-tab-content').prepend(alertHtml);
            
            // Автоматически скрываем через 5 секунд
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
        
        // Функция подтверждения удаления
        function confirmDelete(documentId, filename) {
            if (confirm(`Вы уверены, что хотите удалить документ "${filename}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('partner.projects.documents.destroy', [$project, '__ID__']) }}`.replace('__ID__', documentId);
                
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
        
        // Функция открытия документа
        function openDocument(documentId) {
            window.open(`{{ route('partner.projects.documents.view', [$project, '__ID__']) }}`.replace('__ID__', documentId), '_blank');
        }
        
        console.log('✅ Скрипты страницы документов загружены');
    </script>
@endsection
