<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProjectDocument extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'file_size' => 'integer',
        'document_date' => 'date',
        'is_signed' => 'boolean',
        'requires_signature' => 'boolean',
        'required_signatures_count' => 'integer',
        'received_signatures_count' => 'integer',
        'signature_deadline' => 'datetime',
        'signature_settings' => 'array',
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

    // Удалены связи с системой электронных подписей

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
     * Получить название типа документа
     */
    public function getDocumentTypeNameAttribute()
    {
        $types = [
            'contract' => 'Договор',
            'estimate' => 'Смета',
            'invoice' => 'Счет',
            'permit' => 'Разрешение',
            'certificate' => 'Сертификат',
            'report' => 'Отчет',
            'specification' => 'Спецификация',
            'manual' => 'Руководство',
            'other' => 'Другое',
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }

    /**
     * Получить название категории
     */
    public function getCategoryNameAttribute()
    {
        $categories = [
            'legal' => 'Юридические',
            'financial' => 'Финансовые',
            'technical' => 'Технические',
            'administrative' => 'Административные',
            'other' => 'Другие',
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Получить статус подписи
     */
    public function getSignatureStatusAttribute()
    {
        return $this->getAttribute('is_signed') ? 'Подписан' : 'Не подписан';
    }

    /**
     * Получить статус подписи документа
     */
    public function getSignatureStatusNameAttribute()
    {
        $statuses = [
            'unsigned' => 'Не подписан',
            'partial' => 'Частично подписан',
            'fully_signed' => 'Полностью подписан',
        ];

        return $statuses[$this->getAttribute('signature_status')] ?? $this->getAttribute('signature_status');
    }

    /**
     * Проверить, требуется ли подпись
     */
    public function requiresSignature()
    {
        return $this->getAttribute('requires_signature');
    }

    /**
     * Проверить, полностью ли подписан документ
     */
    public function isFullySigned()
    {
        return $this->getAttribute('signature_status') === 'fully_signed';
    }

    /**
     * Проверить, истек ли срок подписи
     */
    public function isSignatureExpired()
    {
        return $this->getAttribute('signature_deadline') && 
               Carbon::parse($this->getAttribute('signature_deadline'))->isPast();
    }

    /**
     * Получить прогресс подписания
     */
    public function getSignatureProgressAttribute()
    {
        $required = $this->getAttribute('required_signatures_count');
        $received = $this->getAttribute('received_signatures_count');

        if ($required === 0) {
            return 0;
        }

        return round(($received / $required) * 100);
    }

    /**
     * Обновить статус подписи документа
     */
    public function updateSignatureStatus()
    {
        $signaturesCount = $this->signatures()->where('status', 'signed')->count();
        $this->received_signatures_count = $signaturesCount;

        if ($signaturesCount === 0) {
            $this->signature_status = 'unsigned';
        } elseif ($signaturesCount >= $this->required_signatures_count) {
            $this->signature_status = 'fully_signed';
            $this->is_signed = true;
        } else {
            $this->signature_status = 'partial';
        }

        $this->save();
    }

    /**
     * Удалить файл при удалении модели
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });
    }
}
