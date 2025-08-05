<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
  <!-- Основной шрифт Inter для единого дизайна -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- ЕДИНАЯ ДИЗАЙН-СИСТЕМА - основные стили -->
   
    <!-- Дополнительные стили для специфических компонентов -->
    @stack('styles')
    @yield('styles')
    
    <!-- Vite Assets для JS -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AJAX Helper -->
    <script src="{{ asset('js/ajax-helper.js') }}"></script>
    
    <!-- Mobile Enhancements -->
    <script src="{{ asset('js/mobile-enhancements.js') }}"></script>
    
    @yield('head')
</head>
<body>
    <div class="auth-container">
        <div class="auth-wrapper">
            <div class="row g-0 h-100">
                <!-- Форма авторизации -->
                <div class="col-lg-6 order-lg-1 order-2">
                    <div class="auth-form-section">
                        @yield('content')
                    </div>
                </div>
                
                <!-- Изображение и брендинг -->
                <div class="col-lg-6 order-lg-2 order-1">
                    <div class="auth-image-section">
                        <div class="auth-image-content">
                            @yield('image-content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Глобальный AJAX обработчик -->
    <script>
        // Настройка CSRF токена для AJAX запросов
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
