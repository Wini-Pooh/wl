<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFinance extends Model
{
    use HasFactory;

    protected $table = 'project_finances';

    protected $fillable = [
        'project_id',
        'type',         // income (доходы) или expense (расходы)
        'category',     // Категория: employee, materials, contractor, supplier, other
        'title',        // Название записи
        'amount',       // Сумма
        'operation_date', // Дата операции
        'description',  // Описание
        'status',       // planned, paid, overdue
        'contractor',   // Подрядчик/исполнитель
        'payment_method', // cash, bank_transfer, card
        'is_planned',   // Запланированная операция
        'notes'         // Заметки
    ];

    protected $casts = [
        'operation_date' => 'date',
        'amount' => 'decimal:2',
        'is_planned' => 'boolean',
    ];

    /**
     * Связь с проектом
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope для доходов
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope для расходов
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope для оплаченных операций
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope для запланированных операций
     */
    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    /**
     * Форматированная сумма
     */
    public function getFormattedAmountAttribute()
    {
        return number_format((float)$this->amount, 2, ',', ' ') . ' ₽';
    }
}
