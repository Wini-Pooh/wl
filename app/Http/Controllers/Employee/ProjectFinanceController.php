<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectWork;
use App\Models\ProjectMaterial;
use App\Models\ProjectTransport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectFinanceController extends Controller
{
    public function __construct()
    {
        // Доступ к финансам проектов для сотрудников, прорабов, партнеров и админов
        $this->middleware(['auth', 'employee:employee,foreman,partner,admin']);
    }

    /**
     * Проверка доступа к проекту
     */
    private function checkProjectAccess(Project $project)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($user);
        
        if ($project->partner_id !== $partnerId) {
            abort(403, 'Нет доступа к этому проекту');
        }
    }

    /**
     * Получение ID партнера
     */
    private function getPartnerId($user)
    {
        if ($user->isAdmin()) {
            return request()->get('partner_id') ?? $user->id;
        }
        
        if ($user->isPartner()) {
            return $user->id;
        }
        
        $partnerId = request()->attributes->get('employee_partner_id');
        if ($partnerId) {
            return $partnerId;
        }
        
        if ($user->isEmployee() || $user->isEstimator() || $user->isForeman()) {
            $employeeProfile = $user->employeeProfile;
            if ($employeeProfile && $employeeProfile->status === 'active') {
                return $employeeProfile->partner_id;
            }
        }
        
        return null;
    }

    /**
     * Получить сводку финансов
     */
    public function getFinanceSummary(Project $project)
    {
        $this->checkProjectAccess($project);
        
        try {
            // Получаем данные работ
            $basicWorks = $project->basicWorks;
            $additionalWorks = $project->additionalWorks;
            
            // Получаем данные материалов
            $basicMaterials = $project->basicMaterials;
            $additionalMaterials = $project->additionalMaterials;
            
            // Получаем данные транспорта
            $transports = $project->transports;
            
            // Подсчеты для работ
            $worksData = [
                'basic' => [
                    'total' => $basicWorks->sum('amount'),
                    'paid' => $basicWorks->sum('paid_amount'),
                    'count' => $basicWorks->count()
                ],
                'additional' => [
                    'total' => $additionalWorks->sum('amount'),
                    'paid' => $additionalWorks->sum('paid_amount'),
                    'count' => $additionalWorks->count()
                ]
            ];
            
            // Подсчеты для материалов
            $materialsData = [
                'basic' => [
                    'total' => $basicMaterials->sum('amount'),
                    'paid' => $basicMaterials->sum('paid_amount'),
                    'count' => $basicMaterials->count()
                ],
                'additional' => [
                    'total' => $additionalMaterials->sum('amount'),
                    'paid' => $additionalMaterials->sum('paid_amount'),
                    'count' => $additionalMaterials->count()
                ]
            ];
            
            // Подсчеты для транспорта
            $transportsData = [
                'total' => $transports->sum('amount'),
                'paid' => $transports->sum('paid_amount'),
                'count' => $transports->count()
            ];
            
            // Общие подсчеты
            $grandTotal = $worksData['basic']['total'] + $worksData['additional']['total'] +
                         $materialsData['basic']['total'] + $materialsData['additional']['total'] +
                         $transportsData['total'];
                         
            $grandPaid = $worksData['basic']['paid'] + $worksData['additional']['paid'] +
                        $materialsData['basic']['paid'] + $materialsData['additional']['paid'] +
                        $transportsData['paid'];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'works' => $worksData,
                    'materials' => $materialsData,
                    'transports' => $transportsData,
                    'summary' => [
                        'total' => $grandTotal,
                        'paid' => $grandPaid,
                        'remaining' => $grandTotal - $grandPaid,
                        'progress' => $grandTotal > 0 ? round(($grandPaid / $grandTotal) * 100, 2) : 0
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки сводки по финансам'
            ], 500);
        }
    }

    // ===== РАБОТЫ =====

    /**
     * Добавить работу к проекту
     */
    public function storeWork(Request $request, Project $project)
    {
        $this->checkProjectAccess($project);

        // Нормализуем числовые поля - заменяем запятые на точки
        $normalizedData = $request->all();
        $numericFields = ['quantity', 'price', 'paid_amount'];
        
        foreach ($numericFields as $field) {
            if (isset($normalizedData[$field]) && is_string($normalizedData[$field])) {
                $normalizedData[$field] = str_replace(',', '.', $normalizedData[$field]);
            }
        }
        
        $request->merge($normalizedData);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:basic,additional',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Вычисляем общую сумму
        $validated['amount'] = $validated['quantity'] * $validated['price'];
        $validated['type'] = $validated['type'] ?? 'basic';
        $validated['project_id'] = $project->id;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $work = ProjectWork::create($validated);
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Работа успешно добавлена',
            'work' => $work
        ]);
    }

    /**
     * Получить данные работы
     */
    public function showWork(Project $project, ProjectWork $work)
    {
        $this->checkProjectAccess($project);
        
        if ($work->project_id !== $project->id) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'work' => $work
        ]);
    }

    /**
     * Обновить работу
     */
    public function updateWork(Request $request, Project $project, ProjectWork $work)
    {
        $this->checkProjectAccess($project);
        
        if ($work->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:basic,additional',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Вычисляем общую сумму
        $validated['amount'] = $validated['quantity'] * $validated['price'];
        $validated['type'] = $validated['type'] ?? 'basic';
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $work->update($validated);
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Работа успешно обновлена',
            'work' => $work
        ]);
    }

    /**
     * Удалить работу
     */
    public function destroyWork(Project $project, ProjectWork $work)
    {
        $this->checkProjectAccess($project);
        
        if ($work->project_id !== $project->id) {
            abort(404);
        }

        $work->delete();
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Работа успешно удалена'
        ]);
    }

    // ===== МАТЕРИАЛЫ =====

    /**
     * Добавить материал к проекту
     */
    public function storeMaterial(Request $request, Project $project)
    {
        $this->checkProjectAccess($project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:basic,additional',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Вычисляем общую сумму
        $validated['amount'] = $validated['quantity'] * $validated['price'];
        $validated['type'] = $validated['type'] ?? 'basic';
        $validated['project_id'] = $project->id;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $material = ProjectMaterial::create($validated);
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Материал успешно добавлен',
            'material' => $material
        ]);
    }

    /**
     * Получить данные материала
     */
    public function showMaterial(Project $project, ProjectMaterial $material)
    {
        $this->checkProjectAccess($project);
        
        if ($material->project_id !== $project->id) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'material' => $material
        ]);
    }

    /**
     * Обновить материал
     */
    public function updateMaterial(Request $request, Project $project, ProjectMaterial $material)
    {
        $this->checkProjectAccess($project);
        
        if ($material->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:basic,additional',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Вычисляем общую сумму
        $validated['amount'] = $validated['quantity'] * $validated['price'];
        $validated['type'] = $validated['type'] ?? 'basic';
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $material->update($validated);
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Материал успешно обновлен',
            'material' => $material
        ]);
    }

    /**
     * Удалить материал
     */
    public function destroyMaterial(Project $project, ProjectMaterial $material)
    {
        $this->checkProjectAccess($project);
        
        if ($material->project_id !== $project->id) {
            abort(404);
        }

        $material->delete();
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Материал успешно удален'
        ]);
    }

    // ===== ТРАНСПОРТ =====

    /**
     * Добавить транспорт к проекту
     */
    public function storeTransport(Request $request, Project $project)
    {
        $this->checkProjectAccess($project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Вычисляем общую сумму
        $validated['amount'] = $validated['quantity'] * $validated['price'];
        $validated['project_id'] = $project->id;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $transport = ProjectTransport::create($validated);
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Транспорт успешно добавлен',
            'transport' => $transport
        ]);
    }

    /**
     * Получить данные транспорта
     */
    public function showTransport(Project $project, ProjectTransport $transport)
    {
        $this->checkProjectAccess($project);
        
        if ($transport->project_id !== $project->id) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'transport' => $transport
        ]);
    }

    /**
     * Обновить транспорт
     */
    public function updateTransport(Request $request, Project $project, ProjectTransport $transport)
    {
        $this->checkProjectAccess($project);
        
        if ($transport->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // Вычисляем общую сумму
        $validated['amount'] = $validated['quantity'] * $validated['price'];
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $transport->update($validated);
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Транспорт успешно обновлен',
            'transport' => $transport
        ]);
    }

    /**
     * Удалить транспорт
     */
    public function destroyTransport(Project $project, ProjectTransport $transport)
    {
        $this->checkProjectAccess($project);
        
        if ($transport->project_id !== $project->id) {
            abort(404);
        }

        $transport->delete();
        $project->recalculateCosts();

        return response()->json([
            'success' => true,
            'message' => 'Транспорт успешно удален'
        ]);
    }

    // ===== ЧАСТИЧНЫЕ ДАННЫЕ ДЛЯ AJAX =====

    /**
     * Получить частичные данные о работах для AJAX-запросов
     */ 
    public function getWorksPartial(Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            $works = $project->works()->orderBy('created_at', 'desc')->get();
            
            $html = view('partner.projects.finance.partials.works-partial', compact('works'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $works->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Получить частичные данные о материалах для AJAX-запросов
     */
    public function getMaterialsPartial(Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            $materials = $project->materials()->orderBy('created_at', 'desc')->get();
            
            $html = view('partner.projects.finance.partials.materials-partial', compact('materials'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $materials->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Получить частичные данные о транспорте для AJAX-запросов
     */
    public function getTransportsPartial(Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            $transports = $project->transports()->orderBy('created_at', 'desc')->get();
            
            $html = view('partner.projects.finance.partials.transports-partial', compact('transports'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $transports->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
