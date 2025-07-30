<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'data',
        'created_by',
        'description',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // Типы шаблонов (совпадают с типами смет)
    public static function getTypes()
    {
        return [
            'main' => 'Основная смета работ',
            'additional' => 'Дополнительная смета',
            'materials' => 'Смета материалов',
        ];
    }

    // Связь с создателем шаблона
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Получить тип шаблона в текстовом виде
    public function getTypeNameAttribute()
    {
        return self::getTypes()[$this->type] ?? 'Неизвестно';
    }

    // Получить шаблоны по типу для текущего пользователя
    public static function getTemplatesByType($type, $userId = null)
    {
        $query = self::where('type', $type);
        
        if ($userId) {
            $query->where('created_by', $userId);
        }
        
        return $query->get();
    }
}
