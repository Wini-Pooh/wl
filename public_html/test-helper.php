<!DOCTYPE html>
<html>
<head>
    <title>Тест UserRoleHelper</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-block { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Тест UserRoleHelper для проекта #1</h1>
    
    <?php
    try {
        // Включаем автозагрузку Laravel
        require_once __DIR__ . '/../vendor/autoload.php';
        
        // Загружаем Laravel приложение
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $request = Illuminate\Http\Request::capture();
        $response = $kernel->handle($request);
        
        echo '<div class="test-block">';
        echo '<h3>Проверка класса UserRoleHelper</h3>';
        
        if (class_exists('\App\Helpers\UserRoleHelper')) {
            echo '<div class="success">✓ Класс UserRoleHelper найден</div>';
            
            // Проверяем методы
            $methods = ['canManageProjects', 'canSeeActionButtons', 'canAccessProjects', 'getUserRoleDisplay'];
            foreach ($methods as $method) {
                if (method_exists('\App\Helpers\UserRoleHelper', $method)) {
                    echo '<div class="success">✓ Метод ' . $method . ' существует</div>';
                } else {
                    echo '<div class="error">✗ Метод ' . $method . ' не найден</div>';
                }
            }
        } else {
            echo '<div class="error">✗ Класс UserRoleHelper не найден</div>';
        }
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="test-block">';
        echo '<div class="error">Ошибка: ' . $e->getMessage() . '</div>';
        echo '</div>';
    }
    ?>
    
    <div class="test-block">
        <h3>Ссылки</h3>
        <a href="/partner/projects/1">Перейти к проекту #1</a><br>
        <a href="/">На главную</a>
    </div>
</body>
</html>
