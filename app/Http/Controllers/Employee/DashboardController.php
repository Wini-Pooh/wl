<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Estimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'employee:employee,foreman,estimator,partner,admin']);
    }

    /**
     * Показывает дашборд для сотрудника/сметчика
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Получаем ID партнера
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        // Получаем проекты партнера
        $projects = Project::forPartner($partnerId)
            ->with(['stages', 'estimates'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Получаем сметы партнера
        $estimates = Estimate::whereHas('project', function($q) use ($partnerId) {
            $q->where('partner_id', $partnerId);
        })->with('project')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
        
        // Статистика
        $stats = [
            'total_projects' => Project::forPartner($partnerId)->count(),
            'active_projects' => Project::forPartner($partnerId)->where('project_status', 'in_progress')->count(),
            'total_estimates' => Estimate::whereHas('project', function($q) use ($partnerId) {
                $q->where('partner_id', $partnerId);
            })->count(),
            'pending_estimates' => Estimate::whereHas('project', function($q) use ($partnerId) {
                $q->where('partner_id', $partnerId);
            })->where('status', 'draft')->count(),
        ];
        
        return view('employee.dashboard', compact('projects', 'estimates', 'stats', 'user'));
    }
    
    /**
     * Получает ID партнера для текущего пользователя
     */
    private function getPartnerId(Request $request, $user)
    {
        // Если пользователь партнер или админ
        if ($user->isPartner() || $user->isAdmin()) {
            return $user->id;
        }
        
        // Если сотрудник, прораб или сметчик, получаем ID партнера из middleware
        $partnerId = $request->attributes->get('employee_partner_id');
        if ($partnerId) {
            return $partnerId;
        }
        
        // Если не удалось получить через middleware, пробуем через профиль
        if ($user->isEmployee() || $user->isForeman() || $user->isEstimator()) {
            $employeeProfile = $user->employeeProfile;
            if ($employeeProfile) {
                return $employeeProfile->partner_id;
            }
        }
        
        return null;
    }
}
