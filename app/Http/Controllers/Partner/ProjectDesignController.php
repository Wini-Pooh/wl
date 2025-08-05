<?php

namespace App\Http\Controllers\Partner;

use App\Models\Project;
use App\Models\ProjectDesignFile;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectDesignController extends BaseFileController
{
    protected ImageProcessingService $imageProcessingService;

    public function __construct(ImageProcessingService $imageProcessingService)
    {
        // Доступ к дизайну проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к дизайну проектов (только к сметам)
        // Клиенты имеют доступ только на чтение (просмотр и скачивание)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
        $this->imageProcessingService = $imageProcessingService;
    }

    /**
     * Разрешенные типы файлов для дизайна
     */
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/bmp',
        'image/tiff',
        'application/pdf',
        'application/postscript',
        'application/illustrator',
        'application/x-photoshop',
        'application/x-sketch',
        'application/x-figma'
    ];

    /**
     * Разрешенные расширения файлов для дизайна
     */
    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff', 'tif',
        'pdf', 'ai', 'eps', 'psd', 'sketch', 'fig', 'xd'
    ];

    /**
     * Максимальный размер файла для дизайна (50MB)
     */
    protected int $maxFileSize = 50 * 1024 * 1024;

    /**
     * Получить директорию для дизайна
     */
    protected function getFileDirectory(Project $project): string
    {
        return "projects/{$project->id}/design";
    }

    /**
     * Получить категорию файла
     */
    protected function getFileCategory(): string
    {
        return 'design';
    }

    /**
     * Получить дополнительные метаданные для дизайна
     */
    protected function getAdditionalFileMetadata(Request $request, $fileName): array
    {
        return [
            'design_type' => $request->get('type', 'concept'), // Исправлено: используем 'type' вместо 'design_type'
            'room' => $request->get('room'),
            'style' => $request->get('style'),
            'stage' => $request->get('stage'),
            'designer' => auth()->user()->name ?? null,
            'software' => $this->detectSoftware($fileName),
        ];
    }

    /**
     * Показать все файлы дизайна проекта
     */
    public function index(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            $query = $project->designFiles()->orderBy('created_at', 'desc');

            // Применяем фильтры
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                      ->orWhere('original_name', 'like', '%' . $request->get('search') . '%');
            }

            if ($request->filled('type')) {
                $query->where('design_type', $request->get('type'));
            }

            if ($request->filled('room')) {
                $query->where('room', $request->get('room'));
            }

            if ($request->filled('style')) {
                $query->where('style', $request->get('style'));
            }

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->get('date'));
            }

            $perPage = $request->get('per_page', 12);
            $designFiles = $query->paginate($perPage);

            $formattedFiles = $designFiles->map(function ($designFile) {
                return $this->formatDesignResponse($designFile);
            });

            return response()->json([
                'success' => true,
                'files' => $formattedFiles,
                'pagination' => [
                    'current_page' => $designFiles->currentPage(),
                    'per_page' => $designFiles->perPage(),
                    'total' => $designFiles->total(),
                    'last_page' => $designFiles->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading design files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки файлов дизайна'
            ], 500);
        }
    }

    /**
     * Загрузить файлы дизайна через модель ProjectDesignFile
     */
    public function store(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            // Валидация
            $request->validate([
                'files' => 'required|array|max:10', // Максимум 10 файлов за раз
                'files.*' => [
                    'required',
                    'file',
                    'max:' . ($this->maxFileSize / 1024), // В килобайтах
                    'mimes:' . implode(',', $this->allowedExtensions),
                ],
                'type' => 'nullable|string|max:50',
                'room' => 'nullable|string|max:50',
                'style' => 'nullable|string|max:50',
                'stage' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:1000',
            ], [
                'files.required' => 'Необходимо выбрать хотя бы один файл для загрузки.',
                'files.*.required' => 'Файл не выбран.',
                'files.*.file' => 'Загружаемый объект должен быть файлом.',
                'files.*.max' => 'Размер файла не должен превышать :max КБ.',
                'files.*.mimes' => 'Неподдерживаемый формат файла. Разрешены: ' . implode(', ', $this->allowedExtensions),
            ]);

            $uploadedFiles = [];
            $files = $request->file('files', []);
            $successCount = 0;
            $errors = [];

            foreach ($files as $file) {
                try {
                    $designFile = $this->createDesignFileRecord($project, $file, $request);
                    if ($designFile) {
                        $uploadedFiles[] = $this->formatDesignResponse($designFile);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    Log::error('Error uploading design file: ' . $e->getMessage());
                    $errors[] = 'Ошибка загрузки файла ' . $file->getClientOriginalName() . ': ' . $e->getMessage();
                }
            }

            if ($successCount > 0) {
                $message = "Успешно загружено файлов: {$successCount}";
                if (count($errors) > 0) {
                    $message .= '. Ошибки: ' . implode('; ', $errors);
                }
                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'Не удалось загрузить ни одного файла. ' . implode('; ', $errors));
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error uploading design files: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка загрузки файлов дизайна: ' . $e->getMessage());
        }
    }

    /**
     * Удалить файл дизайна
     */
    public function destroy(Project $project, string $designId)
    {
        try {
            $this->checkProjectAccess($project);

            $designFile = $project->designFiles()->findOrFail($designId);
            $fileName = $designFile->original_name;
            $designFile->delete();

            return redirect()->back()->with('success', "Файл дизайна \"{$fileName}\" успешно удален");

        } catch (\Exception $e) {
            Log::error('Error deleting design file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка удаления файла дизайна');
        }
    }

    /**
     * Показать конкретный файл дизайна
     */
    public function show(Project $project, string $designId)
    {
        try {
            $this->checkProjectAccess($project);

            $designFile = $project->designFiles()->findOrFail($designId);

            return response()->json([
                'success' => true,
                'file' => $this->formatDesignResponse($designFile)
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing design file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Файл дизайна не найден'
            ], 404);
        }
    }

    /**
     * Скачать файл дизайна
     */
    public function download(Project $project, string $designId)
    {
        try {
            $this->checkProjectAccess($project);

            $designFile = $project->designFiles()->findOrFail($designId);

            if (!Storage::exists($designFile->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

            return Storage::download($designFile->file_path, $designFile->original_name);

        } catch (\Exception $e) {
            Log::error('Error downloading design file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка скачивания файла дизайна'
            ], 500);
        }
    }

    /**
     * Создать запись файла дизайна в базе данных
     */
    private function createDesignFileRecord(Project $project, $file, Request $request): ?ProjectDesignFile
    {
        try {
            Log::info('Creating design file record', [
                'project_id' => $project->id,
                'original_name' => $file->getClientOriginalName(),
                'original_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'design_type' => $request->get('type'),
                'room' => $request->get('room'),
                'description' => $request->get('description'),
            ]);

            $directory = $this->getFileDirectory($project);
            
            // Проверяем, является ли файл изображением
            if ($this->imageProcessingService->isImageFile($file)) {
                // Обрабатываем изображение
                $baseFileName = $this->generateBaseFileName($file);
                
                Log::info('Processing design image with ImageProcessingService', [
                    'base_filename' => $baseFileName,
                    'directory' => $directory,
                    'original_size' => $file->getSize()
                ]);

                $processedImage = $this->imageProcessingService->processUploadedImage(
                    $file, 
                    $directory, 
                    $baseFileName
                );

                // Создаем запись в базе данных с оптимизированными данными
                $designFile = ProjectDesignFile::create([
                    'project_id' => $project->id,
                    'name' => $processedImage['original']['filename'],
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $processedImage['original']['path'],
                    'file_size' => $processedImage['original']['file_size'],
                    'original_file_size' => $file->getSize(),
                    'mime_type' => $processedImage['original']['mime_type'],
                    'design_type' => $request->get('type', 'concept'),
                    'room' => $request->get('room'),
                    'style' => $request->get('style'),
                    'stage' => $request->get('stage'),
                    'designer' => auth()->user()->name ?? null,
                    'software' => $this->detectSoftware($file->getClientOriginalName()),
                    'description' => $request->get('description'),
                    'uploaded_by' => auth()->id(),
                    'is_optimized' => true,
                    'optimization_data' => json_encode([
                        'thumbnails' => $processedImage['thumbnails'],
                        'original_dimensions' => $processedImage['original']['dimensions'] ?? null,
                        'compression_ratio' => round((1 - $processedImage['original']['file_size'] / $file->getSize()) * 100, 2),
                        'format_converted' => $processedImage['original']['format_changed']
                    ])
                ]);

                Log::info('Design image processed and record created', [
                    'design_file_id' => $designFile->id,
                    'original_size' => $file->getSize(),
                    'optimized_size' => $processedImage['original']['file_size'],
                    'compression_ratio' => round((1 - $processedImage['original']['file_size'] / $file->getSize()) * 100, 2),
                    'thumbnails_created' => count($processedImage['thumbnails'])
                ]);

            } else {
                // Обрабатываем обычный файл (не изображение)
                $fileName = $this->generateFileName($file);
                $filePath = $directory . '/' . $fileName;

                // Сохраняем файл
                $file->storeAs($directory, $fileName, 'public');

                // Создаем запись в базе данных
                $designFile = ProjectDesignFile::create([
                    'project_id' => $project->id,
                    'name' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'design_type' => $request->get('type', 'concept'),
                    'room' => $request->get('room'),
                    'style' => $request->get('style'),
                    'stage' => $request->get('stage'),
                    'designer' => auth()->user()->name ?? null,
                    'software' => $this->detectSoftware($file->getClientOriginalName()),
                    'description' => $request->get('description'),
                    'uploaded_by' => auth()->id(),
                    'is_optimized' => false,
                ]);

                Log::info('Design file (non-image) record created', [
                    'design_file_id' => $designFile->id,
                    'file_size' => $file->getSize()
                ]);
            }

            return $designFile;

        } catch (\Exception $e) {
            Log::error('Error creating design file record: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'file_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
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
     * Форматировать ответ с файлом дизайна
     */
    private function formatDesignResponse(ProjectDesignFile $designFile): array
    {
        $response = [
            'id' => $designFile->id,
            'name' => $designFile->original_name,
            'original_name' => $designFile->original_name,
            'file_name' => $designFile->name,
            'url' => $designFile->url,
            'file_path' => $designFile->file_path,
            'size' => $designFile->file_size,
            'file_size' => $designFile->file_size, // Добавляем для совместимости
            'formatted_size' => $designFile->formatted_size,
            'mime_type' => $designFile->mime_type,
            'design_type' => $designFile->design_type,
            'design_type_name' => $designFile->design_type_name,
            'type' => $designFile->design_type, // Дублируем для совместимости
            'type_name' => $designFile->design_type_name, // Дублируем для совместимости
            'room' => $designFile->room,
            'room_name' => $designFile->room_name,
            'style' => $designFile->style,
            'style_name' => $designFile->style_name,
            'stage' => $designFile->stage,
            'designer' => $designFile->designer,
            'software' => $designFile->software,
            'description' => $designFile->description,
            'uploaded_by' => $designFile->uploaded_by,
            'created_at' => $designFile->created_at->format('c'),
            'updated_at' => $designFile->updated_at->format('c'),
            'is_image' => $designFile->isImage(),
            'is_optimized' => $designFile->is_optimized ?? false,
            // Добавляем категорию для совместимости с файл-менеджером
            'category' => $designFile->design_type_name . ($designFile->room_name ? ' - ' . $designFile->room_name : ''),
        ];

        // Добавляем информацию об оптимизации для изображений
        if ($designFile->is_optimized && $designFile->isImage()) {
            $response['optimization'] = [
                'original_size' => $designFile->original_file_size,
                'optimized_size' => $designFile->file_size,
                'compression_ratio' => $designFile->compression_ratio,
                'thumbnails' => $designFile->optimization_data['thumbnails'] ?? [],
                'format_converted' => $designFile->optimization_data['format_converted'] ?? false,
            ];

            // Добавляем URLs для миниатюр
            if (isset($designFile->optimization_data['thumbnails'])) {
                $response['thumbnails'] = [];
                foreach ($designFile->optimization_data['thumbnails'] as $size => $thumbnail) {
                    $response['thumbnails'][$size] = [
                        'url' => asset('storage/' . $thumbnail['path']),
                        'width' => $thumbnail['dimensions']['width'],
                        'height' => $thumbnail['dimensions']['height'],
                        'size' => $thumbnail['file_size'],
                    ];
                }
            }

            // Используем миниатюру для preview URL если доступна
            $response['thumbnail_url'] = $designFile->thumbnail_url;
        }

        return $response;
    }

    /**
     * Определить программное обеспечение по расширению файла
     */
    private function detectSoftware(string $fileName): ?string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $softwareMap = [
            'psd' => 'Photoshop',
            'ai' => 'Illustrator',
            'eps' => 'Illustrator',
            'sketch' => 'Sketch',
            'fig' => 'Figma',
            'xd' => 'Adobe XD',
            'pdf' => 'PDF Document',
        ];
        
        return $softwareMap[$extension] ?? null;
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
     * Загрузить дизайн-файлы - альтернативный метод для API с projectId
     */
    public function storeByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->store($request, $project);
    }

    /**
     * Получить список дизайн-файлов - альтернативный метод для API с projectId
     */
    public function indexByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->index($request, $project);
    }

    /**
     * Удалить дизайн-файл - альтернативный метод для API с projectId
     */
    public function destroyByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->destroy($project, $fileId);
    }

    /**
     * Показать дизайн-файл - альтернативный метод для API с projectId
     */
    public function showByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->show($project, $fileId);
    }

    /**
     * Скачать дизайн-файл - альтернативный метод для API с projectId
     */
    public function downloadByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->download($project, $fileId);
    }

    /**
     * Получить опции для фильтров дизайна (кастомные значения)
     */
    public function getFilterOptions(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            // Получаем уникальные значения для каждого поля
            // Используем прямой запрос к модели вместо отношения, чтобы избежать проблем с ORDER BY + DISTINCT
            $types = ProjectDesignFile::where('project_id', $project->id)
                ->whereNotNull('design_type')
                ->where('design_type', '!=', '')
                ->distinct()
                ->pluck('design_type')
                ->filter()
                ->sort()
                ->values();

            $rooms = ProjectDesignFile::where('project_id', $project->id)
                ->whereNotNull('room')
                ->where('room', '!=', '')
                ->distinct()
                ->pluck('room')
                ->filter()
                ->sort()
                ->values();

            $styles = ProjectDesignFile::where('project_id', $project->id)
                ->whereNotNull('style')
                ->where('style', '!=', '')
                ->distinct()
                ->pluck('style')
                ->filter()
                ->sort()
                ->values();

            // Логирование для отладки
            Log::info('Design filter options loaded', [
                'project_id' => $project->id,
                'types_count' => $types->count(),
                'rooms_count' => $rooms->count(),
                'styles_count' => $styles->count(),
                'types' => $types->toArray(),
                'rooms' => $rooms->toArray(),
                'styles' => $styles->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'types' => $types,
                    'rooms' => $rooms,
                    'styles' => $styles
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка получения опций фильтров дизайна: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения опций фильтров'
            ], 500);
        }
    }

    /**
     * Получить опции для фильтров дизайна - альтернативный метод для API с projectId
     */
    public function getFilterOptionsByProjectId(Request $request, string $projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            return $this->getFilterOptions($request, $project);
        } catch (\Exception $e) {
            Log::error('Ошибка получения опций фильтров дизайна по projectId: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Проект не найден или нет доступа'
            ], 404);
        }
    }
}
