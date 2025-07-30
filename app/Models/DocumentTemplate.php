<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'document_type',
        'template_content',
        'variables',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь с создателем шаблона
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Связь с документами, созданными по этому шаблону
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'template_id');
    }

    /**
     * Заменить переменные в шаблоне
     */
    public function renderTemplate($variables = [])
    {
        $content = $this->template_content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Получить список переменных из шаблона
     */
    public function extractVariables()
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $this->template_content, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Скопы
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}
