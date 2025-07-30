<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\MigratePhotosSeeder;

class MigratePhotosToDatabase extends Command
{
    /**
     * Название команды
     *
     * @var string
     */
    protected $signature = 'photos:migrate';

    /**
     * Описание команды
     *
     * @var string
     */
    protected $description = 'Перенос всех фотографий из файловой системы в базу данных';

    /**
     * Выполнение команды
     */
    public function handle()
    {
        $this->info('Начинаем миграцию фотографий...');
        
        // Запускаем сидер
        $seeder = new MigratePhotosSeeder();
        $seeder->setContainer($this->laravel);
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('Миграция фотографий завершена.');
        
        return 0;
    }
}
