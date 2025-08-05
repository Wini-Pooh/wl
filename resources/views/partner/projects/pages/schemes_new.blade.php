@extends('partner.projects.layouts.project-base')

@section('page-content')
    @include('partner.projects.tabs.schemes')
    
    <!-- Модальное окно для схем -->
    @include('partner.projects.tabs.modals.scheme-modal')
@endsection

@section('styles')
    @parent
    <!-- Дополнительные стили для страницы схем -->
    <style>
        /* Специфичные стили для страницы схем уже включены в tabs/schemes.blade.php */
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            console.log('Страница схем загружена');
            
            // Показываем сообщения если есть
            @if(session('success'))
                showMessage('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showMessage('{{ session('error') }}', 'error');
            @endif
        });
        
        // Функция для показа сообщений
        function showMessage(message, type = 'info') {
            const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Добавляем сообщение в начало контейнера
            $('#schemes-tab-content').prepend(alertHtml);
            
            // Автоматически скрываем через 5 секунд
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
@endsection
