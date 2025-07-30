<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        // Обязательные поля
        'client_first_name',
        'client_last_name',
        'client_phone',
        'object_type',
        'work_type',
        'project_status',
        
        // Паспортные данные
        'passport_series',
        'passport_number',
        'passport_issued_by',
        'passport_issued_date',
        'passport_department_code',
        
        // Личные данные
        'birth_date',
        'birth_place',
        'client_email',
        
        // Адрес прописки
        'registration_postal_code',
        'registration_city',
        'registration_street',
        'registration_house',
        'registration_apartment',
        
        // Характеристики объекта
        'apartment_number',
        'object_city',
        'object_street',
        'object_house',
        'object_entrance',
        'object_area',
        'camera_link',
        
        // Финансовые показатели исключены из fillable
        // Они обновляются только через метод updateCostsFromEstimates()
        
        // Временные рамки
        'contract_date',
        'work_start_date',
        'estimated_end_date',
        'contract_number',
        
        // Связи
        'partner_id',
    ];

    protected $casts = [
        'passport_issued_date' => 'date',
        'birth_date' => 'date',
        'contract_date' => 'date',
        'work_start_date' => 'date',
        'estimated_end_date' => 'date',
        'object_area' => 'decimal:2',
        'work_cost' => 'decimal:2',
        'materials_cost' => 'decimal:2',
        'additional_work_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    // Связи
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function works()
    {
        return $this->hasMany(ProjectWork::class);
    }

    public function materials()
    {
        return $this->hasMany(ProjectMaterial::class);
    }

    public function transports()
    {
        return $this->hasMany(ProjectTransport::class);
    }

    public function stages()
    {
        return $this->hasMany(ProjectStage::class)->orderBy('order');
    }

    public function events()
    {
        return $this->hasMany(ProjectEvent::class)->orderBy('event_date');
    }
    
    public function photos()
    {
        return $this->hasMany(ProjectPhoto::class)->orderBy('created_at', 'desc');
    }

    public function schemes()
    {
        return $this->hasMany(ProjectScheme::class)->orderBy('created_at', 'desc');
    }

    public function designFiles()
    {
        return $this->hasMany(ProjectDesignFile::class)->orderBy('created_at', 'desc');
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class)->orderBy('created_at', 'desc');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    public function basicWorks()
    {
        return $this->works()->where('type', 'basic');
    }

    public function additionalWorks()
    {
        return $this->works()->where('type', 'additional');
    }

    public function basicMaterials()
    {
        return $this->materials()->where('type', 'basic');
    }

    public function additionalMaterials()
    {
        return $this->materials()->where('type', 'additional');
    }

    // Мутаторы для автоматического расчета общей стоимости
    public function setWorkCostAttribute($value)
    {
        $this->attributes['work_cost'] = $value;
        $this->calculateTotalCost();
    }

    public function setMaterialsCostAttribute($value)
    {
        $this->attributes['materials_cost'] = $value;
        $this->calculateTotalCost();
    }

    public function setAdditionalWorkCostAttribute($value)
    {
        $this->attributes['additional_work_cost'] = $value;
        $this->calculateTotalCost();
    }

    private function calculateTotalCost()
    {
        // Расчет на основе связанных данных, если проект уже существует
        if ($this->exists) {
            $basicWorksSum = $this->basicWorks()->sum('amount');
            $additionalWorksSum = $this->additionalWorks()->sum('amount');
            $basicMaterialsSum = $this->basicMaterials()->sum('amount');
            $additionalMaterialsSum = $this->additionalMaterials()->sum('amount');
            $transportSum = $this->transports()->sum('amount');

            $this->attributes['work_cost'] = $basicWorksSum;
            $this->attributes['materials_cost'] = $basicMaterialsSum;
            $this->attributes['additional_work_cost'] = $additionalWorksSum + $additionalMaterialsSum + $transportSum;
            $this->attributes['total_cost'] = $basicWorksSum + $additionalWorksSum + $basicMaterialsSum + $additionalMaterialsSum + $transportSum;
        } else {
            // Расчет на основе переданных значений для нового проекта
            $this->attributes['total_cost'] = 
                ($this->attributes['work_cost'] ?? 0) + 
                ($this->attributes['materials_cost'] ?? 0) + 
                ($this->attributes['additional_work_cost'] ?? 0);
        }
    }

    // Метод для пересчета стоимости проекта
    public function recalculateCosts()
    {
        $this->calculateTotalCost();
        $this->save();
    }

    // Метод для обновления финансовых показателей на основе смет
    public function updateCostsFromEstimates()
    {
        $workCost = 0;          // Стоимость работ из смет main
        $materialsCost = 0;     // Стоимость материалов из смет materials
        $additionalWorkCost = 0; // Дополнительные работы из смет additional

        // Получаем все сметы проекта
        $estimates = $this->estimates;
        
        foreach ($estimates as $estimate) {
            $totals = $estimate->data['totals'] ?? [];
            // Берем итоговую сумму для клиента
            $clientTotal = $totals['client_total'] ?? $totals['grand_total'] ?? 0;
            
            // Распределяем по типам смет
            switch ($estimate->type) {
                case 'main':
                    $workCost += $clientTotal;
                    break;
                case 'materials':
                    $materialsCost += $clientTotal;
                    break;
                case 'additional':
                    $additionalWorkCost += $clientTotal;
                    break;  
            }
        }

        // Обновляем поля напрямую через attributes (без мутаторов)
        $this->attributes['work_cost'] = $workCost;
        $this->attributes['materials_cost'] = $materialsCost;
        $this->attributes['additional_work_cost'] = $additionalWorkCost;
        $this->attributes['total_cost'] = $workCost + $materialsCost + $additionalWorkCost;
        
        $this->save();
    }

    // Accessor для полного имени клиента
    public function getClientFullNameAttribute()
    {
        return $this->client_first_name . ' ' . $this->client_last_name;
    }
    
    // Accessor для названия проекта (псевдоним для полного имени клиента)
    public function getNameAttribute()
    {
        return $this->client_full_name;
    }

    // Accessor для полного адреса объекта
    public function getObjectFullAddressAttribute()
    {
        $parts = array_filter([
            $this->object_city,
            $this->object_street,
            'д. ' . $this->object_house,
            $this->apartment_number ? 'кв. ' . $this->apartment_number : null
        ]);
        
        return implode(', ', $parts);
    }

    // Scope для фильтрации по партнеру
    public function scopeForPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    // Scope для поиска по телефону клиента
    public function scopeByClientPhone($query, $phone)
    {
        return $query->where('client_phone', 'like', '%' . $phone . '%');
    }

    // Константы для статусов
    const STATUS_DRAFT = 'draft';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_COMPLETED => 'Завершен',
            self::STATUS_CANCELLED => 'Отменен',
        ];
    }

    // Константы для типов объектов
    const OBJECT_TYPE_APARTMENT = 'apartment';
    const OBJECT_TYPE_HOUSE = 'house';
    const OBJECT_TYPE_OFFICE = 'office';
    const OBJECT_TYPE_COMMERCIAL = 'commercial';

    public static function getObjectTypes()
    {
        return [
            self::OBJECT_TYPE_APARTMENT => 'Квартира',
            self::OBJECT_TYPE_HOUSE => 'Дом',
            self::OBJECT_TYPE_OFFICE => 'Офис',
            self::OBJECT_TYPE_COMMERCIAL => 'Коммерческое помещение',
        ];
    }

    // Константы для типов работ
    const WORK_TYPE_RENOVATION = 'renovation';
    const WORK_TYPE_REPAIR = 'repair';
    const WORK_TYPE_DESIGN = 'design';
    const WORK_TYPE_CONSTRUCTION = 'construction';

    public static function getWorkTypes()
    {
        return [
            self::WORK_TYPE_RENOVATION => 'Ремонт',
            self::WORK_TYPE_REPAIR => 'Косметический ремонт',
            self::WORK_TYPE_DESIGN => 'Дизайн',
            self::WORK_TYPE_CONSTRUCTION => 'Строительство',
        ];
    }
}
