<?php

namespace App\Http\Controllers\Partner;

use App\Models\Project;
use App\Models\ProjectPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectPhotoController extends BaseFileController
{
    public function __construct()
    {
        // Доступ к фотографиям проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к фотографиям проектов (только к сметам)
        // Клиенты имеют доступ только на чтение (просмотр и скачивание)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
    }

    /**
     * Разрешенные типы файлов для фотографий
     */
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/bmp',
        'image/tiff'
    ];

    /**
     * Разрешенные расширения файлов для фотографий
     */
    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff', 'tif'
    ];

    /**
     * Получить директорию для фотографий
     */
    protected function getFileDirectory(Project $project): string
    {
        return "projects/{$project->id}/photos";
    }

    /**
     * Получить категорию файла
     */
    protected function getFileCategory(): string
    {
        return 'photos';
    }

    /**
     * Получить дополнительные метаданные для фотографий
     */
    protected function getAdditionalFileMetadata(Request $request, $fileName): array
    {
        // Логирование для отладки
        Log::debug('Загрузка фотографии - метаданные', [
            'request_all' => $request->all(),
            'category' => $request->get('category'),
            'location' => $request->get('location'),
        ]);
        
        return [
            'stage' => $request->get('stage'),
            'location' => $request->get('location'),
            'category' => $request->get('category'), // Добавляем обработку категории
            'is_before' => $request->boolean('is_before'),
            'is_after' => $request->boolean('is_after'),
            'photographer' => auth()->user()->name ?? null,
        ];
    }

    /**
     * Показать все фотографии проекта
     */
    public function index(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            // Получаем фотографии из базы данных с применением фильтров
            $query = $project->photos();
            
            // Применяем фильтр по категории
            if ($request->has('category') && !empty($request->get('category'))) {
                $query->where('category', $request->get('category'));
            }
            
            // Применяем фильтр по локации
            if ($request->has('location') && !empty($request->get('location'))) {
                $query->where('location', $request->get('location'));
            }
            
            // Выполняем запрос с сортировкой
            $photos = $query->orderBy('created_at', 'desc')->get();

            $formattedPhotos = $photos->map(function ($photo) {
                return $this->formatPhotoResponse($photo);
            });

            return response()->json([
                'success' => true,
                'files' => $formattedPhotos,
                'total' => $photos->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading photos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки фотографий'
            ], 500);
        }
    }

    /**
     * Загрузить фотографии через модель ProjectPhoto
     */
    public function store(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            $request->validate([
                'files.*' => 'required|image|mimes:' . implode(',', $this->allowedExtensions) . '|max:' . ($this->maxFileSize / 1024),
                'category' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:1000',
                'stage' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:100',
            ]);

            $uploadedFiles = [];
            
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $photo = $this->createPhotoRecord($project, $file, $request);
                    if ($photo) {
                        $uploadedFiles[] = $this->formatPhotoResponse($photo);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => 'Фотографии успешно загружены'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading photos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки фотографий'
            ], 500);
        }
    }

    /**
     * Удалить фотографию
     */
    public function destroy(Project $project, string $fileId)
    {
        try {
            $this->checkProjectAccess($project);

            $photo = ProjectPhoto::where('project_id', $project->id)
                ->where('id', $fileId)
                ->first();

            if (!$photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Фотография не найдена'
                ], 404);
            }

            // Удаляем файл из хранилища
            if (Storage::disk('public')->exists($photo->path)) {
                Storage::disk('public')->delete($photo->path);
            }

            // Удаляем запись из базы данных
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Фотография успешно удалена'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления фотографии'
            ], 500);
        }
    }

    /**
     * Показать конкретную фотографию
     */
    public function show(Project $project, string $fileId)
    {
        try {
            $this->checkProjectAccess($project);

            $photo = ProjectPhoto::where('project_id', $project->id)
                ->where('id', $fileId)
                ->first();

            if (!$photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Фотография не найдена'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'photo' => $this->formatPhotoResponse($photo)
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения фотографии'
            ], 500);
        }
    }

    /**
     * Скачать фотографию
     */
    public function download(Project $project, string $fileId)
    {
        try {
            $this->checkProjectAccess($project);

            $photo = ProjectPhoto::where('project_id', $project->id)
                ->where('id', $fileId)
                ->first();

            if (!$photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Фотография не найдена'
                ], 404);
            }

            // Проверяем, существует ли файл
            if (!Storage::disk('public')->exists($photo->path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден на сервере'
                ], 404);
            }

            $pathToFile = storage_path('app/public/' . $photo->path);
            $filename = $photo->original_name ?: $photo->filename;

            return response()->download($pathToFile, $filename);

        } catch (\Exception $e) {
            Log::error('Error downloading photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка скачивания фотографии'
            ], 500);
        }
    }

    /**
     * Миграция существующих фотографий
     */
    public function migrateExistingPhotos(Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            $directory = $this->getFileDirectory($project);
            $files = Storage::disk('public')->files($directory);
            $migratedCount = 0;

            foreach ($files as $file) {
                $fileName = basename($file);
                
                // Проверяем, есть ли уже запись в базе данных
                $existingPhoto = ProjectPhoto::where('project_id', $project->id)
                    ->where('filename', $fileName)
                    ->first();

                if (!$existingPhoto) {
                    $size = Storage::disk('public')->size($file);
                    $lastModified = Storage::disk('public')->lastModified($file);

                    ProjectPhoto::create([
                        'project_id' => $project->id,
                        'filename' => $fileName,
                        'original_name' => $fileName,
                        'path' => $file,
                        'file_size' => $size,
                        'mime_type' => $this->guessMimeType($file),
                        'comment' => null,
                        'category' => null,
                        'photo_date' => date('Y-m-d', $lastModified),
                        'file_hash' => md5_file(storage_path('app/public/' . $file)),
                        'created_at' => date('Y-m-d H:i:s', $lastModified),
                        'updated_at' => now()
                    ]);

                    $migratedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Мигрировано фотографий: {$migratedCount}"
            ]);

        } catch (\Exception $e) {
            Log::error('Error migrating photos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка миграции фотографий'
            ], 500);
        }
    }

    /**
     * Создать запись фотографии в базе данных
     */
    private function createPhotoRecord(Project $project, $file, Request $request): ?ProjectPhoto
    {
        try {
            // Логируем входящие данные для отладки
            Log::info('Creating photo with metadata', [
                'project_id' => $project->id,
                'original_name' => $file->getClientOriginalName(),
                'category' => $request->get('category'),
                'location' => $request->get('location'),
                'description' => $request->get('description'),
                'all_request_data' => $request->all()
            ]);

            // Вычисляем хеш файла для проверки дублирования
            $fileHash = md5_file($file->getRealPath());
            
            // Проверяем, есть ли уже файл с таким же хешем в этом проекте
            $existingPhoto = ProjectPhoto::where('project_id', $project->id)
                ->where('file_hash', $fileHash)
                ->first();
                
            if ($existingPhoto) {
                Log::info('Duplicate photo detected - returning existing photo', [
                    'project_id' => $project->id,
                    'file_hash' => $fileHash,
                    'existing_photo_id' => $existingPhoto->id,
                    'original_name' => $file->getClientOriginalName(),
                    'existing_file_name' => $existingPhoto->original_name
                ]);
                
                // Возвращаем существующую фотографию вместо создания новой
                return $existingPhoto;
            }

            // Генерируем уникальное имя файла
            $fileName = $this->generateFileName($file);
            $directory = $this->getFileDirectory($project);
            $filePath = $directory . '/' . $fileName;

            // Сохраняем файл
            $file->storeAs($directory, $fileName, 'public');

            Log::info('Creating new photo record', [
                'project_id' => $project->id,
                'file_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'file_hash' => $fileHash
            ]);

            // Создаем запись в базе данных
            return ProjectPhoto::create([
                'project_id' => $project->id,
                'filename' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'comment' => $request->get('description'),
                'category' => $request->get('category'),
                'location' => $request->get('location'),
                'photo_date' => now()->format('Y-m-d'),
                'file_hash' => $fileHash,
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating photo record: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'file_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Форматировать ответ с фотографией
     */
    private function formatPhotoResponse(ProjectPhoto $photo): array
    {
        return [
            'id' => $photo->id,
            'name' => $photo->original_name ?? $photo->filename,
            'file_name' => $photo->original_name ?? $photo->filename,
            'file_path' => $photo->path,
            'file_size' => $photo->file_size,
            'size' => $photo->file_size, // Добавляем для совместимости
            'mime_type' => $photo->mime_type,
            'url' => asset('storage/' . $photo->path),
            'thumbnail_url' => $this->generateThumbnailUrl(asset('storage/' . $photo->path)),
            'description' => $photo->comment,
            'category' => $photo->category,
            'stage' => $photo->category,
            'location' => $photo->location, // Используем реальное значение локации
            'is_before' => $photo->category === 'before',
            'is_after' => $photo->category === 'after',
            'created_at' => $photo->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $photo->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Генерировать URL миниатюры
     */
    private function generateThumbnailUrl(string $originalUrl): string
    {
        // Пока возвращаем оригинальный URL
        // В будущем здесь можно добавить генерацию миниатюр
        return $originalUrl;
    }

    /**
     * Проверить, является ли файл изображением
     */
    private function isImageFile(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Генерировать уникальное имя файла
     */
    protected function generateFileName($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "{$name}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Альтернативные методы для работы с projectId как строкой
     */
    
    /**
     * Загрузить фотографии - альтернативный метод для API с projectId
     */
    public function storeByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->store($request, $project);
    }

    /**
     * Получить список фотографий - альтернативный метод для API с projectId
     */
    public function indexByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->index($request, $project);
    }

    /**
     * Удалить фотографию - альтернативный метод для API с projectId
     */
    public function destroyByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->destroy($project, $fileId);
    }

    /**
     * Показать фотографию - альтернативный метод для API с projectId
     */
    public function showByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->show($project, $fileId);
    }

    /**
     * Скачать фотографию - альтернативный метод для API с projectId
     */
    public function downloadByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->download($project, $fileId);
    }
}
