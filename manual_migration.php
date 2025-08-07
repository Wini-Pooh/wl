<?php

/**
 * Скрипт для ручного выполнения миграций документооборота
 * 
 * Использование:
 * php manual_migration.php
 * 
 * или через artisan:
 * php artisan migrate --path=database/migrations/2025_08_06_114000_create_doc_templates_table.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Настройка подключения к базе данных
$capsule = new Capsule;

// Загружаем конфигурацию из .env файла
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$capsule->addConnection([
    'driver' => env('DB_CONNECTION', 'mysql'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$schema = $capsule->schema();

echo "Начинаем выполнение миграций документооборота...\n\n";

try {
    // 1. Создание таблицы шаблонов документов
    if (!$schema->hasTable('doc_templates')) {
        echo "Создание таблицы doc_templates...\n";
        $schema->create('doc_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('document_type');
            $table->string('category')->nullable();
            $table->longText('template_content');
            $table->json('variables')->nullable();
            $table->json('default_values')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->string('file_format')->default('html');
            $table->json('formatting_options')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            
            $table->index('document_type');
            $table->index('category');
            $table->index('is_active');
            $table->index('is_system');
            $table->index('created_by');
            $table->index('file_format');
        });
        echo "✓ Таблица doc_templates создана успешно\n";
    } else {
        echo "Таблица doc_templates уже существует\n";
    }

    // 2. Создание таблицы документов
    if (!$schema->hasTable('docs')) {
        echo "Создание таблицы docs...\n";
        $schema->create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('document_type');
            $table->string('category')->nullable();
            
            $table->foreignId('template_id')->nullable()->constrained('doc_templates')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('recipient_type')->nullable();
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_name')->nullable();
            
            $table->string('status')->default('draft');
            $table->string('priority')->default('normal');
            $table->boolean('is_internal')->default(true);
            
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('file_hash')->nullable();
            
            $table->boolean('signature_required')->default(false);
            $table->string('signature_status')->default('not_required');
            $table->json('signature_data')->nullable();
            $table->text('digital_signature')->nullable();
            $table->text('signature_certificate')->nullable();
            
            $table->integer('version')->default(1);
            $table->foreignId('parent_id')->nullable()->constrained('docs')->onDelete('cascade');
            $table->boolean('is_current_version')->default(true);
            
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            
            $table->json('metadata')->nullable();
            $table->json('template_variables')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 3)->default('RUB');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Индексы
            $table->index(['document_type', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['recipient_type', 'recipient_id']);
            $table->index(['project_id', 'status']);
            $table->index('signature_status');
            $table->index('priority');
            $table->index(['version', 'parent_id']);
            $table->index('is_current_version');
            $table->index('expires_at');
            $table->index('sent_at');
            $table->index('category');
            $table->index('file_hash');
        });
        echo "✓ Таблица docs создана успешно\n";
    } else {
        echo "Таблица docs уже существует\n";
    }

    // 3. Создание таблицы вложений документов
    if (!$schema->hasTable('doc_attachments')) {
        echo "Создание таблицы doc_attachments...\n";
        $schema->create('doc_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('docs')->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name')->nullable();
            $table->string('path');
            $table->bigInteger('size')->default(0);
            $table->string('mime_type');
            $table->string('file_hash')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('doc_id');
            $table->index('mime_type');
            $table->index('uploaded_by');
            $table->index('file_hash');
        });
        echo "✓ Таблица doc_attachments создана успешно\n";
    } else {
        echo "Таблица doc_attachments уже существует\n";
    }

    // 4. Создание таблицы истории документов
    if (!$schema->hasTable('doc_history')) {
        echo "Создание таблицы doc_history...\n";
        $schema->create('doc_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('docs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('doc_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
        echo "✓ Таблица doc_history создана успешно\n";
    } else {
        echo "Таблица doc_history уже существует\n";
    }

    // 5. Создание таблицы разрешений доступа к документам
    if (!$schema->hasTable('doc_permissions')) {
        echo "Создание таблицы doc_permissions...\n";
        $schema->create('doc_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('docs')->onDelete('cascade');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('permission');
            $table->foreignId('granted_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('doc_id');
            $table->index(['entity_type', 'entity_id']);
            $table->index('permission');
            $table->index('granted_by');
            $table->index('expires_at');
            
            $table->unique(['doc_id', 'entity_type', 'entity_id', 'permission'], 'doc_permissions_unique');
        });
        echo "✓ Таблица doc_permissions создана успешно\n";
    } else {
        echo "Таблица doc_permissions уже существует\n";
    }

    // 6. Создание таблицы комментариев к документам
    if (!$schema->hasTable('doc_comments')) {
        echo "Создание таблицы doc_comments...\n";
        $schema->create('doc_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('docs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('doc_comments')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_internal')->default(true);
            $table->string('status')->default('active');
            $table->json('mentions')->nullable();
            $table->timestamps();
            
            $table->index('doc_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('is_internal');
            $table->index('created_at');
        });
        echo "✓ Таблица doc_comments создана успешно\n";
    } else {
        echo "Таблица doc_comments уже существует\n";
    }

    echo "\n✅ Все миграции документооборота выполнены успешно!\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка при выполнении миграций: " . $e->getMessage() . "\n";
    echo "Подробности: " . $e->getTraceAsString() . "\n";
}

echo "\nДля проверки созданных таблиц используйте следующие команды:\n";
echo "SHOW TABLES LIKE 'doc_%';\n";
echo "DESCRIBE docs;\n";
echo "DESCRIBE doc_attachments;\n";
echo "DESCRIBE doc_templates;\n";
echo "DESCRIBE doc_history;\n";
echo "DESCRIBE doc_permissions;\n";
echo "DESCRIBE doc_comments;\n";
