<?php

namespace App\Console\Commands;

use App\Services\SimpleImageProcessingService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TestSimpleImageProcessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:simple-image-processing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование простого сервиса обработки изображений';

    private SimpleImageProcessingService $imageProcessingService;

    public function __construct(SimpleImageProcessingService $imageProcessingService)
    {
        parent::__construct();
        $this->imageProcessingService = $imageProcessingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️ Тестирование простого сервиса обработки изображений...');

        // Проверка конфигурации
        $this->info('📋 Проверка конфигурации:');
        $this->info('- Качество JPEG: ' . config('image_processing.webp_quality', 85));
        $this->info('- Максимальная ширина: ' . config('image_processing.max_width', 1920));
        $this->info('- Максимальная высота: ' . config('image_processing.max_height', 1080));

        // Проверка GD
        $this->info('🔍 Проверка GD расширения...');
        if (extension_loaded('gd')) {
            $this->info('✅ GD расширение доступно');
        } else {
            $this->warn('⚠️ GD расширение недоступно - изменение размеров не будет работать');
        }

        // Создание тестового изображения
        $testImagePath = $this->createTestImage();
        if (!$testImagePath) {
            $this->error('❌ Не удалось создать тестовое изображение');
            return 1;
        }

        $this->info("✅ Тестовое изображение создано: {$testImagePath}");

        // Обработка тестового изображения
        $this->info('🔄 Обработка тестового изображения...');
        
        try {
            $uploadedFile = new UploadedFile(
                $testImagePath,
                'test_image.jpg',
                'image/jpeg',
                null,
                true
            );

            $originalSize = filesize($testImagePath);
            
            $result = $this->imageProcessingService->processUploadedImage(
                $uploadedFile,
                'test/processed',
                'test_processed_image'
            );

            $this->info('✅ Изображение успешно обработано!');
            $this->info('📊 Результаты обработки:');
            $this->info("- Оригинальный размер: {$originalSize} байт");
            $this->info("- Обработанный размер: {$result['original']['file_size']} байт");
            
            if ($result['original']['optimized']) {
                $savings = $originalSize - $result['original']['file_size'];
                $savingsPercent = round(($savings / $originalSize) * 100, 1);
                $this->info("- Экономия: {$savings} байт ({$savingsPercent}%)");
                $this->info("- Изображение было изменено в размере");
            } else {
                $this->info("- Оптимизация не требовалась");
            }

            // Проверка созданных файлов
            $this->info('🔍 Проверка созданных файлов:');
            if (Storage::disk('public')->exists($result['original']['path'])) {
                $this->info("✅ Основной файл: {$result['original']['path']}");
            } else {
                $this->error("❌ Основной файл не найден: {$result['original']['path']}");
            }

            // Тестирование удаления файлов
            $this->info('🗑️ Тестирование удаления файлов...');
            $deleted = $this->imageProcessingService->deleteImageWithThumbnails($result['original']['path']);
            
            if ($deleted) {
                $this->info('✅ Файлы успешно удалены');
            } else {
                $this->warn('⚠️ Не все файлы были удалены');
            }

        } catch (\Exception $e) {
            $this->error("❌ Ошибка при обработке: {$e->getMessage()}");
            return 1;
        } finally {
            // Очищаем тестовый файл
            if (file_exists($testImagePath)) {
                unlink($testImagePath);
            }
        }

        $this->info('🎉 Тестирование завершено успешно!');
        return 0;
    }

    private function createTestImage(): ?string
    {
        try {
            $testDir = storage_path('app');
            if (!is_dir($testDir)) {
                mkdir($testDir, 0755, true);
            }

            $imagePath = $testDir . '/test_image.jpg';

            if (extension_loaded('gd')) {
                // Создаем изображение 2000x1500 для тестирования изменения размера
                $image = imagecreatetruecolor(2000, 1500);
                $background = imagecolorallocate($image, 200, 200, 200);
                $textColor = imagecolorallocate($image, 50, 50, 50);
                
                imagefill($image, 0, 0, $background);
                imagestring($image, 5, 900, 750, 'TEST IMAGE', $textColor);
                
                imagejpeg($image, $imagePath, 80);
                imagedestroy($image);
            } else {
                // Создаем простой тестовый файл
                file_put_contents($imagePath, base64_decode('
                    /9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEB
                    AQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEB
                    AQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAAB
                    AAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA
                    /8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEA
                    PwDwAoA=
                '));
            }

            return $imagePath;

        } catch (\Exception $e) {
            $this->error("Ошибка создания тестового изображения: {$e->getMessage()}");
            return null;
        }
    }
}
