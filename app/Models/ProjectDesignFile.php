<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectDesignFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'original_name',
        'file_path',
        'file_size',
        'original_file_size',
        'mime_type',
        'design_type',
        'room',
        'style',
        'stage',
        'designer',
        'software',
        'description',
        'uploaded_by',
        'is_optimized',
        'optimization_data',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'original_file_size' => 'integer',
        'is_optimized' => 'boolean',
        'optimization_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Связь с пользователем, который загрузил файл
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Получить URL файла
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Получить полный путь к файлу
     */
    public function getFullPathAttribute()
    {
        return Storage::path($this->file_path);
    }

    /**
     * Проверить, является ли файл изображением
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Получить отформатированный размер файла
     */
    public function getFormattedSizeAttribute()
    {
        return $this->formatFileSize($this->file_size);
    }

    /**
     * Получить отформатированный оригинальный размер файла
     */
    public function getFormattedOriginalSizeAttribute()
    {
        if ($this->original_file_size) {
            return $this->formatFileSize($this->original_file_size);
        }
        
        return $this->formatted_size;
    }

    /**
     * Получить экономию места в процентах
     */
    public function getCompressionRatioAttribute()
    {
        if ($this->original_file_size && $this->original_file_size > 0) {
            return round((1 - $this->file_size / $this->original_file_size) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Получить URL миниатюры
     */
    public function getThumbnailUrlAttribute($size = 'medium')
    {
        if ($this->is_optimized && isset($this->optimization_data['thumbnails'][$size])) {
            return asset('storage/' . $this->optimization_data['thumbnails'][$size]['path']);
        }
        
        // Возвращаем оригинальное изображение, если миниатюры нет
        return $this->url;
    }

    /**
     * Форматировать размер файла
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Получить название типа дизайна
     */
    public function getDesignTypeNameAttribute()
    {
        $types = [
            '3d' => '3D визуализация',
            'layout' => 'Планировка',
            'sketch' => 'Эскиз',
            'render' => 'Рендер',
            'draft' => 'Черновик',
            'concept' => 'Концепт',
            'mood_board' => 'Мудборд',
            'color_scheme' => 'Цветовая схема',
            'furniture' => 'Мебель',
            'lighting' => 'Освещение',
            'materials' => 'Материалы',
            'final' => 'Финальный дизайн',
        ];

        return $types[$this->design_type] ?? $this->design_type;
    }

    /**
     * Получить название помещения
     */
    public function getRoomNameAttribute()
    {
        $rooms = [
            'kitchen' => 'Кухня',
            'living_room' => 'Гостиная',
            'bedroom' => 'Спальня',
            'bathroom' => 'Ванная',
            'hallway' => 'Прихожая',
            'office' => 'Кабинет',
            'children' => 'Детская',
            'general' => 'Общий план',
            'other' => 'Другое',
        ];

        return $rooms[$this->room] ?? $this->room;
    }

    /**
     * Получить название стиля
     */
    public function getStyleNameAttribute()
    {
        $styles = [
            'modern' => 'Современный',
            'classic' => 'Классический',
            'minimalist' => 'Минимализм',
            'loft' => 'Лофт',
            'scandinavian' => 'Скандинавский',
            'provence' => 'Прованс',
            'high_tech' => 'Хай-тек',
            'eco' => 'Эко',
            'other' => 'Другое',
        ];

        return $styles[$this->style] ?? $this->style;
    }

    /**
     * Удалить файл при удалении модели
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($designFile) {
            if (Storage::exists($designFile->file_path)) {
                Storage::delete($designFile->file_path);
            }
        });
    }
}
