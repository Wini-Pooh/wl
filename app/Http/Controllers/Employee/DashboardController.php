<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Estimate;
use App\Helpers\ProjectAccessHelper;
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
        
        // Получаем проекты с учетом новой логики доступа (только доступные пользователю)
        $accessibleProjects = ProjectAccessHelper::getAccessibleProjects($user);
        $projects = $accessibleProjects->take(10);
            
        // Получаем сметы только для доступных проектов
        $projectIds = $accessibleProjects->pluck('id');
        $estimates = Estimate::whereIn('project_id', $projectIds)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Статистика для доступных проектов
        $stats = [
            'total_projects' => $accessibleProjects->count(),
            'active_projects' => $accessibleProjects->where('project_status', 'in_progress')->count(),
            'total_estimates' => Estimate::whereIn('project_id', $projectIds)->count(),
            'pending_estimates' => Estimate::whereIn('project_id', $projectIds)->where('status', 'draft')->count(),
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
