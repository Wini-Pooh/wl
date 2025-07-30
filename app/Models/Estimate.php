<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'type',
        'status',
        'data',
        'total_amount',
        'created_by',
        'updated_by',
        'description',
    ];

    protected $casts = [
        'data' => 'array',
        'total_amount' => 'decimal:2',
    ];

    // Типы смет
    public static function getTypes()
    {
        return [
            'main' => 'Основная смета работ',
            'additional' => 'Дополнительная смета',
            'materials' => 'Смета материалов',
        ];
    }

    // Статусы смет
    public static function getStatuses()
    {
        return [
            'draft' => 'Черновик',
            'pending' => 'На рассмотрении',
            'approved' => 'Утверждена',
            'rejected' => 'Отклонена',
            'in_progress' => 'В работе',
            'completed' => 'Выполнена',
        ];
    }

    // Связь с проектом
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Связь с создателем сметы
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Получить тип сметы в текстовом виде
    public function getTypeNameAttribute()
    {
        return self::getTypes()[$this->type] ?? 'Неизвестно';
    }

    // Получить статус сметы в текстовом виде
    public function getStatusNameAttribute()
    {
        return self::getStatuses()[$this->status] ?? 'Неизвестно';
    }

    // Получить цвет статуса
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'in_progress' => 'info',
            'completed' => 'primary',
            default => 'secondary',
        };
    }

    // Получить шаблон сметы по типу
    public static function getTemplateByType($type)
    {
        $path = storage_path('app/templates/estimates/' . $type . '.json');
        
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        
        return null;
    }
}
