<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Модель финансовых обязательств сотрудников
 * 
 * @property int $id
 * @property int $employee_id
 * @property int|null $project_id
 * @property string $type
 * @property float $amount
 * @property string $status
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $paid_date
 * @property string|null $description
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\Project|null $project
 * @property-read string $type_label
 * @property-read string $status_label
 * @property-read string $formatted_amount
 */
class EmployeeFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'amount',
        'currency',
        'title',
        'description',
        'status',
        'due_date',
        'paid_date',
        'project_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    // Связи
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Скопы
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                     ->where('due_date', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('status', 'pending')
                     ->where('due_date', '<=', now()->addDays($days));
    }

    // Статические методы
    public static function getTypes()
    {
        return [
            'salary' => 'Зарплата',
            'bonus' => 'Премия',
            'penalty' => 'Штраф',
            'expense' => 'Расход',
            'debt' => 'Долг',
        ];
    }

    public static function getStatuses()
    {
        return [
            'pending' => 'Ожидает выплаты',
            'paid' => 'Выплачено',
            'overdue' => 'Просрочено',
        ];
    }

    // Аксессоры и мутаторы
    public function getTypeNameAttribute()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function getStatusNameAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function getDaysUntilDueAttribute()
    {
        if ($this->status !== 'pending') {
            return null;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', ' ') . ' ' . $this->currency;
    }

    // Методы
    public function markAsPaid($paidDate = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => $paidDate ?? now()->toDateString()
        ]);
    }

    public function markAsOverdue()
    {
        if ($this->status === 'pending' && $this->due_date < now()) {
            $this->update(['status' => 'overdue']);
        }
    }

    // Автоматическое обновление статуса при сохранении
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($finance) {
            // Автоматически помечаем как просроченное, если дата прошла
            if ($finance->status === 'pending' && $finance->due_date < now()) {
                $finance->status = 'overdue';
            }
        });
    }
}
