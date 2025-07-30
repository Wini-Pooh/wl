<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estimate;
use App\Models\EstimateTemplate;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EstimateController extends Controller
{
    /**
     * Конструктор
     */
    public function __construct()
    {
        // Доступ к сметам для партнеров, сотрудников, прорабов, сметчиков и админов
        // Прорабы и сотрудники имеют полный доступ ко всем функциям смет
        // Сметчики имеют доступ только к сметам партнера
        $this->middleware(['auth', 'role:partner,employee,foreman,estimator,admin']);
    }
    
    /**
     * Получает ID партнера для текущего пользователя
     */
    private function getPartnerId(Request $request, $user)
    {
        // Админ может работать со всеми сметами
        if ($user->isAdmin()) {
            return $request->get('partner_id') ?? $user->id;
        }
        
        // Партнер работает со своими сметами
        if ($user->isPartner()) {
            return $user->id;
        }
        
        // Сотрудники, прорабы и сметчики работают со сметами своего партнера
        $partnerId = $request->attributes->get('employee_partner_id');
        if ($partnerId) {
            return $partnerId;
        }
        
        if ($user->isEmployee() || $user->isForeman() || $user->isEstimator()) {
            $employeeProfile = $user->employeeProfile;
            if ($employeeProfile && $employeeProfile->status === 'active') {
                return $employeeProfile->partner_id;
            }
        }
        
        return null;
    }
    
    /**
     * Показывает страницу управления сметами
     */
    public function index(Request $request)
    {
        $query = Estimate::with('project');
        
        // Получаем ID партнера
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        // Фильтрация по партнеру (только свои сметы, кроме админа)
        $isAdmin = $user->defaultRole && $user->defaultRole->name === 'admin';
        
        if (!$isAdmin) {
            $query->whereHas('project', function ($subQuery) use ($partnerId) {
                $subQuery->where('partner_id', $partnerId);
            });
        }
            
        // Фильтр по поиску
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%');
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
        
        $estimates = $query->orderBy('created_at', 'desc')->paginate(10);
            
        return view('partner.estimates.index', compact('estimates'));
    }
    
    /**
     * Показывает форму создания новой сметы
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        $isAdmin = $user->defaultRole && $user->defaultRole->name === 'admin';
        
        if ($isAdmin) {
            $projects = Project::all();
        } else {
            $projects = Project::forPartner($partnerId)->get();
        }
        
        $estimateTypes = Estimate::getTypes();
        
        // Получаем пользовательские шаблоны для каждого типа
        $userTemplates = [];
        foreach (array_keys($estimateTypes) as $type) {
            $userTemplates[$type] = EstimateTemplate::getTemplatesByType($type, Auth::id());
        }
        
        return view('partner.estimates.create', compact('projects', 'estimateTypes', 'userTemplates'));
    }
    
    /**
     * Сохраняет новую смету
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        $isAdmin = $user->defaultRole && $user->defaultRole->name === 'admin';
        
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                function ($attribute, $value, $fail) use ($isAdmin, $partnerId) {
                    if (!$isAdmin && !Project::where('id', $value)->where('partner_id', $partnerId)->exists()) {
                        $fail('Выбранный проект не найден или у вас нет прав доступа к нему.');
                    }
                }
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:main,additional,materials',
            'description' => 'nullable|string',
            'template_id' => 'nullable|exists:estimate_templates,id',
        ]);

        // Получаем шаблон сметы
        $template = null;
        $templateData = null;
        
        if ($validated['template_id']) {
            // Используем пользовательский шаблон
            $template = EstimateTemplate::where('id', $validated['template_id'])
                ->where('created_by', Auth::id())
                ->where('type', $validated['type'])
                ->first();
                
            if ($template) {
                $templateData = $template->data;
            }
        }
        
        // Если пользовательский шаблон не найден, используем стандартный
        if (!$templateData) {
            $template = Estimate::getTemplateByType($validated['type']);
            $templateData = $template;
        }
        
        if (!$templateData) {
            return redirect()->back()->with('error', 'Шаблон сметы не найден');
        }
        
        // Создаем директорию для хранения данных смет, если её нет
        $estimatesDir = storage_path('app/estimates');
        if (!file_exists($estimatesDir)) {
            mkdir($estimatesDir, 0755, true);
        }
        
        // Создаем новую смету
        $estimate = new Estimate();
        $estimate->project_id = $validated['project_id'];
        $estimate->name = $validated['name'];
        $estimate->type = $validated['type'];
        $estimate->status = 'in_progress';
        $estimate->created_by = Auth::id();
        $estimate->updated_by = Auth::id();
        $estimate->description = $validated['description'] ?? null;
        $estimate->data = []; // Временно пустые данные
        $estimate->save(); // Сохраняем, чтобы получить ID
        
        // Сохраняем копию шаблона в отдельный файл для этой сметы
        $estimateJsonPath = storage_path('app/estimates/' . $estimate->id . '.json');
        
        // Создаем директорию для хранения данных смет, если её нет
        $estimatesDir = storage_path('app/estimates');
        if (!file_exists($estimatesDir)) {
            mkdir($estimatesDir, 0755, true);
        }
        
        // Конвертируем шаблон в рабочий формат
        $dataToSave = $this->convertTemplateToWorkingData($templateData);
        
        // Сохраняем конвертированные данные в модель
        $estimate->data = $dataToSave;
        $estimate->save();
        
        // Сохраняем данные в JSON файл
        file_put_contents($estimateJsonPath, json_encode($dataToSave, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return redirect()->route('partner.estimates.edit', $estimate->id)
            ->with('success', 'Смета успешно создана');
    }
    
    /**
     * Показывает смету
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        $estimate = Estimate::with('project')
            ->whereHas('project', function ($query) use ($partnerId) {
                $query->where('partner_id', $partnerId);
            })
            ->findOrFail($id);
        
        return view('partner.estimates.show', compact('estimate'));
    }
    
    /**
     * Показывает форму редактирования сметы
     */
    public function edit(Request $request, $id)
    {
        $user = Auth::user();
        $partnerId = $this->getPartnerId($request, $user);
        
        if (!$partnerId) {
            abort(403, 'Не удается определить партнера');
        }
        
        $estimate = Estimate::with('project')
            ->whereHas('project', function ($query) use ($partnerId) {
                $query->where('partner_id', $partnerId);
            })
            ->findOrFail($id);
        
        // Проверяем наличие файла с данными сметы
        $estimateJsonPath = storage_path('app/estimates/' . $estimate->id . '.json');
        
        if (file_exists($estimateJsonPath)) {
            // Если файл существует, загружаем данные из него
            $estimateData = json_decode(file_get_contents($estimateJsonPath), true);
            
            // Проверяем, что данные в правильном формате
            if (!$this->isValidEstimateData($estimateData)) {
                // Если данные повреждены, восстанавливаем их из шаблона
                $template = Estimate::getTemplateByType($estimate->type);
                if ($template) {
                    $estimateData = $this->convertTemplateToWorkingData($template);
                    // Сохраняем восстановленные данные
                    file_put_contents($estimateJsonPath, json_encode($estimateData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $estimate->data = $estimateData;
                    $estimate->save();
                }
            }
        } else {
            // Если файла нет, восстанавливаем из шаблона
            $template = Estimate::getTemplateByType($estimate->type);
            if ($template) {
                $estimateData = $this->convertTemplateToWorkingData($template);
                
                // Создаем директорию для хранения данных смет, если её нет
                $estimatesDir = storage_path('app/estimates');
                if (!file_exists($estimatesDir)) {
                    mkdir($estimatesDir, 0755, true);
                }
                
                // Сохраняем восстановленные данные
                file_put_contents($estimateJsonPath, json_encode($estimateData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $estimate->data = $estimateData;
                $estimate->save();
            } else {
                // Если не удалось получить шаблон, используем данные из базы
                $estimateData = $estimate->data;
            }
        }
        
        // Инициализируем итоги если их нет
        if (!isset($estimateData['totals'])) {
            $estimateData['totals'] = [];
        }
        
        // Получаем правильные итоги
        $totals = $this->getTotalsFromEstimateData($estimateData);
        $estimateData['totals'] = array_merge($estimateData['totals'], $totals);
        
        return view('partner.estimates.edit', compact('estimate', 'estimateData'));
    }
    
    /**
     * Обновляет смету
     */
    public function update(Request $request, $id)
    {
        $estimate = Estimate::whereHas('project', function ($query) {
            $query->where('partner_id', Auth::id());
        })->findOrFail($id);
        
        $validated = $request->validate([
            'data' => 'required|array',
        ]);
        
        $estimate->data = $validated['data'];
        $estimate->updated_by = Auth::id();
        
        // Рассчитываем общую сумму сметы для клиента
        $totalAmount = 0;
        if (isset($validated['data']['totals']['client_total'])) {
            $totalAmount = $validated['data']['totals']['client_total'];
        } elseif (isset($validated['data']['totals']['grand_total'])) {
            // Fallback для старых данных
            $totalAmount = $validated['data']['totals']['grand_total'];
        }
        
        $estimate->total_amount = $totalAmount;
        $estimate->save();
        
        // Обновляем финансовые показатели проекта
        $project = $estimate->project;
        $oldCosts = [
            'work_cost' => $project->work_cost,
            'materials_cost' => $project->materials_cost,
            'additional_work_cost' => $project->additional_work_cost,
            'total_cost' => $project->total_cost,
        ];
        
        $project->updateCostsFromEstimates();
        
        // Логируем изменения
        Log::info("Обновление финансовых показателей проекта {$project->id}", [
            'old_costs' => $oldCosts,
            'new_costs' => [
                'work_cost' => $project->work_cost,
                'materials_cost' => $project->materials_cost,
                'additional_work_cost' => $project->additional_work_cost,
                'total_cost' => $project->total_cost,
            ],
            'estimate_type' => $estimate->type,
            'estimate_total' => $totalAmount
        ]);
        
        // Сохраняем обновленные данные в отдельный файл
        $estimateJsonPath = storage_path('app/estimates/' . $estimate->id . '.json');
        file_put_contents($estimateJsonPath, json_encode($validated['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return redirect()->route('partner.estimates.edit', $estimate->id)
            ->with('success', 'Смета успешно обновлена');
    }
    
    /**
     * Удаляет смету
     */
    public function destroy($id)
    {
        $estimate = Estimate::whereHas('project', function ($query) {
            $query->where('partner_id', Auth::id());
        })->findOrFail($id);
        
        // Удаляем файл с данными сметы, если он существует
        $estimateJsonPath = storage_path('app/estimates/' . $estimate->id . '.json');
        if (file_exists($estimateJsonPath)) {
            unlink($estimateJsonPath);
        }
        
        $estimate->delete();
        
        return redirect()->route('partner.estimates.index')
            ->with('success', 'Смета успешно удалена');
    }


    
    /**
     * Обновляет данные сметы без потери структуры
     */
    private function updateEstimateData($currentData, $newData)
    {
        // Если в current данных нет корректной структуры, создаем её
        if (!isset($currentData['sections']) || !is_array($currentData['sections'])) {
            $currentData['sections'] = [];
        }
        
        if (!isset($currentData['totals']) || !is_array($currentData['totals'])) {
            $currentData['totals'] = [
                'markup_percent' => 20,
                'discount_percent' => 0,
                'work_total' => 0,
                'materials_total' => 0,
                'grand_total' => 0
            ];
        }
        
        // Обновляем итоги
        if (isset($newData['totals'])) {
            $currentData['totals'] = array_merge($currentData['totals'], $newData['totals']);
        }
        
        // Обновляем секции
        if (isset($newData['sections'])) {
            foreach ($newData['sections'] as $sectionId => $newSection) {
                // Если секция существует в текущих данных
                if (isset($currentData['sections'][$sectionId])) {
                    // Обновляем заголовок секции
                    if (isset($newSection['title'])) {
                        $currentData['sections'][$sectionId]['title'] = $newSection['title'];
                    }
                    
                    // Обновляем элементы секции
                    if (isset($newSection['items']) && is_array($newSection['items'])) {
                        $currentData['sections'][$sectionId]['items'] = $newSection['items'];
                    }
                } else {
                    // Если секция новая, добавляем её
                    $currentData['sections'][$sectionId] = $newSection;
                }
            }
        }
        
        return $currentData;
    }

    /**
     * Конвертирует шаблон в рабочий формат для редактирования
     */
    private function convertTemplateToWorkingData($template)
    {
        // Создаем базовую структуру данных на основе шаблона
        $dataToSave = [
            'type' => $template['type'] ?? 'main',
            'version' => $template['version'] ?? '1.0',
            'meta' => [
                'template_name' => $template['meta']['template_name'] ?? 'Смета',
                'is_template' => false,
                'created_at' => date('c'),
                'description' => $template['meta']['description'] ?? 'Рабочая смета на основе шаблона'
            ],
            'structure' => $template['structure'] ?? [
                'columns' => [
                    [ 'title' => '№', 'width' => 50, 'type' => 'numeric', 'readonly' => true ],
                    [ 'title' => 'Наименование работ', 'width' => 300, 'type' => 'text' ],
                    [ 'title' => 'Ед.изм.', 'width' => 80, 'type' => 'text' ],
                    [ 'title' => 'Кол-во', 'width' => 80, 'type' => 'numeric' ],
                    [ 'title' => 'Цена', 'width' => 100, 'type' => 'currency' ],
                    [ 'title' => 'Стоимость', 'width' => 120, 'type' => 'currency', 'formula' => 'quantity*price', 'readonly' => true ],
                    [ 'title' => 'Наценка %', 'width' => 80, 'type' => 'numeric', 'default' => 20 ],
                    [ 'title' => 'Скидка %', 'width' => 80, 'type' => 'numeric', 'default' => 0 ],
                    [ 'title' => 'Цена клиента', 'width' => 120, 'type' => 'currency', 'formula' => 'price*(1+markup/100)*(1-discount/100)', 'readonly' => true ],
                    [ 'title' => 'Сумма клиента', 'width' => 120, 'type' => 'currency', 'formula' => 'quantity*client_price', 'readonly' => true ]
                ],
                'settings' => [
                    'readonly_columns' => [0,5,8,9],
                    'formula_columns' => [5,8,9],
                    'numeric_columns' => [0,3,4,5,6,7,8,9],
                    'currency_columns' => [4,5,8,9]
                ]
            ],
            'sections' => [],
            'totals' => $template['totals'] ?? [
                'work_total' => 0,
                'materials_total' => 0,
                'grand_total' => 0,
                'markup_percent' => 20,
                'discount_percent' => 0
            ],
            'footer' => $template['footer'] ?? [
                'items' => [
                    [
                        'name' => 'ОБЩИЙ ИТОГ:',
                        'unit' => '',
                        'quantity' => '',
                        'price' => '',
                        'markup' => '',
                        'discount' => '',
                        '_type' => 'grand_total',
                        'sum' => 0,
                        'is_grand_total' => true,
                        'readonly' => true
                    ]
                ]
            ]
        ];
        
        // Конвертируем массив секций в объект
        if (isset($template['sections']) && is_array($template['sections'])) {
            foreach ($template['sections'] as $section) {
                $sectionId = $section['id'];
                $dataToSave['sections'][$sectionId] = [
                    'title' => $section['title'],
                    'id' => $section['id'],
                    'type' => $section['type'],
                    'items' => []
                ];
                
                // Конвертируем элементы секции в формат с уникальными ID
                if (isset($section['items']) && is_array($section['items'])) {
                    foreach ($section['items'] as $index => $item) {
                        $itemId = 'row_' . time() . '_' . $index . '_' . rand(100, 999);
                        $dataToSave['sections'][$sectionId]['items'][$itemId] = [
                            'name' => $item['name'] ?? '',
                            'unit' => $item['unit'] ?? 'шт',
                            'quantity' => $item['quantity'] ?? 0,
                            'price' => $item['price'] ?? 0,
                            'markup' => $item['markup'] ?? 20,
                            'discount' => $item['discount'] ?? 0,
                            'amount' => ($item['quantity'] ?? 0) * ($item['price'] ?? 0),
                            'client_price' => ($item['price'] ?? 0) * (1 + ($item['markup'] ?? 20) / 100) * (1 - ($item['discount'] ?? 0) / 100),
                            'client_amount' => ($item['quantity'] ?? 0) * ($item['price'] ?? 0) * (1 + ($item['markup'] ?? 20) / 100) * (1 - ($item['discount'] ?? 0) / 100)
                        ];
                    }
                }
            }
        }
        
        return $dataToSave;
    }

    /**
     * Проверяет валидность данных сметы
     */
    private function isValidEstimateData($data)
    {
        // Проверяем наличие основных структур
        if (!isset($data['sections']) || !is_array($data['sections'])) {
            return false;
        }
        
        if (!isset($data['totals']) || !is_array($data['totals'])) {
            return false;
        }
        
        // Проверяем наличие базовых полей
        if (!isset($data['type']) || !isset($data['version'])) {
            return false;
        }
        
        // Проверяем наличие структуры
        if (!isset($data['structure']) || !is_array($data['structure'])) {
            return false;
        }
        
        // Проверяем наличие мета-данных
        if (!isset($data['meta']) || !is_array($data['meta'])) {
            return false;
        }
        
        // Проверяем, что sections содержат правильные данные
        foreach ($data['sections'] as $sectionId => $section) {
            if (!isset($section['title']) || !isset($section['id']) || !isset($section['type'])) {
                return false;
            }
            
            // Проверяем, что у секции есть items как объект (не массив)
            if (!isset($section['items']) || !is_array($section['items'])) {
                return false;
            }
            
            // Проверяем, что это не массив (который может быть признаком поврежденных данных)
            if (isset($section['items'][0]) && is_array($section['items'][0])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Получает детальную информацию об ошибках валидации
     */
    private function getValidationErrors($data)
    {
        $errors = [];
        
        if (!isset($data['sections']) || !is_array($data['sections'])) {
            $errors[] = 'Отсутствует или неверный тип поля sections';
        }
        
        if (!isset($data['totals']) || !is_array($data['totals'])) {
            $errors[] = 'Отсутствует или неверный тип поля totals';
        }
        
        if (!isset($data['type'])) {
            $errors[] = 'Отсутствует поле type';
        }
        
        if (!isset($data['version'])) {
            $errors[] = 'Отсутствует поле version';
        }
        
        if (!isset($data['structure']) || !is_array($data['structure'])) {
            $errors[] = 'Отсутствует или неверный тип поля structure';
        }
        
        if (!isset($data['meta']) || !is_array($data['meta'])) {
            $errors[] = 'Отсутствует или неверный тип поля meta';
        }
        
        if (isset($data['sections']) && is_array($data['sections'])) {
            foreach ($data['sections'] as $sectionId => $section) {
                if (!isset($section['title'])) {
                    $errors[] = "Секция {$sectionId}: отсутствует поле title";
                }
                if (!isset($section['id'])) {
                    $errors[] = "Секция {$sectionId}: отсутствует поле id";
                }
                if (!isset($section['type'])) {
                    $errors[] = "Секция {$sectionId}: отсутствует поле type";
                }
                if (!isset($section['items']) || !is_array($section['items'])) {
                    $errors[] = "Секция {$sectionId}: отсутствует или неверный тип поля items";
                }
            }
        }
        
        return $errors;
    }

    /**
     * Автосохранение сметы (AJAX)
     */
    public function autosave(Request $request, $id)
    {
        try {
            $estimate = Estimate::whereHas('project', function ($query) {
                $query->where('partner_id', Auth::id());
            })->findOrFail($id);
            
            // Проверяем права доступа - используем middleware проверку
            // Права уже проверены в middleware 'role:partner,admin'
            
            // Получаем данные для сохранения
            $data = $request->input('data', []);
            
            // Логируем входящие данные для отладки
            Log::info("Автосохранение: получены данные для сметы {$id}", [
                'data_keys' => array_keys($data),
                'sections_count' => isset($data['sections']) ? count($data['sections']) : 0,
                'has_totals' => isset($data['totals']),
                'has_structure' => isset($data['structure']),
                'has_meta' => isset($data['meta']),
                'has_type' => isset($data['type']),
                'has_version' => isset($data['version'])
            ]);
            
            // Проверяем валидность данных
            if (empty($data)) {
                Log::warning("Автосохранение: пустые данные для сметы {$id}");
                return response()->json(['success' => false, 'message' => 'Пустые данные для сохранения'], 400);
            }
            
            // Получаем текущие данные сметы
            $currentData = $estimate->data ?? [];
            
            // Объединяем данные с сохранением структуры
            $updatedData = $this->mergeEstimateData($currentData, $data);
            
            // Валидируем финальные данные
            if (!$this->isValidEstimateData($updatedData)) {
                Log::warning("Автосохранение: невалидные данные для сметы {$id}", [
                    'validation_errors' => $this->getValidationErrors($updatedData)
                ]);
                return response()->json(['success' => false, 'message' => 'Невалидные данные'], 400);
            }
            
            // Сохраняем в базу данных
            $estimate->data = $updatedData;
            $estimate->updated_by = Auth::id();
            
            // Обновляем итоговую сумму для клиента
            $totalAmount = 0;
            if (isset($updatedData['totals']['client_total'])) {
                $totalAmount = $updatedData['totals']['client_total'];
            } elseif (isset($updatedData['totals']['grand_total'])) {
                // Fallback для старых данных
                $totalAmount = $updatedData['totals']['grand_total'];
            }
            $estimate->total_amount = $totalAmount;
            
            $estimate->save();
            
            // Обновляем финансовые показатели проекта
            $project = $estimate->project;
            $oldCosts = [
                'work_cost' => $project->work_cost,
                'materials_cost' => $project->materials_cost,
                'additional_work_cost' => $project->additional_work_cost,
                'total_cost' => $project->total_cost,
            ];
            
            $project->updateCostsFromEstimates();
            
            // Логируем изменения
            Log::info("Обновление финансовых показателей проекта {$project->id}", [
                'old_costs' => $oldCosts,
                'new_costs' => [
                    'work_cost' => $project->work_cost,
                    'materials_cost' => $project->materials_cost,
                    'additional_work_cost' => $project->additional_work_cost,
                    'total_cost' => $project->total_cost,
                ],
                'estimate_type' => $estimate->type,
                'estimate_total' => $totalAmount
            ]);
            
            // Сохраняем в JSON файл с защитой от повреждения
            $this->saveEstimateToFile($estimate->id, $updatedData);
            
            Log::info("Автосохранение: успешно сохранена смета {$id}");
            
            return response()->json([
                'success' => true, 
                'message' => 'Автосохранение выполнено',
                'timestamp' => now()->format('H:i:s')
            ]);
            
        } catch (\Exception $e) {
            Log::error("Ошибка автосохранения сметы {$id}: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'data' => $request->input('data')
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Ошибка автосохранения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Объединяет данные сметы с сохранением структуры
     */
    private function mergeEstimateData($currentData, $newData)
    {
        // Если текущие данные пустые, используем новые
        if (empty($currentData)) {
            return $newData;
        }
        
        // Сохраняем основную структуру
        $result = $currentData;
        
        // Обновляем основные поля
        if (isset($newData['type'])) {
            $result['type'] = $newData['type'];
        }
        
        if (isset($newData['version'])) {
            $result['version'] = $newData['version'];
        }
        
        // Обновляем структуру
        if (isset($newData['structure'])) {
            $result['structure'] = array_merge(
                $result['structure'] ?? [],
                $newData['structure']
            );
        }
        
        // Обновляем мета-данные
        if (isset($newData['meta'])) {
            $result['meta'] = array_merge(
                $result['meta'] ?? [],
                $newData['meta']
            );
            // Обновляем время последнего изменения
            $result['meta']['updated_at'] = now()->toISOString();
        }
        
        // Обновляем секции
        if (isset($newData['sections'])) {
            $result['sections'] = $result['sections'] ?? [];
            foreach ($newData['sections'] as $sectionId => $sectionData) {
                if (isset($result['sections'][$sectionId])) {
                    // Обновляем существующую секцию
                    $result['sections'][$sectionId] = array_merge(
                        $result['sections'][$sectionId], 
                        $sectionData
                    );
                } else {
                    // Добавляем новую секцию
                    $result['sections'][$sectionId] = $sectionData;
                }
            }
        }
        
        // Обновляем итоги
        if (isset($newData['totals'])) {
            $result['totals'] = array_merge(
                $result['totals'] ?? [],
                $newData['totals']
            );
        }
        
        // Обновляем footer
        if (isset($newData['footer'])) {
            $result['footer'] = array_merge(
                $result['footer'] ?? [],
                $newData['footer']
            );
        }
        
        return $result;
    }

    /**
     * Сохраняет данные сметы в JSON файл с защитой от повреждения
     */
    private function saveEstimateToFile($estimateId, $data)
    {
        $filePath = storage_path("app/estimates/{$estimateId}.json");
        $backupPath = storage_path("app/estimates/{$estimateId}.json.backup");
        
        try {
            // Создаем директорию если её нет
            $dir = dirname($filePath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Создаем резервную копию если файл существует
            if (file_exists($filePath)) {
                copy($filePath, $backupPath);
            }
            
            // Сохраняем данные во временный файл
            $tempPath = $filePath . '.tmp';
            $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            if ($jsonData === false) {
                throw new \Exception('Не удалось сериализовать данные в JSON');
            }
            
            // Записываем во временный файл
            if (file_put_contents($tempPath, $jsonData) === false) {
                throw new \Exception('Не удалось записать данные во временный файл');
            }
            
            // Проверяем целостность временного файла
            $testData = json_decode(file_get_contents($tempPath), true);
            if ($testData === null) {
                throw new \Exception('Временный файл содержит некорректный JSON');
            }
            
            // Атомарное переименование (замена файла)
            if (!rename($tempPath, $filePath)) {
                throw new \Exception('Не удалось переместить временный файл');
            }
            
            // Удаляем резервную копию при успешном сохранении
            if (file_exists($backupPath)) {
                unlink($backupPath);
            }
            
        } catch (\Exception $e) {
            // В случае ошибки восстанавливаем из резервной копии
            if (file_exists($backupPath) && file_exists($filePath)) {
                copy($backupPath, $filePath);
            }
            
            // Удаляем временный файл если он существует
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            throw $e;
        }
    }

    /**
     * Экспорт сметы в PDF
     */
    public function exportPdf(Request $request, $id)
    {
        try {
            $estimate = Estimate::with('project')
                ->whereHas('project', function ($query) {
                    $query->where('partner_id', Auth::id());
                })
                ->findOrFail($id);
            $type = $request->input('type', 'master'); // full, master, client
            $clientDownload = $request->input('client_download', false);
            
            // Получаем данные сметы
            $estimateData = $request->input('data');
            
            // Если данные не переданы в запросе, получаем из сохраненного файла или базы данных
            if (!$estimateData) {
                // Пытаемся получить данные из файла
                $estimateJsonPath = storage_path('app/estimates/' . $estimate->id . '.json');
                if (file_exists($estimateJsonPath)) {
                    $estimateData = json_decode(file_get_contents($estimateJsonPath), true);
                } else {
                    // Если файл не найден, используем данные из базы
                    $estimateData = $estimate->data;
                }
            }
            
            // Получаем только заполненные строки
            $filledRows = $this->getFilledRows($estimateData);
            
            if (empty($filledRows)) {
                return response()->json(['error' => 'Нет данных для экспорта'], 400);
            }
            
            // Создаем HTML для PDF
            $html = $this->generatePdfHtml($estimate, $filledRows, $type);
            
            // Настройки для mPDF с поддержкой русского языка
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => ($type === 'full') ? 'L' : 'P', // Альбомная для полной версии
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'default_font' => 'dejavusans'
            ]);
            
            // Записываем HTML в PDF
            $mpdf->WriteHTML($html);
            
            // Формируем название файла
            $fileName = 'smeta_' . $id . '_' . $type . '.pdf';
            if ($clientDownload) {
                $fileName = 'smeta_dlya_klienta_' . $id . '.pdf';
            }
            
            // Возвращаем PDF файл
            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка экспорта PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка при экспорте PDF'], 500);
        }
    }
    
    /**
     * Экспорт сметы в Excel
     */
    public function exportExcel(Request $request, $id)
    {
        try {
            $estimate = Estimate::with('project')
                ->whereHas('project', function ($query) {
                    $query->where('partner_id', Auth::id());
                })
                ->findOrFail($id);
            $estimateData = $request->input('data');
            $type = $request->input('type', 'master'); // full, master, client
            
            // Получаем только заполненные строки
            $filledRows = $this->getFilledRows($estimateData);
            
            if (empty($filledRows)) {
                return response()->json(['error' => 'Нет данных для экспорта'], 400);
            }
            
            // Создаем новый spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Настройки листа
            $sheet->setTitle('Смета');
            $sheet->getPageSetup()->setOrientation(
                ($type === 'full') ? \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE 
                                  : \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT
            );
            
            // Заголовок документа
            $sheet->setCellValue('A1', 'СМЕТА #EST-' . str_pad($estimate->id, 4, '0', STR_PAD_LEFT));
            
            // Определяем заголовки и количество колонок в зависимости от типа
            $headers = $this->getExcelHeaders($type);
            $maxCol = chr(64 + count($headers));
            
            $sheet->mergeCells('A1:' . $maxCol . '1');
            $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Заголовки колонок
            $currentRow = 3;
            
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . $currentRow, $header);
            }
            
            // Стили для заголовков
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6E6E6']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $currentRow . ':' . $maxCol . $currentRow)->applyFromArray($headerStyle);
            
            $currentRow++;
            $itemNumber = 1;
            $totalSum = 0;
            
            // Добавляем данные
            foreach ($filledRows as $section) {
                // Заголовок секции
                $sheet->setCellValue('A' . $currentRow, $section['section']);
                $sheet->mergeCells('A' . $currentRow . ':' . $maxCol . $currentRow);
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $currentRow)->getFill()->getStartColor()->setRGB('F0F0F0');
                $currentRow++;
                
                // Строки секции
                foreach ($section['rows'] as $row) {
                    $this->addExcelRow($sheet, $currentRow, $itemNumber, $row, $type);
                    $totalSum += ($type === 'client') ? $row['client_amount'] : $row['total'];
                    $itemNumber++;
                    $currentRow++;
                }
            }
            
            // Итоговая строка
            $sheet->setCellValue('A' . $currentRow, 'ИТОГО:');
            $lastCol = chr(64 + count($headers) - 1);
            $sheet->mergeCells('A' . $currentRow . ':' . chr(64 + count($headers) - 1) . $currentRow);
            $sheet->setCellValue(chr(64 + count($headers)) . $currentRow, $totalSum);
            
            $totalStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $currentRow . ':' . $maxCol . $currentRow)->applyFromArray($totalStyle);
            $sheet->getStyle(chr(64 + count($headers)) . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
            
            // Автоподбор ширины колонок
            foreach (range('A', $maxCol) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            
            // Создаем writer и сохраняем
            $writer = new Xlsx($spreadsheet);
            
            // Создаем временный файл
            $tempFile = tempnam(sys_get_temp_dir(), 'smeta_export_');
            $writer->save($tempFile);
            
            // Возвращаем файл
            return response()->download($tempFile, 'smeta_' . $id . '_' . $type . '.xlsx')->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            Log::error('Ошибка экспорта Excel: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка при экспорте Excel'], 500);
        }
    }
    
    /**
     * Получает только заполненные строки из данных сметы
     */
    private function getFilledRows($estimateData)
    {
        $filledRows = [];
        
        if (!isset($estimateData['sections']) || empty($estimateData['sections'])) {
            return $filledRows;
        }
        
        foreach ($estimateData['sections'] as $sectionId => $section) {
            if (!isset($section['items']) || empty($section['items'])) {
                continue;
            }
            
            $sectionRows = [];
            
            foreach ($section['items'] as $itemId => $item) {
                // Проверяем, что строка заполнена (есть название и количество больше 0)
                if (!empty($item['name']) && isset($item['quantity']) && $item['quantity'] > 0) {
                    $quantity = floatval($item['quantity'] ?? 0);
                    $price = floatval($item['price'] ?? 0);
                    $total = $quantity * $price;
                    
                    // Расчет клиентской цены
                    $markup = floatval($item['markup'] ?? 0);
                    $discount = floatval($item['discount'] ?? 0);
                    
                    $clientPrice = $price;
                    if ($markup > 0) {
                        $clientPrice = $price * (1 + $markup / 100);
                    }
                    if ($discount > 0) {
                        $clientPrice = $clientPrice * (1 - $discount / 100);
                    }
                    
                    $clientAmount = $quantity * $clientPrice;
                    
                    $sectionRows[] = [
                        'name' => $item['name'],
                        'unit' => $item['unit'] ?? 'шт',
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $total,
                        'markup' => $markup,
                        'discount' => $discount,
                        'client_price' => $clientPrice,
                        'client_amount' => $clientAmount
                    ];
                }
            }
            
            // Добавляем секцию только если в ней есть заполненные строки
            if (!empty($sectionRows)) {
                $filledRows[] = [
                    'section' => $section['title'] ?? 'Раздел',
                    'rows' => $sectionRows
                ];
            }
        }
        
        return $filledRows;
    }
    
    /**
     * Генерация HTML для PDF
     */
    private function generatePdfHtml($estimate, $filledRows, $type = 'master')
    {
        // Определяем заголовки в зависимости от типа
        $headers = $this->getPdfHeaders($type);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: dejavusans; font-size: ' . ($type === 'full' ? '8px' : '12px') . '; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { font-size: 18px; margin: 0; }
                .header h2 { font-size: 14px; margin: 5px 0; }
                .info { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f0f0f0; font-weight: bold; }
                .section-header { background-color: #e6e6e6; font-weight: bold; }
                .number { text-align: center; width: 40px; }
                .quantity, .price, .total { text-align: right; }
                .total-row { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>СМЕТА</h1>
                <h2>Смета #EST-' . str_pad($estimate->id, 4, '0', STR_PAD_LEFT) . '</h2>
                <h3>' . $this->getTypeTitle($type) . '</h3>
            </div>
            
            <div class="info">
                <p><strong>Проект:</strong> ' . htmlspecialchars($estimate->project->name) . '</p>
                <p><strong>Дата:</strong> ' . date('d.m.Y') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>';
        
        foreach ($headers as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        
        $html .= '    </tr>
                </thead>
                <tbody>';
        
        $itemNumber = 1;
        $totalSum = 0;
        
        foreach ($filledRows as $section) {
            $html .= '<tr class="section-header">
                        <td colspan="' . count($headers) . '">' . htmlspecialchars($section['section']) . '</td>
                      </tr>';
            
            foreach ($section['rows'] as $row) {
                $html .= '<tr>';
                $html .= $this->generatePdfRowHtml($row, $itemNumber, $type);
                $html .= '</tr>';
                
                $totalSum += ($type === 'client') ? ($row['client_amount'] ?? $row['total']) : $row['total'];
                $itemNumber++;
            }
        }
        
        $html .= '    <tr class="total-row">
                        <td colspan="' . (count($headers) - 1) . '" style="text-align: right;"><strong>ИТОГО:</strong></td>
                        <td class="total"><strong>' . number_format($totalSum, 2, ',', ' ') . '</strong></td>
                      </tr>
                </tbody>
            </table>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Получает заголовки для PDF в зависимости от типа экспорта
     */
    private function getPdfHeaders($type)
    {
        switch ($type) {
            case 'full':
                return [
                    '№', 'Наименование работ/материалов', 'Ед.изм.', 'Кол-во', 'Цена', 'Сумма',
                    'Наценка,%', 'Скидка,%', 'Цена клиента', 'Сумма клиента'
                ];
            case 'client':
                return ['№', 'Наименование работ/материалов', 'Ед.изм.', 'Кол-во', 'Цена клиента', 'Сумма клиента'];
            case 'master':
            default:
                return ['№', 'Наименование работ/материалов', 'Ед.изм.', 'Кол-во', 'Цена', 'Сумма'];
        }
    }

    /**
     * Генерирует HTML для строки PDF в зависимости от типа
     */
    private function generatePdfRowHtml($row, $itemNumber, $type)
    {
        $html = '<td class="number">' . $itemNumber . '</td>';
        $html .= '<td>' . htmlspecialchars($row['name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['unit']) . '</td>';
        $html .= '<td class="quantity">' . number_format($row['quantity'], 2, ',', ' ') . '</td>';
        
        switch ($type) {
            case 'full':
                $html .= '<td class="price">' . number_format($row['price'], 2, ',', ' ') . '</td>';
                $html .= '<td class="total">' . number_format($row['total'], 2, ',', ' ') . '</td>';
                $html .= '<td class="quantity">' . number_format($row['markup'] ?? 0, 2, ',', ' ') . '</td>';
                $html .= '<td class="quantity">' . number_format($row['discount'] ?? 0, 2, ',', ' ') . '</td>';
                $html .= '<td class="price">' . number_format($row['client_price'] ?? $row['price'], 2, ',', ' ') . '</td>';
                $html .= '<td class="total">' . number_format($row['client_amount'] ?? $row['total'], 2, ',', ' ') . '</td>';
                break;
                
            case 'client':
                $html .= '<td class="price">' . number_format($row['client_price'] ?? $row['price'], 2, ',', ' ') . '</td>';
                $html .= '<td class="total">' . number_format($row['client_amount'] ?? $row['total'], 2, ',', ' ') . '</td>';
                break;
                
            case 'master':
            default:
                $html .= '<td class="price">' . number_format($row['price'], 2, ',', ' ') . '</td>';
                $html .= '<td class="total">' . number_format($row['total'], 2, ',', ' ') . '</td>';
                break;
        }
        
        return $html;
    }

    /**
     * Возвращает название типа экспорта
     */
    private function getTypeTitle($type)
    {
        switch ($type) {
            case 'full':
                return '(Полная версия)';
            case 'client':
                return '(Для клиента)';
            case 'master':
            default:
                return '(Для мастера)';
        }
    }

    /**
     * Получает заголовки для Excel в зависимости от типа экспорта
     */
    private function getExcelHeaders($type)
    {
        switch ($type) {
            case 'full':
                return [
                    '№', 'Наименование работ/материалов', 'Ед.изм.', 'Кол-во', 'Цена', 'Сумма',
                    'Наценка, %', 'Скидка, %', 'Цена клиента', 'Сумма клиента'
                ];
            case 'client':
                return ['№', 'Наименование работ/материалов', 'Ед.изм.', 'Кол-во', 'Цена клиента', 'Сумма клиента'];
            case 'master':
            default:
                return ['№', 'Наименование работ/материалов', 'Ед.изм.', 'Кол-во', 'Цена', 'Сумма'];
        }
    }

    /**
     * Добавляет строку в Excel в зависимости от типа экспорта
     */
    private function addExcelRow($sheet, $currentRow, $itemNumber, $row, $type)
    {
        switch ($type) {
            case 'full':
                $sheet->setCellValue('A' . $currentRow, $itemNumber);
                $sheet->setCellValue('B' . $currentRow, $row['name']);
                $sheet->setCellValue('C' . $currentRow, $row['unit']);
                $sheet->setCellValue('D' . $currentRow, $row['quantity']);
                $sheet->setCellValue('E' . $currentRow, $row['price']);
                $sheet->setCellValue('F' . $currentRow, $row['total']);
                $sheet->setCellValue('G' . $currentRow, $row['markup'] ?? 0);
                $sheet->setCellValue('H' . $currentRow, $row['discount'] ?? 0);
                $sheet->setCellValue('I' . $currentRow, $row['client_price'] ?? $row['price']);
                $sheet->setCellValue('J' . $currentRow, $row['client_amount'] ?? $row['total']);
                
                // Форматирование числовых значений
                foreach (['D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
                    $sheet->getStyle($col . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                }
                break;
                
            case 'client':
                $sheet->setCellValue('A' . $currentRow, $itemNumber);
                $sheet->setCellValue('B' . $currentRow, $row['name']);
                $sheet->setCellValue('C' . $currentRow, $row['unit']);
                $sheet->setCellValue('D' . $currentRow, $row['quantity']);
                $sheet->setCellValue('E' . $currentRow, $row['client_price'] ?? $row['price']);
                $sheet->setCellValue('F' . $currentRow, $row['client_amount'] ?? $row['total']);
                
                // Форматирование числовых значений
                foreach (['D', 'E', 'F'] as $col) {
                    $sheet->getStyle($col . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                }
                break;
                
            case 'master':
            default:
                $sheet->setCellValue('A' . $currentRow, $itemNumber);
                $sheet->setCellValue('B' . $currentRow, $row['name']);
                $sheet->setCellValue('C' . $currentRow, $row['unit']);
                $sheet->setCellValue('D' . $currentRow, $row['quantity']);
                $sheet->setCellValue('E' . $currentRow, $row['price']);
                $sheet->setCellValue('F' . $currentRow, $row['total']);
                
                // Форматирование числовых значений
                foreach (['D', 'E', 'F'] as $col) {
                    $sheet->getStyle($col . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                }
                break;
        }
    }

    /**
     * Получает правильные итоги из данных сметы
     */
    private function getTotalsFromEstimateData($estimateData)
    {
        $totals = [
            'work_cost' => 0,
            'materials_cost' => 0,
            'client_total' => 0,
            'profit_amount' => 0,
            'markup_percent' => 20,
            'discount_percent' => 0
        ];
        
        if (isset($estimateData['totals'])) {
            $dataTotals = $estimateData['totals'];
            
            // Новый формат данных
            if (isset($dataTotals['client_total'])) {
                $totals['client_total'] = $dataTotals['client_total'];
            }
            if (isset($dataTotals['work_cost'])) {
                $totals['work_cost'] = $dataTotals['work_cost'];
            }
            if (isset($dataTotals['materials_cost'])) {
                $totals['materials_cost'] = $dataTotals['materials_cost'];
            }
            if (isset($dataTotals['profit_amount'])) {
                $totals['profit_amount'] = $dataTotals['profit_amount'];
            }
            
            // Старый формат данных для совместимости
            if (isset($dataTotals['grand_total']) && !isset($dataTotals['client_total'])) {
                $totals['client_total'] = $dataTotals['grand_total'];
            }
            if (isset($dataTotals['work_total']) && !isset($dataTotals['work_cost'])) {
                $totals['work_cost'] = $dataTotals['work_total'];
            }
            if (isset($dataTotals['materials_total']) && !isset($dataTotals['materials_cost'])) {
                $totals['materials_cost'] = $dataTotals['materials_total'];
            }
            
            // Проценты
            if (isset($dataTotals['markup_percent'])) {
                $totals['markup_percent'] = $dataTotals['markup_percent'];
            }
            if (isset($dataTotals['discount_percent'])) {
                $totals['discount_percent'] = $dataTotals['discount_percent'];
            }
        }
        
        return $totals;
    }

    /**
     * Сохранить текущую смету как шаблон
     */
    public function saveAsTemplate(Request $request, $id)
    {
        $estimate = Estimate::with('project')
            ->whereHas('project', function ($query) {
                $query->where('partner_id', Auth::id());
            })
            ->findOrFail($id);

        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'template_description' => 'nullable|string|max:500',
        ]);

        // Загружаем текущие данные сметы
        $estimateJsonPath = storage_path('app/estimates/' . $estimate->id . '.json');
        
        if (!file_exists($estimateJsonPath)) {
            return response()->json(['error' => 'Файл сметы не найден'], 404);
        }

        $estimateData = json_decode(file_get_contents($estimateJsonPath), true);
        
        if (!$estimateData) {
            return response()->json(['error' => 'Не удалось прочитать данные сметы'], 500);
        }

        // Создаем новый шаблон
        $template = EstimateTemplate::create([
            'name' => $validated['template_name'],
            'type' => $estimate->type,
            'data' => $estimateData,
            'created_by' => Auth::id(),
            'description' => $validated['template_description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Шаблон успешно сохранен',
            'template' => $template
        ]);
    }

    /**
     * Получить шаблоны для типа сметы
     */
    public function getTemplatesByType($type)
    {
        if (!$type) {
            return response()->json(['error' => 'Тип сметы не указан'], 400);
        }

        $templates = EstimateTemplate::getTemplatesByType($type, Auth::id());
        
        return response()->json([
            'templates' => $templates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'description' => $template->description,
                    'created_at' => $template->created_at->format('d.m.Y H:i'),
                ];
            })
        ]);
    }

}
