<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectDocumentController extends Controller
{
    /**
     * Загрузка документов проекта
     */
    public function store(Request $request, $projectId)
    {
        // Проверяем наличие проекта
        $project = Project::findOrFail($projectId);
        
        // Проверяем наличие файлов
        if (!$request->hasFile('documents')) {
            return response()->json([
                'success' => false,
                'message' => 'Нет загруженных документов'
            ], 400);
        }
        
        // Валидация
        $validator = Validator::make($request->all(), [
            'documents.*' => 'required|file|max:20480', // 20MB максимум
            'category' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $results = [];
        $errors = [];
        $documents = $request->file('documents');
        
        // Обрабатываем каждый файл
        foreach ($documents as $document) {
            try {
                // Генерируем уникальное имя файла
                $fileName = Str::uuid() . '.' . $document->getClientOriginalExtension();
                
                // Путь для сохранения - используем route key вместо id
                $projectId = $project->getKey();
                $path = 'projects/' . $projectId . '/documents/' . $fileName;
                
                // Сохранение файла
                Storage::disk('public')->put($path, file_get_contents($document));
                
                // Создание записи в БД
                $projectDocument = new ProjectDocument();
                $projectDocument->fill([
                    'project_id' => $projectId,
                    'file_name' => $fileName,
                    'original_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $document->getSize(),
                    'mime_type' => $document->getMimeType(),
                    'category' => $request->input('category'),
                    'description' => $request->input('description')
                ]);
                $projectDocument->save();
                
                // Добавляем результат
                $results[] = [
                    'id' => $projectDocument->getKey(),
                    'name' => $document->getClientOriginalName(),
                    'path' => Storage::url($path)
                ];
            } catch (\Exception $e) {
                // Логируем ошибку
                Log::error('Ошибка при загрузке документа: ' . $e->getMessage());
                
                // Добавляем информацию об ошибке
                $errors[] = [
                    'name' => $document->getClientOriginalName(),
                    'error' => 'Не удалось загрузить документ'
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Документы успешно загружены',
            'results' => $results,
            'errors' => $errors
        ]);
    }
    
    /**
     * Удаление документа
     */
    public function destroy($id)
    {
        $document = ProjectDocument::findOrFail($id);
        
        // Получаем путь к файлу из объекта модели безопасным способом
        $filePath = $document->getAttribute('file_path');
        
        // Удаляем файл
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        // Удаляем запись из БД
        $document->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Документ успешно удален'
        ]);
    }
}
