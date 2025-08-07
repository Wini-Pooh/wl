<?php

/**
 * Скрипт для ручного создания таблиц документооборота с прямыми SQL запросами
 * Запуск: php create_document_tables_sql.php
 */

// Настройки подключения к БД
$host = '127.0.0.1';
$port = '3306';
$dbname = 'rem';
$username = 'root';
$password = '';

echo "=== Создание таблиц документооборота через SQL ===\n";

try {
    // Создаем подключение через mysqli
    $mysqli = new mysqli($host, $username, $password, $dbname, $port);
    
    if ($mysqli->connect_error) {
        throw new Exception("Ошибка подключения: " . $mysqli->connect_error);
    }
    
    echo "✓ Подключение к базе данных установлено\n";
    
    // Устанавливаем кодировку
    $mysqli->set_charset("utf8mb4");
    
} catch (Exception $e) {
    echo "✗ Ошибка подключения к БД: " . $e->getMessage() . "\n";
    exit(1);
}

// Функция для выполнения SQL запроса
function executeSql($mysqli, $sql, $description) {
    echo "\n--- $description ---\n";
    
    try {
        if ($mysqli->query($sql)) {
            echo "✓ $description выполнено успешно\n";
            return true;
        } else {
            echo "✗ Ошибка: " . $mysqli->error . "\n";
            return false;
        }
    } catch (Exception $e) {
        echo "✗ Ошибка выполнения SQL: " . $e->getMessage() . "\n";
        return false;
    }
}

// Удаляем старые таблицы если есть
echo "\n--- Удаление старых таблиц ---\n";

$dropTables = [
    'DROP TABLE IF EXISTS doc_comments',
    'DROP TABLE IF EXISTS doc_permissions', 
    'DROP TABLE IF EXISTS doc_history',
    'DROP TABLE IF EXISTS doc_attachments',
    'DROP TABLE IF EXISTS docs',
    'DROP TABLE IF EXISTS doc_templates',
    'DROP VIEW IF EXISTS document_templates',
    'DROP TABLE IF EXISTS document_comments',
    'DROP TABLE IF EXISTS document_permissions',
    'DROP TABLE IF EXISTS document_history',
    'DROP TABLE IF EXISTS document_attachments',
    'DROP TABLE IF EXISTS documents'
];

foreach ($dropTables as $sql) {
    $mysqli->query($sql);
}

echo "✓ Старые таблицы удалены\n";

// 1. Создание таблицы document_templates
$sql = "CREATE TABLE `document_templates` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL COMMENT 'Название шаблона',
    `description` text COMMENT 'Описание шаблона',
    `document_type` varchar(255) NOT NULL COMMENT 'Тип документа',
    `category` varchar(255) DEFAULT NULL COMMENT 'Категория шаблона',
    `content` longtext NOT NULL COMMENT 'Содержимое шаблона',
    `variables` json DEFAULT NULL COMMENT 'Переменные шаблона',
    `default_values` json DEFAULT NULL COMMENT 'Значения по умолчанию',
    `validation_rules` json DEFAULT NULL COMMENT 'Правила валидации',
    `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Активен ли шаблон',
    `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Системный шаблон',
    `file_format` varchar(255) NOT NULL DEFAULT 'html' COMMENT 'Формат файла',
    `formatting_options` json DEFAULT NULL COMMENT 'Опции форматирования',
    `created_by` bigint unsigned NOT NULL COMMENT 'Создатель',
    `usage_count` int NOT NULL DEFAULT '0' COMMENT 'Количество использований',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `document_templates_document_type_index` (`document_type`),
    KEY `document_templates_category_index` (`category`),
    KEY `document_templates_is_active_index` (`is_active`),
    KEY `document_templates_is_system_index` (`is_system`),
    KEY `document_templates_created_by_index` (`created_by`),
    KEY `document_templates_file_format_index` (`file_format`),
    CONSTRAINT `document_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSql($mysqli, $sql, 'Создание таблицы document_templates');

// 2. Создание таблицы documents
$sql = "CREATE TABLE `documents` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL COMMENT 'Заголовок документа',
    `description` text COMMENT 'Описание документа',
    `content` longtext COMMENT 'Содержание документа',
    `document_type` varchar(255) NOT NULL COMMENT 'Тип документа',
    `category` varchar(255) DEFAULT NULL COMMENT 'Категория документа',
    `template_id` bigint unsigned DEFAULT NULL COMMENT 'ID шаблона',
    `project_id` bigint unsigned DEFAULT NULL COMMENT 'ID проекта',
    `created_by` bigint unsigned NOT NULL COMMENT 'Создатель',
    `assigned_to` bigint unsigned DEFAULT NULL COMMENT 'Назначен пользователю',
    `recipient_type` varchar(255) DEFAULT NULL COMMENT 'Тип получателя',
    `recipient_id` bigint unsigned DEFAULT NULL COMMENT 'ID получателя',
    `recipient_email` varchar(255) DEFAULT NULL COMMENT 'Email получателя',
    `recipient_name` varchar(255) DEFAULT NULL COMMENT 'Имя получателя',
    `status` varchar(255) NOT NULL DEFAULT 'draft' COMMENT 'Статус документа',
    `priority` varchar(255) NOT NULL DEFAULT 'normal' COMMENT 'Приоритет',
    `is_internal` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Внутренний документ',
    `file_path` varchar(255) DEFAULT NULL COMMENT 'Путь к файлу',
    `original_filename` varchar(255) DEFAULT NULL COMMENT 'Оригинальное имя файла',
    `file_size` bigint DEFAULT NULL COMMENT 'Размер файла',
    `mime_type` varchar(255) DEFAULT NULL COMMENT 'MIME тип',
    `file_hash` varchar(255) DEFAULT NULL COMMENT 'Хеш файла',
    `signature_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Требуется подпись',
    `signature_status` varchar(255) NOT NULL DEFAULT 'not_required' COMMENT 'Статус подписи',
    `signature_data` json DEFAULT NULL COMMENT 'Данные подписи',
    `digital_signature` text COMMENT 'Цифровая подпись',
    `signature_certificate` text COMMENT 'Сертификат подписи',
    `version` int NOT NULL DEFAULT '1' COMMENT 'Версия документа',
    `parent_id` bigint unsigned DEFAULT NULL COMMENT 'Родительский документ',
    `is_current_version` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Текущая версия',
    `sent_at` timestamp NULL DEFAULT NULL COMMENT 'Дата отправки',
    `received_at` timestamp NULL DEFAULT NULL COMMENT 'Дата получения',
    `viewed_at` timestamp NULL DEFAULT NULL COMMENT 'Дата просмотра',
    `signed_at` timestamp NULL DEFAULT NULL COMMENT 'Дата подписания',
    `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Дата истечения',
    `archived_at` timestamp NULL DEFAULT NULL COMMENT 'Дата архивирования',
    `metadata` json DEFAULT NULL COMMENT 'Метаданные',
    `template_variables` json DEFAULT NULL COMMENT 'Переменные шаблона',
    `notes` text COMMENT 'Заметки',
    `amount` decimal(12,2) DEFAULT NULL COMMENT 'Сумма',
    `currency` varchar(3) NOT NULL DEFAULT 'RUB' COMMENT 'Валюта',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `documents_document_type_status_index` (`document_type`,`status`),
    KEY `documents_created_by_status_index` (`created_by`,`status`),
    KEY `documents_assigned_to_status_index` (`assigned_to`,`status`),
    KEY `documents_recipient_type_recipient_id_index` (`recipient_type`,`recipient_id`),
    KEY `documents_project_id_status_index` (`project_id`,`status`),
    KEY `documents_signature_status_index` (`signature_status`),
    KEY `documents_priority_index` (`priority`),
    KEY `documents_version_parent_id_index` (`version`,`parent_id`),
    KEY `documents_is_current_version_index` (`is_current_version`),
    KEY `documents_expires_at_index` (`expires_at`),
    KEY `documents_sent_at_index` (`sent_at`),
    KEY `documents_category_index` (`category`),
    KEY `documents_file_hash_index` (`file_hash`),
    CONSTRAINT `documents_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE SET NULL,
    CONSTRAINT `documents_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `documents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `documents_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `documents_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSql($mysqli, $sql, 'Создание таблицы documents');

// 3. Создание таблицы document_attachments
$sql = "CREATE TABLE `document_attachments` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `document_id` bigint unsigned NOT NULL COMMENT 'ID документа',
    `filename` varchar(255) NOT NULL COMMENT 'Имя файла',
    `original_name` varchar(255) NOT NULL COMMENT 'Оригинальное имя',
    `path` varchar(255) NOT NULL COMMENT 'Путь к файлу',
    `size` bigint NOT NULL DEFAULT '0' COMMENT 'Размер файла',
    `mime_type` varchar(255) NOT NULL COMMENT 'MIME тип',
    `file_hash` varchar(255) DEFAULT NULL COMMENT 'Хеш файла',
    `metadata` json DEFAULT NULL COMMENT 'Метаданные',
    `uploaded_by` bigint unsigned NOT NULL COMMENT 'Загрузчик',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `document_attachments_document_id_index` (`document_id`),
    KEY `document_attachments_mime_type_index` (`mime_type`),
    KEY `document_attachments_uploaded_by_index` (`uploaded_by`),
    KEY `document_attachments_file_hash_index` (`file_hash`),
    KEY `document_attachments_created_at_index` (`created_at`),
    CONSTRAINT `document_attachments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSql($mysqli, $sql, 'Создание таблицы document_attachments');

// 4. Создание таблицы document_history
$sql = "CREATE TABLE `document_history` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `document_id` bigint unsigned NOT NULL COMMENT 'ID документа',
    `user_id` bigint unsigned NOT NULL COMMENT 'ID пользователя',
    `action` varchar(255) NOT NULL COMMENT 'Действие',
    `description` text COMMENT 'Описание',
    `changes` json DEFAULT NULL COMMENT 'Изменения',
    `ip_address` varchar(255) DEFAULT NULL COMMENT 'IP адрес',
    `user_agent` varchar(255) DEFAULT NULL COMMENT 'User agent',
    `additional_data` json DEFAULT NULL COMMENT 'Дополнительные данные',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `document_history_document_id_index` (`document_id`),
    KEY `document_history_user_id_index` (`user_id`),
    KEY `document_history_action_index` (`action`),
    KEY `document_history_created_at_index` (`created_at`),
    CONSTRAINT `document_history_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSql($mysqli, $sql, 'Создание таблицы document_history');

// 5. Создание таблицы document_permissions
$sql = "CREATE TABLE `document_permissions` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `document_id` bigint unsigned NOT NULL COMMENT 'ID документа',
    `user_id` bigint unsigned NOT NULL COMMENT 'ID пользователя',
    `permission` varchar(255) NOT NULL COMMENT 'Разрешение',
    `granted` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Предоставлено',
    `granted_at` timestamp NULL DEFAULT NULL COMMENT 'Дата предоставления',
    `granted_by` bigint unsigned DEFAULT NULL COMMENT 'Кем предоставлено',
    `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Дата истечения',
    `notes` text COMMENT 'Заметки',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `document_permissions_document_id_user_id_permission_unique` (`document_id`,`user_id`,`permission`),
    KEY `document_permissions_document_id_index` (`document_id`),
    KEY `document_permissions_user_id_index` (`user_id`),
    KEY `document_permissions_permission_index` (`permission`),
    KEY `document_permissions_granted_index` (`granted`),
    KEY `document_permissions_expires_at_index` (`expires_at`),
    KEY `document_permissions_granted_by_index` (`granted_by`),
    CONSTRAINT `document_permissions_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_permissions_granted_by_foreign` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSql($mysqli, $sql, 'Создание таблицы document_permissions');

// 6. Создание таблицы document_comments
$sql = "CREATE TABLE `document_comments` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `document_id` bigint unsigned NOT NULL COMMENT 'ID документа',
    `user_id` bigint unsigned NOT NULL COMMENT 'ID пользователя',
    `parent_id` bigint unsigned DEFAULT NULL COMMENT 'Родительский комментарий',
    `content` text NOT NULL COMMENT 'Содержание',
    `is_internal` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Внутренний комментарий',
    `status` varchar(255) NOT NULL DEFAULT 'active' COMMENT 'Статус',
    `mentions` json DEFAULT NULL COMMENT 'Упоминания',
    `attachments` json DEFAULT NULL COMMENT 'Вложения',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `document_comments_document_id_index` (`document_id`),
    KEY `document_comments_user_id_index` (`user_id`),
    KEY `document_comments_parent_id_index` (`parent_id`),
    KEY `document_comments_status_index` (`status`),
    KEY `document_comments_is_internal_index` (`is_internal`),
    KEY `document_comments_created_at_index` (`created_at`),
    CONSTRAINT `document_comments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `document_comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

executeSql($mysqli, $sql, 'Создание таблицы document_comments');

// Вставка записей в таблицу migrations
echo "\n--- Обновление таблицы migrations ---\n";

$migrations = [
    '2025_08_06_101804_create_document_templates_table',
    '2025_08_06_101810_create_documents_table',
    '2025_08_06_101817_create_document_attachments_table',
    '2025_08_06_101836_create_document_history_table',
    '2025_08_06_101846_create_document_permissions_table',
    '2025_08_06_101853_create_document_comments_table'
];

// Получаем максимальный batch
$result = $mysqli->query("SELECT MAX(batch) as max_batch FROM migrations");
$row = $result->fetch_assoc();
$nextBatch = ($row['max_batch'] ?? 0) + 1;

foreach ($migrations as $migration) {
    // Проверяем, есть ли уже такая миграция
    $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM migrations WHERE migration = ?");
    $stmt->bind_param("s", $migration);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        $stmt = $mysqli->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->bind_param("si", $migration, $nextBatch);
        if ($stmt->execute()) {
            echo "✓ Миграция $migration добавлена\n";
        } else {
            echo "✗ Ошибка добавления миграции $migration\n";
        }
    } else {
        echo "⚠ Миграция $migration уже существует\n";
    }
}

// Вставка тестовых данных
echo "\n--- Вставка тестовых данных ---\n";

// Проверяем количество шаблонов
$result = $mysqli->query("SELECT COUNT(*) as count FROM document_templates");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $testTemplates = [
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
            ])
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
            ])
        ]
    ];
    
    foreach ($testTemplates as $template) {
        $stmt = $mysqli->prepare("INSERT INTO document_templates (name, description, document_type, category, content, variables, is_active, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, 1, NOW(), NOW())");
        $stmt->bind_param("ssssss", 
            $template['name'], 
            $template['description'], 
            $template['document_type'], 
            $template['category'], 
            $template['content'], 
            $template['variables']
        );
        
        if ($stmt->execute()) {
            echo "✓ Шаблон '{$template['name']}' добавлен\n";
        } else {
            echo "✗ Ошибка добавления шаблона '{$template['name']}'\n";
        }
    }
} else {
    echo "⚠ Тестовые данные уже существуют\n";
}

// Закрываем подключение
$mysqli->close();

echo "\n=== Создание таблиц завершено ===\n";
echo "Созданы таблицы:\n";
echo "- document_templates\n";
echo "- documents\n";
echo "- document_attachments\n";
echo "- document_history\n";
echo "- document_permissions\n";
echo "- document_comments\n";

echo "\nТеперь страница https://rem/documents должна работать!\n";
