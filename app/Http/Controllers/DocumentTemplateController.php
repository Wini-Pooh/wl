<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DocumentTemplateController extends Controller
{
    /**
     * Список шаблонов
     */
    public function index()
    {
        $templates = DocumentTemplate::active()
            ->with('creator')
            ->orderBy('name')
            ->paginate(20);
            
        return view('documents.templates.index', compact('templates'));
    }

    /**
     * Форма создания шаблона
     */
    public function create()
    {
        return view('documents.templates.create');
    }

    /**
     * Сохранение нового шаблона
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type' => 'required|string|in:contract,act,invoice,estimate,technical,other',
            'template_content' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $template = new DocumentTemplate();
            $template->fill($request->only([
                'name', 'description', 'document_type', 'template_content', 'variables'
            ]));
            
            $template->created_by = Auth::id();
            $template->is_active = true;
            
            $template->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Шаблон успешно создан',
                    'template_id' => $template->id
                ]);
            }

            return redirect()->route('document-templates.show', $template->id)
                ->with('success', 'Шаблон успешно создан');

        } catch (\Exception $e) {
            Log::error('Ошибка при создании шаблона: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Произошла ошибка при создании шаблона'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Произошла ошибка при создании шаблона')
                ->withInput();
        }
    }

    /**
     * Просмотр шаблона
     */
    public function show($id)
    {
        $template = DocumentTemplate::with('creator')->findOrFail($id);
        
        return view('documents.templates.show', compact('template'));
    }

    /**
     * Редактирование шаблона
     */
    public function edit($id)
    {
        $template = DocumentTemplate::with('creator')
            ->withCount('documents')
            ->findOrFail($id);
        
        return view('documents.templates.edit', compact('template'));
    }

    /**
     * Обновление шаблона
     */
    public function update(Request $request, $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type' => 'required|string|in:contract,act,invoice,estimate,technical,other',
            'template_content' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $template->update($request->only([
                'name', 'description', 'document_type', 'template_content', 
                'variables', 'is_active'
            ]));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Шаблон успешно обновлен'
                ]);
            }

            return redirect()->route('document-templates.show', $template->id)
                ->with('success', 'Шаблон успешно обновлен');

        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении шаблона: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Произошла ошибка при обновлении шаблона'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Произошла ошибка при обновлении шаблона')
                ->withInput();
        }
    }

    /**
     * Удаление шаблона
     */
    public function destroy($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        // Проверяем, что нет документов, использующих этот шаблон
        if ($template->documents()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить шаблон, который используется в документах'
            ], 400);
        }

        try {
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Шаблон успешно удален'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении шаблона: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при удалении шаблона'
            ], 500);
        }
    }

    /**
     * Получение шаблона для создания документа
     */
    public function getTemplate($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'content' => $template->template_content,
                'variables' => $template->extractVariables(),
                'document_type' => $template->document_type,
            ]
        ]);
    }

    /**
     * Получение списка проектов для автозаполнения
     */
    public function getProjects()
    {
        try {
            $projects = Project::select('id', 'client_first_name', 'client_last_name', 'object_type', 'work_type')
                ->orderBy('client_last_name')
                ->orderBy('client_first_name')
                ->get()
                ->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'name' => $project->client_last_name . ' ' . $project->client_first_name,
                        'object_type' => $project->object_type,
                        'work_type' => $project->work_type,
                    ];
                });

            return response()->json([
                'success' => true,
                'projects' => $projects
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка проектов: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка проектов'
            ], 500);
        }
    }

    /**
     * Получение списка сотрудников для автозаполнения
     */
    public function getEmployees()
    {
        try {
            $employees = Employee::select('id', 'first_name', 'last_name', 'middle_name', 'role', 'phone', 'email')
                ->where('status', 'active')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->full_name,
                        'role' => $employee->role,
                        'phone' => $employee->phone,
                        'email' => $employee->email,
                    ];
                });

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка сотрудников: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка сотрудников'
            ], 500);
        }
    }

    /**
     * Получение данных проекта для автозаполнения
     */
    public function getProjectData($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            return response()->json([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'client_first_name' => $project->client_first_name,
                    'client_last_name' => $project->client_last_name,
                    'client_phone' => $project->client_phone,
                    'object_type' => $project->object_type,
                    'work_type' => $project->work_type,
                    'project_status' => $project->project_status,
                    'passport_series' => $project->passport_series,
                    'passport_number' => $project->passport_number,
                    'passport_issued_by' => $project->passport_issued_by,
                    'passport_issued_date' => $project->passport_issued_date,
                    'passport_department_code' => $project->passport_department_code,
                    'birth_date' => $project->birth_date,
                    'birth_place' => $project->birth_place,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении данных проекта: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении данных проекта'
            ], 500);
        }
    }

    /**
     * Получение списка шаблонов для API
     */
    public function getTemplatesList()
    {
        try {
            $templates = DocumentTemplate::active()
                ->select('id', 'name', 'document_type', 'description')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка шаблонов: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка шаблонов'
            ], 500);
        }
    }

    /**
     * Получение полей шаблона по типу
     */
    public function getTemplateFields($templateType)
    {
        try {
            // Базовые поля для всех типов документов
            $baseFields = [
                'client_name' => 'Имя клиента',
                'client_phone' => 'Телефон клиента',
                'project_type' => 'Тип проекта',
                'work_type' => 'Тип работ',
                'current_date' => 'Текущая дата',
            ];

            // Специфичные поля для разных типов документов
            $specificFields = [];
            
            switch ($templateType) {
                case 'contract':
                    $specificFields = [
                        'passport_series' => 'Серия паспорта',
                        'passport_number' => 'Номер паспорта',
                        'passport_issued_by' => 'Кем выдан паспорт',
                        'passport_issued_date' => 'Дата выдачи паспорта',
                        'birth_date' => 'Дата рождения',
                        'birth_place' => 'Место рождения',
                    ];
                    break;
                case 'act':
                    $specificFields = [
                        'work_description' => 'Описание выполненных работ',
                        'work_cost' => 'Стоимость работ',
                        'completion_date' => 'Дата завершения работ',
                    ];
                    break;
                case 'invoice':
                    $specificFields = [
                        'invoice_number' => 'Номер счета',
                        'invoice_date' => 'Дата счета',
                        'total_amount' => 'Общая сумма',
                        'payment_terms' => 'Условия оплаты',
                    ];
                    break;
                case 'estimate':
                    $specificFields = [
                        'estimate_number' => 'Номер сметы',
                        'materials_cost' => 'Стоимость материалов',
                        'labor_cost' => 'Стоимость работ',
                        'total_estimate' => 'Общая стоимость',
                    ];
                    break;
                default:
                    $specificFields = [];
            }

            $allFields = array_merge($baseFields, $specificFields);

            return response()->json([
                'success' => true,
                'fields' => $allFields
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении полей шаблона: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении полей шаблона'
            ], 500);
        }
    }

    /**
     * Получение данных проекта для автозаполнения документа
     */
    public function getProjectAutoFillData($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            $data = [
                'client_name' => $project->client_last_name . ' ' . $project->client_first_name,
                'client_first_name' => $project->client_first_name,
                'client_last_name' => $project->client_last_name,
                'client_phone' => $project->client_phone,
                'project_type' => $project->object_type,
                'work_type' => $project->work_type,
                'current_date' => now()->format('d.m.Y'),
                'passport_series' => $project->passport_series,
                'passport_number' => $project->passport_number,
                'passport_issued_by' => $project->passport_issued_by,
                'passport_issued_date' => $project->passport_issued_date ? (string)$project->passport_issued_date : '',
                'birth_date' => $project->birth_date ? (string)$project->birth_date : '',
                'birth_place' => $project->birth_place,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении данных проекта для автозаполнения: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении данных проекта'
            ], 500);
        }
    }

    /**
     * Получение данных сотрудника для автозаполнения документа
     */
    public function getEmployeeAutoFillData($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);

            $data = [
                'employee_name' => $employee->full_name,
                'employee_first_name' => $employee->first_name,
                'employee_last_name' => $employee->last_name,
                'employee_middle_name' => $employee->middle_name,
                'employee_role' => $employee->role,
                'employee_phone' => $employee->phone,
                'employee_email' => $employee->email,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении данных сотрудника для автозаполнения: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении данных сотрудника'
            ], 500);
        }
    }

    /**
     * Создание документа из шаблона
     */
    public function createFromTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:document_templates,id',
            'data' => 'required|array',
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $template = DocumentTemplate::findOrFail($request->template_id);
            $data = $request->data;

            // Заменяем переменные в содержимом шаблона
            $content = $template->template_content;
            foreach ($data as $key => $value) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
                $content = str_replace('{' . $key . '}', $value, $content);
            }

            // Создаем документ
            $document = new \App\Models\Document();
            $document->title = $request->title ?: ($template->name . ' - ' . now()->format('d.m.Y'));
            $document->content = $content;
            $document->document_type = $template->document_type;
            $document->template_id = $template->id;
            $document->created_by = Auth::id();
            $document->status = 'draft';
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Документ успешно создан',
                'document_id' => $document->id,
                'redirect_url' => route('documents.show', $document->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при создании документа из шаблона: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании документа'
            ], 500);
        }
    }
}
