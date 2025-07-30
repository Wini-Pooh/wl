<?php

namespace App\Http\Controllers\Partner;

use App\Models\Project;
use App\Models\ProjectDesignFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectDesignController extends BaseFileController
{
    public function __construct()
    {
        // Доступ к дизайну проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к дизайну проектов (только к сметам)
        // Клиенты имеют доступ только на чтение (просмотр и скачивание)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
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
            ]);

            $uploadedFiles = [];
            $files = $request->file('files', []);

            foreach ($files as $file) {
                $designFile = $this->createDesignFileRecord($project, $file, $request);
                if ($designFile) {
                    $uploadedFiles[] = $this->formatDesignResponse($designFile);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Файлы дизайна успешно загружены',
                'files' => $uploadedFiles,
                'count' => count($uploadedFiles)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading design files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки файлов дизайна'
            ], 500);
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
            $designFile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Файл дизайна успешно удален'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting design file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления файла дизайна'
            ], 500);
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
            $fileName = $this->generateFileName($file);
            $directory = $this->getFileDirectory($project);
            $filePath = $directory . '/' . $fileName;

            // Сохраняем файл
            $file->storeAs($directory, $fileName, 'public');

            // Логирование для отладки
            Log::info('Creating design file record', [
                'project_id' => $project->id,
                'original_name' => $file->getClientOriginalName(),
                'design_type' => $request->get('type'),
                'room' => $request->get('room'),
                'description' => $request->get('description'),
                'all_request_data' => $request->all()
            ]);

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
            ]);

            // Логирование созданной записи
            Log::info('Design file record created', [
                'id' => $designFile->id,
                'design_type' => $designFile->design_type,
                'room' => $designFile->room,
                'description' => $designFile->description
            ]);

            return $designFile;

        } catch (\Exception $e) {
            Log::error('Error creating design file record: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            return null;
        }
    }

    /**
     * Форматировать ответ с файлом дизайна
     */
    private function formatDesignResponse(ProjectDesignFile $designFile): array
    {
        return [
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
            // Добавляем категорию для совместимости с файл-менеджером
            'category' => $designFile->design_type_name . ($designFile->room_name ? ' - ' . $designFile->room_name : ''),
        ];
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
}
