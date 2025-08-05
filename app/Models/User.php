<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'default_role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    /**
     * Роли пользователя
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    /**
     * Роль по умолчанию
     */
    public function defaultRole()
    {
        return $this->belongsTo(Role::class, 'default_role_id');
    }

    /**
     * Профиль сотрудника (если пользователь является сотрудником)
     */
    public function employeeProfile()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }
    
    /**
     * Проверяет, имеет ли пользователь указанную роль
     */
    public function hasRole($roleName)
    {
        // Проверяем сначала default_role_id
        if ($this->defaultRole && $this->defaultRole->name === $roleName) {
            return true;
        }
        
        // Затем проверяем связи many-to-many
        return $this->roles()->where('name', $roleName)->exists();
    }
    
    /**
     * Проверяет, является ли пользователь администратором
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Проверяет, является ли пользователь клиентом
     */
    public function isClient()
    {
        return $this->hasRole('client');
    }
    
    /**
     * Проверяет, является ли пользователь партнером
     */
    public function isPartner()
    {
        return $this->hasRole('partner');
    }
    
    /**
     * Проверяет, является ли пользователь сотрудником
     */
    public function isEmployee()
    {
        return $this->hasRole('employee') || $this->hasRole('foreman') || $this->hasRole('estimator');
    }
    
    /**
     * Проверяет, является ли пользователь прорабом
     */
    public function isForeman()
    {
        return $this->hasRole('foreman');
    }
    
    /**
     * Проверяет, является ли пользователь сметчиком
     */
    public function isEstimator()
    {
        return $this->hasRole('estimator');
    }
    
    /**
     * Получает партнера для сотрудника
     */
    public function getPartnerAttribute()
    {
        if ($this->isEmployee() || $this->isEstimator() || $this->isForeman()) {
            $employeeProfile = $this->employeeProfile;
            return $employeeProfile ? $employeeProfile->partner : null;
        }
        return $this;
    }
    
    /**
     * Получает партнера для сотрудника (метод)
     */
    public function getPartner()
    {
        if ($this->isEmployee() || $this->isEstimator() || $this->isForeman()) {
            $employeeProfile = $this->employeeProfile;
            return $employeeProfile ? $employeeProfile->partner : null;
        }
        return $this;
    }
    
    /**
     * Проекты партнера
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'partner_id');
    }
    
    /**
     * Проекты, к которым назначен пользователь (для сотрудников, прорабов)
     */
    public function assignedProjects()
    {
        return $this->belongsToMany(Project::class, 'employee_projects', 'employee_id', 'project_id');
    }
    
    /**
     * Сотрудники партнера
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'partner_id');
    }
    
    /**
     * Назначает пользователю роль
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        
        $this->roles()->syncWithoutDetaching($role);
        return $this;
    }
    
    /**
     * Определяет поле для аутентификации (phone)
     */
    public function username()
    {
        return 'phone';
    }
    
    /**
     * Переопределяем метод findForPassport для работы с телефоном
     */
    public function findForPassport($username)
    {
        return $this->where('phone', $username)->first();
    }
    
    /**
     * Подписки пользователя
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
    
    /**
     * Активная подписка пользователя
     */
    public function activeSubscription()
    {
        // Если это сотрудник, получаем подписку от партнера
        if ($this->isEmployee() || $this->isEstimator() || $this->isForeman()) {
            $partner = $this->partner;
            if ($partner && $partner->id !== $this->id) {
                return $partner->activeSubscription();
            }
        }
        
        // Для партнеров или если партнер не найден - собственная подписка
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('expires_at');
    }
    
    /**
     * Геттер для получения активной подписки как свойства
     */
    public function getActiveSubscriptionAttribute()
    {
        return $this->activeSubscription()->first();
    }
    
    /**
     * Проверить лимит ресурса
     */
    public function checkResourceLimit($resource)
    {
        $subscription = $this->activeSubscription()->first();
        
        if (!$subscription) {
            return false;
        }
        
        return $subscription->checkResourceLimit($resource);
    }
    
    /**
     * Получить процент использования ресурса
     */
    public function getResourceUsage($resource)
    {
        $subscription = $this->activeSubscription()->first();
        
        if (!$subscription) {
            return 100; // Если нет подписки, считаем что лимит исчерпан
        }
        
        $plan = $subscription->subscriptionPlan;
        $currentField = 'current_' . $resource;
        $limitField = 'max_' . $resource;
        
        $current = $subscription->$currentField ?? 0;
        $limit = $plan->$limitField ?? 1;
        
        return $limit > 0 ? ($current / $limit) * 100 : 0;
    }
    
    /**
     * Проверить доступ к функции
     */
    public function hasFeatureAccess($feature)
    {
        $subscription = $this->activeSubscription()->first();
        
        if (!$subscription) {
            return false;
        }
        
        return $subscription->subscriptionPlan->hasAccess($feature);
    }
}
