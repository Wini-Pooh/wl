<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        // Доступ только для клиентов и админов
        $this->middleware(['auth', 'role:client,admin']);
    }

    /**
     * Проверяет доступ клиента к проекту по номеру телефона
     */
    private function checkClientAccess(Project $project)
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Админы имеют доступ ко всем проектам
        if ($user->isAdmin()) {
            return true;
        }
        
        // Для клиентов - проверяем номер телефона пользователя с номером в проекте
        $userPhone = $user->phone ?? $user->email; // fallback на email если нет телефона
        
        if (!$userPhone) {
            return false;
        }
        
        // Очищаем номера телефонов от символов для сравнения
        $userPhoneClean = preg_replace('/[^0-9]/', '', $userPhone);
        $projectPhoneClean = preg_replace('/[^0-9]/', '', $project->client_phone);
        
        return $userPhoneClean === $projectPhoneClean;
    }

    /**
     * Получает проекты клиента по его номеру телефона
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Project::with('partner');
        
        // Фильтрация для клиентов (админы видят все)
        if (!$user->isAdmin()) {
            $userPhone = $user->phone ?? $user->email;
            if ($userPhone) {
                $userPhoneClean = preg_replace('/[^0-9]/', '', $userPhone);
                
                // Показываем проекты только этого клиента
                // Используем SQL-функции, которые работают в старых версиях MySQL
                $query->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(client_phone, ' ', ''), '(', ''), ')', ''), '-', ''), '+', '') LIKE ?", ['%' . $userPhoneClean . '%']);
            } else {
                // Если нет номера телефона, показываем пустой результат
                $query->where('id', -1);
            }
        }

        // Дополнительные фильтры (если клиент хочет отфильтровать свои проекты)
        if ($request->filled('status')) {
            $query->where('project_status', $request->input('status'));
        }

        if ($request->filled('object_type')) {
            $query->where('object_type', $request->input('object_type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('object_city', 'like', "%{$search}%")
                  ->orWhere('object_street', 'like', "%{$search}%")
                  ->orWhere('contract_number', 'like', "%{$search}%");
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(8);
        
        return view('client.projects.index', compact('projects'));
    }

    /**
     * Показывает детали проекта (только для чтения)
     */
    public function show(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkClientAccess($project)) {
            abort(403, 'У вас нет доступа к этому проекту');
        }
        
        // Загружаем связанные данные
        $project->load([
            'basicWorks',
            'additionalWorks', 
            'basicMaterials',
            'additionalMaterials',
            'transports',
            'stages',
            'events'
        ]);

        // Подготовка пустых данных - реальные данные будут загружены через AJAX
        $documents = [];
        $photos = [];
        $designFiles = [];
        $schemes = [];

        // Если это AJAX запрос для конкретной вкладки
        if ($request->ajax() && $request->has('tab')) {
            $tab = $request->get('tab');
            
            try {
                // Используем партнерские представления для клиентов (только для чтения)
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
        
        // Используем партнерское представление для клиентов (только для чтения)
        return view('partner.projects.show', compact('project', 'documents', 'photos', 'designFiles', 'schemes'));
    }
}
