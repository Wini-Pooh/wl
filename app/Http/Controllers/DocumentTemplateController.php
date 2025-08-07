<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показать список шаблонов документов
     */
    public function index()
    {
        $templates = DocumentTemplate::where('is_active', true)
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(15);
        
        return view('document-templates.index', compact('templates'));
    }

    /**
     * Показать форму создания шаблона
     */
    public function create()
    {
        return view('document-templates.create');
    }

    /**
     * Сохранить новый шаблон
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'document_type' => 'required|string|in:contract,act,invoice,estimate,technical,other',
            'variables' => 'nullable|array',
        ]);

        DocumentTemplate::create([
            'name' => $request->input('name'),
            'content' => $request->input('content'),
            'description' => $request->input('description'),
            'document_type' => $request->input('document_type'),
            'variables' => $request->input('variables', []),
            'created_by' => Auth::id(),
            'is_active' => true,
        ]);

        return redirect()->route('document-templates.index')
                        ->with('success', 'Шаблон успешно создан');
    }

    /**
     * Показать шаблон
     */
    public function show($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        // Если это AJAX запрос, возвращаем JSON
        if (request()->ajax()) {
            return response()->json([
                'id' => $template->id,
                'name' => $template->name,
                'content' => $template->content,
                'description' => $template->description,
                'variables' => $template->variables ?: []
            ]);
        }
        
        return view('document-templates.show', compact('template'));
    }

    /**
     * Показать форму редактирования шаблона
     */
    public function edit($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        return view('document-templates.edit', compact('template'));
    }

    /**
     * Обновить шаблон
     */
    public function update(Request $request, $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'document_type' => 'required|string|in:contract,act,invoice,estimate,technical,other',
            'variables' => 'nullable|array',
        ]);

        $template->update([
            'name' => $request->input('name'),
            'content' => $request->input('content'),
            'description' => $request->input('description'),
            'document_type' => $request->input('document_type'),
            'variables' => $request->input('variables', []),
        ]);

        return redirect()->route('document-templates.index')
                        ->with('success', 'Шаблон успешно обновлен');
    }

    /**
     * Удалить шаблон
     */
    public function destroy($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $template->update(['is_active' => false]);

        return redirect()->route('document-templates.index')
                        ->with('success', 'Шаблон удален');
    }

    /**
     * Получить данные шаблона для AJAX
     */
    public function getTemplate($id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);
            
            Log::info('Template loaded', [
                'template_id' => $id,
                'template_name' => $template->name,
                'content_length' => strlen($template->content),
                'variables_count' => count($template->extractVariables())
            ]);
            
            return response()->json([
                'success' => true,
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'content' => $template->content,
                    'description' => $template->description,
                    'variables' => $template->extractVariables(),
                    'document_type' => $template->document_type
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Template loading error', [
                'template_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке шаблона: ' . $e->getMessage()
            ], 500);
        }
    }
}
