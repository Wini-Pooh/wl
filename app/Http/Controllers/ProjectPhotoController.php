<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectPhotoController extends Controller
{
    /**
     * Сохранить новую фотографию проекта
     *
     * @param Request $request
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Project $project)
    {
        // Проверка данных запроса
        $validator = Validator::make($request->all(), [
            'photos.*' => 'required|image|max:10240', // 10MB максимальный размер
            'category' => 'required|string|max:50',
            'room' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ошибка валидации данных',
                'errors' => $validator->errors()
            ], 422);
        }

        $results = [];
        $errors = [];

        // Обработка каждой загруженной фотографии
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                try {
                    // Генерируем уникальное имя файла
                    $fileName = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                    
                    // Путь для сохранения - используем route key вместо id
                    $projectId = $project->getKey();
                    $path = 'projects/' . $projectId . '/photos/' . $fileName;
                    
                    // Сохранение файла
                    Storage::disk('public')->put($path, file_get_contents($photo));
                    
                    // Создание записи в БД
                    $projectPhoto = new ProjectPhoto();
                    $projectPhoto->fill([
                        'project_id' => $projectId,
                        'file_name' => $fileName,
                        'original_name' => $photo->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $photo->getSize(),
                        'mime_type' => $photo->getMimeType(),
                        'category' => $request->input('category'),
                        'room' => $request->input('room'),
                        'description' => $request->input('description')
                    ]);
                    $projectPhoto->save();
                    
                    // Добавляем результат, используя массив для доступа к свойствам
                    $results[] = [
                        'id' => $projectPhoto->getKey(),
                        'name' => $photo->getClientOriginalName(),
                        'path' => Storage::url($path)
                    ];
                } catch (\Exception $e) {
                    // Логируем ошибку
                    Log::error('Ошибка при загрузке фото: ' . $e->getMessage());
                    
                    // Добавляем информацию об ошибке
                    $errors[] = [
                        'name' => $photo->getClientOriginalName(),
                        'error' => 'Не удалось загрузить фото'
                    ];
                }
            }
        }

        // Формирование ответа
        return response()->json([
            'message' => count($results) > 0 ? 'Фотографии успешно загружены' : 'Не удалось загрузить фотографии',
            'success' => count($results) > 0,
            'uploaded' => $results,
            'errors' => $errors
        ], count($results) > 0 ? 200 : 500);
    }
    
    /**
     * Удаление фотографии
     */
    public function destroy($id)
    {
        $photo = ProjectPhoto::findOrFail($id);
        
        // Получаем путь к файлу из объекта модели безопасным способом
        $filePath = $photo->getAttribute('file_path');
        
        // Удаляем файл
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        // Удаляем запись из БД
        $photo->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Фотография успешно удалена'
        ]);
    }
}
