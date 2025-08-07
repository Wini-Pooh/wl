<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectDocument extends Model
{
    use HasFactory;
    
    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'original_name', 
        'file_path',
        'file_size',
        'mime_type',
        'document_type',
        'importance',
        'category',
        'version',
        'document_date',
        'author',
        'is_signed',
        'description',
        'uploaded_by',
        'requires_signature',
        'signature_status',
        'required_signatures_count',
        'received_signatures_count',
        'signature_deadline',
        'signature_settings',
        'is_template_generated',
        'template_type',
        'template_variables',
    ];
    
    /**
     * Преобразование атрибутов
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'is_signed' => 'boolean',
        'requires_signature' => 'boolean',
        'required_signatures_count' => 'integer',
        'received_signatures_count' => 'integer',
        'is_template_generated' => 'boolean',
        'signature_settings' => 'array',
        'template_variables' => 'array',
        'document_date' => 'date',
        'signature_deadline' => 'datetime',
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
     * Связь с пользователем, который загрузил документ
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
        return $this->file_path ? Storage::url($this->file_path) : null;
    }
    
    /**
     * Получить размер файла в человекочитаемом формате
     */
    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) {
            return 'Неизвестно';
        }
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Получить название типа документа
     */
    public function getDocumentTypeNameAttribute()
    {
        $types = self::getDocumentTypes();
        return $types[$this->document_type] ?? $this->document_type;
    }
    
    /**
     * Проверить, является ли файл изображением
     */
    public function isImage()
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml'
        ]);
    }
    
    /**
     * Проверить, является ли файл PDF
     */
    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }
    
    /**
     * Получить иконку для типа файла
     */
    public function getFileIconAttribute()
    {
        if ($this->isImage()) {
            return 'fas fa-image';
        }
        
        if ($this->isPdf()) {
            return 'fas fa-file-pdf';
        }
        
        switch ($this->mime_type) {
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return 'fas fa-file-excel';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'fas fa-file-word';
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                return 'fas fa-file-powerpoint';
            case 'application/zip':
            case 'application/x-rar-compressed':
            case 'application/x-7z-compressed':
                return 'fas fa-file-archive';
            case 'text/plain':
                return 'fas fa-file-alt';
            default:
                return 'fas fa-file';
        }
    }
    
    /**
     * Типы документов
     */
    public static function getDocumentTypes()
    {
        return [
            'contract' => 'Договор',
            'act' => 'Акт',
            'invoice' => 'Счет',
            'estimate' => 'Смета',
            'technical' => 'Техническая документация',
            'drawing' => 'Чертеж',
            'photo' => 'Фотография',
            'certificate' => 'Сертификат',
            'permit' => 'Разрешение',
            'other' => 'Прочее'
        ];
    }
    
    /**
     * Статусы документов
     */
    public static function getStatuses()
    {
        return [
            'draft' => 'Черновик',
            'review' => 'На рассмотрении',
            'approved' => 'Утвержден',
            'rejected' => 'Отклонен',
            'archived' => 'Архивирован'
        ];
    }
}
