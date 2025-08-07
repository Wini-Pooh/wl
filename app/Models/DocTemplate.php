<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'document_type',
        'content',
        'variables',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Пользователь, создавший шаблон
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Документы, созданные по этому шаблону
     */
    public function docs()
    {
        return $this->hasMany(Doc::class, 'template_id');
    }
}
