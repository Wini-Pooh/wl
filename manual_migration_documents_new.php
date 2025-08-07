<?php

/**
 * Скрипт для ручного выполнения миграций документооборота
 * Запуск: php manual_migration_documents_new.php
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Ручное выполнение миграций документооборота (новые таблицы) ===\n";

try {
    // Проверяем подключение к БД
    DB::connection()->getPdo();
    echo "✓ Подключение к базе данных установлено\n";
} catch (Exception $e) {
    echo "✗ Ошибка подключения к БД: " . $e->getMessage() . "\n";
    exit(1);
}

// Функция для удаления старых таблиц
function dropTableIfExists($tableName) {
    try {
        if (Schema::hasTable($tableName)) {
            Schema::dropIfExists($tableName);
            echo "✓ Таблица $tableName удалена\n";
        } else {
            echo "⚠ Таблица $tableName не существует\n";
        }
    } catch (Exception $e) {
        echo "✗ Ошибка удаления таблицы $tableName: " . $e->getMessage() . "\n";
    }
}

// Удаляем старые таблицы документов если они есть
echo "\n--- Удаление старых таблиц ---\n";
dropTableIfExists('doc_comments');
dropTableIfExists('doc_permissions');
dropTableIfExists('doc_history');
dropTableIfExists('doc_attachments');
dropTableIfExists('docs');
dropTableIfExists('doc_templates');

// Удаляем представление если есть
try {
    DB::statement('DROP VIEW IF EXISTS document_templates');
    echo "✓ Представление document_templates удалено\n";
} catch (Exception $e) {
    echo "⚠ Представление document_templates не существует или уже удалено\n";
}

echo "\n--- Запуск новых миграций ---\n";

// Запускаем миграции через artisan
try {
    $output = [];
    $returnCode = 0;
    
    exec('php artisan migrate --path=database/migrations/2025_08_06_101804_create_document_templates_table.php --force 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✓ Миграция document_templates выполнена\n";
    } else {
        echo "✗ Ошибка миграции document_templates: " . implode("\n", $output) . "\n";
    }
    
    $output = [];
    exec('php artisan migrate --path=database/migrations/2025_08_06_101810_create_documents_table.php --force 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✓ Миграция documents выполнена\n";
    } else {
        echo "✗ Ошибка миграции documents: " . implode("\n", $output) . "\n";
    }
    
    $output = [];
    exec('php artisan migrate --path=database/migrations/2025_08_06_101817_create_document_attachments_table.php --force 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✓ Миграция document_attachments выполнена\n";
    } else {
        echo "✗ Ошибка миграции document_attachments: " . implode("\n", $output) . "\n";
    }
    
    $output = [];
    exec('php artisan migrate --path=database/migrations/2025_08_06_101836_create_document_history_table.php --force 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✓ Миграция document_history выполнена\n";
    } else {
        echo "✗ Ошибка миграции document_history: " . implode("\n", $output) . "\n";
    }
    
    $output = [];
    exec('php artisan migrate --path=database/migrations/2025_08_06_101846_create_document_permissions_table.php --force 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✓ Миграция document_permissions выполнена\n";
    } else {
        echo "✗ Ошибка миграции document_permissions: " . implode("\n", $output) . "\n";
    }
    
    $output = [];
    exec('php artisan migrate --path=database/migrations/2025_08_06_101853_create_document_comments_table.php --force 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✓ Миграция document_comments выполнена\n";
    } else {
        echo "✗ Ошибка миграции document_comments: " . implode("\n", $output) . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Ошибка выполнения миграций: " . $e->getMessage() . "\n";
}

// Вставка тестовых данных
echo "\n--- Вставка тестовых данных ---\n";

try {
    // Проверяем, есть ли уже данные
    $templatesCount = DB::table('document_templates')->count();
    
    if ($templatesCount == 0) {
        // Вставляем несколько тестовых шаблонов
        DB::table('document_templates')->insert([
            [
                'name' => 'Договор подряда',
                'description' => 'Стандартный шаблон договора подряда',
                'document_type' => 'contract',
                'category' => 'legal',
                'content' => '<h1>ДОГОВОР ПОДРЯДА №{{contract_number}}</h1>
<p>г. {{city}}, {{date}}</p>
<p>{{contractor_name}}, именуемый в дальнейшем «Подрядчик», с одной стороны, и {{client_name}}, именуемый в дальнейшем «Заказчик», с другой стороны, заключили настоящий договор о нижеследующем:</p>

<h2>1. ПРЕДМЕТ ДОГОВОРА</h2>
<p>1.1. Подрядчик обязуется выполнить работы: {{work_description}}</p>
<p>1.2. Стоимость работ составляет: {{amount}} руб.</p>

<h2>2. СРОКИ ВЫПОЛНЕНИЯ</h2>
<p>2.1. Срок выполнения работ: до {{deadline}}</p>',
                'variables' => json_encode([
                    'contract_number' => 'Номер договора',
                    'city' => 'Город',
                    'date' => 'Дата',
                    'contractor_name' => 'Название подрядчика',
                    'client_name' => 'Название заказчика',
                    'work_description' => 'Описание работ',
                    'amount' => 'Сумма',
                    'deadline' => 'Срок выполнения'
                ]),
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Акт выполненных работ',
                'description' => 'Шаблон акта выполненных работ',
                'document_type' => 'report',
                'category' => 'project',
                'content' => '<h1>АКТ ВЫПОЛНЕННЫХ РАБОТ №{{act_number}}</h1>
<p>г. {{city}}, {{date}}</p>
<p>По договору №{{contract_number}} от {{contract_date}}</p>

<h2>ВЫПОЛНЕННЫЕ РАБОТЫ:</h2>
<table>
    <tr>
        <th>Наименование работ</th>
        <th>Единица измерения</th>
        <th>Количество</th>
        <th>Цена</th>
        <th>Сумма</th>
    </tr>
    {{work_table}}
</table>

<p><strong>Итого: {{total_amount}} руб.</strong></p>',
                'variables' => json_encode([
                    'act_number' => 'Номер акта',
                    'city' => 'Город',
                    'date' => 'Дата',
                    'contract_number' => 'Номер договора',
                    'contract_date' => 'Дата договора',
                    'work_table' => 'Таблица работ',
                    'total_amount' => 'Общая сумма'
                ]),
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Счет на оплату',
                'description' => 'Шаблон счета на оплату',
                'document_type' => 'invoice',
                'category' => 'financial',
                'content' => '<h1>СЧЕТ НА ОПЛАТУ №{{invoice_number}}</h1>
<p>от {{date}}</p>

<h2>Поставщик:</h2>
<p>{{supplier_info}}</p>

<h2>Покупатель:</h2>
<p>{{buyer_info}}</p>

<h2>К ОПЛАТЕ:</h2>
<table>
    <tr>
        <th>Наименование</th>
        <th>Количество</th>
        <th>Цена</th>
        <th>Сумма</th>
    </tr>
    {{items_table}}
</table>

<p><strong>Всего к оплате: {{total_amount}} руб.</strong></p>',
                'variables' => json_encode([
                    'invoice_number' => 'Номер счета',
                    'date' => 'Дата',
                    'supplier_info' => 'Информация о поставщике',
                    'buyer_info' => 'Информация о покупателе',
                    'items_table' => 'Таблица товаров/услуг',
                    'total_amount' => 'Общая сумма'
                ]),
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
        
        echo "✓ Тестовые шаблоны документов добавлены\n";
    } else {
        echo "⚠ Тестовые данные уже существуют, пропускаем\n";
    }
    
} catch (Exception $e) {
    echo "✗ Ошибка вставки тестовых данных: " . $e->getMessage() . "\n";
}

echo "\n=== Миграции завершены ===\n";
echo "Созданы новые таблицы:\n";
echo "- document_templates\n";
echo "- documents\n";
echo "- document_attachments\n";
echo "- document_history\n";
echo "- document_permissions\n";
echo "- document_comments\n";

echo "\nПроверка таблиц:\n";
try {
    echo "document_templates: " . (Schema::hasTable('document_templates') ? "✓" : "✗") . "\n";
    echo "documents: " . (Schema::hasTable('documents') ? "✓" : "✗") . "\n";
    echo "document_attachments: " . (Schema::hasTable('document_attachments') ? "✓" : "✗") . "\n";
    echo "document_history: " . (Schema::hasTable('document_history') ? "✓" : "✗") . "\n";
    echo "document_permissions: " . (Schema::hasTable('document_permissions') ? "✓" : "✗") . "\n";
    echo "document_comments: " . (Schema::hasTable('document_comments') ? "✓" : "✗") . "\n";
} catch (Exception $e) {
    echo "✗ Ошибка проверки таблиц: " . $e->getMessage() . "\n";
}

echo "\nТеперь можно запускать приложение!\n";
