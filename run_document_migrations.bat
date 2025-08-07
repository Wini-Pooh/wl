@echo off
chcp 65001 >nul
echo ===== Выполнение миграций документооборота =====
echo.

REM Проверяем наличие artisan
if not exist "artisan" (
    echo ❌ Файл artisan не найден. Убедитесь, что скрипт запускается из корневой директории Laravel проекта.
    pause
    exit /b 1
)

echo 📋 Список миграций для выполнения:
echo 1. doc_templates - Шаблоны документов
echo 2. docs - Основная таблица документов  
echo 3. doc_attachments - Вложения документов
echo 4. doc_history - История изменений документов
echo 5. doc_permissions - Разрешения доступа к документам
echo 6. doc_comments - Комментарии к документам
echo.

echo 🔄 Начинаем выполнение миграций...
echo.

REM Выполняем миграции в правильном порядке
echo 🔄 Создание таблицы шаблонов документов...
php artisan migrate --path=database/migrations/2025_08_06_114000_create_doc_templates_table.php --force
if %errorlevel% neq 0 (
    echo ❌ Ошибка при создании таблицы doc_templates
) else (
    echo ✅ Таблица doc_templates создана успешно
)
echo.

echo 🔄 Создание основной таблицы документов...
php artisan migrate --path=database/migrations/2025_08_06_115000_create_docs_table.php --force
if %errorlevel% neq 0 (
    echo ❌ Ошибка при создании таблицы docs
) else (
    echo ✅ Таблица docs создана успешно
)
echo.

echo 🔄 Создание таблицы вложений документов...
php artisan migrate --path=database/migrations/2025_08_06_120000_create_doc_attachments_table.php --force
if %errorlevel% neq 0 (
    echo ❌ Ошибка при создании таблицы doc_attachments
) else (
    echo ✅ Таблица doc_attachments создана успешно
)
echo.

echo 🔄 Создание таблицы истории документов...
php artisan migrate --path=database/migrations/2025_08_06_121000_create_doc_history_table.php --force
if %errorlevel% neq 0 (
    echo ❌ Ошибка при создании таблицы doc_history
) else (
    echo ✅ Таблица doc_history создана успешно
)
echo.

echo 🔄 Создание таблицы разрешений доступа...
php artisan migrate --path=database/migrations/2025_08_06_122000_create_doc_permissions_table.php --force
if %errorlevel% neq 0 (
    echo ❌ Ошибка при создании таблицы doc_permissions
) else (
    echo ✅ Таблица doc_permissions создана успешно
)
echo.

echo 🔄 Создание таблицы комментариев...
php artisan migrate --path=database/migrations/2025_08_06_123000_create_doc_comments_table.php --force
if %errorlevel% neq 0 (
    echo ❌ Ошибка при создании таблицы doc_comments
) else (
    echo ✅ Таблица doc_comments создана успешно
)
echo.

echo ===== Проверка статуса миграций =====
echo.
echo 📊 Статус миграций:
php artisan migrate:status

echo.
echo ===== Дополнительные команды =====
echo.
echo Для отката миграций используйте:
echo php artisan migrate:rollback --step=6
echo.
echo Для пересоздания миграций:
echo php artisan migrate:refresh --path=database/migrations/2025_08_06_*.php
echo.
echo Для создания сидеров (начальных данных):
echo php artisan make:seeder DocTemplatesSeeder
echo php artisan make:seeder DocumentTypesSeeder
echo.

echo ✅ Выполнение скрипта завершено!
echo.
pause
