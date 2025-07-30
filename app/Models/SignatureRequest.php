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
        'status',
        'signature_type',
        'message',
        'requested_at',
        'completed_at',
        'expires_at',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Статусы запроса
    const STATUS_PENDING = 'pending';
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
            self::STATUS_COMPLETED => 'Подписан',
            self::STATUS_CANCELLED => 'Отменен',
            self::STATUS_EXPIRED => 'Просрочен',
        ];

        return $statuses[$this->status] ?? $this->status;
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
