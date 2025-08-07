@php
$pageConfig = [
    'pageName' => 'СТРАНИЦА ДОКУМЕНТОВ',
    'pageNameLower' => 'страницы документов',
    'pageNameFormatted' => 'Страница документов',
    'initIcon' => '📄',
    'handlerIcon' => '📄',
    'cssFile' => 'documents-standard.css',
    'tabFile' => 'documents',
    'modalType' => 'document',
    'modalId' => 'uploadDocumentModal',
    'fileInputId' => 'documentFiles',
    'fileIcon' => 'bi-file-text',
    'tabContentId' => 'documents-tab-content',
    'initFunction' => 'initDocumentsHandlers',
    'itemIdParam' => 'documentId',
    'itemNameAccusative' => 'документ',
    'deleteRoute' => route('partner.projects.documents.delete', [$project, '__ID__']),
    'viewRoute' => route('partner.projects.documents.download', [$project, '__ID__']),
    'openFunction' => 'openDocumentView'
];
@endphp

@include('partner.projects.pages._template', compact('pageConfig'))

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
            
            // Проверяем, что elements правильно загружены
            if (typeof elements === 'undefined') {
                console.error('❌ Elements не загружены! Проверьте подключение template');
                return;
            }
            
            // Добавляем обработчики для фильтров документов
            initDocumentFilters();
            
            // Инициализируем основные обработчики
            if (typeof initDocumentsHandlers === 'function') {
                initDocumentsHandlers();
            } else {
                console.warn('⚠️ initDocumentsHandlers не найдена, используем базовый init');
                if (typeof initGenericHandlers === 'function') {
                    initGenericHandlers();
                }
            }
        }
        
        function initDocumentFilters() {
            console.log('🔍 Инициализация фильтров документов...');
            
            // Обработчики для фильтров
            $('#search').on('input', function() {
                submitFilters();
            });
            
            $('#document_type, #status, #sort').on('change', function() {
                submitFilters();
            });
            
            $('#date_from, #date_to').on('change', function() {
                submitFilters();
            });
            
            // Кнопка сброса фильтров
            $('#resetFilters').on('click', function() {
                $('#documentFilters')[0].reset();
                submitFilters();
            });
        }
        
        function submitFilters() {
            console.log('📋 Отправка фильтров документов...');
            $('#documentFilters').submit();
        }
        
        // Специфичная функция для документов
        function openDocumentView(documentId) {
            console.log('📖 Открытие документа:', documentId);
            const downloadUrl = '{{ route("partner.projects.documents.download", [$project, "__ID__"]) }}'.replace('__ID__', documentId);
            window.open(downloadUrl, '_blank');
        }
        
        // Функция удаления документа
        function deleteDocument(documentId) {
            console.log('🗑️ Удаление документа:', documentId);
            
            if (confirm('Вы уверены, что хотите удалить этот документ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("partner.projects.documents.delete", [$project, "__ID__"]) }}'.replace('__ID__', documentId);
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = '{{ csrf_token() }}';
                
                form.appendChild(methodField);
                form.appendChild(csrfField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
