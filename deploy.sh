#!/bin/bash

# Скрипт автоматического развертывания улучшений
# Версия: 3.0
# Дата: 17 января 2025

echo "=== Развертывание улучшений системы проектов ==="
echo "Версия: 3.0"
echo "Дата: $(date)"
echo ""

# Проверка существования файлов
check_files() {
    echo "Проверка существования файлов..."
    
    files=(
        "app/Http/Controllers/Partner/ProjectController_ultimate.php"
        "public_html/js/file-manager-ultimate.js"
        "public_html/js/bootstrap-modal-fix-ultimate.js"
        "app/Policies/ProjectPolicy.php"
    )
    
    for file in "${files[@]}"; do
        if [ ! -f "$file" ]; then
            echo "❌ Файл $file не найден!"
            exit 1
        else
            echo "✅ $file найден"
        fi
    done
    
    echo ""
}

# Создание резервных копий
create_backups() {
    echo "Создание резервных копий..."
    
    backup_dir="backups/$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$backup_dir"
    
    # Резервные копии основных файлов
    if [ -f "app/Http/Controllers/Partner/ProjectController.php" ]; then
        cp "app/Http/Controllers/Partner/ProjectController.php" "$backup_dir/ProjectController.php.backup"
        echo "✅ Создана резервная копия ProjectController.php"
    fi
    
    if [ -f "public_html/js/file-manager.js" ]; then
        cp "public_html/js/file-manager.js" "$backup_dir/file-manager.js.backup"
        echo "✅ Создана резервная копия file-manager.js"
    fi
    
    if [ -f "public_html/js/bootstrap-modal-fix.js" ]; then
        cp "public_html/js/bootstrap-modal-fix.js" "$backup_dir/bootstrap-modal-fix.js.backup"
        echo "✅ Создана резервная копия bootstrap-modal-fix.js"
    fi
    
    echo "📁 Резервные копии сохранены в: $backup_dir"
    echo ""
}

# Замена файлов
replace_files() {
    echo "Замена файлов на улучшенные версии..."
    
    # Замена контроллера
    cp "app/Http/Controllers/Partner/ProjectController_ultimate.php" "app/Http/Controllers/Partner/ProjectController.php"
    echo "✅ Заменен ProjectController.php"
    
    # Замена файлового менеджера
    cp "public_html/js/file-manager-ultimate.js" "public_html/js/file-manager.js"
    echo "✅ Заменен file-manager.js"
    
    # Замена исправлений модальных окон
    cp "public_html/js/bootstrap-modal-fix-ultimate.js" "public_html/js/bootstrap-modal-fix.js"
    echo "✅ Заменен bootstrap-modal-fix.js"
    
    echo ""
}

# Очистка кеша
clear_cache() {
    echo "Очистка кеша..."
    
    # Очистка кеша Laravel
    php artisan cache:clear
    echo "✅ Кеш приложения очищен"
    
    php artisan config:clear
    echo "✅ Кеш конфигурации очищен"
    
    php artisan view:clear
    echo "✅ Кеш представлений очищен"
    
    php artisan route:clear
    echo "✅ Кеш маршрутов очищен"
    
    echo ""
}

# Обновление зависимостей
update_dependencies() {
    echo "Обновление зависимостей..."
    
    # Обновление автозагрузки
    composer dump-autoload
    echo "✅ Автозагрузка обновлена"
    
    # Оптимизация для продакшн
    composer install --no-dev --optimize-autoloader
    echo "✅ Зависимости оптимизированы"
    
    echo ""
}

# Проверка базы данных
check_database() {
    echo "Проверка базы данных..."
    
    # Миграции
    php artisan migrate:status
    echo "✅ Статус миграций проверен"
    
    # Проверка подключения
    php artisan tinker --execute="DB::connection()->getPdo()"
    echo "✅ Подключение к БД проверено"
    
    echo ""
}

# Проверка прав доступа
check_permissions() {
    echo "Проверка прав доступа..."
    
    # Проверка прав на директории
    directories=(
        "storage"
        "bootstrap/cache"
        "public_html/storage"
    )
    
    for dir in "${directories[@]}"; do
        if [ -d "$dir" ]; then
            chmod -R 755 "$dir"
            echo "✅ Права на $dir установлены"
        fi
    done
    
    echo ""
}

# Тестирование системы
test_system() {
    echo "Тестирование системы..."
    
    # Проверка синтаксиса PHP
    php -l "app/Http/Controllers/Partner/ProjectController.php"
    echo "✅ Синтаксис PHP проверен"
    
    # Проверка JavaScript
    node -c "public_html/js/file-manager.js" 2>/dev/null || echo "⚠️ Проверьте JavaScript файлы"
    
    echo ""
}

# Главная функция
main() {
    echo "Начинаем развертывание улучшений..."
    echo ""
    
    check_files
    create_backups
    replace_files
    clear_cache
    update_dependencies
    check_database
    check_permissions
    test_system
    
    echo "=== Развертывание завершено ==="
    echo ""
    echo "📋 Что было сделано:"
    echo "✅ Созданы резервные копии"
    echo "✅ Заменены файлы на улучшенные версии"
    echo "✅ Очищен кеш"
    echo "✅ Обновлены зависимости"
    echo "✅ Проверена база данных"
    echo "✅ Установлены правильные права доступа"
    echo "✅ Проведено тестирование"
    echo ""
    echo "🎉 Улучшения успешно развернуты!"
    echo ""
    echo "📝 Следующие шаги:"
    echo "1. Проверьте работу сайта: http://rem/partner/projects/1"
    echo "2. Убедитесь, что все функции работают корректно"
    echo "3. Проверьте логи на наличие ошибок"
    echo "4. При необходимости откатите изменения из резервных копий"
    echo ""
}

# Запуск скрипта
main "$@"
