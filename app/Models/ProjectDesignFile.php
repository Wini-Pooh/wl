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
        'mime_type',
        'design_type',
        'room',
        'style',
        'stage',
        'designer',
        'software',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
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
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
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
