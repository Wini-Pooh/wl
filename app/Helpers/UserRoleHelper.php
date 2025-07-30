<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class UserRoleHelper
{
    /**
     * Проверяет, может ли текущий пользователь управлять сотрудниками
     */
    public static function canManageEmployees()
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Только партнеры, сотрудники и админы могут управлять сотрудниками (НЕ прорабы)
        return $user->isPartner() || $user->isEmployee() || $user->isAdmin();
    }
    
    /**
     * Проверяет, может ли текущий пользователь работать с проектами
     */
    public static function canAccessProjects()
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Все роли кроме клиентов и сметчиков могут работать с проектами
        // Сметчики НЕ имеют доступа к проектам согласно требованиям
        return $user->isAdmin() || $user->isPartner() || $user->isEmployee() || $user->isForeman();
    }
    
    /**
     * Проверяет, может ли текущий пользователь создавать/редактировать проекты
     */
    public static function canManageProjects()
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Все роли кроме клиентов и сметчиков могут управлять проектами
        // Проверяем наличие административных ролей (приоритет над клиентской ролью)
        if ($user->hasRole('admin') || $user->hasRole('partner') || $user->hasRole('employee') || $user->hasRole('foreman')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Проверяет, может ли текущий пользователь видеть кнопки действий (редактировать, удалить и т.д.)
     */
    public static function canSeeActionButtons()
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Кнопки действий доступны всем кроме чистых клиентов
        // Проверяем наличие любых административных ролей
        if ($user->hasRole('admin') || $user->hasRole('partner') || $user->hasRole('employee') || $user->hasRole('foreman') || $user->hasRole('estimator')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Проверяет, может ли текущий пользователь работать со сметами
     */
    public static function canAccessEstimates()
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Админы, партнеры, сотрудники, прорабы и сметчики могут работать со сметами
        return $user->isAdmin() || $user->isPartner() || $user->isEmployee() || $user->isForeman() || $user->isEstimator();
    }
    
    /**
     * Получает базовую ссылку для проектов в зависимости от роли
     */
    public static function getProjectsRoute()
    {
        $user = Auth::user();
        if (!$user) return '/';
        
        if ($user->isAdmin() || $user->isPartner() || $user->isForeman()) {
            return route('partner.projects.index');
        }
        
        if ($user->isEmployee()) {
            return route('employee.projects.index');
        }
        
        // Сметчики НЕ имеют доступа к проектам
        
        return '/';
    }
    
    /**
     * Получает базовую ссылку для смет в зависимости от роли
     */
    public static function getEstimatesRoute()
    {
        $user = Auth::user();
        if (!$user) return '/';
        
        // Все роли (включая сметчиков, прорабов) используют партнерские маршруты смет
        if ($user->isAdmin() || $user->isPartner() || $user->isForeman() || $user->isEmployee() || $user->isEstimator()) {
            return route('partner.estimates.index');
        }
        
        return '/';
    }
    
    /**
     * Получает роль пользователя для отображения
     */
    public static function getUserRoleDisplay()
    {
        $user = Auth::user();
        if (!$user) return 'Гость';
        
        if ($user->isAdmin()) return 'Администратор';
        if ($user->isPartner()) return 'Партнер';
        if ($user->isForeman()) return 'Прораб';
        if ($user->isEmployee()) return 'Сотрудник';
        if ($user->isEstimator()) return 'Сметчик';
        if ($user->isClient()) return 'Клиент';
        
        return 'Пользователь';
    }
    
    /**
     * Проверяет, может ли пользователь видеть определенное меню
     */
    public static function canAccessMenu($menuType)
    {
        $user = Auth::user();
        if (!$user) return false;
        
        switch ($menuType) {
            case 'projects':
                return self::canAccessProjects();
                
            case 'estimates':
                return self::canAccessEstimates();
                
            case 'employees':
                return self::canManageEmployees();
                
            case 'admin':
                return $user->isAdmin();
                
            case 'dashboard':
                return true; // Все авторизованные пользователи
                
            default:
                return false;
        }
    }
    
    /**
     * Получает название раздела для пользователя
     */
    public static function getSectionTitle()
    {
        $user = Auth::user();
        if (!$user) return 'Система управления';
        
        if ($user->isAdmin()) return 'Панель администратора';
        if ($user->isPartner()) return 'Панель партнера';
        if ($user->isForeman()) return 'Панель прораба';
        if ($user->isEmployee()) return 'Рабочее место сотрудника';
        if ($user->isEstimator()) return 'Рабочее место сметчика';
        if ($user->isClient()) return 'Личный кабинет';
        
        return 'Система управления';
    }
}
