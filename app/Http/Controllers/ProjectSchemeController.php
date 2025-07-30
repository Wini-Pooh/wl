<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectSchemeController extends Controller
{
    /**
     * Загрузка схем проекта
     */
    public function store(Request $request, $projectId)
    {
        // Проверяем наличие проекта
        $project = Project::findOrFail($projectId);
        
        // Проверяем наличие файлов
        if (!$request->hasFile('schemes')) {
            return response()->json([
                'success' => false,
                'message' => 'Нет загруженных схем'
            ], 400);
        }
        
        // Валидация
        $validator = Validator::make($request->all(), [
            'schemes.*' => 'required|file|max:10240', // 10MB максимум
            'type' => 'required|string|max:255',
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
        $schemes = $request->file('schemes');
        
        // Обрабатываем каждый файл
        foreach ($schemes as $scheme) {
            try {
                // Генерируем уникальное имя файла
                $fileName = Str::uuid() . '.' . $scheme->getClientOriginalExtension();
                
                // Путь для сохранения
                $projectId = $project->getKey();
                $path = 'projects/' . $projectId . '/schemes/' . $fileName;
                
                // Сохранение файла
                Storage::disk('public')->put($path, file_get_contents($scheme));
                
                // Создание записи в БД
                $projectScheme = new ProjectScheme();
                $projectScheme->fill([
                    'project_id' => $projectId,
                    'file_name' => $fileName,
                    'original_name' => $scheme->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $scheme->getSize(),
                    'mime_type' => $scheme->getMimeType(),
                    'type' => $request->input('type'),
                    'description' => $request->input('description')
                ]);
                $projectScheme->save();
                
                // Добавляем результат
                $results[] = [
                    'id' => $projectScheme->getKey(),
                    'name' => $scheme->getClientOriginalName(),
                    'path' => Storage::url($path)
                ];
            } catch (\Exception $e) {
                // Логируем ошибку
                Log::error('Ошибка при загрузке схемы: ' . $e->getMessage());
                
                // Добавляем информацию об ошибке
                $errors[] = [
                    'name' => $scheme->getClientOriginalName(),
                    'error' => 'Не удалось загрузить схему'
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Схемы успешно загружены',
            'results' => $results,
            'errors' => $errors
        ]);
    }
    
    /**
     * Удаление схемы
     */
    public function destroy($id)
    {
        $scheme = ProjectScheme::findOrFail($id);
        
        // Получаем путь к файлу из объекта модели безопасным способом
        $filePath = $scheme->getAttribute('file_path');
        
        // Удаляем файл
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        // Удаляем запись из БД
        $scheme->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Схема успешно удалена'
        ]);
    }
}
