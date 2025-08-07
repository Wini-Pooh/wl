<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'filename',
        'original_name',
        'path',
        'size',
        'mime_type',
        'file_hash',
        'metadata',
        'uploaded_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
    ];

    /**
     * Связь с документом
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Связь с пользователем, загрузившим файл
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
        return Storage::url($this->path);
    }

    /**
     * Получить размер в человекочитаемом формате
     */
    public function getHumanSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
