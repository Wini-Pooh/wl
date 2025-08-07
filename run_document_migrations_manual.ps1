# PowerShell скрипт для запуска миграций документооборота
# Запуск: .\run_document_migrations_manual.ps1

Write-Host "=== Запуск ручных миграций документооборота ===" -ForegroundColor Green

# Проверяем наличие PHP
try {
    $phpVersion = php -v
    Write-Host "✓ PHP найден" -ForegroundColor Green
} catch {
    Write-Host "✗ PHP не найден. Убедитесь, что PHP установлен и добавлен в PATH" -ForegroundColor Red
    exit 1
}

# Проверяем наличие файла миграции
$migrationFile = "manual_migration_documents.php"
if (-not (Test-Path $migrationFile)) {
    Write-Host "✗ Файл $migrationFile не найден" -ForegroundColor Red
    exit 1
}

Write-Host "Запускаем миграции..." -ForegroundColor Yellow

# Запускаем миграцию
try {
    php $migrationFile
    Write-Host "✓ Миграции выполнены успешно!" -ForegroundColor Green
} catch {
    Write-Host "✗ Ошибка выполнения миграций: $_" -ForegroundColor Red
    exit 1
}

Write-Host "`nМиграции завершены. Можно запускать приложение!" -ForegroundColor Green
Write-Host "Для проверки таблиц выполните:" -ForegroundColor Cyan
Write-Host "php artisan tinker" -ForegroundColor Gray
Write-Host ">>> Schema::hasTable('document_templates')" -ForegroundColor Gray
Write-Host ">>> DB::table('doc_templates')->count()" -ForegroundColor Gray
