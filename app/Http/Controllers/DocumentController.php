<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Models\User;
use App\Services\DigitalSignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    protected $digitalSignatureService;

    public function __construct(DigitalSignatureService $digitalSignatureService)
    {
        $this->digitalSignatureService = $digitalSignatureService;
    }
    /**
     * Главная страница документов
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'received');
        $page = $request->get('page', 1);
        $user = Auth::user();
        
        $documents = collect();
        $templates = collect();
        
        switch ($tab) {
            case 'received':
                $documents = Document::where('status', Document::STATUS_RECEIVED)
                    ->where('recipient_type', 'user')
                    ->where('recipient_id', $user->id)
                    ->with(['sender', 'project'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;
                
            case 'created':
                $documents = Document::where('sender_id', $user->id)
                    ->where('status', '!=', Document::STATUS_RECEIVED)
                    ->with(['recipient', 'project'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;
                
            case 'signed':
                $documents = Document::signed()
                    ->forUser($user->id)
                    ->with(['sender', 'recipient', 'project'])
                    ->orderBy('signed_at', 'desc')
                    ->paginate(20);
                break;
                
            case 'templates':
                $templates = DocumentTemplate::active()
                    ->with('creator')
                    ->orderBy('name')
                    ->paginate(20);
                break;
        }

        // Если это AJAX-запрос, возвращаем только содержимое вкладки
        if ($request->ajax()) {
            if ($tab === 'templates') {
                $html = view('documents.partials.templates-tab', compact('templates'))->render();
            } else {
                $html = view('documents.partials.documents-tab', compact('documents', 'tab'))->render();
            }
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'tab' => $tab,
                'page' => $page
            ]);
        }
        
        return view('documents.index', compact('documents', 'templates', 'tab'));
    }

    /**
     * Создание нового документа
     */
    public function create(Request $request)
    {
        $templates = DocumentTemplate::active()->get();
        $projects = Project::all();
        $users = User::all();
        
        return view('documents.create', compact('templates', 'projects', 'users'));
    }

    /**
     * Сохранение нового документа
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'document_type' => 'required|string|in:contract,act,invoice,estimate,technical,other',
            'content' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'recipient_type' => 'required|string|in:user,client,external',
            'recipient_id' => 'required_unless:recipient_type,external|integer',
            'recipient_email' => 'required_if:recipient_type,external|email',
            'signature_required' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
            'template_id' => 'nullable|exists:document_templates,id',
            'file' => 'nullable|file|max:20480|mimes:pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $document = new Document();
            $document->fill($request->only([
                'title', 'content', 'document_type', 'project_id',
                'recipient_type', 'recipient_id', 'template_id'
            ]));
            
            $document->sender_id = Auth::id();
            $document->status = Document::STATUS_DRAFT;
            $document->signature_required = $request->boolean('signature_required');
            $document->expires_at = $request->expires_at;
            
            if ($request->boolean('signature_required')) {
                $document->signature_status = Document::SIGNATURE_STATUS_PENDING;
            } else {
                $document->signature_status = Document::SIGNATURE_STATUS_NOT_REQUIRED;
            }

            // Обработка файла
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = 'documents/' . $fileName;
                
                Storage::disk('public')->put($path, file_get_contents($file));
                
                $document->file_path = $path;
                $document->file_size = $file->getSize();
                $document->mime_type = $file->getMimeType();
                $document->original_name = $file->getClientOriginalName();
            }

            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Документ успешно создан',
                'document_id' => $document->id
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при создании документа: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании документа'
            ], 500);
        }
    }

    /**
     * Просмотр документа
     */
    public function show($id)
    {
        $document = Document::with(['sender', 'recipient', 'project', 'template'])
            ->findOrFail($id);
        
        // Проверка прав доступа
        $user = Auth::user();
        if ($document->sender_id !== $user->id && 
            !($document->recipient_type === 'user' && $document->recipient_id === $user->id)) {
            abort(403, 'Нет доступа к этому документу');
        }
        
        return view('documents.show', compact('document'));
    }

    /**
     * Отправка документа
     */
    public function send($id)
    {
        $document = Document::findOrFail($id);
        
        // Проверка прав
        if ($document->sender_id !== Auth::id()) {
            abort(403);
        }
        
        if ($document->status !== Document::STATUS_DRAFT) {
            return response()->json([
                'success' => false,
                'message' => 'Документ уже отправлен'
            ], 400);
        }

        try {
            $document->update([
                'status' => Document::STATUS_SENT,
                'sent_at' => now()
            ]);

            // Здесь можно добавить отправку уведомления получателю
            
            return response()->json([
                'success' => true,
                'message' => 'Документ успешно отправлен'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при отправке документа: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при отправке документа'
            ], 500);
        }
    }

    /**
     * Подписание документа
     */
    public function sign(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        // Проверка прав на подпись
        $user = Auth::user();
        if (!($document->recipient_type === 'user' && $document->recipient_id === $user->id)) {
            abort(403, 'Нет прав на подписание этого документа');
        }
        
        if (!$document->requiresSignature()) {
            return response()->json([
                'success' => false,
                'message' => 'Документ не требует подписи или уже подписан'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'signature_type' => 'required|string|in:simple,qualified',
            'certificate_data' => 'required_if:signature_type,qualified|string',
            'pin_code' => 'required_if:signature_type,qualified|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->signature_type === 'qualified') {
                // Создание квалифицированной ЭЦП
                $certificateData = json_decode($request->certificate_data, true);
                $this->digitalSignatureService->createQualifiedSignature(
                    $document, 
                    $certificateData, 
                    $request->pin_code
                );
            } else {
                // Простая электронная подпись
                $this->digitalSignatureService->createSimpleSignature($document, $user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Документ успешно подписан'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при подписании документа: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при подписании документа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Скачивание документа
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        // Проверка прав доступа
        $user = Auth::user();
        if ($document->sender_id !== $user->id && 
            !($document->recipient_type === 'user' && $document->recipient_id === $user->id)) {
            abort(403);
        }
        
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Файл не найден');
        }
        
        $fileName = $document->original_name ?? 'document.' . pathinfo($document->file_path, PATHINFO_EXTENSION);
        
        return response()->streamDownload(function() use ($document) {
            echo Storage::disk('public')->get($document->file_path);
        }, $fileName);
    }

    /**
     * Проверка электронной подписи документа
     */
    public function verifySignature($id)
    {
        $document = Document::findOrFail($id);
        
        // Проверка прав доступа
        $user = Auth::user();
        if ($document->sender_id !== $user->id && 
            !($document->recipient_type === 'user' && $document->recipient_id === $user->id)) {
            abort(403);
        }

        try {
            $verification = $this->digitalSignatureService->verifySignature($document);
            
            return response()->json([
                'success' => true,
                'verification' => $verification
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при проверке подписи: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при проверке подписи'
            ], 500);
        }
    }

    /**
     * Экспорт подписи в формате CAdES
     */
    public function exportSignature($id)
    {
        $document = Document::findOrFail($id);
        
        // Проверка прав доступа
        $user = Auth::user();
        if ($document->sender_id !== $user->id && 
            !($document->recipient_type === 'user' && $document->recipient_id === $user->id)) {
            abort(403);
        }

        try {
            $cadesSignature = $this->digitalSignatureService->exportToCAdES($document);
            
            $fileName = 'signature_' . $document->id . '_' . now()->format('Y-m-d_H-i-s') . '.cades';
            
            return response($cadesSignature)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            Log::error('Ошибка при экспорте подписи: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при экспорте подписи: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удаление документа
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Проверка прав
        if ($document->sender_id !== Auth::id()) {
            abort(403);
        }
        
        // Нельзя удалить подписанный документ
        if ($document->isSigned()) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить подписанный документ'
            ], 400);
        }

        try {
            // Удаляем файл
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Документ успешно удален'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении документа: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при удалении документа'
            ], 500);
        }
    }
}
