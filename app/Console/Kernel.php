<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Автоматическое обновление истекших запросов на подпись каждые 5 минут
        // Это обеспечивает соответствие ФЗ-63 "Об электронной подписи"
        $schedule->command('signatures:update-expired')
                 ->everyFiveMinutes()
                 ->appendOutputTo(storage_path('logs/signature-cleanup.log'));
                 
        // Ежедневная очистка логов (оставляем только последние 30 дней)
        $schedule->command('log:clear --keep=30')
                 ->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
