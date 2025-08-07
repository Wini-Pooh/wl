<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo e(config('app.name', 'REM System')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700&display=swap" rel="stylesheet">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Styles -->
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                font-family: 'Nunito', sans-serif;
            }
            
            .welcome-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .welcome-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                padding: 50px;
                text-align: center;
                max-width: 500px;
                width: 100%;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .welcome-icon {
                font-size: 4rem;
                color: #667eea;
                margin-bottom: 30px;
            }
            
            .welcome-title {
                font-size: 2.5rem;
                font-weight: 700;
                color: #2d3748;
                margin-bottom: 15px;
            }
            
            .welcome-subtitle {
                font-size: 1.1rem;
                color: #718096;
                margin-bottom: 40px;
                line-height: 1.6;
            }
            
            .auth-buttons {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-welcome {
                padding: 15px 30px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 1.1rem;
                text-decoration: none;
                transition: all 0.3s ease;
                border: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
            
            .btn-primary-welcome {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .btn-primary-welcome:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
                color: white;
            }
            
            .btn-outline-welcome {
                background: transparent;
                color: #667eea;
                border: 2px solid #667eea;
            }
            
            .btn-outline-welcome:hover {
                background: #667eea;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            }
            
            @media (max-width: 576px) {
                .welcome-card {
                    padding: 30px 25px;
                    margin: 10px;
                }
                
                .welcome-title {
                    font-size: 2rem;
                }
                
                .welcome-subtitle {
                    font-size: 1rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="welcome-container">
            <div class="welcome-card">
                <div class="welcome-icon">
                    <i class="bi bi-building-gear"></i>
                </div>
                
                <h1 class="welcome-title"><?php echo e(config('app.name', 'REM System')); ?></h1>
                <p class="welcome-subtitle">
                    Система управления ремонтными проектами.<br>
                    Создавайте сметы, управляйте проектами и контролируйте финансы.
                </p>
                
                <div class="auth-buttons">
                    <?php if(Route::has('login')): ?>
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(url('/home')); ?>" class="btn-welcome btn-primary-welcome">
                                <i class="bi bi-house-door"></i>
                                Перейти в систему
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" class="btn-welcome btn-primary-welcome">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войти в систему
                            </a>
                            
                            <?php if(Route::has('register')): ?>
                                <a href="<?php echo e(route('register')); ?>" class="btn-welcome btn-outline-welcome">
                                    <i class="bi bi-person-plus"></i>
                                    Создать аккаунт
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>
</html>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/welcome.blade.php ENDPATH**/ ?>