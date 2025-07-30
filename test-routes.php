<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    \Illuminate\Contracts\Http\Kernel::class,
    \App\Http\Kernel::class
);

$app->singleton(
    \Illuminate\Contracts\Console\Kernel::class,
    \App\Console\Kernel::class
);

$app->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \App\Exceptions\Handler::class
);

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Тестируем маршруты
echo "Проверка маршрутов системы документов:\n\n";

$routes = [
    'documents.index' => 'GET /documents',
    'documents.view' => 'GET /documents/view/{id}',
    'documents.signature-requests.view' => 'GET /documents/signature-requests/{id}/view'
];

foreach ($routes as $name => $description) {
    try {
        $route = \Illuminate\Support\Facades\Route::has($name);
        if ($route) {
            echo "✅ $description ($name) - найден\n";
        } else {
            echo "❌ $description ($name) - НЕ найден\n";
        }
    } catch (Exception $e) {
        echo "❌ $description ($name) - ошибка: " . $e->getMessage() . "\n";
    }
}

echo "\nТестирование завершено.\n";
