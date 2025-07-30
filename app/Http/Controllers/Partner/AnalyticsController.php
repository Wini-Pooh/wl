<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeFinance;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:partner,employee,admin']);
    }

    /**
     * Получает ID партнера для текущего пользователя
     */
    private function getPartnerId()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return null; // Админ видит всех
        }
        
        if ($user->isPartner()) {
            return $user->id;
        }
        
        if ($user->isEmployee() && $user->employeeProfile) {
            return $user->employeeProfile->partner_id;
        }
        
        return null;
    }

    /**
     * Главная страница аналитики
     */
    public function dashboard(Request $request)
    {
        $partnerId = $this->getPartnerId();
        $period = $request->get('period', '30'); // по умолчанию 30 дней
        
        // Получаем данные для всех вкладок
        $financialData = $this->getFinancialAnalytics($partnerId, $period);
        $projectData = $this->getProjectAnalytics($partnerId, $period);
        $employeeData = $this->getEmployeeAnalytics($partnerId, $period);
        $generalData = $this->getGeneralAnalytics($partnerId, $period);

        return view('partner.analytics.dashboard', compact(
            'financialData',
            'projectData', 
            'employeeData',
            'generalData',
            'period'
        ));
    }

    /**
     * Финансовая аналитика
     */
    private function getFinancialAnalytics($partnerId, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        // Базовый запрос для проектов
        $projectsQuery = Project::query();
        if ($partnerId) {
            $projectsQuery->where('partner_id', $partnerId);
        }
        
        // Базовый запрос для финансов сотрудников
        $employeeFinanceQuery = EmployeeFinance::query()
            ->join('employees', 'employee_finances.employee_id', '=', 'employees.id');
        if ($partnerId) {
            $employeeFinanceQuery->where('employees.partner_id', $partnerId);
        }

        // Общий доход по проектам
        $projectRevenue = $projectsQuery->clone()
            ->where('created_at', '>=', $startDate)
            ->sum('total_cost');

        // Расходы по проектам (материалы + работа + транспорт)
        $projectExpenses = $projectsQuery->clone()
            ->where('created_at', '>=', $startDate)
            ->sum(DB::raw('COALESCE(materials_cost, 0) + COALESCE(work_cost, 0) + COALESCE(additional_work_cost, 0)'));

        // Добавляем транспортные расходы из связанной таблицы
        $transportExpenses = DB::table('project_transports')
            ->join('projects', 'project_transports.project_id', '=', 'projects.id')
            ->where('projects.created_at', '>=', $startDate);
        if ($partnerId) {
            $transportExpenses->where('projects.partner_id', $partnerId);
        }
        $transportExpenses = $transportExpenses->sum('project_transports.amount');
        
        $projectExpenses += $transportExpenses;

        // Финансы сотрудников по типам
        $employeeFinancesByType = $employeeFinanceQuery->clone()
            ->where('employee_finances.created_at', '>=', $startDate)
            ->select('employee_finances.type', DB::raw('SUM(employee_finances.amount) as total'))
            ->groupBy('employee_finances.type')
            ->get()
            ->pluck('total', 'type');

        // Финансы сотрудников по статусам
        $employeeFinancesByStatus = $employeeFinanceQuery->clone()
            ->where('employee_finances.created_at', '>=', $startDate)
            ->select('employee_finances.status', DB::raw('SUM(employee_finances.amount) as total'))
            ->groupBy('employee_finances.status')
            ->get()
            ->pluck('total', 'status');

        // Динамика доходов по дням (последние 30 дней)
        $revenueByDays = $projectsQuery->clone()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_cost) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Динамика расходов по сотрудникам по дням
        $employeeExpensesByDays = $employeeFinanceQuery->clone()
            ->where('employee_finances.created_at', '>=', Carbon::now()->subDays(30))
            ->whereIn('employee_finances.type', ['salary', 'bonus', 'expense'])
            ->select(
                DB::raw('DATE(employee_finances.created_at) as date'),
                DB::raw('SUM(employee_finances.amount) as expenses')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Топ проектов по доходности
        $topProjects = $projectsQuery->clone()
            ->where('created_at', '>=', $startDate)
            ->orderBy('total_cost', 'desc')
            ->limit(10)
            ->get(['id', 'client_first_name', 'client_last_name', 'total_cost', 'project_status']);

        // Просроченные финансовые обязательства
        $overdueFinances = $employeeFinanceQuery->clone()
            ->where('employee_finances.status', 'pending')
            ->where('employee_finances.due_date', '<', Carbon::now())
            ->with(['employee'])
            ->get();

        return [
            'project_revenue' => $projectRevenue,
            'project_expenses' => $projectExpenses,
            'net_profit' => $projectRevenue - $projectExpenses,
            'employee_finances_by_type' => $employeeFinancesByType,
            'employee_finances_by_status' => $employeeFinancesByStatus,
            'revenue_by_days' => $revenueByDays,
            'employee_expenses_by_days' => $employeeExpensesByDays,
            'top_projects' => $topProjects,
            'overdue_finances' => $overdueFinances,
            'total_employee_debt' => $employeeFinancesByStatus->get('pending', 0),
            'total_employee_paid' => $employeeFinancesByStatus->get('paid', 0),
        ];
    }

    /**
     * Аналитика проектов
     */
    private function getProjectAnalytics($partnerId, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $projectsQuery = Project::query();
        if ($partnerId) {
            $projectsQuery->where('partner_id', $partnerId);
        }

        // Общая статистика проектов
        $totalProjects = $projectsQuery->clone()->count();
        $newProjects = $projectsQuery->clone()->where('created_at', '>=', $startDate)->count();
        
        // Проекты по статусам
        $projectsByStatus = $projectsQuery->clone()
            ->select('project_status', DB::raw('COUNT(*) as count'))
            ->groupBy('project_status')
            ->get()
            ->pluck('count', 'project_status');

        // Проекты по типам работ
        $projectsByWorkType = $projectsQuery->clone()
            ->select('work_type', DB::raw('COUNT(*) as count'))
            ->groupBy('work_type')
            ->get()
            ->pluck('count', 'work_type');

        // Проекты по типам объектов
        $projectsByObjectType = $projectsQuery->clone()
            ->select('object_type', DB::raw('COUNT(*) as count'))
            ->groupBy('object_type')
            ->get()
            ->pluck('count', 'object_type');

        // Динамика создания проектов
        $projectsByDays = $projectsQuery->clone()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Средняя стоимость проекта
        $avgProjectCost = $projectsQuery->clone()->avg('total_cost');

        return [
            'total_projects' => $totalProjects,
            'new_projects' => $newProjects,
            'projects_by_status' => $projectsByStatus,
            'projects_by_work_type' => $projectsByWorkType,
            'projects_by_object_type' => $projectsByObjectType,
            'projects_by_days' => $projectsByDays,
            'avg_project_cost' => $avgProjectCost,
        ];
    }

    /**
     * Аналитика сотрудников
     */
    private function getEmployeeAnalytics($partnerId, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $employeesQuery = Employee::query();
        if ($partnerId) {
            $employeesQuery->where('partner_id', $partnerId);
        }

        // Общая статистика сотрудников
        $totalEmployees = $employeesQuery->clone()->count();
        $activeEmployees = $employeesQuery->clone()->where('status', 'active')->count();
        
        // Сотрудники по ролям
        $employeesByRole = $employeesQuery->clone()
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role');

        // Сотрудники по статусам
        $employeesByStatus = $employeesQuery->clone()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Топ сотрудников по задолженности
        $topDebtors = $employeesQuery->clone()
            ->join('employee_finances', 'employees.id', '=', 'employee_finances.employee_id')
            ->where('employee_finances.status', 'pending')
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                DB::raw('SUM(employee_finances.amount) as total_debt')
            )
            ->groupBy('employees.id', 'employees.first_name', 'employees.last_name')
            ->orderBy('total_debt', 'desc')
            ->limit(10)
            ->get();

        // Сотрудники с наибольшими выплатами
        $topEarners = $employeesQuery->clone()
            ->join('employee_finances', 'employees.id', '=', 'employee_finances.employee_id')
            ->where('employee_finances.status', 'paid')
            ->where('employee_finances.created_at', '>=', $startDate)
            ->select(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                DB::raw('SUM(employee_finances.amount) as total_earned')
            )
            ->groupBy('employees.id', 'employees.first_name', 'employees.last_name')
            ->orderBy('total_earned', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'employees_by_role' => $employeesByRole,
            'employees_by_status' => $employeesByStatus,
            'top_debtors' => $topDebtors,
            'top_earners' => $topEarners,
        ];
    }

    /**
     * Общая аналитика
     */
    private function getGeneralAnalytics($partnerId, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        // Получаем ключевые метрики
        $metrics = [
            'conversion_rate' => 0, // Можно добавить логику расчета конверсии
            'avg_project_duration' => 0, // Средняя длительность проекта
            'client_satisfaction' => 0, // Удовлетворенность клиентов
            'profitability_ratio' => 0, // Коэффициент прибыльности
        ];

        return $metrics;
    }

    /**
     * API для получения данных графиков
     */
    public function getChartData(Request $request)
    {
        $partnerId = $this->getPartnerId();
        $type = $request->get('type');
        $period = $request->get('period', '30');
        
        switch ($type) {
            case 'revenue':
                return response()->json($this->getRevenueChartData($partnerId, $period));
            case 'expenses':
                return response()->json($this->getExpensesChartData($partnerId, $period));
            case 'projects':
                return response()->json($this->getProjectsChartData($partnerId, $period));
            case 'employees':
                return response()->json($this->getEmployeesChartData($partnerId, $period));
            default:
                return response()->json(['error' => 'Unknown chart type'], 400);
        }
    }

    private function getRevenueChartData($partnerId, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $projectsQuery = Project::query();
        if ($partnerId) {
            $projectsQuery->where('partner_id', $partnerId);
        }

        return $projectsQuery
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_cost) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => floatval($item->revenue)
                ];
            });
    }

    private function getExpensesChartData($partnerId, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $employeeFinanceQuery = EmployeeFinance::query()
            ->join('employees', 'employee_finances.employee_id', '=', 'employees.id');
        if ($partnerId) {
            $employeeFinanceQuery->where('employees.partner_id', $partnerId);
        }

        return $employeeFinanceQuery
            ->where('employee_finances.created_at', '>=', $startDate)
            ->whereIn('employee_finances.type', ['salary', 'bonus', 'expense'])
            ->select(
                DB::raw('DATE(employee_finances.created_at) as date'),
                DB::raw('SUM(employee_finances.amount) as expenses')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => floatval($item->expenses)
                ];
            });
    }

    private function getProjectsChartData($partnerId, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        $projectsQuery = Project::query();
        if ($partnerId) {
            $projectsQuery->where('partner_id', $partnerId);
        }

        return $projectsQuery
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => intval($item->count)
                ];
            });
    }

    private function getEmployeesChartData($partnerId, $period)
    {
        $employeesQuery = Employee::query();
        if ($partnerId) {
            $employeesQuery->where('partner_id', $partnerId);
        }

        return $employeesQuery
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->role,
                    'value' => intval($item->count)
                ];
            });
    }
}
