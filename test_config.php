<?php

// Простой тест для проверки конфигурации
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Запуск Laravel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Проверка конфигурации шаблонов
echo "Проверка конфигурации документов...\n";
$config = config('document_templates');
if (!$config) {
    echo "ОШИБКА: Конфигурация document_templates не загружена.\n";
} else {
    echo "Конфигурация document_templates загружена.\n";
    
    if (!isset($config['templates'])) {
        echo "ОШИБКА: Ключ 'templates' отсутствует в конфигурации.\n";
    } else {
        echo "Ключ 'templates' найден в конфигурации.\n";
        
        if (!is_array($config['templates'])) {
            echo "ОШИБКА: Значение 'templates' не является массивом.\n";
        } else {
            echo "Значение 'templates' является массивом.\n";
            
            if (empty($config['templates'])) {
                echo "ОШИБКА: Массив 'templates' пуст.\n";
            } else {
                echo "Массив 'templates' содержит данные.\n";
                echo "Ключи шаблонов: " . implode(', ', array_keys($config['templates'])) . "\n";
            }
        }
    }
}

echo "\nПроверка соединения с базой данных...\n";
try {
    $connection = DB::connection()->getPdo();
    echo "Соединение с базой данных установлено. Версия PDO: " . $connection->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
} catch (\Exception $e) {
    echo "ОШИБКА: Невозможно установить соединение с базой данных: " . $e->getMessage() . "\n";
}

echo "\nГотово.\n";
