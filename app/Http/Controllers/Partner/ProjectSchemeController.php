<?php

namespace App\Http\Controllers\Partner;

use App\Models\Project;
use App\Models\ProjectScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectSchemeController extends BaseFileController
{
    public function __construct()
    {
        // Доступ к схемам проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к схемам проектов (только к сметам)
        // Клиенты имеют доступ только на чтение (просмотр и скачивание)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
    }

    /**
     * Разрешенные типы файлов для схем
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
        'application/acad',
        'application/dwg',
        'application/dxf',
        'application/x-autocad',
        'application/step',
        'application/iges'
    ];

    /**
     * Разрешенные расширения файлов для схем
     */
    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff', 'tif',
        'pdf', 'dwg', 'dxf', 'step', 'stp', 'iges', 'igs'
    ];

    /**
     * Максимальный размер файла для схем (50MB)
     */
    protected int $maxFileSize = 50 * 1024 * 1024;

    /**
     * Получить директорию для схем
     */
    protected function getFileDirectory(Project $project): string
    {
        return "projects/{$project->id}/schemes";
    }

    /**
     * Получить категорию файла
     */
    protected function getFileCategory(): string
    {
        return 'schemes';
    }

    /**
     * Получить дополнительные метаданные для схем
     */
    protected function getAdditionalFileMetadata(Request $request, $fileName): array
    {
        // Получаем тип схемы (базовый или кастомный)
        $schemeType = $request->get('scheme_type');
        if ($schemeType === 'custom' || empty($schemeType)) {
            $schemeType = $request->get('custom_scheme_type', 'technical');
        }
        
        // Получаем помещение (базовое или кастомное)
        $room = $request->get('room');
        if ($room === 'custom' || empty($room)) {
            $room = $request->get('custom_room');
        }
        
        // Получаем масштаб (базовый или кастомный)
        $scale = $request->get('scale');
        if ($scale === 'custom') {
            $scale = $request->get('custom_scale');
        }
        
        return [
            'scheme_type' => $schemeType,
            'room' => $room,
            'scale' => $scale,
            'revision' => $request->get('version', '1.0'), // В БД хранится как revision
            'description' => $request->get('description'),
            'engineer' => auth()->user()->name ?? null,
            'software' => $this->detectCADSoftware($fileName),
        ];
    }

    /**
     * Показать все схемы проекта
     */
    public function index(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            $query = $project->schemes()->orderBy('created_at', 'desc');

            // Применяем фильтры
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                      ->orWhere('original_name', 'like', '%' . $request->get('search') . '%');
            }

            if ($request->filled('type')) {
                $query->where('scheme_type', $request->get('type'));
            }

            if ($request->filled('room')) {
                $query->where('room', $request->get('room'));
            }

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->get('date'));
            }

            $perPage = $request->get('per_page', 12);
            $schemes = $query->paginate($perPage);

            $formattedSchemes = $schemes->map(function ($scheme) {
                return $this->formatSchemeResponse($scheme);
            });

            return response()->json([
                'success' => true,
                'files' => $formattedSchemes,
                'pagination' => [
                    'current_page' => $schemes->currentPage(),
                    'per_page' => $schemes->perPage(),
                    'total' => $schemes->total(),
                    'last_page' => $schemes->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading schemes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки схем'
            ], 500);
        }
    }

    /**
     * Загрузить схемы через модель ProjectScheme
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
                'scheme_type' => 'nullable|string|max:50',
                'room' => 'nullable|string|max:50',
                'system' => 'nullable|string|max:50',
                'scale' => 'nullable|string|max:20',
                'revision' => 'nullable|string|max:10',
                'description' => 'nullable|string|max:1000',
            ]);

            $uploadedSchemes = [];
            $files = $request->file('files', []);

            foreach ($files as $file) {
                $scheme = $this->createSchemeRecord($project, $file, $request);
                if ($scheme) {
                    $uploadedSchemes[] = $this->formatSchemeResponse($scheme);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Схемы успешно загружены',
                'schemes' => $uploadedSchemes,
                'count' => count($uploadedSchemes)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading schemes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки схем'
            ], 500);
        }
    }

    /**
     * Удалить схему
     */
    public function destroy(Project $project, string $schemeId)
    {
        try {
            $this->checkProjectAccess($project);

            $scheme = $project->schemes()->findOrFail($schemeId);
            $scheme->delete();

            return response()->json([
                'success' => true,
                'message' => 'Схема успешно удалена'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting scheme: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления схемы'
            ], 500);
        }
    }

    /**
     * Показать конкретную схему
     */
    public function show(Project $project, string $schemeId)
    {
        try {
            $this->checkProjectAccess($project);

            $scheme = $project->schemes()->findOrFail($schemeId);

            return response()->json([
                'success' => true,
                'scheme' => $this->formatSchemeResponse($scheme)
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing scheme: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Схема не найдена'
            ], 404);
        }
    }

    /**
     * Скачать схему
     */
    public function download(Project $project, string $schemeId)
    {
        try {
            $this->checkProjectAccess($project);

            $scheme = $project->schemes()->findOrFail($schemeId);

            if (!Storage::exists($scheme->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

            return Storage::download($scheme->file_path, $scheme->original_name);

        } catch (\Exception $e) {
            Log::error('Error downloading scheme: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка скачивания схемы'
            ], 500);
        }
    }

    /**
     * Создать запись схемы в базе данных
     */
    private function createSchemeRecord(Project $project, $file, Request $request): ?ProjectScheme
    {
        try {
            $fileName = $this->generateFileName($file);
            $directory = $this->getFileDirectory($project);
            $filePath = $directory . '/' . $fileName;

            // Сохраняем файл
            $file->storeAs($directory, $fileName, 'public');

            // Создаем запись в базе данных
            $scheme = ProjectScheme::create([
                'project_id' => $project->id,
                'name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'scheme_type' => $request->get('scheme_type', 'technical'),
                'room' => $request->get('room'),
                'system' => $request->get('system'),
                'scale' => $request->get('scale'),
                'revision' => $request->get('revision', '1.0'),
                'engineer' => auth()->user()->name ?? null,
                'software' => $this->detectCADSoftware($file->getClientOriginalName()),
                'description' => $request->get('description'),
                'uploaded_by' => auth()->id(),
            ]);

            return $scheme;

        } catch (\Exception $e) {
            Log::error('Error creating scheme record: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Форматировать ответ с схемой
     */
    private function formatSchemeResponse(ProjectScheme $scheme): array
    {
        return [
            'id' => $scheme->id,
            'name' => $scheme->original_name,
            'file_name' => $scheme->name,
            'url' => $scheme->url,
            'file_path' => $scheme->file_path,
            'size' => $scheme->file_size,
            'formatted_size' => $scheme->formatted_size,
            'mime_type' => $scheme->mime_type,
            'scheme_type' => $scheme->scheme_type,
            'scheme_type_name' => $scheme->scheme_type_name,
            'room' => $scheme->room,
            'room_name' => $scheme->room_name,
            'system' => $scheme->system,
            'scale' => $scheme->scale,
            'revision' => $scheme->revision,
            'engineer' => $scheme->engineer,
            'software' => $scheme->software,
            'description' => $scheme->description,
            'uploaded_by' => $scheme->uploaded_by,
            'created_at' => $scheme->created_at->toISOString(),
            'updated_at' => $scheme->updated_at->toISOString(),
            'is_image' => $scheme->isImage(),
        ];
    }

    /**
     * Определить CAD программное обеспечение по расширению файла
     */
    private function detectCADSoftware(string $fileName): ?string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $softwareMap = [
            'dwg' => 'AutoCAD',
            'dxf' => 'AutoCAD',
            'step' => 'CAD System',
            'stp' => 'CAD System',
            'iges' => 'CAD System',
            'igs' => 'CAD System',
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
     * Загрузить схемы - альтернативный метод для API с projectId
     */
    public function storeByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->store($request, $project);
    }

    /**
     * Получить список схем - альтернативный метод для API с projectId
     */
    public function indexByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->index($request, $project);
    }

    /**
     * Удалить схему - альтернативный метод для API с projectId
     */
    public function destroyByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->destroy($project, $fileId);
    }

    /**
     * Показать схему - альтернативный метод для API с projectId
     */
    public function showByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->show($project, $fileId);
    }

    /**
     * Скачать схему - альтернативный метод для API с projectId
     */
    public function downloadByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->download($project, $fileId);
    }

    /**
     * Получить опции для фильтров схем - альтернативный метод для API с projectId
     */
    public function getFilterOptionsByProjectId(string $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        try {
            // Получаем уникальные типы схем и помещения для проекта
            $schemes = $project->schemes()->select('scheme_type', 'room')->get();
            
            $schemeTypes = $schemes->pluck('scheme_type')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
            
            $rooms = $schemes->pluck('room')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
            
            return response()->json([
                'scheme_types' => $schemeTypes,
                'rooms' => $rooms,
                'project_id' => $project->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения опций фильтров схем: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Ошибка получения опций фильтров',
                'scheme_types' => [],
                'rooms' => []
            ], 500);
        }
    }
}
