<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'document_type',
        'template_id',
        'project_id',
        'recipient_type',
        'recipient_id',
        'sender_id',
        'status',
        'file_path',
        'file_size',
        'mime_type',
        'original_name',
        'is_template',
        'template_data',
        'signature_required',
        'signature_status',
        'signature_data',
        'digital_signature',
        'signature_certificate',
        'sent_at',
        'signed_at',
        'expires_at',
        'metadata'
    ];

    protected $casts = [
        'template_data' => 'array',
        'signature_data' => 'array',
        'metadata' => 'array',
        'is_template' => 'boolean',
        'signature_required' => 'boolean',
        'sent_at' => 'datetime',
        'signed_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Статусы документов
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_RECEIVED = 'received';
    const STATUS_SIGNED = 'signed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REJECTED = 'rejected';

    // Типы документов
    const TYPE_CONTRACT = 'contract';
    const TYPE_ACT = 'act';
    const TYPE_INVOICE = 'invoice';
    const TYPE_ESTIMATE = 'estimate';
    const TYPE_TECHNICAL = 'technical';
    const TYPE_OTHER = 'other';

    // Типы получателей
    const RECIPIENT_TYPE_USER = 'user';
    const RECIPIENT_TYPE_CLIENT = 'client';
    const RECIPIENT_TYPE_EXTERNAL = 'external';

    // Статусы подписи
    const SIGNATURE_STATUS_NOT_REQUIRED = 'not_required';
    const SIGNATURE_STATUS_PENDING = 'pending';
    const SIGNATURE_STATUS_SIGNED = 'signed';
    const SIGNATURE_STATUS_REJECTED = 'rejected';
    const SIGNATURE_STATUS_EXPIRED = 'expired';

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Связь с отправителем
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Связь с получателем (полиморфная)
     */
    public function recipient()
    {
        return $this->morphTo();
    }

    /**
     * Связь с шаблоном
     */
    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Связь с запросами на подпись
     */
    public function signatureRequests()
    {
        return $this->hasMany(SignatureRequest::class);
    }

    /**
     * Получить URL файла
     */
    public function getUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Получить отформатированный размер файла
     */
    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) return '';
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Получить название статуса
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_SENT => 'Отправлен',
            self::STATUS_RECEIVED => 'Получен',
            self::STATUS_SIGNED => 'Подписан',
            self::STATUS_EXPIRED => 'Просрочен',
            self::STATUS_REJECTED => 'Отклонен',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Получить название типа документа
     */
    public function getTypeNameAttribute()
    {
        $types = [
            self::TYPE_CONTRACT => 'Договор',
            self::TYPE_ACT => 'Акт',
            self::TYPE_INVOICE => 'Счет',
            self::TYPE_ESTIMATE => 'Смета',
            self::TYPE_TECHNICAL => 'Техническая документация',
            self::TYPE_OTHER => 'Прочее',
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }

    /**
     * Получить название статуса подписи
     */
    public function getSignatureStatusNameAttribute()
    {
        $statuses = [
            self::SIGNATURE_STATUS_NOT_REQUIRED => 'Подпись не требуется',
            self::SIGNATURE_STATUS_PENDING => 'Ожидает подписи',
            self::SIGNATURE_STATUS_SIGNED => 'Подписан',
            self::SIGNATURE_STATUS_REJECTED => 'Отклонен',
            self::SIGNATURE_STATUS_EXPIRED => 'Просрочен',
        ];

        return $statuses[$this->signature_status] ?? $this->signature_status;
    }

    /**
     * Проверить, требуется ли подпись
     */
    public function requiresSignature()
    {
        return $this->signature_required && 
               $this->signature_status !== self::SIGNATURE_STATUS_SIGNED;
    }

    /**
     * Проверить, подписан ли документ
     */
    public function isSigned()
    {
        return $this->signature_status === self::SIGNATURE_STATUS_SIGNED;
    }

    /**
     * Проверить, истек ли срок подписи
     */
    public function isSignatureExpired()
    {
        return $this->expires_at && 
               $this->expires_at->isPast() && 
               !$this->isSigned();
    }

    /**
     * Создать цифровую подпись
     */
    public function createDigitalSignature($privateKey, $certificate)
    {
        // Здесь будет реализация создания ЭЦП по ГОСТ Р 34.10-2012
        // Пока заглушка
        $documentHash = hash('sha256', $this->content);
        
        $signatureData = [
            'algorithm' => 'GOST R 34.10-2012',
            'hash_algorithm' => 'GOST R 34.11-2012',
            'document_hash' => $documentHash,
            'timestamp' => now()->toISOString(),
            'certificate_info' => $certificate,
        ];

        $this->update([
            'digital_signature' => base64_encode(json_encode($signatureData)),
            'signature_certificate' => $certificate,
            'signature_status' => self::SIGNATURE_STATUS_SIGNED,
            'signed_at' => now(),
        ]);

        return true;
    }

    /**
     * Проверить цифровую подпись
     */
    public function verifyDigitalSignature()
    {
        if (!$this->digital_signature) {
            return false;
        }

        try {
            $signatureData = json_decode(base64_decode($this->digital_signature), true);
            $currentHash = hash('sha256', $this->content);
            
            return $signatureData['document_hash'] === $currentHash;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Скопы для фильтрации документов
     */
    public function scopeReceived($query)
    {
        return $query->where('status', self::STATUS_RECEIVED);
    }

    public function scopeCreated($query)
    {
        return $query->where('status', '!=', self::STATUS_RECEIVED);
    }

    public function scopeSigned($query)
    {
        return $query->where('signature_status', self::SIGNATURE_STATUS_SIGNED);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere(function($subQ) use ($userId) {
                  $subQ->where('recipient_type', self::RECIPIENT_TYPE_USER)
                       ->where('recipient_id', $userId);
              });
        });
    }
}
