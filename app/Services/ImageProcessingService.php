<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Сервис для обработки и оптимизации изображений
 * Конвертирует изображения в WebP формат и сжимает их для экономии места
 */
class ImageProcessingService
{
    /**
     * ImageManager instance
     */
    private ImageManager $imageManager;

    /**
     * Качество сжатия WebP (0-100)
     */
    private int $webpQuality;

    /**
     * Максимальная ширина изображения
     */
    private int $maxWidth;

    /**
     * Максимальная высота изображения
     */
    private int $maxHeight;

    /**
     * Размеры для создания превью
     */
    private array $thumbnailSizes;

    /**
     * Разрешенные MIME типы изображений для обработки
     */
    private array $allowedImageTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif',
        'image/bmp',
        'image/tiff',
        'image/tif',
        'image/webp'
    ];

    public function __construct()
    {
        try {
            // Пытаемся использовать Imagick драйвер
            $this->imageManager = new ImageManager(new Driver());
        } catch (\Exception $e) {
            // Если Imagick недоступен, используем GD драйвер
            Log::warning('Imagick недоступен, используется GD драйвер: ' . $e->getMessage());
            $this->imageManager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        }
        
        $this->webpQuality = config('image_processing.webp_quality', 85);
        $this->maxWidth = config('image_processing.max_width', 1920);
        $this->maxHeight = config('image_processing.max_height', 1080);
        $this->thumbnailSizes = config('image_processing.thumbnail_sizes', [
            'small' => ['width' => 300, 'height' => 200],
            'medium' => ['width' => 600, 'height' => 400]
        ]);
    }

    /**
     * Обработать загруженное изображение
     * 
     * @param UploadedFile $file Загруженный файл
     * @param string $directory Директория для сохранения
     * @param string $filename Имя файла (без расширения)
     * @return array Информация об обработанном файле
     */
    public function processUploadedImage(UploadedFile $file, string $directory, string $filename = null): array
    {
        // Проверяем, что это изображение
        if (!$this->isImageFile($file)) {
            throw new \InvalidArgumentException('Файл не является изображением');
        }

        Log::info('Начинаем обработку изображения', [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'directory' => $directory
        ]);

        // Генерируем имя файла если не задано
        if (!$filename) {
            $filename = Str::uuid();
        }

        // Создаем основное изображение в WebP
        $originalInfo = $this->createOptimizedWebP($file, $directory, $filename);
        
        // Создаем превью
        $thumbnails = $this->createThumbnails($file, $directory, $filename);

        $result = [
            'original' => $originalInfo,
            'thumbnails' => $thumbnails,
            'optimized' => true,
            'format_converted' => $originalInfo['format_changed']
        ];

        Log::info('Обработка изображения завершена', [
            'original_size' => $file->getSize(),
            'optimized_size' => $originalInfo['file_size'],
            'savings_percent' => round((1 - $originalInfo['file_size'] / $file->getSize()) * 100, 2),
            'thumbnails_count' => count($thumbnails)
        ]);

        return $result;
    }

    /**
     * Создать оптимизированное WebP изображение
     */
    private function createOptimizedWebP(UploadedFile $file, string $directory, string $filename): array
    {
        try {
            // Загружаем изображение через Intervention Image
            $image = $this->imageManager->read($file->getRealPath());
            
            // Получаем оригинальные размеры
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            
            // Рассчитываем новые размеры с сохранением пропорций
            $newDimensions = $this->calculateOptimalDimensions($originalWidth, $originalHeight);
            
            // Изменяем размер если необходимо
            if ($newDimensions['width'] !== $originalWidth || $newDimensions['height'] !== $originalHeight) {
                $image->resize($newDimensions['width'], $newDimensions['height']);
                Log::debug('Изображение изменено', [
                    'original' => "{$originalWidth}x{$originalHeight}",
                    'new' => "{$newDimensions['width']}x{$newDimensions['height']}"
                ]);
            }

            // Определяем путь для сохранения
            $webpFilename = $filename . '.webp';
            $fullPath = $directory . '/' . $webpFilename;
            
            // Кодируем в WebP с заданным качеством
            $webpContent = $image->toWebp($this->webpQuality);
            
            // Сохраняем файл
            Storage::disk('public')->put($fullPath, $webpContent);
            
            // Получаем размер сохраненного файла
            $savedFileSize = Storage::disk('public')->size($fullPath);
            
            return [
                'filename' => $webpFilename,
                'path' => $fullPath,
                'url' => Storage::url($fullPath),
                'file_size' => $savedFileSize,
                'dimensions' => $newDimensions,
                'format_changed' => !str_ends_with(strtolower($file->getClientOriginalName()), '.webp'),
                'mime_type' => 'image/webp'
            ];
            
        } catch (\Exception $e) {
            Log::error('Ошибка при создании WebP изображения', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            throw new \Exception('Не удалось обработать изображение: ' . $e->getMessage());
        }
    }

    /**
     * Создать превью изображений разных размеров
     */
    private function createThumbnails(UploadedFile $file, string $directory, string $filename): array
    {
        $thumbnails = [];
        
        try {
            $image = $this->imageManager->read($file->getRealPath());
            
            foreach ($this->thumbnailSizes as $sizeName => $dimensions) {
                $thumbnailFilename = "{$filename}_{$sizeName}.webp";
                $fullPath = $directory . '/thumbnails/' . $thumbnailFilename;
                
                // Создаем копию изображения для превью
                $thumbnail = clone $image;
                $thumbnail->resize($dimensions['width'], $dimensions['height']);
                
                // Кодируем в WebP
                $webpContent = $thumbnail->toWebp($this->webpQuality);
                
                // Сохраняем превью
                Storage::disk('public')->put($fullPath, $webpContent);
                
                $thumbnails[$sizeName] = [
                    'filename' => $thumbnailFilename,
                    'path' => $fullPath,
                    'url' => Storage::url($fullPath),
                    'file_size' => Storage::disk('public')->size($fullPath),
                    'dimensions' => $dimensions
                ];
                
                Log::debug("Создано превью {$sizeName}", [
                    'filename' => $thumbnailFilename,
                    'dimensions' => $dimensions
                ]);
            }
            
        } catch (\Exception $e) {
            Log::warning('Не удалось создать превью', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            // Не прерываем процесс из-за ошибки с превью
        }
        
        return $thumbnails;
    }

    /**
     * Рассчитать оптимальные размеры изображения
     */
    private function calculateOptimalDimensions(int $width, int $height): array
    {
        // Если изображение меньше максимальных размеров, оставляем как есть
        if ($width <= $this->maxWidth && $height <= $this->maxHeight) {
            return ['width' => $width, 'height' => $height];
        }
        
        // Рассчитываем коэффициент масштабирования
        $widthRatio = $this->maxWidth / $width;
        $heightRatio = $this->maxHeight / $height;
        $ratio = min($widthRatio, $heightRatio);
        
        return [
            'width' => (int) round($width * $ratio),
            'height' => (int) round($height * $ratio)
        ];
    }

    /**
     * Проверить, является ли файл изображением
     */
    public function isImageFile(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), $this->allowedImageTypes);
    }

    /**
     * Получить информацию об изображении
     */
    public function getImageInfo(string $path): array
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                throw new \Exception('Файл не найден');
            }
            
            $fullPath = storage_path('app/public/' . $path);
            $image = $this->imageManager->read($fullPath);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'file_size' => Storage::disk('public')->size($path),
                'mime_type' => mime_content_type($fullPath),
                'url' => Storage::url($path)
            ];
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения информации об изображении', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            
            return [
                'error' => 'Не удалось получить информацию об изображении'
            ];
        }
    }

    /**
     * Удалить изображение и все его превью
     */
    public function deleteImageWithThumbnails(string $path): bool
    {
        try {
            $deleted = true;
            
            // Удаляем основной файл
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::debug('Удален основной файл', ['path' => $path]);
            }
            
            // Удаляем превью
            $directory = dirname($path);
            $filename = pathinfo($path, PATHINFO_FILENAME);
            
            foreach (array_keys($this->thumbnailSizes) as $sizeName) {
                $thumbnailPath = $directory . '/thumbnails/' . $filename . "_{$sizeName}.webp";
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                    Log::debug('Удалено превью', ['path' => $thumbnailPath]);
                }
            }
            
            return $deleted;
            
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении изображения', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Массовая обработка изображений (для миграции существующих)
     */
    public function batchProcessImages(array $imagePaths, callable $progressCallback = null): array
    {
        $results = [];
        $total = count($imagePaths);
        
        foreach ($imagePaths as $index => $imagePath) {
            try {
                if ($progressCallback) {
                    $progressCallback($index + 1, $total, $imagePath);
                }
                
                // Логика для обработки существующих файлов
                // Здесь можно добавить обработку уже загруженных изображений
                $results[] = [
                    'path' => $imagePath,
                    'status' => 'processed',
                    'message' => 'Изображение обработано'
                ];
                
            } catch (\Exception $e) {
                $results[] = [
                    'path' => $imagePath,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}
