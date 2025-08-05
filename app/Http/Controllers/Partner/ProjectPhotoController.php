<?php

namespace App\Http\Controllers\Partner;

use App\Models\Project;
use App\Models\ProjectPhoto;
use App\Services\SimpleImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectPhotoController extends BaseFileController
{
    protected SimpleImageProcessingService $imageProcessingService;

    public function __construct(SimpleImageProcessingService $imageProcessingService)
    {
        // Доступ к фотографиям проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к фотографиям проектов (только к сметам)
        // Клиенты имеют доступ только на чтение (просмотр и скачивание)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
        $this->imageProcessingService = $imageProcessingService;
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
     * Загрузить фотографии через обычную HTML форму (без AJAX)
     */
    public function upload(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            // Логирование входящих данных для отладки
            Log::info('Upload photos form request:', [
                'project_id' => $project->id,
                'has_files' => $request->hasFile('files'),
                'files_count' => $request->hasFile('files') ? count($request->file('files')) : 0,
            ]);

            $request->validate([
                'files.*' => 'required|image|mimes:' . implode(',', $this->allowedExtensions) . '|max:' . ($this->maxFileSize / 1024),
                'category' => 'nullable|string|max:100',
                'description' => 'nullable|string|max:1000',
                'stage' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:100',
            ]);

            $uploadedCount = 0;
            
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    Log::info('Processing file:', [
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                    
                    $photo = $this->createPhotoRecord($project, $file, $request);
                    if ($photo) {
                        $uploadedCount++;
                    }
                }
            }

            Log::info('Photos uploaded successfully via form:', [
                'project_id' => $project->id,
                'uploaded_count' => $uploadedCount,
            ]);

            return redirect()->route('partner.projects.photos', $project)
                ->with('success', "Успешно загружено {$uploadedCount} фотографий");

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in photo upload form:', [
                'errors' => $e->errors(),
                'project_id' => $project->id
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error uploading photos via form: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ошибка загрузки фотографий: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Загрузить фотографии через модель ProjectPhoto
     */
    public function store(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            // Логирование входящих данных для отладки
            Log::info('Store photos request data:', [
                'project_id' => $project->id,
                'request_all' => $request->all(),
                'has_files' => $request->hasFile('files'),
                'files_count' => $request->hasFile('files') ? count($request->file('files')) : 0,
            ]);

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
                    Log::info('Processing file:', [
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                    
                    $photo = $this->createPhotoRecord($project, $file, $request);
                    if ($photo) {
                        $uploadedFiles[] = $this->formatPhotoResponse($photo);
                    }
                }
            }

            Log::info('Photos uploaded successfully:', [
                'project_id' => $project->id,
                'uploaded_count' => count($uploadedFiles),
            ]);

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => 'Фотографии успешно загружены'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in photo upload:', [
                'errors' => $e->errors(),
                'project_id' => $project->id
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading photos: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки фотографий: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить фотографию через обычную HTML форму (без AJAX)
     */
    public function delete(Request $request, Project $project, string $fileId)
    {
        try {
            $this->checkProjectAccess($project);

            $photo = ProjectPhoto::where('project_id', $project->id)
                ->where('id', $fileId)
                ->first();

            if (!$photo) {
                return redirect()->route('partner.projects.photos', $project)
                    ->with('error', 'Фотография не найдена');
            }

            // Удаляем основной файл
            if ($photo->is_optimized) {
                // Используем сервис для удаления оптимизированного изображения
                $deleted = $this->imageProcessingService->deleteImageWithThumbnails($photo->path);
                
                if (!$deleted) {
                    Log::warning('Failed to delete optimized image files', [
                        'photo_id' => $photo->id,
                        'path' => $photo->path
                    ]);
                }
            } else {
                // Удаляем обычный файл
                if (Storage::disk('public')->exists($photo->path)) {
                    Storage::disk('public')->delete($photo->path);
                }
            }

            // Удаляем запись из базы данных
            $photo->delete();

            Log::info('Photo deleted successfully via form', [
                'photo_id' => $photo->id,
                'project_id' => $project->id,
                'was_optimized' => $photo->is_optimized
            ]);

            return redirect()->route('partner.projects.photos', $project)
                ->with('success', 'Фотография успешно удалена');

        } catch (\Exception $e) {
            Log::error('Error deleting photo via form: ' . $e->getMessage(), [
                'photo_id' => $fileId,
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('partner.projects.photos', $project)
                ->with('error', 'Ошибка удаления фотографии');
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

            // Удаляем основной файл
            if ($photo->is_optimized) {
                // Используем сервис для удаления оптимизированного изображения
                $deleted = $this->imageProcessingService->deleteImageWithThumbnails($photo->path);
                
                if (!$deleted) {
                    Log::warning('Failed to delete optimized image files', [
                        'photo_id' => $photo->id,
                        'path' => $photo->path
                    ]);
                }
            } else {
                // Удаляем обычный файл
                if (Storage::disk('public')->exists($photo->path)) {
                    Storage::disk('public')->delete($photo->path);
                }
            }

            // Удаляем запись из базы данных
            $photo->delete();

            Log::info('Photo deleted successfully', [
                'photo_id' => $photo->id,
                'project_id' => $project->id,
                'was_optimized' => $photo->is_optimized
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Фотография успешно удалена'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting photo: ' . $e->getMessage(), [
                'photo_id' => $fileId,
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString()
            ]);
            
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
                abort(404, 'Фотография не найдена');
            }

            // Если это AJAX запрос, возвращаем JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'photo' => $this->formatPhotoResponse($photo)
                ]);
            }

            // Для обычного HTTP запроса показываем страницу просмотра
            $categoryOptions = [
                'before' => 'До ремонта',
                'after' => 'После ремонта',
                'process' => 'Процесс работы',
                'progress' => 'Ход работ',
                'materials' => 'Материалы',
                'problems' => 'Проблемы',
                'design' => 'Дизайн',
                'furniture' => 'Мебель',
                'decor' => 'Декор',
                'demolition' => 'Демонтаж',
                'floors' => 'Полы',
                'walls' => 'Стены',
                'ceiling' => 'Потолок',
                'electrical' => 'Электрика',
                'plumbing' => 'Сантехника',
                'heating' => 'Отопление',
                'doors' => 'Двери',
                'windows' => 'Окна'
            ];

            $locationOptions = [
                'living_room' => 'Гостиная',
                'kitchen' => 'Кухня',
                'bedroom' => 'Спальня',
                'bathroom' => 'Ванная',
                'toilet' => 'Туалет',
                'hallway' => 'Прихожая',
                'balcony' => 'Балкон',
                'storage' => 'Кладовка',
                'office' => 'Кабинет',
                'garage' => 'Гараж',
                'basement' => 'Подвал',
                'attic' => 'Чердак',
                'exterior' => 'Фасад'
            ];

            return view('partner.projects.pages.photo-view', compact(
                'project', 
                'photo', 
                'categoryOptions', 
                'locationOptions'
            ));

        } catch (\Exception $e) {
            Log::error('Error showing photo: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка получения фотографии'
                ], 500);
            }
            
            abort(500, 'Ошибка получения фотографии');
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
                'original_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'category' => $request->get('category'),
                'location' => $request->get('location'),
                'description' => $request->get('description'),
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
                
                return $existingPhoto;
            }

            // Проверяем, является ли файл изображением
            if (!$this->imageProcessingService->isImageFile($file)) {
                throw new \Exception('Файл не является изображением');
            }

            // Генерируем уникальное базовое имя файла (без расширения)
            $baseFileName = $this->generateBaseFileName($file);
            $directory = $this->getFileDirectory($project);

            Log::info('Processing image with SimpleImageProcessingService', [
                'base_filename' => $baseFileName,
                'directory' => $directory,
                'original_size' => $file->getSize()
            ]);

            // Обрабатываем изображение через простой сервис
            $processedImage = $this->imageProcessingService->processUploadedImage(
                $file, 
                $directory, 
                $baseFileName
            );

            Log::info('Image processed successfully', [
                'original_size' => $file->getSize(),
                'final_size' => $processedImage['original']['file_size'],
                'optimized' => $processedImage['original']['optimized']
            ]);

            // Создаем запись в базе данных
            $photo = ProjectPhoto::create([
                'project_id' => $project->id,
                'filename' => $processedImage['original']['filename'],
                'original_name' => $file->getClientOriginalName(),
                'path' => $processedImage['original']['path'],
                'file_size' => $processedImage['original']['file_size'],
                'original_file_size' => $file->getSize(), // Сохраняем оригинальный размер
                'mime_type' => $processedImage['original']['mime_type'],
                'comment' => $request->get('description'),
                'category' => $request->get('category'),
                'location' => $request->get('location'),
                'photo_date' => now()->format('Y-m-d'),
                'file_hash' => $fileHash,
                'is_optimized' => $processedImage['original']['optimized'],
                'optimization_data' => json_encode([
                    'dimensions' => $processedImage['original']['dimensions'] ?? null,
                    'was_resized' => $processedImage['original']['optimized']
                ])
            ]);

            Log::info('Photo record created successfully', [
                'photo_id' => $photo->id,
                'project_id' => $project->id,
                'file_name' => $processedImage['original']['filename'],
                'optimized' => $processedImage['original']['optimized']
            ]);

            return $photo;

        } catch (\Exception $e) {
            Log::error('Error creating photo record: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'file_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Форматировать ответ с фотографией
     */
    private function formatPhotoResponse(ProjectPhoto $photo): array
    {
        $response = [
            'id' => $photo->id,
            'name' => $photo->original_name ?? $photo->filename,
            'file_name' => $photo->original_name ?? $photo->filename,
            'file_path' => $photo->path,
            'file_size' => $photo->file_size,
            'size' => $photo->file_size, // Добавляем для совместимости
            'mime_type' => $photo->mime_type,
            'url' => asset('storage/' . $photo->path),
            'thumbnail_url' => $photo->thumbnail_url ?? asset('storage/' . $photo->path),
            'description' => $photo->comment,
            'category' => $photo->category,
            'stage' => $photo->category,
            'location' => $photo->location,
            'is_before' => $photo->category === 'before',
            'is_after' => $photo->category === 'after',
            'created_at' => $photo->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $photo->updated_at->format('Y-m-d H:i:s'),
            'is_optimized' => $photo->is_optimized ?? false,
        ];

        // Добавляем информацию об оптимизации, если изображение было обработано
        if ($photo->is_optimized) {
            $response['optimization'] = [
                'original_size' => $photo->original_file_size,
                'optimized_size' => $photo->file_size,
                'compression_ratio' => $photo->compression_ratio,
                'thumbnails' => $photo->optimization_data['thumbnails'] ?? [],
                'format_converted' => $photo->optimization_data['format_converted'] ?? false,
            ];

            // Добавляем URLs для миниатюр
            if (isset($photo->optimization_data['thumbnails'])) {
                $response['thumbnails'] = [];
                foreach ($photo->optimization_data['thumbnails'] as $size => $thumbnail) {
                    $response['thumbnails'][$size] = [
                        'url' => asset('storage/' . $thumbnail['path']),
                        'width' => $thumbnail['dimensions']['width'],
                        'height' => $thumbnail['dimensions']['height'],
                        'size' => $thumbnail['file_size'],
                    ];
                }
            }
        }

        return $response;
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
     * Генерировать базовое имя файла без расширения
     */
    protected function generateBaseFileName($file): string
    {
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "{$name}_{$timestamp}_{$random}";
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
