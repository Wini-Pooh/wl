<?php

namespace App\Console\Commands;

use App\Services\ImageProcessingService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TestImageProcessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:image-processing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестирование сервиса обработки изображений';

    /**
     * Execute the console command.
     */
    public function handle(ImageProcessingService $imageProcessingService)
    {
        $this->info('🚀 Тестирование сервиса обработки изображений...');
        
        // Проверяем конфигурацию
        $this->info('📋 Проверка конфигурации:');
        $this->line('- WebP качество: ' . config('image_processing.webp_quality'));
        $this->line('- Максимальная ширина: ' . config('image_processing.max_width'));
        $this->line('- Максимальная высота: ' . config('image_processing.max_height'));
        $this->line('- Размеры превью: ' . json_encode(config('image_processing.thumbnail_sizes')));

        // Проверяем доступность Intervention Image
        try {
            $this->info('🔧 Проверка Intervention Image...');
            
            // Создаем тестовое изображение
            $testImagePath = storage_path('app/test_image.jpg');
            
            // Создаем простое тестовое изображение
            $this->createTestImage($testImagePath);
            
            if (file_exists($testImagePath)) {
                $this->info('✅ Тестовое изображение создано: ' . $testImagePath);
                
                // Создаем fake UploadedFile для тестирования
                $uploadedFile = new UploadedFile(
                    $testImagePath, 
                    'test-image.jpg', 
                    'image/jpeg', 
                    null, 
                    true
                );
                
                $this->info('📤 Обработка тестового изображения...');
                
                $result = $imageProcessingService->processUploadedImage(
                    $uploadedFile,
                    'test/processed',
                    'test_processed_image'
                );
                
                $this->info('✅ Изображение успешно обработано!');
                $this->line('📊 Результаты обработки:');
                $this->line('- Оригинальный размер: ' . $uploadedFile->getSize() . ' байт');
                $this->line('- Обработанный размер: ' . $result['original']['file_size'] . ' байт');
                $this->line('- Экономия: ' . round((1 - $result['original']['file_size'] / $uploadedFile->getSize()) * 100, 2) . '%');
                $this->line('- Формат изменен: ' . ($result['original']['format_changed'] ? 'Да' : 'Нет'));
                $this->line('- Создано превью: ' . count($result['thumbnails']));
                
                // Проверяем созданные файлы
                $this->info('📁 Проверка созданных файлов:');
                
                if (Storage::disk('public')->exists($result['original']['path'])) {
                    $this->line('✅ Основной файл: ' . $result['original']['path']);
                } else {
                    $this->error('❌ Основной файл не найден: ' . $result['original']['path']);
                }
                
                foreach ($result['thumbnails'] as $size => $thumbnail) {
                    if (Storage::disk('public')->exists($thumbnail['path'])) {
                        $this->line('✅ Превью ' . $size . ': ' . $thumbnail['path']);
                    } else {
                        $this->error('❌ Превью ' . $size . ' не найдено: ' . $thumbnail['path']);
                    }
                }
                
                // Тестируем удаление
                $this->info('🗑️ Тестирование удаления файлов...');
                $deleted = $imageProcessingService->deleteImageWithThumbnails($result['original']['path']);
                
                if ($deleted) {
                    $this->info('✅ Файлы успешно удалены');
                } else {
                    $this->error('❌ Ошибка удаления файлов');
                }
                
                // Очищаем тестовый файл
                if (file_exists($testImagePath)) {
                    unlink($testImagePath);
                }
                
            } else {
                $this->error('❌ Не удалось создать тестовое изображение');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Ошибка при тестировании: ' . $e->getMessage());
            $this->error('Стек ошибки: ' . $e->getTraceAsString());
            return 1;
        }
        
        $this->info('🎉 Тестирование завершено успешно!');
        return 0;
    }
    
    /**
     * Создать простое тестовое изображение
     */
    private function createTestImage(string $path): void
    {
        // Создаем простое изображение 200x200
        $image = imagecreatetruecolor(200, 200);
        
        // Заливаем синим цветом
        $blue = imagecolorallocate($image, 0, 100, 200);
        imagefill($image, 0, 0, $blue);
        
        // Добавляем текст
        $white = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 5, 50, 90, 'TEST', $white);
        
        // Сохраняем как JPEG
        imagejpeg($image, $path, 90);
        imagedestroy($image);
    }
}
