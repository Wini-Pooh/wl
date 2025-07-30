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
        'mime_type',
        'file_hash',
    ];
    
    /**
     * Преобразование атрибутов
     *
     * @var array<string, string>
     */
    protected $casts = [
        'photo_date' => 'date',
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
}
