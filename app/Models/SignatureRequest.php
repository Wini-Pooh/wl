<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'sender_id',
        'recipient_id',
        'recipient_phone',
        'recipient_name',
        'status',
        'signature_type',
        'message',
        'requested_at',
        'completed_at',
        'expires_at',
        'rejection_reason',
        'rejected_at',
        'notification_settings',
        'last_reminder_sent_at',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
        'notification_settings' => 'array',
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Статусы запроса
    const STATUS_PENDING = 'pending';
    const STATUS_SIGNED = 'signed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Типы подписи
    const TYPE_SIMPLE = 'simple';
    const TYPE_ENHANCED = 'enhanced';
    const TYPE_QUALIFIED = 'qualified';

    /**
     * Связь с документом
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Связь с отправителем
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Связь с получателем
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Получить название статуса
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 'Ожидает подписи',
            self::STATUS_SIGNED => 'Подписан',
            self::STATUS_REJECTED => 'Отклонен',
            self::STATUS_COMPLETED => 'Подписан',
            self::STATUS_CANCELLED => 'Отменен',
            self::STATUS_EXPIRED => 'Просрочен',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Получить отображаемое имя получателя
     */
    public function getRecipientDisplayNameAttribute()
    {
        if ($this->recipient) {
            return $this->recipient->name;
        }
        
        return $this->recipient_name ?: $this->recipient_phone;
    }

    /**
     * Проверка, можно ли подписать
     */
    public function canBeSigned()
    {
        return $this->status === self::STATUS_PENDING && !$this->isExpired();
    }

    /**
     * Отклонить запрос
     */
    public function reject($reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'completed_at' => now(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $this;
    }

    /**
     * Подписать запрос
     */
    public function sign()
    {
        $this->update([
            'status' => self::STATUS_SIGNED,
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Отправить напоминание
     */
    public function sendReminder()
    {
        if (!$this->canBeSigned()) {
            return false;
        }

        $this->update([
            'last_reminder_sent_at' => now(),
        ]);

        // Здесь можно добавить логику отправки SMS/Email напоминания

        return true;
    }

    /**
     * Получить название типа подписи
     */
    public function getSignatureTypeNameAttribute()
    {
        $types = [
            self::TYPE_SIMPLE => 'Простая ЭЦП',
            self::TYPE_ENHANCED => 'Усиленная неквалифицированная ЭЦП',
            self::TYPE_QUALIFIED => 'Усиленная квалифицированная ЭЦП',
        ];

        return $types[$this->signature_type] ?? $this->signature_type;
    }

    /**
     * Проверить, истек ли запрос
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Скопы
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('recipient_id', $userId);
    }
}
