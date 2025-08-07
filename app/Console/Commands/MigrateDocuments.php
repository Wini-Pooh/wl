<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class MigrateDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:migrate {--rollback : Откатить миграции} {--fresh : Пересоздать таблицы}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Выполнить миграции для системы документооборота';

    /**
     * Список миграций документооборота в порядке выполнения
     */
    protected $migrations = [
        '2025_08_06_114000_create_doc_templates_table.php',
        '2025_08_06_115000_create_docs_table.php',
        '2025_08_06_120000_create_doc_attachments_table.php',
        '2025_08_06_121000_create_doc_history_table.php',
        '2025_08_06_122000_create_doc_permissions_table.php',
        '2025_08_06_123000_create_doc_comments_table.php',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Управление миграциями документооборота ===');
        $this->newLine();

        if ($this->option('rollback')) {
            $this->rollbackMigrations();
        } elseif ($this->option('fresh')) {
            $this->freshMigrations();
        } else {
            $this->runMigrations();
        }

        $this->newLine();
        $this->showMigrationStatus();
        $this->newLine();
        $this->info('✅ Операция завершена успешно!');
    }

    /**
     * Выполнить миграции
     */
    protected function runMigrations()
    {
        $this->info('🔄 Выполнение миграций документооборота...');
        $this->newLine();

        foreach ($this->migrations as $migration) {
            $description = $this->getMigrationDescription($migration);
            $this->line("🔄 $description");

            try {
                $exitCode = Artisan::call('migrate', [
                    '--path' => "database/migrations/$migration",
                    '--force' => true
                ]);

                if ($exitCode === 0) {
                    $this->info("✅ $description - выполнено успешно");
                } else {
                    $this->error("❌ Ошибка при выполнении: $description");
                    $this->line(Artisan::output());
                }
            } catch (\Exception $e) {
                $this->error("❌ Исключение при выполнении: $description");
                $this->error($e->getMessage());
            }

            $this->newLine();
        }
    }

    /**
     * Откатить миграции
     */
    protected function rollbackMigrations()
    {
        $this->warn('⚠️  Откат миграций документооборота...');
        $this->newLine();

        if ($this->confirm('Вы уверены, что хотите откатить все миграции документооборота?')) {
            $exitCode = Artisan::call('migrate:rollback', [
                '--step' => count($this->migrations),
                '--force' => true
            ]);

            if ($exitCode === 0) {
                $this->info('✅ Миграции успешно откачены');
            } else {
                $this->error('❌ Ошибка при откате миграций');
                $this->line(Artisan::output());
            }
        } else {
            $this->info('Откат отменен');
        }
    }

    /**
     * Пересоздать миграции
     */
    protected function freshMigrations()
    {
        $this->warn('⚠️  Пересоздание миграций документооборота...');
        $this->newLine();

        if ($this->confirm('Внимание! Это удалит все данные в таблицах документооборота. Продолжить?')) {
            // Сначала откатываем
            $this->rollbackMigrations();
            
            $this->newLine();
            
            // Затем выполняем заново
            $this->runMigrations();
        } else {
            $this->info('Пересоздание отменено');
        }
    }

    /**
     * Показать статус миграций
     */
    protected function showMigrationStatus()
    {
        $this->info('📊 Статус миграций:');
        Artisan::call('migrate:status');
        $this->line(Artisan::output());
    }

    /**
     * Получить описание миграции по имени файла
     */
    protected function getMigrationDescription($migration)
    {
        $descriptions = [
            '2025_08_06_114000_create_doc_templates_table.php' => 'Создание таблицы шаблонов документов (doc_templates)',
            '2025_08_06_115000_create_docs_table.php' => 'Создание основной таблицы документов (docs)',
            '2025_08_06_120000_create_doc_attachments_table.php' => 'Создание таблицы вложений документов (doc_attachments)',
            '2025_08_06_121000_create_doc_history_table.php' => 'Создание таблицы истории документов (doc_history)',
            '2025_08_06_122000_create_doc_permissions_table.php' => 'Создание таблицы разрешений доступа (doc_permissions)',
            '2025_08_06_123000_create_doc_comments_table.php' => 'Создание таблицы комментариев (doc_comments)',
        ];

        return $descriptions[$migration] ?? $migration;
    }
}
