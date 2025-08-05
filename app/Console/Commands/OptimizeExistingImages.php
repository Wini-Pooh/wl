<?php

namespace App\Console\Commands;

use App\Models\ProjectPhoto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OptimizeExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:mark-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отметить существующие изображения как необработанные (без фактической обработки)';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📋 Отмечаем существующие изображения...');

        // Получаем изображения для обработки
        $query = ProjectPhoto::query();

        // Получаем только те, которые еще не отмечены
        $query->where(function($q) {
            $q->whereNull('is_optimized')
              ->orWhere('is_optimized', false);
        });

        $photos = $query->limit(50)->get();

        if ($photos->isEmpty()) {
            $this->info('✅ Нет изображений для обработки');
            return 0;
        }

        $this->info("📊 Найдено изображений: {$photos->count()}");

        $processed = 0;
        $errors = 0;

        foreach ($photos as $photo) {
            try {
                $result = $this->markPhoto($photo);
                
                if ($result['success']) {
                    $processed++;
                    $this->line("✅ {$photo->original_name} - {$result['message']}");
                } else {
                    $errors++;
                    $this->error("❌ {$photo->original_name} - {$result['error']}");
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ {$photo->original_name} - {$e->getMessage()}");
                Log::error('Error marking photo', [
                    'photo_id' => $photo->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n📈 Результаты обработки:");
        $this->info("✅ Отмечено: {$processed}");
        $this->info("❌ Ошибок: {$errors}");

        return 0;
    }

    private function markPhoto(ProjectPhoto $photo): array
    {
        // Проверяем, существует ли файл
        if (!Storage::disk('public')->exists($photo->path)) {
            return [
                'success' => false,
                'error' => 'Файл не найден',
                'message' => ''
            ];
        }

        try {
            // Обновляем размер файла если он не указан
            $fileSize = $photo->file_size ?: Storage::disk('public')->size($photo->path);
            
            // Отмечаем как необработанный существующий файл
            $photo->update([
                'file_size' => $fileSize,
                'original_file_size' => null, // Нет оригинального размера
                'is_optimized' => false, // Не оптимизирован
                'optimization_data' => json_encode([
                    'marked_as_existing' => true,
                    'marked_at' => now()->toISOString()
                ])
            ]);

            return [
                'success' => true,
                'error' => null,
                'message' => 'отмечен как существующий'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => ''
            ];
        }
    }
}
