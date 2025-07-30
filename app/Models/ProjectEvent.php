<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'type',
        'event_date',
        'event_time',
        'status',
        'location',
        'notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
    ];

    // Связи
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Типы событий
    public static function getTypes()
    {
        return [
            'meeting' => 'Встреча',
            'delivery' => 'Доставка',
            'inspection' => 'Проверка',
            'milestone' => 'Веха',
            'other' => 'Другое',
        ];
    }

    // Статусы событий
    public static function getStatuses()
    {
        return [
            'planned' => 'Запланировано',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
        ];
    }

    // Получить цвет типа события
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'meeting' => 'primary',
            'delivery' => 'info',
            'inspection' => 'warning',
            'milestone' => 'success',
            'other' => 'secondary',
            default => 'secondary'
        };
    }

    // Получить цвет статуса
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'planned' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    // Получить название типа
    public function getTypeNameAttribute()
    {
        return self::getTypes()[$this->type] ?? 'Неизвестно';
    }

    // Получить название статуса
    public function getStatusNameAttribute()
    {
        return self::getStatuses()[$this->status] ?? 'Неизвестно';
    }

    // Проверить, прошло ли событие
    public function getIsPastAttribute()
    {
        return now()->format('Y-m-d') > $this->event_date;
    }

    // Проверить, сегодня ли событие
    public function getIsTodayAttribute()
    {
        return now()->format('Y-m-d') === $this->event_date;
    }
}
