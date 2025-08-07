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
        'category',
        'content',
        'variables',
        'default_values',
        'validation_rules',
        'is_active',
        'is_system',
        'file_format',
        'formatting_options',
        'created_by',
        'usage_count',
    ];

    protected $casts = [
        'variables' => 'array',
        'default_values' => 'array',
        'validation_rules' => 'array',
        'formatting_options' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь с пользователем, создавшим шаблон
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Связь с документами, созданными на основе этого шаблона
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'template_id');
    }

    /**
     * Scope для активных шаблонов
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для системных шаблонов
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope по типу документа
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope по категории
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Инкремент счетчика использования
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Извлечение переменных из содержимого шаблона
     */
    public function extractVariables()
    {
        if (empty($this->content)) {
            return [];
        }
        
        // Ищем все переменные в формате @{{variable_name}}
        preg_match_all('/@\{\{([^}]+)\}\}/', $this->content, $matches);
        
        if (empty($matches[1])) {
            return [];
        }
        
        // Возвращаем уникальные переменные
        return array_unique(array_map('trim', $matches[1]));
    }

    /**
     * Рендер шаблона с подстановкой переменных
     */
    public function renderTemplate($variables = [])
    {
        $content = $this->content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('@{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }
}
