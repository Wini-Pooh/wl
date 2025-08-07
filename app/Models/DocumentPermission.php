<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'permission',
        'granted',
        'granted_at',
        'granted_by',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь с документом
     */
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Связь с пользователем, предоставившим разрешение
     */
    public function grantor()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /**
     * Scope для активных разрешений
     */
    public function scopeGranted($query)
    {
        return $query->where('granted', true);
    }

    /**
     * Scope для неистекших разрешений
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Проверка, истекло ли разрешение
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
