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
    
    <!-- Стили для auth страниц в стиле welcome -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            min-height: 600px;
        }
        
        .auth-form-section {
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 600px;
        }
        
        .auth-image-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            min-height: 600px;
            padding: 50px;
        }
        
        .auth-brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-brand p {
            font-size: 1.1rem;
            color: #718096;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .auth-form {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            padding: 15px 20px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }
        
        .form-control.is-invalid {
            border-color: #e53e3e;
        }
        
        .invalid-feedback {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        .form-check {
            margin: 20px 0;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-label {
            color: #4a5568;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
        
        .auth-links {
            text-align: center;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .auth-links a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }
        
        .text-muted {
            color: #718096 !important;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background: rgba(72, 187, 120, 0.1);
            color: #2f855a;
            border-left: 4px solid #48bb78;
        }
        
        .auth-image-content h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .auth-image-content p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        .auth-image-content i {
            margin-top: 30px;
            opacity: 0.7;
        }
        
        @media (max-width: 991px) {
            .auth-wrapper {
                margin: 10px;
            }
            
            .auth-form-section,
            .auth-image-section {
                padding: 30px 25px;
                min-height: auto;
            }
            
            .auth-brand h1 {
                font-size: 2rem;
            }
            
            .auth-image-content h2 {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .auth-container {
                padding: 10px;
            }
            
            .auth-form-section,
            .auth-image-section {
                padding: 25px 20px;
            }
            
            .auth-brand h1 {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 10px;
            }
            
            .auth-brand h1 i {
                font-size: 2rem;
            }
        }
    </style>
   
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
