<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    // Константы статусов
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_RECEIVED = 'received';
    const STATUS_VIEWED = 'viewed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ARCHIVED = 'archived';

    // Константы приоритетов
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Константы типов получателей
    const RECIPIENT_USER = 'user';
    const RECIPIENT_CLIENT = 'client';
    const RECIPIENT_EXTERNAL = 'external';

    // Константы статусов подписи
    const SIGNATURE_NOT_REQUIRED = 'not_required';
    const SIGNATURE_PENDING = 'pending';
    const SIGNATURE_SIGNED = 'signed';
    const SIGNATURE_REJECTED = 'rejected';

    protected $fillable = [
        'title',
        'description',
        'content',
        'document_type',
        'category',
        'template_id',
        'project_id',
        'created_by',
        'assigned_to',
        'recipient_type',
        'recipient_id',
        'recipient_email',
        'recipient_name',
        'recipient_phone',
        'status',
        'priority',
        'is_internal',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'file_hash',
        'signature_required',
        'signature_status',
        'signature_data',
        'digital_signature',
        'signature_certificate',
        'version',
        'parent_id',
        'parent_document_id',
        'is_current_version',
        'can_be_deleted',
        'rejection_reason',
        'rejected_at',
        'delivery_status',
        'sent_at',
        'received_at',
        'viewed_at',
        'signed_at',
        'expires_at',
        'archived_at',
        'metadata',
        'template_variables',
        'notes',
        'amount',
        'currency',
    ];

    protected $casts = [
        'signature_data' => 'array',
        'metadata' => 'array',
        'template_variables' => 'array',
        'delivery_status' => 'array',
        'signature_required' => 'boolean',
        'is_internal' => 'boolean',
        'is_current_version' => 'boolean',
        'can_be_deleted' => 'boolean',
        'file_size' => 'integer',
        'version' => 'integer',
        'amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'viewed_at' => 'datetime',
        'signed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expires_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Связь с шаблоном документа
     */
    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Связь с создателем документа
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Связь с назначенным пользователем
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Связь с родительским документом (для версий)
     */
    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    /**
     * Связь с дочерними документами (версиями)
     */
    public function versions()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    /**
     * Связь с вложениями
     */
    public function attachments()
    {
        return $this->hasMany(DocumentAttachment::class, 'document_id');
    }

    /**
     * Связь с историей изменений
     */
    public function history()
    {
        return $this->hasMany(DocumentHistory::class, 'document_id');
    }

    /**
     * Связь с разрешениями
     */
    public function permissions()
    {
        return $this->hasMany(DocumentPermission::class, 'document_id');
    }

    /**
     * Связь с комментариями
     */
    public function comments()
    {
        return $this->hasMany(DocumentComment::class, 'document_id');
    }

    /**
     * Связь с запросами на подпись
     */
    public function signatureRequests()
    {
        return $this->hasMany(SignatureRequest::class, 'document_id');
    }

    /**
     * Связь с активным запросом на подпись
     */
    public function activeSignatureRequest()
    {
        return $this->hasOne(SignatureRequest::class, 'document_id')
                    ->where('status', 'pending')
                    ->latest();
    }

    /**
     * Scope для черновиков
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope для отправленных документов
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope для активных версий
     */
    public function scopeCurrentVersions($query)
    {
        return $query->where('is_current_version', true);
    }

    /**
     * Scope по типу документа
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope по приоритету
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope для документов, требующих подписи
     */
    public function scopeRequiringSignature($query)
    {
        return $query->where('signature_required', true);
    }

    /**
     * Scope для внутренних документов
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope для внешних документов
     */
    public function scopeExternal($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Проверка, просрочен ли документ
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Проверка, подписан ли документ
     */
    public function isSigned()
    {
        return $this->signature_status === self::SIGNATURE_SIGNED;
    }

    /**
     * Проверка, отклонен ли документ
     */
    public function isRejected()
    {
        return $this->signature_status === self::SIGNATURE_REJECTED;
    }

    /**
     * Проверка, можно ли удалить документ
     */
    public function canBeDeleted()
    {
        // Если документ отправлялся на подпись, его нельзя удалить
        if ($this->signatureRequests()->count() > 0) {
            return false;
        }
        
        return $this->can_be_deleted;
    }

    /**
     * Проверка, был ли документ когда-либо отправлен на подпись
     */
    public function wasEverSentForSignature()
    {
        return $this->signatureRequests()->count() > 0;
    }

    /**
     * Отправить документ на подпись
     */
    public function sendForSignature($recipientData, $message = null)
    {
        // Создаем запрос на подпись
        $signatureRequest = $this->signatureRequests()->create([
            'sender_id' => auth()->id(),
            'recipient_id' => $recipientData['recipient_id'] ?? null,
            'recipient_phone' => $recipientData['recipient_phone'] ?? null,
            'recipient_name' => $recipientData['recipient_name'] ?? null,
            'status' => 'pending',
            'message' => $message,
            'requested_at' => now(),
            'expires_at' => now()->addDays(30), // 30 дней на подпись
        ]);

        // Обновляем статус документа
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
            'can_be_deleted' => false, // Больше нельзя удалить
            'recipient_type' => $recipientData['recipient_type'] ?? 'external',
            'recipient_id' => $recipientData['recipient_id'] ?? null,
            'recipient_phone' => $recipientData['recipient_phone'] ?? null,
            'recipient_name' => $recipientData['recipient_name'] ?? null,
            'recipient_email' => $recipientData['recipient_email'] ?? null,
        ]);

        return $signatureRequest;
    }

    /**
     * Подписать документ
     */
    public function signDocument($signatureData)
    {
        $this->update([
            'signature_status' => self::SIGNATURE_SIGNED,
            'signed_at' => now(),
            'signature_data' => $signatureData,
            'status' => self::STATUS_COMPLETED,
        ]);

        // Обновляем активный запрос на подпись
        $this->activeSignatureRequest()?->update([
            'status' => 'signed',
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Отклонить подпись документа
     */
    public function rejectSignature($reason = null)
    {
        $this->update([
            'signature_status' => self::SIGNATURE_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Обновляем активный запрос на подпись
        $this->activeSignatureRequest()?->update([
            'status' => 'rejected',
            'completed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $this;
    }

    /**
     * Проверка, является ли документ черновиком
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Получить все доступные статусы
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_SENT => 'Отправлен',
            self::STATUS_RECEIVED => 'Получен',
            self::STATUS_VIEWED => 'Просмотрен',
            self::STATUS_COMPLETED => 'Завершен',
            self::STATUS_ARCHIVED => 'Архивирован',
        ];
    }

    /**
     * Получить все доступные приоритеты
     */
    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW => 'Низкий',
            self::PRIORITY_NORMAL => 'Обычный',
            self::PRIORITY_HIGH => 'Высокий',
            self::PRIORITY_URGENT => 'Срочный',
        ];
    }

    /**
     * Получить цвет статуса для отображения
     */
    public function getStatusColor()
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return 'secondary';
            case self::STATUS_SENT:
                return 'primary';
            case self::STATUS_RECEIVED:
                return 'info';
            case self::STATUS_VIEWED:
                return 'warning';
            case self::STATUS_COMPLETED:
                return 'success';
            case self::STATUS_ARCHIVED:
                return 'dark';
            default:
                return 'secondary';
        }
    }

    /**
     * Получить текстовую метку статуса
     */
    public function getStatusLabel()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Получить цвет статуса подписи
     */
    public function getSignatureStatusColor()
    {
        switch ($this->signature_status) {
            case self::SIGNATURE_NOT_REQUIRED:
                return 'secondary';
            case self::SIGNATURE_PENDING:
                return 'warning';
            case self::SIGNATURE_SIGNED:
                return 'success';
            case self::SIGNATURE_REJECTED:
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Получить текстовую метку статуса подписи
     */
    public function getSignatureStatusLabel()
    {
        switch ($this->signature_status) {
            case self::SIGNATURE_NOT_REQUIRED:
                return 'Подпись не требуется';
            case self::SIGNATURE_PENDING:
                return 'Ожидает подписи';
            case self::SIGNATURE_SIGNED:
                return 'Подписан';
            case self::SIGNATURE_REJECTED:
                return 'Отклонен';
            default:
                return $this->signature_status;
        }
    }

    /**
     * Получить иконку файла
     */
    public function getFileIcon()
    {
        if (!$this->original_filename) {
            return 'file';
        }

        $extension = strtolower(pathinfo($this->original_filename, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'pdf';
            case 'doc':
            case 'docx':
                return 'word';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'image';
            case 'txt':
                return 'alt';
            default:
                return 'file';
        }
    }

    /**
     * Получить отформатированный размер файла
     */
    public function getFormattedFileSize()
    {
        if (!$this->file_size) {
            return 'Неизвестно';
        }

        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Проверить, может ли пользователь подписать документ
     */
    public function canSignDocument()
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Может подписать, если:
        // 1. Документ требует подписи
        // 2. Статус подписи - ожидает
        // 3. Пользователь указан как получатель по ID или по телефону/email
        return $this->signature_required 
            && $this->signature_status === self::SIGNATURE_PENDING
            && (
                $this->recipient_id === $user->id 
                || $this->recipient_phone === $user->phone
                || $this->recipient_email === $user->email
            );
    }
}
