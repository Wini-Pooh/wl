<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
}
