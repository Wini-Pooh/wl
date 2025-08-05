<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Models\User;

class UpdateSubscriptionCounters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:update-counters {--user-id= : ID конкретного пользователя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление счетчиков использования ресурсов в подписках';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Пользователь с ID {$userId} не найден.");
                return 1;
            }
            
            $subscription = $user->activeSubscription;
            if (!$subscription) {
                $this->warn("У пользователя {$user->name} нет активной подписки.");
                return 0;
            }
            
            $subscription->updateResourceCounters();
            $this->info("Счетчики обновлены для пользователя: {$user->name}");
            
        } else {
            $activeSubscriptions = UserSubscription::active()->with('user')->get();
            
            if ($activeSubscriptions->isEmpty()) {
                $this->info('Активных подписок не найдено.');
                return 0;
            }
            
            $this->info("Найдено активных подписок: " . $activeSubscriptions->count());
            
            $progressBar = $this->output->createProgressBar($activeSubscriptions->count());
            $progressBar->start();
            
            $updated = 0;
            $errors = 0;
            
            foreach ($activeSubscriptions as $subscription) {
                try {
                    $subscription->updateResourceCounters();
                    $updated++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("Ошибка обновления для пользователя {$subscription->user->name}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            $this->info("Обновление завершено!");
            $this->info("Успешно обновлено: {$updated}");
            if ($errors > 0) {
                $this->warn("Ошибок: {$errors}");
            }
        }
        
        return 0;
    }
}
