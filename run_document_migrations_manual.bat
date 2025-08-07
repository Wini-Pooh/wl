@echo off
echo === Запуск создания таблиц документооборота через SQL ===

REM Проверяем наличие PHP
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP не найден. Убедитесь, что PHP установлен и добавлен в PATH
    pause
    exit /b 1
)

echo PHP найден, создаем таблицы через SQL...

REM Запускаем SQL скрипт
php create_document_tables_sql.php

if %errorlevel% equ 0 (
    echo.
    echo Таблицы созданы успешно!
    echo Теперь страница https://rem/documents должна работать.
    echo.
    echo Для проверки таблиц выполните в браузере:
    echo https://rem/documents
    echo.
    echo Или через PHPMyAdmin проверьте таблицы:
    echo - document_templates
    echo - documents
    echo - document_attachments
    echo - document_history
    echo - document_permissions
    echo - document_comments
) else (
    echo ERROR: Ошибка создания таблиц
)

pause
