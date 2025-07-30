<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstimateController extends Controller
{
    public function __construct()
    {
        // Доступ ко всем функциям смет для сметчиков, сотрудников, прорабов, партнеров и админов
        $this->middleware(['auth', 'employee:estimator,employee,foreman,partner,admin']);
    }

    /**
     * Показывает список смет
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        $query = Estimate::with('project')
            ->whereHas('project', function($q) use ($partnerId) {
                $q->where('partner_id', $partnerId);
            });
            
        // Фильтр по поиску
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('project', function($pq) use ($search) {
                      $pq->where('client_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Фильтр по проекту
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        // Фильтр по типу
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $estimates = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Получаем проекты для фильтра
        $projects = Project::forPartner($partnerId)->get();
        
        return view('employee.estimates.index', compact('estimates', 'projects'));
    }

    /**
     * Показывает детали сметы
     */
    public function show(Request $request, Estimate $estimate)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        // Проверяем доступ к смете
        if ($estimate->project->partner_id !== $partnerId) {
            abort(403, 'Нет доступа к этой смете');
        }
        
        $estimate->load(['project', 'template']);
        
        return view('employee.estimates.show', compact('estimate'));
    }

    /**
     * Показывает форму создания сметы (для сметчиков, прорабов, сотрудников и партнеров)
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        $projects = Project::forPartner($partnerId)->get();
        
        return view('employee.estimates.create', compact('projects'));
    }

    /**
     * Сохраняет новую смету
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Только сметчики, партнеры и админы могут создавать сметы
        if (!$user->isEstimator() && !$user->isPartner() && !$user->isAdmin()) {
            abort(403, 'Недостаточно прав для создания смет');
        }
        
        $partnerId = $this->getPartnerId($request, $user);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:work,material,transport',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        
        // Проверяем, что проект принадлежит партнеру
        $project = Project::findOrFail($validated['project_id']);
        if ($project->partner_id !== $partnerId) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        $estimate = Estimate::create([
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'items' => $validated['items'],
            'status' => 'draft',
            'created_by' => $user->id,
        ]);
        
        return redirect()->route('employee.estimates.show', $estimate)
                        ->with('success', 'Смета успешно создана!');
    }
    
    /**
     * Показывает форму редактирования сметы
     */
    public function edit(Request $request, Estimate $estimate)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        // Проверяем доступ к смете
        if ($estimate->project->partner_id !== $partnerId) {
            abort(403, 'Нет доступа к этой смете');
        }
        
        // Получаем проекты партнера для выбора
        $projects = Project::forPartner($partnerId)
            ->select('id', 'client_name', 'object_address')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('employee.estimates.edit', compact('estimate', 'projects'));
    }
    
    /**
     * Обновляет смету
     */
    public function update(Request $request, Estimate $estimate)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        // Проверяем доступ к смете
        if ($estimate->project->partner_id !== $partnerId) {
            abort(403, 'Нет доступа к этой смете');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);
        
        // Проверяем что проект принадлежит партнеру
        $project = Project::where('id', $validated['project_id'])
                          ->where('partner_id', $partnerId)
                          ->firstOrFail();
        
        // Обновляем смету
        $estimate->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'project_id' => $validated['project_id'],
            'items' => json_encode($validated['items']),
            'total_amount' => collect($validated['items'])->sum(function($item) {
                return $item['quantity'] * $item['price'];
            }),
        ]);
        
        return redirect()->route('employee.estimates.show', $estimate)
                        ->with('success', 'Смета успешно обновлена!');
    }
    
    /**
     * Удаляет смету
     */
    public function destroy(Request $request, Estimate $estimate)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        // Проверяем доступ к смете
        if ($estimate->project->partner_id !== $partnerId) {
            abort(403, 'Нет доступа к этой смете');
        }
        
        $estimate->delete();
        
        return redirect()->route('employee.estimates.index')
                        ->with('success', 'Смета успешно удалена!');
    }
    
    /**
     * Получает ID партнера для текущего пользователя
     */
    private function getPartnerId(Request $request, $user)
    {
        if ($user->isPartner() || $user->isAdmin()) {
            return $user->id;
        }
        
        $partnerId = $request->attributes->get('employee_partner_id');
        if ($partnerId) {
            return $partnerId;
        }
        
        $employeeProfile = $user->employeeProfile;
        if ($employeeProfile) {
            return $employeeProfile->partner_id;
        }
        
        return null;
    }
}
