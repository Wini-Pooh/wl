<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doc extends Model
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
        'recipient_email',
        'recipient_name',
        'sender_id',
        'status',
        'message',
        'signature_status',
        'signature',
        'sent_at',
        'signed_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    // Константы статусов
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_RECEIVED = 'received';

    // Константы статусов подписи
    const SIGNATURE_STATUS_PENDING = 'pending';
    const SIGNATURE_STATUS_SIGNED = 'signed';
    const SIGNATURE_STATUS_REJECTED = 'rejected';

    /**
     * Отправитель документа
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Получатель документа (если это пользователь системы)
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Проект, к которому относится документ
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Шаблон документа
     */
    public function template()
    {
        return $this->belongsTo(DocTemplate::class, 'template_id');
    }

    /**
     * Вложения документа
     */
    public function attachments()
    {
        return $this->hasMany(DocAttachment::class);
    }
}
