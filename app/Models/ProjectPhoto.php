<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPhoto extends Model
{
    use HasFactory;
    
    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'filename',
        'original_name',
        'path',
        'category',
        'location',
        'comment',
        'photo_date',
        'file_size',
        'original_file_size',
        'mime_type',
        'file_hash',
        'is_optimized',
        'optimization_data',
    ];
    
    /**
     * Преобразование атрибутов
     *
     * @var array<string, string>
     */
    protected $casts = [
        'photo_date' => 'date',
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
     * Получить URL фотографии
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Получить исходное имя файла
     */
    public function getOriginalNameAttribute($value)
    {
        return $value ?: $this->filename;
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
     * Получить форматированный размер файла
     */
    public function getFormattedSizeAttribute()
    {
        return $this->formatFileSize($this->file_size);
    }

    /**
     * Получить форматированный оригинальный размер файла
     */
    public function getFormattedOriginalSizeAttribute()
    {
        if ($this->original_file_size) {
            return $this->formatFileSize($this->original_file_size);
        }
        
        return $this->formatted_size;
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
}
