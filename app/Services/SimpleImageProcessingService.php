<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Простой сервис для обработки изображений без внешних библиотек
 * Работает только с новыми загрузками, не трогает существующие файлы
 */
class SimpleImageProcessingService
{
    /**
     * Качество JPEG сжатия (0-100)
     */
    private int $jpegQuality;

    /**
     * Максимальная ширина изображения
     */
    private int $maxWidth;

    /**
     * Максимальная высота изображения
     */
    private int $maxHeight;

    /**
     * Разрешенные MIME типы изображений для обработки
     */
    private array $allowedImageTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif',
        'image/webp'
    ];

    public function __construct()
    {
        $this->jpegQuality = config('image_processing.webp_quality', 85);
        $this->maxWidth = config('image_processing.max_width', 1920);
        $this->maxHeight = config('image_processing.max_height', 1080);
    }

    /**
     * Обработать загруженное изображение (только базовая оптимизация)
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

        Log::info('Начинаем простую обработку изображения', [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'directory' => $directory
        ]);

        // Генерируем имя файла если не задано
        if (!$filename) {
            $filename = Str::uuid();
        }

        // Создаем основное изображение с базовой оптимизацией
        $originalInfo = $this->createOptimizedImage($file, $directory, $filename);

        $result = [
            'original' => $originalInfo,
            'thumbnails' => [], // Пока не создаем миниатюры
            'optimized' => $originalInfo['optimized'],
            'format_converted' => false
        ];

        Log::info('Обработка изображения завершена', [
            'original_size' => $file->getSize(),
            'final_size' => $originalInfo['file_size'],
            'optimized' => $originalInfo['optimized']
        ]);

        return $result;
    }

    /**
     * Создать оптимизированное изображение с базовым сжатием
     */
    private function createOptimizedImage(UploadedFile $file, string $directory, string $filename): array
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $finalFilename = $filename . '.' . $extension;
            $fullPath = $directory . '/' . $finalFilename;

            // Получаем информацию об изображении
            $imageInfo = getimagesize($file->getRealPath());
            if (!$imageInfo) {
                throw new \Exception('Не удалось получить информацию об изображении');
            }

            list($width, $height, $type) = $imageInfo;
            
            // Проверяем, нужно ли изменять размер
            $needResize = $width > $this->maxWidth || $height > $this->maxHeight;
            $optimized = false;

            if ($needResize) {
                // Рассчитываем новые размеры
                $newDimensions = $this->calculateOptimalDimensions($width, $height);
                $optimizedImage = $this->resizeImage($file->getRealPath(), $newDimensions, $type);
                
                if ($optimizedImage) {
                    // Сохраняем оптимизированное изображение
                    Storage::disk('public')->put($fullPath, $optimizedImage);
                    $optimized = true;
                    
                    Log::debug('Изображение изменено', [
                        'original' => "{$width}x{$height}",
                        'new' => "{$newDimensions['width']}x{$newDimensions['height']}"
                    ]);
                } else {
                    // Если не удалось изменить размер, сохраняем оригинал
                    $file->storeAs($directory, $finalFilename, 'public');
                }
            } else {
                // Сохраняем файл как есть
                $file->storeAs($directory, $finalFilename, 'public');
            }

            // Получаем размер сохраненного файла
            $savedFileSize = Storage::disk('public')->size($fullPath);
            
            return [
                'filename' => $finalFilename,
                'path' => $fullPath,
                'url' => Storage::url($fullPath),
                'file_size' => $savedFileSize,
                'dimensions' => $needResize ? $newDimensions : ['width' => $width, 'height' => $height],
                'optimized' => $optimized,
                'mime_type' => $file->getMimeType()
            ];
            
        } catch (\Exception $e) {
            Log::error('Ошибка при обработке изображения', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            // В случае ошибки сохраняем оригинал
            $extension = strtolower($file->getClientOriginalExtension());
            $finalFilename = $filename . '.' . $extension;
            $fullPath = $directory . '/' . $finalFilename;
            
            $file->storeAs($directory, $finalFilename, 'public');
            
            return [
                'filename' => $finalFilename,
                'path' => $fullPath,
                'url' => Storage::url($fullPath),
                'file_size' => $file->getSize(),
                'dimensions' => ['width' => 0, 'height' => 0],
                'optimized' => false,
                'mime_type' => $file->getMimeType()
            ];
        }
    }

    /**
     * Изменить размер изображения используя GD
     */
    private function resizeImage(string $filePath, array $newDimensions, int $imageType): ?string
    {
        // Проверяем, доступна ли GD
        if (!extension_loaded('gd')) {
            return null;
        }

        try {
            // Создаем исходное изображение
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($filePath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($filePath);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($filePath);
                    break;
                default:
                    return null;
            }

            if (!$source) {
                return null;
            }

            // Создаем новое изображение
            $resized = imagecreatetruecolor($newDimensions['width'], $newDimensions['height']);
            
            // Для PNG сохраняем прозрачность
            if ($imageType === IMAGETYPE_PNG) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $newDimensions['width'], $newDimensions['height'], $transparent);
            }

            // Изменяем размер
            $originalWidth = imagesx($source);
            $originalHeight = imagesy($source);
            
            imagecopyresampled(
                $resized, $source,
                0, 0, 0, 0,
                $newDimensions['width'], $newDimensions['height'],
                $originalWidth, $originalHeight
            );

            // Выводим в буфер
            ob_start();
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    imagejpeg($resized, null, $this->jpegQuality);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($resized);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($resized);
                    break;
            }
            $imageData = ob_get_contents();
            ob_end_clean();

            // Освобождаем память
            imagedestroy($source);
            imagedestroy($resized);

            return $imageData;

        } catch (\Exception $e) {
            Log::error('Ошибка при изменении размера изображения', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);
            return null;
        }
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
     * Удалить изображение (простая версия)
     */
    public function deleteImageWithThumbnails(string $path): bool
    {
        try {
            // Удаляем основной файл
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::debug('Удален файл', ['path' => $path]);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении изображения', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
