<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SignatureRequest;
use App\Models\DocumentSignature;

class UpdateExpiredSignatureRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signatures:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление статуса истекших запросов на подпись в соответствии с ФЗ-63';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем обновление истекших запросов на подпись...');

        // Обновляем истекшие запросы на подпись
        $expiredRequests = SignatureRequest::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->count();

        if ($expiredRequests > 0) {
            SignatureRequest::updateExpiredRequests();
            $this->info("Обновлено запросов на подпись: {$expiredRequests}");
        } else {
            $this->info('Истекших запросов на подпись не найдено');
        }

        // Обновляем истекшие подписи
        $expiredSignatures = DocumentSignature::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->count();

        if ($expiredSignatures > 0) {
            DocumentSignature::updateExpiredSignatures();
            $this->info("Обновлено подписей: {$expiredSignatures}");
        } else {
            $this->info('Истекших подписей не найдено');
        }

        // Статистика
        $totalPendingRequests = SignatureRequest::pending()->count();
        $totalSignedDocuments = DocumentSignature::signed()->count();

        $this->table(
            ['Статистика', 'Количество'],
            [
                ['Активные запросы на подпись', $totalPendingRequests],
                ['Подписанные документы', $totalSignedDocuments],
                ['Обновлено истекших запросов', $expiredRequests],
                ['Обновлено истекших подписей', $expiredSignatures],
            ]
        );

        $this->info('✅ Обновление завершено успешно!');
        
        return Command::SUCCESS;
    }
}
