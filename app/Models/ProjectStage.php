<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjectStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'duration_days',
        'order',
        'progress',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'progress' => 'decimal:2',
    ];

    // Связи
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Статусы этапов
    public static function getStatuses()
    {
        return [
            'not_started' => 'Не начато',
            'in_progress' => 'В работе',
            'completed' => 'Завершено',
            'on_hold' => 'Приостановлено',
        ];
    }

    // Получить цвет статуса
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'not_started' => 'secondary',
            'in_progress' => 'primary',
            'completed' => 'success',
            'on_hold' => 'warning',
            default => 'secondary'
        };
    }

    // Получить название статуса
    public function getStatusNameAttribute()
    {
        return self::getStatuses()[$this->status] ?? 'Неизвестно';
    }

    // Проверить просрочен ли этап
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'completed' || !$this->planned_end_date) {
            return false;
        }
        
        return now()->format('Y-m-d') > $this->planned_end_date->format('Y-m-d');
    }
}
