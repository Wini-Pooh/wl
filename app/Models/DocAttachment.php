<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id',
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
        'size' => 'integer'
    ];

    /**
     * Документ, к которому относится вложение
     */
    public function doc()
    {
        return $this->belongsTo(Doc::class);
    }

    /**
     * Пользователь, который загрузил вложение
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Получить размер файла в человекочитаемом формате
     */
    public function getHumanFileSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Проверить существование файла
     */
    public function fileExists()
    {
        return Storage::exists($this->path);
    }

    /**
     * Получить URL для скачивания файла
     */
    public function getDownloadUrl()
    {
        return route('documents.attachment.download', $this->id);
    }

    /**
     * Проверить является ли файл изображением
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Получить иконку файла по его типу
     */
    public function getFileIcon()
    {
        $icons = [
            'application/pdf' => 'fas fa-file-pdf',
            'application/msword' => 'fas fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word',
            'application/vnd.ms-excel' => 'fas fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel',
            'application/vnd.ms-powerpoint' => 'fas fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fas fa-file-powerpoint',
            'application/zip' => 'fas fa-file-archive',
            'application/x-rar-compressed' => 'fas fa-file-archive',
            'text/plain' => 'fas fa-file-alt',
        ];

        if ($this->isImage()) {
            return 'fas fa-file-image';
        }

        return $icons[$this->mime_type] ?? 'fas fa-file';
    }

    /**
     * Удалить файл при удалении записи
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            if ($attachment->fileExists()) {
                Storage::delete($attachment->path);
            }
        });
    }
}
