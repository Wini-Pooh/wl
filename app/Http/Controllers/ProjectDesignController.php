<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDesignFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectDesignController extends Controller
{
    /**
     * Загрузка файлов дизайна проекта
     */
    public function store(Request $request, $projectId)
    {
        // Проверяем наличие проекта
        $project = Project::findOrFail($projectId);
        
        // Проверяем наличие файлов
        if (!$request->hasFile('designs')) {
            return response()->json([
                'success' => false,
                'message' => 'Нет загруженных файлов дизайна'
            ], 400);
        }
        
        // Валидация
        $validator = Validator::make($request->all(), [
            'designs.*' => 'required|file|max:50000', // 50MB максимум
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
        $designs = $request->file('designs');
        
        // Обрабатываем каждый файл
        foreach ($designs as $design) {
            try {
                // Генерируем уникальное имя файла
                $fileName = Str::uuid() . '.' . $design->getClientOriginalExtension();
                
                // Путь для сохранения
                $projectId = $project->getKey();
                $path = 'projects/' . $projectId . '/designs/' . $fileName;
                
                // Сохранение файла
                Storage::disk('public')->put($path, file_get_contents($design));
                
                // Создание записи в БД
                $projectDesign = new ProjectDesignFile();
                $projectDesign->fill([
                    'project_id' => $projectId,
                    'file_name' => $fileName,
                    'original_name' => $design->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $design->getSize(),
                    'mime_type' => $design->getMimeType(),
                    'category' => $request->input('category'),
                    'description' => $request->input('description')
                ]);
                $projectDesign->save();
                
                // Добавляем результат
                $results[] = [
                    'id' => $projectDesign->getKey(),
                    'name' => $design->getClientOriginalName(),
                    'path' => Storage::url($path)
                ];
            } catch (\Exception $e) {
                // Логируем ошибку
                Log::error('Ошибка при загрузке файла дизайна: ' . $e->getMessage());
                
                // Добавляем информацию об ошибке
                $errors[] = [
                    'name' => $design->getClientOriginalName(),
                    'error' => 'Не удалось загрузить файл дизайна'
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Файлы дизайна успешно загружены',
            'results' => $results,
            'errors' => $errors
        ]);
    }
    
    /**
     * Удаление файла дизайна
     */
    public function destroy($id)
    {
        $design = ProjectDesignFile::findOrFail($id);
        
        // Получаем путь к файлу из объекта модели безопасным способом
        $filePath = $design->getAttribute('file_path');
        
        // Удаляем файл
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        // Удаляем запись из БД
        $design->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Файл дизайна успешно удален'
        ]);
    }
}
