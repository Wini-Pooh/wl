<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'type',
        'unit',
        'quantity',
        'price',
        'amount',
        'paid_amount',
        'payment_date',
        'description',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Связи
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Типы работ
    public static function getTypes()
    {
        return [
            'basic' => 'Основные работы',
            'additional' => 'Дополнительные работы',
        ];
    }

    // Статус оплаты
    public function getPaymentStatusAttribute()
    {
        if ($this->paid_amount == 0) {
            return 'not_paid';
        } elseif ($this->paid_amount >= $this->amount) {
            return 'fully_paid';
        } else {
            return 'partially_paid';
        }
    }

    // Оставшаяся сумма
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }
}
