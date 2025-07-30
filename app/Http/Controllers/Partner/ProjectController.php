<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function __construct()
    {
        // Доступ к проектам для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к проектам (только к сметам)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
        
        // Ограничиваем доступ к созданию и редактированию для прорабов и клиентов
        $this->middleware(['role:partner,employee,admin'])->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Проверяет доступ пользователя к проекту
     */
    private function checkProjectAccess(Project $project)
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('partner')) {
            return $project->partner_id === $user->id;
        }
        
        if (($user->hasRole('employee') || $user->hasRole('foreman')) && isset($user->employeeProfile)) {
            return $project->partner_id === $user->employeeProfile->partner_id;
        }
        
        // Проверка доступа для клиентов по номеру телефона
        if ($user->hasRole('client')) {
            $userPhone = $user->phone ?? $user->email;
            if ($userPhone) {
                $userPhoneClean = preg_replace('/[^0-9]/', '', $userPhone);
                $projectPhoneClean = preg_replace('/[^0-9]/', '', $project->client_phone);
                return $userPhoneClean === $projectPhoneClean;
            }
        }
        
        return false;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with('partner');
        
        // Фильтрация по партнеру
        $user = Auth::user();
        if (!$user->isAdmin()) {
            if ($user->isPartner()) {
                $query->forPartner($user->getKey());
            } elseif (($user->isEmployee() || $user->isForeman()) && isset($user->employeeProfile)) {
                // Сотрудник и прораб видят проекты своего партнера
                $query->forPartner($user->employeeProfile->partner_id);
            } elseif ($user->isClient()) {
                // Клиент видит только свои проекты
                $userPhone = $user->phone ?? $user->email;
                if ($userPhone) {
                    // Очищаем номер телефона пользователя от символов
                    $userPhoneClean = preg_replace('/[^0-9]/', '', $userPhone);
                    
                    // Используем SQL-функции, которые работают в старых версиях MySQL
                    $query->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(client_phone, ' ', ''), '(', ''), ')', ''), '-', ''), '+', '') LIKE ?", ['%' . $userPhoneClean . '%']);
                } else {
                    // Если у клиента нет номера телефона, показываем пустой результат
                    $query->where('id', -1);
                }
            } else {
                // Если нет подходящей роли, показываем пустой результат
                $query->where('id', -1);
            }
        }
        
        // Поиск по телефону клиента
        if ($request->filled('phone')) {
            $query->byClientPhone($request->input('phone'));
        }
        
        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('project_status', $request->input('status'));
        }
        
        // Фильтр по типу объекта
        if ($request->filled('object_type')) {
            $query->where('object_type', $request->input('object_type'));
        }
        
        // Поиск по имени клиента
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('client_first_name', 'like', "%{$search}%")
                  ->orWhere('client_last_name', 'like', "%{$search}%")
                  ->orWhere('object_city', 'like', "%{$search}%")
                  ->orWhere('object_street', 'like', "%{$search}%");
            });
        }
        
        // Определяем количество элементов на странице в зависимости от режима просмотра
        $viewMode = $request->get('view_mode', 'list');
        $perPage = $viewMode === 'cards' ? 6 : 8;
        
        // Логирование для отладки
        \Log::info('Projects index - View mode: ' . $viewMode . ', Per page: ' . $perPage);
        
        $projects = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('partner.projects.index', compact('projects', 'viewMode'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('partner.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Обязательные поля
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'object_type' => ['required', Rule::in(array_keys(Project::getObjectTypes()))],
            'work_type' => ['required', Rule::in(array_keys(Project::getWorkTypes()))],
            'project_status' => ['required', Rule::in(array_keys(Project::getStatuses()))],
            
            // Паспортные данные
            'passport_series' => 'nullable|string|max:10',
            'passport_number' => 'nullable|string|max:20',
            'passport_issued_by' => 'nullable|string|max:500',
            'passport_issued_date' => 'nullable|date',
            'passport_department_code' => 'nullable|string|max:10',
            
            // Личные данные
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            
            // Адрес прописки
            'registration_postal_code' => 'nullable|string|max:10',
            'registration_city' => 'nullable|string|max:255',
            'registration_street' => 'nullable|string|max:255',
            'registration_house' => 'nullable|string|max:20',
            'registration_apartment' => 'nullable|string|max:20',
            
            // Характеристики объекта
            'apartment_number' => 'nullable|string|max:20',
            'object_city' => 'nullable|string|max:255',
            'object_street' => 'nullable|string|max:255',
            'object_house' => 'nullable|string|max:20',
            'object_entrance' => 'nullable|string|max:20',
            'object_area' => 'nullable|numeric|min:0',
            'camera_link' => 'nullable|url|max:500',
            
            // Финансовые показатели исключены - обновляются только через сметы
            
            // Временные рамки
            'contract_date' => 'nullable|date',
            'work_start_date' => 'nullable|date',
            'estimated_end_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:100',
        ]);
        
        $validated['partner_id'] = Auth::id();
        
        $project = Project::create($validated);
        
        return redirect()->route('partner.projects.show', $project)
                        ->with('success', 'Проект успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Project $project)
    {
        // Проверка доступа
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
                $view = "partner.projects.tabs.{$tab}";
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
        
        return view('partner.projects.show', compact('project', 'documents', 'photos', 'designFiles', 'schemes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        return view('partner.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        $validated = $request->validate([
            // Обязательные поля
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'object_type' => ['required', Rule::in(array_keys(Project::getObjectTypes()))],
            'work_type' => ['required', Rule::in(array_keys(Project::getWorkTypes()))],
            'project_status' => ['required', Rule::in(array_keys(Project::getStatuses()))],
            
            // Паспортные данные
            'passport_series' => 'nullable|string|max:10',
            'passport_number' => 'nullable|string|max:20',
            'passport_issued_by' => 'nullable|string|max:500',
            'passport_issued_date' => 'nullable|date',
            'passport_department_code' => 'nullable|string|max:10',
            
            // Личные данные
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            
            // Адрес прописки
            'registration_postal_code' => 'nullable|string|max:10',
            'registration_city' => 'nullable|string|max:255',
            'registration_street' => 'nullable|string|max:255',
            'registration_house' => 'nullable|string|max:20',
            'registration_apartment' => 'nullable|string|max:20',
            
            // Характеристики объекта
            'apartment_number' => 'nullable|string|max:20',
            'object_city' => 'nullable|string|max:255',
            'object_street' => 'nullable|string|max:255',
            'object_house' => 'nullable|string|max:20',
            'object_entrance' => 'nullable|string|max:20',
            'object_area' => 'nullable|numeric|min:0',
            'camera_link' => 'nullable|url|max:500',
            
            // Финансовые показатели исключены из валидации
            // Они обновляются автоматически из смет
            
            // Временные рамки
            'contract_date' => 'nullable|date',
            'work_start_date' => 'nullable|date',
            'estimated_end_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:100',
        ]);
        
        $project->update($validated);
        
        return redirect()->route('partner.projects.show', $project)
                        ->with('success', 'Проект успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Проверка доступа
        if (!Auth::user()->hasRole('admin') && $project->partner_id !== Auth::id()) {
            abort(403);
        }
        
        $project->delete();
        
        return redirect()->route('partner.projects.index')
                        ->with('success', 'Проект успешно удален!');
    }

    /**
     * Поиск проектов по номеру телефона
     */
    public function searchByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);
        
        $query = Project::byClientPhone($request->phone);
        
        // Фильтрация по партнеру (только свои проекты, кроме админа)
        if (!Auth::user()->hasRole('admin')) {
            $query->forPartner(Auth::id());
        }
        
        $projects = $query->get();
        
        return response()->json($projects);
    }
    
    /**
     * Временный метод для тестирования кнопок
     */
    public function testButtons(Project $project)
    {
        $user = Auth::user();
        
        $buttonTest = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'default_role' => $user->defaultRole ? $user->defaultRole->name : null,
            'isAdmin' => $user->isAdmin(),
            'isPartner' => $user->isPartner(),
            'isEmployee' => $user->isEmployee(),
            'isForeman' => $user->isForeman(),
            'isEstimator' => $user->isEstimator(),
            'isClient' => $user->isClient(),
            'hasRole_admin' => $user->hasRole('admin'),
            'hasRole_partner' => $user->hasRole('partner'),
            'hasRole_employee' => $user->hasRole('employee'),
            'hasRole_foreman' => $user->hasRole('foreman'),
            'hasRole_estimator' => $user->hasRole('estimator'),
            'canSeeActionButtons' => \App\Helpers\UserRoleHelper::canSeeActionButtons(),
        ];
        
        return response()->json($buttonTest);
    }
}
