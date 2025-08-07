<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Models\User;
use App\Models\SignatureRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показать список документов
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'received');
        
        // Логируем для диагностики
        Log::info('Documents index request', [
            'tab' => $tab,
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'filters' => $request->except(['tab', 'page', '_token']),
            'headers' => [
                'X-Requested-With' => $request->header('X-Requested-With'),
                'Accept' => $request->header('Accept'),
                'Content-Type' => $request->header('Content-Type'),
                'X-CSRF-TOKEN' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing'
            ],
            'user_agent' => $request->userAgent()
        ]);
        
        // Получаем документы в зависимости от вкладки с применением фильтров
        $documents = collect();
        $templates = collect();
        
        // Базовый запрос в зависимости от вкладки
        switch ($tab) {
            case 'created':
                $query = Document::where('created_by', Auth::id())
                    ->with(['template', 'project', 'signatureRequests']);
                break;
                
            case 'received':
                $query = Document::where(function($q) {
                    $q->where('recipient_id', Auth::id())
                      ->orWhere('recipient_phone', Auth::user()->phone ?? '')
                      ->orWhere('recipient_email', Auth::user()->email);
                })->with(['template', 'project', 'creator', 'signatureRequests']);
                break;
                
            case 'signed':
                $query = Document::where('signature_status', Document::SIGNATURE_SIGNED)
                    ->where(function($q) {
                        $q->where('created_by', Auth::id())
                          ->orWhere('recipient_id', Auth::id());
                    })->with(['template', 'project', 'creator', 'signatureRequests']);
                break;
                
            default:
                $query = Document::where('recipient_id', Auth::id())
                    ->with(['template', 'project', 'creator', 'signatureRequests']);
                break;
        }

        // Применяем фильтры
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->get('document_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('signature_status')) {
            $query->where('signature_status', $request->get('signature_status'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Применяем сортировку
        $sort = $request->get('sort', 'created_at_desc');
        switch ($sort) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'created_at_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Если есть сортировка по подписанным, добавляем дополнительную сортировку
        if ($tab === 'signed') {
            $query->orderBy('signed_at', 'desc');
        }

        $documents = $query->paginate(12)->appends($request->except('page'));
        
        // Если это AJAX запрос, возвращаем JSON
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $html = view('documents.partials.documents-tab', compact('documents', 'tab'))->render();
                
                Log::info('Returning AJAX response', ['tab' => $tab, 'html_length' => strlen($html)]);
                
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'tab' => $tab
                ]);
            } catch (\Exception $e) {
                Log::error('Error loading tab content: ' . $e->getMessage(), [
                    'tab' => $tab,
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при загрузке содержимого вкладки: ' . $e->getMessage()
                ], 500);
            }
        }

        // Обычный запрос - возвращаем полную страницу
        Log::info('Returning full page view', ['tab' => $tab]);
        
        return view('documents.index', compact('documents', 'tab'));
    }

    /**
     * Показать форму создания документа
     */
    public function create()
    {
        $templates = DocumentTemplate::active()->get();
        $projects = Project::where('partner_id', Auth::id())->get();
        
        return view('documents.create', compact('templates', 'projects'));
    }

    /**
     * Сохранить новый документ
     */
    public function store(Request $request)
    {
        // Логируем начало обработки запроса
        Log::info('Document store request started', [
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'x_requested_with' => $request->header('X-Requested-With'),
            'user_id' => Auth::id(),
            'request_data' => $request->except(['document_file', '_token']),
            'has_file' => $request->hasFile('document_file'),
            'file_info' => $request->hasFile('document_file') ? [
                'name' => $request->file('document_file')->getClientOriginalName(),
                'size' => $request->file('document_file')->getSize(),
                'mime' => $request->file('document_file')->getMimeType()
            ] : null
        ]);

        try {
            // Дополнительная отладка перед валидацией
            Log::info('Validation attempt', [
                'request_data' => $request->all(),
                'recipient_type' => $request->recipient_type,
                'has_project_id' => $request->has('project_id'),
                'has_employee_id' => $request->has('employee_id'),
                'has_recipient_name' => $request->has('recipient_name'),
                'has_recipient_phone' => $request->has('recipient_phone'),
                'file_details' => $request->hasFile('document_file') ? [
                    'original_name' => $request->file('document_file')->getClientOriginalName(),
                    'mime_type' => $request->file('document_file')->getMimeType(),
                    'size' => $request->file('document_file')->getSize(),
                    'is_valid' => $request->file('document_file')->isValid(),
                    'error' => $request->file('document_file')->getError()
                ] : 'No file found'
            ]);
            
            $validationRules = [
                'title' => 'required|string|max:255',
                'document_type' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'recipient_type' => 'required|in:employee,client,external',
                'signature_required' => 'boolean',
                'expires_in' => 'nullable|integer|in:1,3,7,14,30',
                'priority' => 'nullable|in:low,normal,high,urgent',
                'message' => 'nullable|string|max:1000',
                'document_file' => 'required|file|mimes:pdf,doc,docx,txt,rtf|max:10240', // 10MB
            ];

            // Добавляем правила валидации в зависимости от типа получателя
            if ($request->recipient_type === 'employee') {
                $validationRules['employee_id'] = 'required|exists:users,id';
            } elseif ($request->recipient_type === 'client') {
                $validationRules['project_id'] = 'required|exists:projects,id';
            } elseif ($request->recipient_type === 'external') {
                $validationRules['recipient_name'] = 'required|string|max:255';
                $validationRules['recipient_phone'] = 'required|string|max:20';
                $validationRules['recipient_email'] = 'nullable|email|max:255';
            }
            
            // Добавляем опциональные правила для всех типов получателей
            if ($request->filled('recipient_name')) {
                $validationRules['recipient_name'] = 'string|max:255';
            }
            if ($request->filled('recipient_phone')) {
                $validationRules['recipient_phone'] = 'string|max:20';
            }
            if ($request->filled('recipient_email')) {
                $validationRules['recipient_email'] = 'email|max:255';
            }
            if ($request->filled('project_id')) {
                $validationRules['project_id'] = 'exists:projects,id';
            }

            $request->validate($validationRules);
            
            Log::info('Validation passed successfully', [
                'validated_fields' => array_keys($validationRules),
                'recipient_type' => $request->recipient_type
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info('Validation failed', ['errors' => $e->errors()]);
            
            // Если это AJAX запрос, возвращаем JSON с ошибками валидации
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации данных',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            Log::info('Starting document creation process', [
                'recipient_type' => $request->recipient_type,
                'has_file' => $request->hasFile('document_file')
            ]);

            $documentData = [
                'title' => $request->title,
                'content' => $request->input('description'),
                'document_type' => $request->document_type,
                'project_id' => $request->recipient_type === 'client' ? $request->project_id : null,
                'created_by' => Auth::id(),
                'signature_required' => $request->boolean('signature_required'),
                'signature_status' => $request->boolean('signature_required') 
                    ? Document::SIGNATURE_PENDING 
                    : Document::SIGNATURE_NOT_REQUIRED,
                'status' => Document::STATUS_SENT,
                'priority' => $request->input('priority', Document::PRIORITY_NORMAL),
                'version' => 1,
                'is_current_version' => true,
                'can_be_deleted' => true,
            ];

            // Устанавливаем срок действия
            if ($request->expires_in) {
                $documentData['expires_at'] = now()->addDays($request->expires_in);
            }

            // Определяем получателя на основе типа
            if ($request->recipient_type === 'employee') {
                $employee = User::find($request->employee_id);
                if ($employee) {
                    $documentData['recipient_id'] = $employee->id;
                    $documentData['recipient_type'] = Document::RECIPIENT_USER;
                    $documentData['recipient_name'] = trim($employee->first_name . ' ' . $employee->last_name);
                    $documentData['recipient_phone'] = $employee->phone;
                    $documentData['recipient_email'] = $employee->email;
                }
            } elseif ($request->recipient_type === 'client') {
                $project = Project::find($request->project_id);
                if ($project) {
                    $documentData['recipient_type'] = Document::RECIPIENT_CLIENT;
                    $documentData['recipient_name'] = trim($project->client_first_name . ' ' . $project->client_last_name);
                    $documentData['recipient_phone'] = $project->client_phone;
                    $documentData['recipient_email'] = $project->client_email;
                }
            } elseif ($request->recipient_type === 'external') {
                $documentData['recipient_type'] = Document::RECIPIENT_EXTERNAL;
                $documentData['recipient_name'] = $request->recipient_name;
                $documentData['recipient_phone'] = $request->recipient_phone;
                $documentData['recipient_email'] = $request->recipient_email;
            }

            // Обработка загруженного файла
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'documents/' . date('Y/m') . '/' . $fileName;
                
                $file->storeAs('public/' . dirname($filePath), basename($filePath));
                
                $documentData['file_path'] = $filePath;
                $documentData['original_filename'] = $file->getClientOriginalName();
                $documentData['file_size'] = $file->getSize();
                $documentData['mime_type'] = $file->getMimeType();
                $documentData['file_hash'] = md5_file($file->getPathname());
            }

            $document = Document::create($documentData);

            // Если требуется подпись, создаем запрос на подпись
            if ($document->signature_required) {
                $expiresAt = $request->expires_in ? now()->addDays($request->expires_in) : now()->addDays(7);
                
                // Подготавливаем данные для запроса подписи
                $signatureData = [
                    'document_id' => $document->id,
                    'sender_id' => Auth::id(),
                    'recipient_id' => $document->recipient_id,
                    'status' => SignatureRequest::STATUS_PENDING,
                    'signature_type' => SignatureRequest::TYPE_SIMPLE,
                    'expires_at' => $expiresAt,
                ];
                
                // Добавляем данные получателя только если они существуют
                if ($document->recipient_name) {
                    $signatureData['recipient_name'] = $document->recipient_name;
                }
                if ($document->recipient_phone) {
                    $signatureData['recipient_phone'] = $document->recipient_phone;
                }
                if ($request->input('message')) {
                    $signatureData['message'] = $request->input('message');
                }
                
                SignatureRequest::create($signatureData);
                
                $message = 'Документ создан и отправлен на подпись';
            } else {
                $message = 'Документ успешно создан и отправлен';
            }

            // Если это AJAX запрос (из бокового модального окна)
            if ($request->ajax() || $request->wantsJson()) {
                Log::info('Returning successful JSON response', [
                    'document_id' => $document->id,
                    'message' => $message
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'document_id' => $document->id,
                    'redirect_url' => route('documents.show', $document)
                ]);
            }

            Log::info('Redirecting to document show page', ['document_id' => $document->id]);
            return redirect()->route('documents.show', $document)
                           ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Если это AJAX запрос, возвращаем JSON с ошибками валидации
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации данных',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Error creating document: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['document_file', '_token']),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Произошла ошибка при создании документа. Попробуйте еще раз.';
            
            // Если это AJAX запрос
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return back()->withInput()
                        ->with('error', $errorMessage);
        }
    }

    /**
     * Показать документ
     */
    public function show(Document $document)
    {
        // Проверяем права доступа
        if (!$this->canViewDocument($document)) {
            abort(403, 'Нет доступа к этому документу');
        }

        $document->load(['template', 'project', 'creator', 'signatureRequests.sender', 'signatureRequests.recipient']);
        
        // Отмечаем документ как просмотренный
        if ($document->recipient_id === Auth::id() && !$document->viewed_at) {
            $document->update([
                'viewed_at' => now(),
                'status' => Document::STATUS_VIEWED
            ]);
        }

        return view('documents.show', compact('document'));
    }

    /**
     * Показать форму редактирования
     */
    public function edit(Document $document)
    {
        if ($document->created_by !== Auth::id()) {
            abort(403, 'Нет доступа к редактированию этого документа');
        }

        if (!$document->canBeDeleted()) {
            return back()->with('error', 'Документ нельзя редактировать после отправки на подпись');
        }

        $templates = DocumentTemplate::active()->get();
        $projects = Project::where('partner_id', Auth::id())->get();
        
        return view('documents.edit', compact('document', 'templates', 'projects'));
    }

    /**
     * Обновить документ
     */
    public function update(Request $request, Document $document)
    {
        if ($document->created_by !== Auth::id()) {
            abort(403);
        }

        if (!$document->canBeDeleted()) {
            return back()->with('error', 'Документ нельзя редактировать после отправки на подпись');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'signature_required' => 'boolean',
        ]);

        $document->update([
            'title' => $request->title,
            'content' => $request->input('content'),
            'signature_required' => $request->boolean('signature_required'),
            'signature_status' => $request->boolean('signature_required') 
                ? Document::SIGNATURE_PENDING 
                : Document::SIGNATURE_NOT_REQUIRED,
        ]);

        return redirect()->route('documents.show', $document)
                       ->with('success', 'Документ успешно обновлен');
    }

    /**
     * Удалить документ
     */
    /**
     * УДАЛЕНИЕ ДОКУМЕНТОВ ОТКЛЮЧЕНО ПО ТРЕБОВАНИЯМ БЕЗОПАСНОСТИ
     * Документы нельзя удалять ни при каких условиях
     */
    public function destroy(Document $document)
    {
        // Удаление документов полностью запрещено
        abort(403, 'Удаление документов запрещено системными требованиями безопасности');
    }

    /**
     * Отправить документ на подпись
     */
    public function send(Request $request, Document $document)
    {
        if ($document->created_by !== Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к этому документу'], 403);
            }
            abort(403);
        }

        // Если это AJAX запрос без данных получателя, просто меняем статус
        if ($request->wantsJson() && !$request->has('recipient_type')) {
            try {
                $document->update(['status' => Document::STATUS_SENT]);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Документ отправлен',
                    'document' => [
                        'id' => $document->id,
                        'status' => $document->status
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error sending document: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Ошибка при отправке документа'], 500);
            }
        }

        $request->validate([
            'recipient_type' => 'required|in:user,phone,email',
            'recipient_id' => 'nullable|exists:users,id',
            'recipient_phone' => 'nullable|string',
            'recipient_email' => 'nullable|email',
            'recipient_name' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            $recipientData = [
                'recipient_type' => $request->recipient_type,
                'recipient_id' => $request->recipient_id,
                'recipient_phone' => $request->recipient_phone,
                'recipient_email' => $request->recipient_email,
                'recipient_name' => $request->recipient_name,
            ];

            $signatureRequest = $document->sendForSignature($recipientData, $request->message);

            // Здесь можно добавить отправку уведомления по SMS/Email

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Документ отправлен на подпись',
                    'document' => [
                        'id' => $document->id,
                        'status' => $document->status
                    ]
                ]);
            }

            return back()->with('success', 'Документ отправлен на подпись');

        } catch (\Exception $e) {
            Log::error('Error sending document for signature: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Ошибка при отправке документа'], 500);
            }
            
            return back()->with('error', 'Ошибка при отправке документа');
        }
    }

    /**
     * Подписать документ
     */
    public function sign(Request $request, Document $document)
    {
        // Логируем запрос для диагностики
        Log::info('Document sign request', [
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'csrf_token' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing',
            'signature_required' => $document->signature_required,
            'signature_status' => $document->signature_status,
            'recipient_id' => $document->recipient_id,
            'recipient_phone' => $document->recipient_phone,
            'recipient_email' => $document->recipient_email,
            'user_phone' => Auth::user()->phone ?? null,
            'user_email' => Auth::user()->email,
            'request_data' => $request->only(['signature', 'agreement'])
        ]);

        // Проверяем права на подпись
        if (!$this->canSignDocument($document)) {
            Log::warning('Document sign access denied', [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'recipient_id' => $document->recipient_id,
                'recipient_phone' => $document->recipient_phone,
                'recipient_email' => $document->recipient_email,
                'user_phone' => Auth::user()->phone ?? null,
                'user_email' => Auth::user()->email,
                'signature_status' => $document->signature_status,
                'signature_required' => $document->signature_required,
                'can_sign_check' => [
                    'signature_required' => $document->signature_required,
                    'signature_status_pending' => $document->signature_status === Document::SIGNATURE_PENDING,
                    'recipient_id_match' => $document->recipient_id === Auth::id(),
                    'phone_match' => $document->recipient_phone === (Auth::user()->phone ?? ''),
                    'email_match' => $document->recipient_email === Auth::user()->email
                ]
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Нет доступа к подписанию этого документа. Проверьте, что вы являетесь получателем документа.'
                ], 403);
            }
            abort(403, 'Нет доступа к подписанию этого документа');
        }

        // Валидация данных
        try {
            $request->validate([
                'signature' => 'required|string|min:2|max:255',
                'agreement' => 'required|accepted',
            ], [
                'signature.required' => 'Поле подписи обязательно для заполнения',
                'signature.min' => 'Подпись должна содержать минимум 2 символа',
                'signature.max' => 'Подпись не должна превышать 255 символов',
                'agreement.required' => 'Необходимо подтвердить согласие с условиями',
                'agreement.accepted' => 'Необходимо подтвердить согласие с условиями',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Document sign validation failed', [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'request_data' => $request->only(['signature', 'agreement'])
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации данных',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $signatureData = [
                'signature_text' => $request->signature,
                'signed_by' => Auth::id(),
                'signed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $document->signDocument($signatureData);

            Log::info('Document signed successfully', [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'signature_status' => $document->signature_status
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Документ успешно подписан',
                    'document' => [
                        'id' => $document->id,
                        'signature_status' => $document->signature_status,
                        'signed_at' => $document->signed_at?->format('d.m.Y H:i')
                    ]
                ]);
            }

            return back()->with('success', 'Документ успешно подписан');

        } catch (\Exception $e) {
            Log::error('Error signing document', [
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ошибка при подписании документа: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Ошибка при подписании документа');
        }
    }

    /**
     * Отклонить подпись документа
     */
    public function reject(Request $request, Document $document)
    {
        if (!$this->canSignDocument($document)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Нет доступа к этому документу'], 403);
            }
            abort(403, 'Нет доступа к этому документу');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $document->rejectSignature($request->reason);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Документ отклонен',
                    'document' => [
                        'id' => $document->id,
                        'signature_status' => $document->signature_status,
                        'rejected_at' => $document->rejected_at?->format('d.m.Y H:i'),
                        'rejection_reason' => $document->rejection_reason
                    ]
                ]);
            }

            return back()->with('success', 'Документ отклонен');

        } catch (\Exception $e) {
            Log::error('Error rejecting document: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Ошибка при отклонении документа'], 500);
            }
            
            return back()->with('error', 'Ошибка при отклонении документа');
        }
    }

    /**
     * Скачать документ
     */
    public function download(Document $document)
    {
        if (!$this->canViewDocument($document)) {
            abort(403);
        }

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Файл не найден');
        }

        $pathToFile = storage_path('app/public/' . $document->file_path);
        return response()->download($pathToFile, $document->original_filename);
    }

    /**
     * Получить данные проекта для AJAX
     */
    public function getProjectData(Project $project)
    {
        if ($project->partner_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'id' => $project->id,
            'client_name' => $project->client_first_name . ' ' . $project->client_last_name,
            'client_phone' => $project->client_phone,
            'client_email' => $project->client_email,
        ]);
    }

    /**
     * Проверить, может ли пользователь просматривать документ
     */
    private function canViewDocument(Document $document)
    {
        $user = Auth::user();
        
        // Создатель документа
        if ($document->created_by === $user->id) {
            return true;
        }
        
        // Получатель документа
        if ($document->recipient_id === $user->id) {
            return true;
        }
        
        // Получатель по телефону или email
        if ($document->recipient_phone === ($user->phone ?? '') || 
            $document->recipient_email === $user->email) {
            return true;
        }
        
        return false;
    }

    /**
     * Проверить, может ли пользователь подписывать документ
     */
    private function canSignDocument(Document $document)
    {
        $user = Auth::user();
        
        Log::info('Checking document sign permission', [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'signature_required' => $document->signature_required,
            'signature_status' => $document->signature_status,
            'recipient_id' => $document->recipient_id,
            'recipient_phone' => $document->recipient_phone,
            'recipient_email' => $document->recipient_email,
            'user_phone' => $user->phone ?? null,
            'user_email' => $user->email,
        ]);
        
        // Проверяем, что документ требует подписи
        if (!$document->signature_required) {
            Log::info('Document does not require signature', ['document_id' => $document->id]);
            return false;
        }
        
        // Проверяем, что документ еще не подписан и не отклонен
        if ($document->signature_status !== Document::SIGNATURE_PENDING) {
            Log::info('Document signature status is not pending', [
                'document_id' => $document->id,
                'current_status' => $document->signature_status
            ]);
            return false;
        }
        
        // Проверяем, что пользователь является получателем
        $canSign = $document->recipient_id === $user->id ||
                   $document->recipient_phone === ($user->phone ?? '') ||
                   $document->recipient_email === $user->email;
                   
        Log::info('Document sign permission result', [
            'document_id' => $document->id,
            'can_sign' => $canSign,
            'checks' => [
                'recipient_id_match' => $document->recipient_id === $user->id,
                'phone_match' => $document->recipient_phone === ($user->phone ?? ''),
                'email_match' => $document->recipient_email === $user->email
            ]
        ]);
        
        return $canSign;
    }
}
