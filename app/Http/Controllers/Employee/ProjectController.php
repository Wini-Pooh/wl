<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Helpers\ProjectAccessHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'employee:employee,estimator,partner,admin']);
    }

    /**
     * Отображает список проектов с учетом прав доступа пользователя
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Используем новую логику доступа
        $accessibleProjects = ProjectAccessHelper::getAccessibleProjects($user);
        
        // Создаем query builder из коллекции доступных проектов
        if ($accessibleProjects->isEmpty()) {
            $query = Project::where('id', -1); // Пустой результат
        } else {
            $query = Project::whereIn('id', $accessibleProjects->pluck('id'));
        }
        
        $query->with('partner');
        
        // Поиск по телефону клиента
        if ($request->filled('phone')) {
            $query->byClientPhone($request->phone);
        }
        
        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('project_status', $request->status);
        }
        
        // Фильтр по типу объекта
        if ($request->filled('object_type')) {
            $query->where('object_type', $request->object_type);
        }
        
        // Поиск по имени клиента
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_first_name', 'like', "%{$search}%")
                  ->orWhere('client_last_name', 'like', "%{$search}%")
                  ->orWhere('client_phone', 'like', "%{$search}%")
                  ->orWhere('object_city', 'like', "%{$search}%")
                  ->orWhere('object_street', 'like', "%{$search}%");
            });
        }
        
        // Определяем количество элементов на странице в зависимости от режима просмотра
        $viewMode = $request->get('view_mode', 'list');
        $perPage = $viewMode === 'cards' ? 6 : 8;
        
        $projects = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('employee.projects.index', compact('projects', 'viewMode'));
    }

    /**
     * Показывает детали проекта с учетом прав доступа
     */
    public function show(Request $request, Project $project)
    {
        // Проверяем доступ к проекту с помощью новой логики
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        // Загружаем связанные данные для финансов и расписания
        $project->load([
            'basicWorks',
            'additionalWorks', 
            'basicMaterials',
            'additionalMaterials',
            'transports',
            'stages',
            'events'
        ]);

        // Подготовка данных для вкладок (пустые массивы - реальные данные будут из БД)
        $documents = [];
        $photos = [];
        $designFiles = [];
        $schemes = [];

        // Если это AJAX запрос для конкретной вкладки
        if ($request->ajax() && $request->has('tab')) {
            $tab = $request->get('tab');
            
            try {
                $view = "employee.projects.tabs.{$tab}";
                $html = view($view, compact('project', 'documents', 'photos', 'designFiles', 'schemes'))->render();
                
                return response()->json([
                    'success' => true,
                    'html' => $html
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка загрузки вкладки'
                ], 500);
            }
        }
        
        return view('employee.projects.show', compact('project', 'documents', 'photos', 'designFiles', 'schemes'));
    }

    /**
     * Создание нового проекта (только для сотрудников с правами)
     */
    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Только сотрудники могут создавать проекты (не сметчики)
        if (!$user->isEmployee() && !$user->isPartner() && !$user->isAdmin()) {
            abort(403, 'Недостаточно прав для создания проекта');
        }
        
        return view('employee.projects.create');
    }

    /**
     * Сохранение нового проекта
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Только сотрудники могут создавать проекты (не сметчики)
        if (!$user->isEmployee() && !$user->isPartner() && !$user->isAdmin()) {
            abort(403, 'Недостаточно прав для создания проекта');
        }
        
        $partnerId = $this->getEmployeePartnerId($user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        $validated = $request->validate([
            // Обязательные поля
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'object_type' => 'required|in:apartment,house,office,commercial,other',
            'work_type' => 'required|string|max:255',
            'project_status' => 'required|in:new,in_progress,paused,completed,cancelled',
            
            // Дополнительные поля
            'client_middle_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'object_city' => 'nullable|string|max:255',
            'object_street' => 'nullable|string|max:255',
            'object_house' => 'nullable|string|max:20',
            'apartment_number' => 'nullable|string|max:20',
            'object_area' => 'nullable|numeric|min:0',
            'contract_date' => 'nullable|date',
            'work_start_date' => 'nullable|date',
            'estimated_end_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:100',
        ]);

        // Привязываем к партнеру
        $validated['partner_id'] = $partnerId;
        
        $project = Project::create($validated);
        
        return redirect()->route('employee.projects.show', $project)
                        ->with('success', 'Проект успешно создан!');
    }
    
    /**
     * Проверяет доступ к проекту
     */
    private function checkProjectAccess(Project $project)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return ProjectAccessHelper::canAccessProject($user, $project);
    }

    /**
     * Получает ID партнера для сотрудника
     */
    private function getEmployeePartnerId($user)
    {
        if ($user->isAdmin()) {
            return null; // Админ может работать со всеми проектами
        }
        
        if ($user->isPartner()) {
            return $user->id;
        }
        
        if ($user->isEmployee() || $user->isEstimator()) {
            $employeeProfile = $user->employeeProfile;
            if ($employeeProfile && $employeeProfile->status === 'active') {
                return $employeeProfile->partner_id;
            }
        }
        
        return null;
    }
    
    /**
     * Поиск проектов по номеру телефона с учетом прав доступа
     */
    public function searchByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Получаем доступные проекты с учетом новой логики
        $accessibleProjects = ProjectAccessHelper::getAccessibleProjects($user);
        
        // Фильтруем по номеру телефона
        $projects = $accessibleProjects->filter(function($project) use ($request) {
            $phoneClean = preg_replace('/[^0-9]/', '', $request->phone);
            $projectPhoneClean = preg_replace('/[^0-9]/', '', $project->client_phone);
            return strpos($projectPhoneClean, $phoneClean) !== false;
        });
        
        return response()->json($projects->values());
    }
}
