<?php
// Проверяем доступность подписи документов

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Создаем фейковый HTTP запрос
$request = Illuminate\Http\Request::create('/documents/1/sign', 'POST', [
    'signature' => 'тест',
    'agreement' => true
], [], [], [
    'HTTP_X_CSRF_TOKEN' => 'test-token',
    'HTTP_CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
]);

// Обрабатываем запрос
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";

$kernel->terminate($request, $response);
