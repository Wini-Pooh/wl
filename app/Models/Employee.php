<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Модель сотрудников партнера
 * 
 * @property int $id
 * @property int $partner_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property string $phone
 * @property string|null $email
 * @property string $role
 * @property string $status
 * @property string|null $description
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $partner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmployeeFinance[] $finances
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects
 * @property-read string $full_name
 * @property-read string $role_label
 * @property-read string $status_label
 * @property-read float $total_debt
 * @property-read float $total_paid
 * @property-read float $total_pending
 */
class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'email',
        'role',
        'status',
        'description',
        'notes',
        'is_right_hand',
    ];

    protected $casts = [
        'role' => 'string',
        'status' => 'string',
        'is_right_hand' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'short_name',
        'role_name',
        'status_name'
    ];

    // Связи
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function finances()
    {
        return $this->hasMany(EmployeeFinance::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'employee_projects');
    }

    // Аксессоры
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getShortNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Скопы
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Статические методы
    public static function getRoles()
    {
        return [
            'foreman' => 'Прораб',
            'subcontractor' => 'Субподрядчик',
            'estimator' => 'Сметчик',
        ];
    }

    public static function getStatuses()
    {
        return [
            'active' => 'Активен',
            'inactive' => 'Неактивен',
            'fired' => 'Уволен',
        ];
    }

    public function getRoleNameAttribute()
    {
        return self::getRoles()[$this->role] ?? $this->role;
    }

    public function getStatusNameAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    // Финансовые методы
    public function getTotalPendingAmount()
    {
        return $this->finances()
            ->where('status', 'pending')
            ->sum('amount');
    }

    public function getTotalPaidAmount()
    {
        return $this->finances()
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getOverdueAmount()
    {
        return $this->finances()
            ->where('status', 'overdue')
            ->orWhere(function($query) {
                $query->where('status', 'pending')
                      ->where('due_date', '<', now());
            })
            ->sum('amount');
    }

    public function getUpcomingWeekAmount()
    {
        return $this->finances()
            ->where('status', 'pending')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->sum('amount');
    }

    public function getPendingPaymentsByDate()
    {
        return $this->finances()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get()
            ->groupBy(function($item) {
                return $item->due_date->format('Y-m-d');
            });
    }
}
