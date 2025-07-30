<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectWork;
use App\Models\ProjectMaterial;
use App\Models\ProjectTransport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectFinanceController extends Controller
{
    public function __construct()
    {
        // Доступ к финансам проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к финансам проектов (только к сметам)
        // Клиенты имеют доступ только на чтение
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
    }

    /**
     * Получить все финансовые данные проекта
     */
    public function index(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            $works = $project->works()->orderBy('created_at', 'desc')->get();
            $materials = $project->materials()->orderBy('created_at', 'desc')->get();
            $transports = $project->transports()->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'works' => $works,
                    'materials' => $materials,
                    'transports' => $transports,
                    'summary' => [
                        'total_works' => $works->sum('amount'),
                        'total_materials' => $materials->sum('amount'),
                        'total_transport' => $transports->sum('amount'),
                        'total_paid_works' => $works->sum('paid_amount'),
                        'total_paid_materials' => $materials->sum('paid_amount'),
                        'total_paid_transport' => $transports->sum('paid_amount'),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки финансовых данных'
            ], 500);
        }
    }
    
    /**
     * Получить сводку по финансам проекта
     */
    public function summary(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            // Обновляем финансовые данные из смет
            $project->updateCostsFromEstimates();
            $project->refresh();
            
            // Получаем данные из смет (основной источник финансовой информации)
            $estimates = $project->estimates;
            
            $estimatesSummary = [
                'main' => ['total' => 0, 'count' => 0],
                'materials' => ['total' => 0, 'count' => 0], 
                'additional' => ['total' => 0, 'count' => 0]
            ];
            
            foreach ($estimates as $estimate) {
                $totals = $estimate->data['totals'] ?? [];
                $clientTotal = $totals['client_total'] ?? $totals['grand_total'] ?? 0;
                
                if (isset($estimatesSummary[$estimate->type])) {
                    $estimatesSummary[$estimate->type]['total'] += $clientTotal;
                    $estimatesSummary[$estimate->type]['count']++;
                }
            }
            
            $totalFromEstimates = $estimatesSummary['main']['total'] + 
                                $estimatesSummary['materials']['total'] + 
                                $estimatesSummary['additional']['total'];
            
            // Получаем данные планирования (для информации, не для финансовых расчетов)
            $planningData = [
                'works' => [
                    'count' => $project->works()->count(),
                    'basic_count' => $project->works()->where('type', 'basic')->count(),
                    'additional_count' => $project->works()->where('type', 'additional')->count()
                ],
                'materials' => [
                    'count' => $project->materials()->count(),
                    'basic_count' => $project->materials()->where('type', 'basic')->count(),
                    'additional_count' => $project->materials()->where('type', 'additional')->count()
                ],
                'transport' => [
                    'count' => $project->transports()->count()
                ]
            ];
            
            return response()->json([
                'success' => true,
                'summary' => [
                    // Финансовые данные из смет (основной источник)
                    'financial' => [
                        'main_works' => [
                            'total' => $estimatesSummary['main']['total'],
                            'estimates_count' => $estimatesSummary['main']['count']
                        ],
                        'materials' => [
                            'total' => $estimatesSummary['materials']['total'],
                            'estimates_count' => $estimatesSummary['materials']['count']
                        ],
                        'additional_works' => [
                            'total' => $estimatesSummary['additional']['total'],
                            'estimates_count' => $estimatesSummary['additional']['count']
                        ],
                        'total' => [
                            'amount' => $totalFromEstimates,
                            'estimates_count' => $estimates->count()
                        ]
                    ],
                    // Данные планирования (для информации)
                    'planning' => $planningData
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
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $validated['project_id'] = $project->id;
        $validated['amount'] = $validated['quantity'] * $validated['unit_price'];
        $validated['type'] = $validated['type'] ?? 'basic';
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
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $validated['amount'] = $validated['quantity'] * $validated['unit_price'];
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

    // ===== ДОПОЛНИТЕЛЬНЫЕ МЕТОДЫ =====

    /**
     * Получить сводку финансов
     */
    public function getFinanceSummary(Project $project)
    {
        $this->checkProjectAccess($project);

        $worksTotal = $project->works()->sum('amount') ?? 0;
        $worksPaid = $project->works()->sum('paid_amount') ?? 0;
        $materialsTotal = $project->materials()->sum('amount') ?? 0;
        $materialsPaid = $project->materials()->sum('paid_amount') ?? 0;
        $transportTotal = $project->transports()->sum('amount') ?? 0;
        $transportPaid = $project->transports()->sum('paid_amount') ?? 0;
        
        return response()->json([
            'success' => true,
            'summary' => [
                'works_total' => $worksTotal,
                'works_paid' => $worksPaid,
                'materials_total' => $materialsTotal,
                'materials_paid' => $materialsPaid,
                'transport_total' => $transportTotal,
                'transport_paid' => $transportPaid,
                'grand_total' => $worksTotal + $materialsTotal + $transportTotal,
                'total_paid' => $worksPaid + $materialsPaid + $transportPaid
            ]
        ]);
    }

    /**
     * Получить счетчики для табов
     */
    public function getFinanceCounts(Project $project)
    {
        $this->checkProjectAccess($project);

        return response()->json([
            'success' => true,
            'counts' => [
                'works' => $project->works()->count(),
                'materials' => $project->materials()->count(),
                'transports' => $project->transports()->count()
            ]
        ]);
    }
    
    /**
     * Получить частичные данные о работах для AJAX-запросов
     */
    public function getWorksPartial(Project $project)
    {
        $this->checkProjectAccess($project);
        $works = $project->works()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'works' => $works
        ]);
    }
    
    /**
     * Получить частичные данные о материалах для AJAX-запросов
     */
    public function getMaterialsPartial(Project $project)
    {
        $this->checkProjectAccess($project);
        $materials = $project->materials()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'materials' => $materials
        ]);
    }
    
    /**
     * Получить частичные данные о транспорте для AJAX-запросов
     */
    public function getTransportsPartial(Project $project)
    {
        $this->checkProjectAccess($project);
        $transports = $project->transports()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'transports' => $transports
        ]);
    }

    /**
     * Генерация PDF отчета по финансам проекта
     */
    public function generateFinancePDF(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            // Логирование запроса
            Log::info('Запрос на генерацию PDF', [
                'project_id' => $project->id,
                'request_content_type' => $request->header('Content-Type')
            ]);
            
            // Получаем данные для PDF из JSON запроса
            $data = $request->json()->all();
            
            // Логирование полученных данных
            Log::info('Получены данные для PDF', [
                'project_id' => $project->id,
                'summary' => isset($data['summary']),
                'works_count' => isset($data['works']) ? count($data['works']) : 0,
                'materials_count' => isset($data['materials']) ? count($data['materials']) : 0,
                'transports_count' => isset($data['transports']) ? count($data['transports']) : 0
            ]);
            
            // Подготавливаем данные для представления
            $pdfData = [
                'project' => $project,
                'summary' => $data['summary'] ?? [],
                'works' => $data['works'] ?? [],
                'materials' => $data['materials'] ?? [],
                'transports' => $data['transports'] ?? [],
                'generated_at' => now()->format('d.m.Y H:i:s')
            ];
            
            // Генерируем HTML для PDF
            $html = view('partner.projects.finance-pdf', $pdfData)->render();
            
            // Создаем папку temp если её нет
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Проверяем права доступа к папке
            if (!is_writable($tempDir)) {
                Log::error('Папка temp недоступна для записи', ['path' => $tempDir]);
                throw new \Exception('Папка для временных файлов недоступна для записи');
            }
            
            // Сохраняем HTML во временную папку
            $fileName = "finance_report_project_{$project->id}_" . now()->format('Y-m-d_H-i-s') . '.html';
            $filePath = $tempDir . '/' . $fileName;
            
            $bytesWritten = file_put_contents($filePath, $html);
            if ($bytesWritten === false) {
                Log::error('Ошибка при записи файла', ['path' => $filePath]);
                throw new \Exception('Ошибка при сохранении отчета');
            }
            
            Log::info('PDF успешно сгенерирован', [
                'project_id' => $project->id,
                'file_name' => $fileName,
                'file_size' => filesize($filePath)
            ]);
            
            // Возвращаем URL для скачивания
            $downloadUrl = route('partner.projects.finance.download-pdf', ['project' => $project->id, 'file' => $fileName]);
            
            return response()->json([
                'success' => true,
                'pdf_url' => $downloadUrl,
                'message' => 'PDF отчет успешно сгенерирован'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка при генерации PDF', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при генерации PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Скачать PDF отчет
     */
    public function downloadFinancePDF(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            $fileName = $request->get('file');
            if (!$fileName) {
                // Если не указан файл, берем последний созданный для этого проекта
                $tempDir = storage_path('app/temp');
                $pattern = "finance_report_project_{$project->id}_*.html";
                $files = glob($tempDir . '/' . $pattern);
                
                if (empty($files)) {
                    Log::error('PDF файл не найден для проекта', ['project_id' => $project->id]);
                    return response()->json([
                        'success' => false,
                        'message' => 'PDF файл не найден. Пожалуйста, сгенерируйте его сначала.'
                    ], 404);
                }
                
                // Получаем самый свежий файл
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                $filePath = $files[0];
                $fileName = basename($filePath);
            } else {
                $filePath = storage_path('app/temp/' . $fileName);
            }
            
            if (!file_exists($filePath)) {
                Log::error('Файл не найден', ['file' => $filePath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }
            
            $downloadName = str_replace('.html', '.pdf', $fileName);
            
            return response()->download($filePath, $downloadName, [
                'Content-Type' => 'text/html',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при скачивании PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Проверка доступа к проекту
     */
    private function checkProjectAccess(Project $project)
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('partner')) {
            if ($project->partner_id !== $user->id) {
                abort(403, 'Нет доступа к этому проекту');
            }
            return true;
        }
        
        if (($user->hasRole('employee') || $user->hasRole('foreman')) && isset($user->employeeProfile)) {
            if ($project->partner_id !== $user->employeeProfile->partner_id) {
                abort(403, 'Нет доступа к этому проекту');
            }
            return true;
        }
        
        if ($user->hasRole('client')) {
            // Для клиентов - проверяем номер телефона пользователя с номером в проекте
            $userPhone = $user->phone ?? $user->email; // fallback на email если нет телефона
            
            if (!$userPhone) {
                abort(403, 'Нет доступа к этому проекту');
            }
            
            // Очищаем номера телефонов от символов для сравнения
            $userPhoneClean = preg_replace('/[^0-9]/', '', $userPhone);
            $projectPhoneClean = preg_replace('/[^0-9]/', '', $project->client_phone);
            
            if ($userPhoneClean === $projectPhoneClean) {
                return true;
            }
            
            abort(403, 'Нет доступа к этому проекту');
        }
        
        abort(403, 'Нет доступа к этому проекту');
    }
}
