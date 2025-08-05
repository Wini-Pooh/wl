<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'scheme_type',
        'room',
        'scale',
        'revision', // В БД хранится как revision, но в форме передается как version
        'engineer',
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
     * Получить название типа схемы
     */
    public function getSchemeTypeNameAttribute()
    {
        $types = [
            'floor_plan' => 'Планировка',
            'electrical' => 'Электрическая схема',
            'plumbing' => 'Сантехническая схема',
            'heating' => 'Отопление',
            'ventilation' => 'Вентиляция',
            'technical' => 'Техническая схема',
            'construction' => 'Строительный чертеж',
        ];

        return $types[$this->scheme_type] ?? $this->scheme_type;
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
            'general' => 'Общий план',
            'other' => 'Другое',
        ];

        return $rooms[$this->room] ?? $this->room;
    }

    /**
     * Удалить файл при удалении модели
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($scheme) {
            if (Storage::exists($scheme->file_path)) {
                Storage::delete($scheme->file_path);
            }
        });
    }
}
