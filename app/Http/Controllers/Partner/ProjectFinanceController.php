<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectWork;
use App\Models\ProjectMaterial;
use App\Models\ProjectTransport;
use App\Models\ProjectFinance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectFinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
    }

    /**
     * Безопасная проверка доступа к проекту
     */
    private function checkProjectAccess($projectId)
    {
        $user = Auth::user();
        
        // Найти проект по ID
        $project = Project::find($projectId);
        if (!$project) {
            Log::error('Project not found', ['project_id' => $projectId, 'user_id' => $user->id]);
            abort(404, 'Проект не найден');
        }

        // Проверка прав доступа для разных ролей
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (in_array('admin', $userRoles)) {
            return $project; // Админ имеет доступ ко всем проектам
        }

        if (in_array('partner', $userRoles)) {
            if ($project->partner_id !== $user->id) {
                Log::warning('Access denied - partner mismatch', [
                    'project_id' => $projectId,
                    'project_partner_id' => $project->partner_id,
                    'user_id' => $user->id
                ]);
                abort(403, 'Нет доступа к этому проекту');
            }
            return $project;
        }

        if (in_array('employee', $userRoles) || in_array('foreman', $userRoles)) {
            // Получаем партнера сотрудника/прораба
            $employee = $user->employee;
            if (!$employee) {
                Log::error('Employee record not found', ['user_id' => $user->id]);
                abort(403, 'Данные сотрудника не найдены');
            }

            if ($project->partner_id !== $employee->partner_id) {
                Log::warning('Access denied - employee partner mismatch', [
                    'project_id' => $projectId,
                    'project_partner_id' => $project->partner_id,
                    'employee_partner_id' => $employee->partner_id,
                    'user_id' => $user->id
                ]);
                abort(403, 'Нет доступа к этому проекту');
            }
            return $project;
        }

        if (in_array('client', $userRoles)) {
            // Проверяем по номеру телефона
            $userPhone = preg_replace('/[^0-9]/', '', $user->phone ?? '');
            $projectPhone = preg_replace('/[^0-9]/', '', $project->client_phone ?? '');
            
            if (strlen($userPhone) >= 10 && strlen($projectPhone) >= 10) {
                $userPhoneClean = substr($userPhone, -10);
                $projectPhoneClean = substr($projectPhone, -10);
                
                if ($userPhoneClean === $projectPhoneClean) {
                    return $project;
                }
            }
            
            Log::warning('Access denied - client phone mismatch', [
                'project_id' => $projectId,
                'user_phone' => $userPhone,
                'project_phone' => $projectPhone,
                'user_id' => $user->id
            ]);
            abort(403, 'Нет доступа к этому проекту');
        }

        abort(403, 'Нет доступа к этому проекту');
    }

    /**
     * Получить сводку по финансам проекта
     */
    public function getFinanceSummary($projectId)
    {
        try {
            $project = $this->checkProjectAccess($projectId);

            // Подсчет финансовых данных
            $worksTotal = $project->works()->sum('amount') ?? 0;
            $worksPaid = $project->works()->sum('paid_amount') ?? 0;
            $materialsTotal = $project->materials()->sum('amount') ?? 0;
            $materialsPaid = $project->materials()->sum('paid_amount') ?? 0;
            $transportTotal = $project->transports()->sum('amount') ?? 0;
            $transportPaid = $project->transports()->sum('paid_amount') ?? 0;
            
            $summary = [
                'works_total' => $worksTotal,
                'works_paid' => $worksPaid,
                'materials_total' => $materialsTotal,
                'materials_paid' => $materialsPaid,
                'transport_total' => $transportTotal,
                'transport_paid' => $transportPaid,
                'grand_total' => $worksTotal + $materialsTotal + $transportTotal,
                'total_paid' => $worksPaid + $materialsPaid + $transportPaid
            ];
            
            $counts = [
                'works' => $project->works()->count(),
                'materials' => $project->materials()->count(),
                'transports' => $project->transports()->count()
            ];
            
            $html = view('partner.projects.finance.partials.finance-summary', compact('summary', 'counts'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'summary' => $summary,
                'counts' => $counts
            ]);
            
        } catch (\Exception $e) {
            Log::error('Finance summary error', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки финансовой сводки: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить счетчики для табов
     */
    public function getFinanceCounts($projectId)
    {
        try {
            $project = $this->checkProjectAccess($projectId);

            $counts = [
                'works' => $project->works()->count(),
                'materials' => $project->materials()->count(),
                'transports' => $project->transports()->count()
            ];

            return response()->json([
                'success' => true,
                'counts' => $counts
            ]);
            
        } catch (\Exception $e) {
            Log::error('Finance counts error', [
                'project_id' => $projectId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки счетчиков'
            ], 500);
        }
    }
    
    /**
     * Получить частичные данные о работах для AJAX-запросов
     */
    public function getWorksPartial($projectId)
    {
        try {
            Log::info('Finance Works Partial Request', [
                'project_id' => $projectId,
                'user_id' => Auth::id(),
                'url' => request()->url()
            ]);
            
            $project = $this->checkProjectAccess($projectId);
            $works = $project->works()->orderBy('created_at', 'desc')->get();
            
            $html = view('partner.projects.finance.partials.works-partial', compact('works'))->render();
            
            Log::info('Finance Works Partial Success', [
                'project_id' => $project->id,
                'count' => $works->count()
            ]);
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $works->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Finance Works Partial Error', [
                'project_id' => $projectId ?? 'unknown',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки работ: ' . $e->getMessage(),
                'html' => '<div class="alert alert-danger">Ошибка загрузки данных о работах</div>'
            ], 500);
        }
    }

    /**
     * Получить частичные данные о материалах для AJAX-запросов
     */
    public function getMaterialsPartial($projectId)
    {
        try {
            Log::info('Finance Materials Partial Request START', [
                'project_id' => $projectId,
                'user_id' => Auth::id(),
                'user_roles' => Auth::user() ? Auth::user()->roles->pluck('name')->toArray() : [],
                'url' => request()->url()
            ]);
            
            $project = $this->checkProjectAccess($projectId);
            $materials = $project->materials()->orderBy('created_at', 'desc')->get();
            
            $html = view('partner.projects.finance.partials.materials-partial', compact('materials'))->render();
            
            Log::info('Finance Materials Partial Success', [
                'project_id' => $project->id,
                'count' => $materials->count()
            ]);
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $materials->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Finance Materials Partial Error', [
                'project_id' => $projectId ?? 'unknown',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки материалов: ' . $e->getMessage(),
                'html' => '<div class="alert alert-danger">Ошибка загрузки данных о материалах</div>'
            ], 500);
        }
    }

    /**
     * Получить частичные данные о транспорте для AJAX-запросов
     */
    public function getTransportsPartial($projectId)
    {
        try {
            Log::info('Finance Transports Partial Request', [
                'project_id' => $projectId,
                'user_id' => Auth::id()
            ]);
            
            $project = $this->checkProjectAccess($projectId);
            $transports = $project->transports()->orderBy('created_at', 'desc')->get();
            
            $html = view('partner.projects.finance.partials.transports-partial', compact('transports'))->render();
            
            Log::info('Finance Transports Partial Success', [
                'project_id' => $project->id,
                'count' => $transports->count()
            ]);
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $transports->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Finance Transports Partial Error', [
                'project_id' => $projectId ?? 'unknown',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки транспорта: ' . $e->getMessage(),
                'html' => '<div class="alert alert-danger">Ошибка загрузки данных о транспорте</div>'
            ], 500);
        }
    }

    // ===== CRUD ОПЕРАЦИИ ДЛЯ РАБОТ =====

    /**
     * Добавить работу к проекту
     */
    public function storeWork(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project->id);

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

            $validated['amount'] = $validated['quantity'] * $validated['price'];
            $validated['project_id'] = $project->id;
            $validated['type'] = $validated['type'] ?? 'basic';
            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            $work = ProjectWork::create($validated);
            $project->recalculateCosts();

            return response()->json([
                'success' => true,
                'message' => 'Работа успешно добавлена',
                'work' => $work
            ]);
            
        } catch (\Exception $e) {
            Log::error('Store work error', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка добавления работы: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Добавить материал к проекту
     */
    public function storeMaterial(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project->id);

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

            $validated['amount'] = $validated['quantity'] * $validated['price'];
            $validated['project_id'] = $project->id;
            $validated['type'] = $validated['type'] ?? 'basic';
            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            $material = ProjectMaterial::create($validated);
            $project->recalculateCosts();

            return response()->json([
                'success' => true,
                'message' => 'Материал успешно добавлен',
                'material' => $material
            ]);
            
        } catch (\Exception $e) {
            Log::error('Store material error', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка добавления материала: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Добавить транспорт к проекту
     */
    public function storeTransport(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project->id);

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
                'unit' => 'nullable|string|max:50',
                'quantity' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'payment_date' => 'nullable|date',
                'description' => 'nullable|string',
            ]);

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
            
        } catch (\Exception $e) {
            Log::error('Store transport error', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка добавления транспорта: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===== FULL CRUD OPERATIONS FOR WORKS =====

    /**
     * Получить данные работы
     */
    public function showWork(Project $project, ProjectWork $work)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($work->project_id !== $project->id) {
                abort(404, 'Работа не найдена в данном проекте');
            }

            return response()->json([
                'success' => true,
                'work' => $work
            ]);
            
        } catch (\Exception $e) {
            Log::error('Show work error', [
                'project_id' => $project->id,
                'work_id' => $work->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения данных работы: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить работу
     */
    public function updateWork(Request $request, Project $project, ProjectWork $work)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($work->project_id !== $project->id) {
                abort(404, 'Работа не найдена в данном проекте');
            }

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
                'unit' => 'nullable|string|max:50',
                'quantity' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'payment_date' => 'nullable|date',
                'description' => 'nullable|string',
                'type' => 'nullable|string|in:basic,additional'
            ]);

            $validated['amount'] = $validated['quantity'] * $validated['price'];
            $validated['type'] = $validated['type'] ?? 'basic';
            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            $work->update($validated);
            $project = $this->checkProjectAccess($project->id);
            $project->recalculateCosts();

            return response()->json([
                'success' => true,
                'message' => 'Работа успешно обновлена',
                'work' => $work
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update work error', [
                'project_id' => $project->id,
                'work_id' => $work->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления работы: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить работу
     */
    public function destroyWork(Project $project, ProjectWork $work)
    {
        try {
            Log::info('Destroy work called', [
                'project_id' => $project->id,
                'work_id' => $work->id,
                'work_project_id' => $work->project_id,
                'user_id' => auth()->id()
            ]);
            
            $this->checkProjectAccess($project->id);
            
            if ($work->project_id !== $project->id) {
                Log::warning('Work does not belong to project', [
                    'project_id' => $project->id,
                    'work_id' => $work->id,
                    'work_project_id' => $work->project_id
                ]);
                abort(404, 'Работа не найдена в данном проекте');
            }

            $work->delete();
            $project = $this->checkProjectAccess($project->id);
            $project->recalculateCosts();

            Log::info('Work deleted successfully', [
                'project_id' => $project->id,
                'work_id' => $work->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Работа успешно удалена'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Destroy work error', [
                'project_id' => $project->id ?? 'unknown',
                'work_id' => $work->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления работы: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===== FULL CRUD OPERATIONS FOR MATERIALS =====

    /**
     * Получить данные материала
     */
    public function showMaterial(Project $project, ProjectMaterial $material)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($material->project_id !== $project->id) {
                abort(404, 'Материал не найден в данном проекте');
            }

            return response()->json([
                'success' => true,
                'material' => $material
            ]);
            
        } catch (\Exception $e) {
            Log::error('Show material error', [
                'project_id' => $project->id,
                'material_id' => $material->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения данных материала: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить материал
     */
    public function updateMaterial(Request $request, Project $project, ProjectMaterial $material)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($material->project_id !== $project->id) {
                abort(404, 'Материал не найден в данном проекте');
            }

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
                'unit' => 'nullable|string|max:50',
                'quantity' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'payment_date' => 'nullable|date',
                'description' => 'nullable|string',
                'type' => 'nullable|string|in:basic,additional'
            ]);

            $validated['amount'] = $validated['quantity'] * $validated['price'];
            $validated['type'] = $validated['type'] ?? 'basic';
            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            $material->update($validated);
            $project = $this->checkProjectAccess($project->id);
            $project->recalculateCosts();

            return response()->json([
                'success' => true,
                'message' => 'Материал успешно обновлен',
                'material' => $material
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update material error', [
                'project_id' => $project->id,
                'material_id' => $material->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления материала: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить материал
     */
    public function destroyMaterial(Project $project, ProjectMaterial $material)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($material->project_id !== $project->id) {
                abort(404, 'Материал не найден в данном проекте');
            }

            $material->delete();
            $project = $this->checkProjectAccess($project->id);
            $project->recalculateCosts();

            return response()->json([
                'success' => true,
                'message' => 'Материал успешно удален'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Destroy material error', [
                'project_id' => $project->id,
                'material_id' => $material->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления материала: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===== FULL CRUD OPERATIONS FOR TRANSPORTS =====

    /**
     * Получить данные транспорта
     */
    public function showTransport(Project $project, ProjectTransport $transport)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($transport->project_id !== $project->id) {
                abort(404, 'Транспорт не найден в данном проекте');
            }

            return response()->json([
                'success' => true,
                'transport' => $transport
            ]);
            
        } catch (\Exception $e) {
            Log::error('Show transport error', [
                'project_id' => $project->id,
                'transport_id' => $transport->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения данных транспорта: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить транспорт
     */
    public function updateTransport(Request $request, Project $project, ProjectTransport $transport)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($transport->project_id !== $project->id) {
                abort(404, 'Транспорт не найден в данном проекте');
            }

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
                'unit' => 'nullable|string|max:50',
                'quantity' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'payment_date' => 'nullable|date',
                'description' => 'nullable|string',
            ]);

            $validated['amount'] = $validated['quantity'] * $validated['price'];
            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            $transport->update($validated);
            $project = $this->checkProjectAccess($project->id);
            $project->recalculateCosts();

            return response()->json([
                'success' => true,
                'message' => 'Транспорт успешно обновлен',
                'transport' => $transport
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update transport error', [
                'project_id' => $project->id,
                'transport_id' => $transport->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления транспорта: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить транспорт
     */
    public function destroyTransport(Project $project, ProjectTransport $transport)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($transport->project_id !== $project->id) {
                abort(404, 'Транспорт не найден в данном проекте');
            }

            $transport->delete();
            $project = $this->checkProjectAccess($project->id);
            $project->recalculateCosts();

            return response()->json([
                'success' => true,
                'message' => 'Транспорт успешно удален'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Destroy transport error', [
                'project_id' => $project->id,
                'transport_id' => $transport->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления транспорта: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===== ADDITIONAL METHODS =====

    /**
     * Показать главную страницу финансов
     */
    public function index(Project $project)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            // Загружаем все финансовые данные
            $project->load(['works', 'materials', 'transports', 'finances']);
            
            return view('partner.projects.finance.index', compact('project'));
            
        } catch (\Exception $e) {
            Log::error('Finance index error', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Ошибка загрузки финансовой страницы']);
        }
    }

    /**
     * Добавить финансовую запись
     */
    public function storeFinance(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project->id);

            $validated = $request->validate([
                'type' => 'required|string|in:income,expense,planned_income,planned_expense',
                'category' => 'nullable|string|max:255',
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'payment_method' => 'nullable|string|max:100'
            ]);

            // Конвертируем поле date в operation_date для модели
            $validated['operation_date'] = $validated['date'];
            unset($validated['date']);
            
            // Устанавливаем статус на основе типа
            if (in_array($validated['type'], ['planned_income', 'planned_expense'])) {
                $validated['is_planned'] = true;
                $validated['status'] = 'planned';
                // Для планируемых операций убираем префикс planned_
                $validated['type'] = str_replace('planned_', '', $validated['type']);
            } else {
                $validated['is_planned'] = false;
                $validated['status'] = 'paid';
            }

            $validated['project_id'] = $project->id;
            $finance = ProjectFinance::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Финансовая запись успешно добавлена',
                'finance' => $finance
            ]);
            
        } catch (\Exception $e) {
            Log::error('Store finance error', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка добавления финансовой записи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Показать финансовую запись
     */
    public function showFinance(Project $project, ProjectFinance $finance)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($finance->project_id !== $project->id) {
                abort(404, 'Финансовая запись не найдена в данном проекте');
            }

            return response()->json([
                'success' => true,
                'finance' => $finance
            ]);
            
        } catch (\Exception $e) {
            Log::error('Show finance error', [
                'project_id' => $project->id,
                'finance_id' => $finance->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения финансовой записи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить финансовую запись
     */
    public function updateFinance(Request $request, Project $project, ProjectFinance $finance)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($finance->project_id !== $project->id) {
                abort(404, 'Финансовая запись не найдена в данном проекте');
            }

            $validated = $request->validate([
                'type' => 'required|string|in:income,expense',
                'category' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'payment_method' => 'nullable|string|max:100'
            ]);

            $finance->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Финансовая запись успешно обновлена',
                'finance' => $finance
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update finance error', [
                'project_id' => $project->id,
                'finance_id' => $finance->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка обновления финансовой записи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить финансовую запись
     */
    public function destroyFinance(Project $project, ProjectFinance $finance)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            if ($finance->project_id !== $project->id) {
                abort(404, 'Финансовая запись не найдена в данном проекте');
            }

            $finance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Финансовая запись успешно удалена'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Destroy finance error', [
                'project_id' => $project->id,
                'finance_id' => $finance->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления финансовой записи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Генерировать PDF отчет
     */
    public function generateFinancePDF(Project $project)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            // Загружаем все данные для отчета
            $project->load(['works', 'materials', 'transports', 'finances']);
            
            // Здесь должна быть логика генерации PDF
            // Пока возвращаем заглушку
            return response()->json([
                'success' => true,
                'message' => 'PDF отчет будет сгенерирован',
                'download_url' => route('partner.projects.finance-pdf', $project)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Generate PDF error', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка генерации PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Скачать PDF отчет
     */
    public function downloadFinancePDF(Project $project)
    {
        try {
            $this->checkProjectAccess($project->id);
            
            // Здесь должна быть логика скачивания PDF
            // Пока возвращаем заглушку
            return response()->json([
                'success' => false,
                'message' => 'Функция скачивания PDF пока не реализована'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Download PDF error', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка скачивания PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
