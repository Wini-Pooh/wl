@extends('partner.projects.layouts.project-base')

@section('page-content')
    @include('partner.projects.tabs.finance')
@endsection

@section('styles')
    @parent
    <!-- Дополнительные стили для финансовой страницы -->
    <style>
        /* Улучшения для финансовых таблиц */
        .table-responsive {
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .finance-summary .card {
            transition: transform 0.2s;
        }
        
        .finance-summary .card:hover {
            transform: translateY(-2px);
        }
        
        /* Стили для загрузчиков */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Улучшения для модальных окон */
        .modal-content {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        /* Стили для уведомлений */
        .toast-container {
            z-index: 9999;
        }
    </style>
@endsection

@section('scripts')
    @parent
    
    <!-- Подключаем AJAX обработчик для финансов -->
    <script src="{{ asset('js/finance-ajax.js') }}"></script>
    
    <script>
        // Используем унифицированную систему инициализации
        $(document).ready(function() {
            console.log('💰 Финансовая страница проекта #{{ $project->id }} загружена');
            
            // Инициализация через новый менеджер
            if (window.projectManager) {
                window.projectManager.initPage('finance', function() {
                    console.log('📊 Дополнительная инициализация финансовой страницы...');
                    
                    // Инициализация контента финансовой вкладки
                    if (typeof loadFinanceContent === 'function') {
                        loadFinanceContent();
                    }
                    
                    // Автоматическая инициализация масок
                    if (window.inputMaskManager) {
                        window.inputMaskManager.init();
                    }
                });
            } else {
                console.warn('⚠️ ProjectManager не найден, проверьте подключение скриптов');
            }
        });
    </script>
</div>

@endsection
