# Скрипт для выполнения миграций документооборота
# PowerShell скрипт

Write-Host "=== Выполнение миграций документооборота ===" -ForegroundColor Green
Write-Host ""

# Проверяем наличие artisan
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Файл artisan не найден. Убедитесь, что скрипт запускается из корневой директории Laravel проекта." -ForegroundColor Red
    exit 1
}

Write-Host "📋 Список миграций для выполнения:" -ForegroundColor Yellow
Write-Host "1. doc_templates - Шаблоны документов"
Write-Host "2. docs - Основная таблица документов"
Write-Host "3. doc_attachments - Вложения документов"
Write-Host "4. doc_history - История изменений документов"
Write-Host "5. doc_permissions - Разрешения доступа к документам"
Write-Host "6. doc_comments - Комментарии к документам"
Write-Host ""

# Функция для выполнения миграции
function Run-Migration {
    param($migrationFile, $description)
    
    Write-Host "🔄 Выполнение: $description" -ForegroundColor Cyan
    
    try {
        $result = php artisan migrate --path="database/migrations/$migrationFile" --force
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ $description - выполнено успешно" -ForegroundColor Green
        } else {
            Write-Host "❌ Ошибка при выполнении: $description" -ForegroundColor Red
            Write-Host $result -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ Исключение при выполнении: $description" -ForegroundColor Red
        Write-Host $_.Exception.Message -ForegroundColor Red
    }
    
    Write-Host ""
}

# Выполняем миграции в правильном порядке (с учетом зависимостей)
Run-Migration "2025_08_06_114000_create_doc_templates_table.php" "Создание таблицы шаблонов документов"
Run-Migration "2025_08_06_115000_create_docs_table.php" "Создание основной таблицы документов"
Run-Migration "2025_08_06_120000_create_doc_attachments_table.php" "Создание таблицы вложений документов"
Run-Migration "2025_08_06_121000_create_doc_history_table.php" "Создание таблицы истории документов"
Run-Migration "2025_08_06_122000_create_doc_permissions_table.php" "Создание таблицы разрешений доступа"
Run-Migration "2025_08_06_123000_create_doc_comments_table.php" "Создание таблицы комментариев"

Write-Host "=== Проверка статуса миграций ===" -ForegroundColor Green
Write-Host ""

# Проверяем статус миграций
Write-Host "📊 Статус миграций:" -ForegroundColor Yellow
php artisan migrate:status

Write-Host ""
Write-Host "=== Дополнительные команды ===" -ForegroundColor Green
Write-Host ""
Write-Host "Для отката миграций используйте:"
Write-Host "php artisan migrate:rollback --step=6" -ForegroundColor Cyan
Write-Host ""
Write-Host "Для пересоздания миграций:"
Write-Host "php artisan migrate:refresh --path=database/migrations/2025_08_06_*.php" -ForegroundColor Cyan
Write-Host ""
Write-Host "Для создания сидеров (начальных данных):"
Write-Host "php artisan make:seeder DocTemplatesSeeder" -ForegroundColor Cyan
Write-Host "php artisan make:seeder DocumentTypesSeeder" -ForegroundColor Cyan

Write-Host ""
Write-Host "✅ Выполнение скрипта завершено!" -ForegroundColor Green
