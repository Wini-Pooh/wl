<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'parent_id',
        'content',
        'is_internal',
        'status',
        'mentions',
        'attachments',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'mentions' => 'array',
        'attachments' => 'array',
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
     * Связь с родительским комментарием
     */
    public function parent()
    {
        return $this->belongsTo(DocumentComment::class, 'parent_id');
    }

    /**
     * Связь с дочерними комментариями
     */
    public function replies()
    {
        return $this->hasMany(DocumentComment::class, 'parent_id');
    }

    /**
     * Scope для активных комментариев
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope для внутренних комментариев
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope для внешних комментариев
     */
    public function scopeExternal($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope для корневых комментариев
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
