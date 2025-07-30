<?php

namespace App\Http\Controllers\Partner;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectDocumentController extends BaseFileController
{
    public function __construct()
    {
        // Доступ к документам проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к документам проектов (только к сметам)
        // Клиенты имеют доступ только на чтение (просмотр и скачивание)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
    }

    /**
     * Разрешенные типы файлов для документов
     */
    protected array $allowedMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/rtf',
        'application/zip',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/bmp',
        'image/tiff'
    ];

    /**
     * Разрешенные расширения файлов для документов
     */
    protected array $allowedExtensions = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf',
        'zip', 'rar', '7z', 'odt', 'ods', 'odp',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff', 'tif'
    ];

    /**
     * Максимальный размер файла для документов (50MB)
     */
    protected int $maxFileSize = 50 * 1024 * 1024;

    /**
     * Получить директорию для документов
     */
    protected function getFileDirectory(Project $project): string
    {
        return "projects/{$project->id}/documents";
    }

    /**
     * Получить категорию файла
     */
    protected function getFileCategory(): string
    {
        return 'documents';
    }

    /**
     * Показать все документы проекта
     */
    public function index(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            $query = $project->documents()->orderBy('created_at', 'desc');

            // Применяем фильтры
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                      ->orWhere('original_name', 'like', '%' . $request->get('search') . '%');
            }

            if ($request->filled('type')) {
                $query->where('document_type', $request->get('type'));
            }

            if ($request->filled('category')) {
                $query->where('category', $request->get('category'));
            }

            if ($request->filled('is_signed')) {
                $query->where('is_signed', $request->get('is_signed'));
            }

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->get('date'));
            }

            $perPage = $request->get('per_page', 12);
            $documents = $query->paginate($perPage);

            $formattedDocuments = $documents->map(function ($document) {
                return $this->formatDocumentResponse($document);
            });

            return response()->json([
                'success' => true,
                'files' => $formattedDocuments,
                'pagination' => [
                    'current_page' => $documents->currentPage(),
                    'per_page' => $documents->perPage(),
                    'total' => $documents->total(),
                    'last_page' => $documents->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки документов'
            ], 500);
        }
    }

    /**
     * Загрузить документы через модель ProjectDocument
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
                'document_type' => 'nullable|string|max:50',
                'category' => 'nullable|string|max:50',
                'version' => 'nullable|string|max:10',
                'document_date' => 'nullable|date',
                'author' => 'nullable|string|max:255',
                'is_signed' => 'nullable|boolean',
                'description' => 'nullable|string|max:1000',
            ]);

            $uploadedDocuments = [];
            $files = $request->file('files', []);

            foreach ($files as $file) {
                $document = $this->createDocumentRecord($project, $file, $request);
                if ($document) {
                    $uploadedDocuments[] = $this->formatDocumentResponse($document);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Документы успешно загружены',
                'documents' => $uploadedDocuments,
                'count' => count($uploadedDocuments)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки документов'
            ], 500);
        }
    }

    /**
     * Удалить документ
     */
    public function destroy(Project $project, string $documentId)
    {
        try {
            $this->checkProjectAccess($project);

            $document = $project->documents()->findOrFail($documentId);
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Документ успешно удален'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления документа'
            ], 500);
        }
    }

    /**
     * Показать конкретный документ
     */
    public function show(Project $project, string $documentId)
    {
        try {
            $this->checkProjectAccess($project);

            $document = $project->documents()->findOrFail($documentId);

            return response()->json([
                'success' => true,
                'document' => $this->formatDocumentResponse($document)
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Документ не найден'
            ], 404);
        }
    }

    /**
     * Скачать документ
     */
    public function download(Project $project, string $documentId)
    {
        try {
            $this->checkProjectAccess($project);

            $document = $project->documents()->findOrFail($documentId);

            if (!Storage::exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

            return Storage::download($document->file_path, $document->original_name);

        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка скачивания документа'
            ], 500);
        }
    }

    /**
     * Создать запись документа в базе данных
     */
    private function createDocumentRecord(Project $project, $file, Request $request): ?ProjectDocument
    {
        try {
            $fileName = $this->generateFileName($file);
            $directory = $this->getFileDirectory($project);
            $filePath = $directory . '/' . $fileName;

            // Проверка на дубликат по хешу файла
            $fileHash = md5_file($file->getRealPath());
            Log::info('Checking document hash: ' . $fileHash);

            $existingDocument = ProjectDocument::where('project_id', $project->id)
                ->where('file_size', $file->getSize())
                ->where('mime_type', $file->getMimeType())
                ->get()
                ->first(function ($doc) use ($fileHash) {
                    $existingPath = storage_path('app/public/' . $doc->file_path);
                    return file_exists($existingPath) && md5_file($existingPath) === $fileHash;
                });

            if ($existingDocument) {
                Log::info('Document duplicate found, returning existing record: ' . $existingDocument->id);
                return $existingDocument;
            }

            // Сохраняем файл
            $file->storeAs($directory, $fileName, 'public');

            // Создаем запись в базе данных
            $document = ProjectDocument::create([
                'project_id' => $project->id,
                'name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'document_type' => $request->get('document_type', 'contract'),
                'category' => $request->get('category'),
                'version' => $request->get('version', '1.0'),
                'document_date' => $request->get('document_date'),
                'author' => $request->get('author') ?? auth()->user()->name,
                'is_signed' => $request->get('is_signed', false),
                'description' => $request->get('description'),
                'uploaded_by' => auth()->id(),
            ]);

            Log::info('Document created successfully: ' . $document->id);
            return $document;

        } catch (\Exception $e) {
            Log::error('Error creating document record: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Форматировать ответ с документом
     */
    private function formatDocumentResponse(ProjectDocument $document): array
    {
        return [
            'id' => $document->id,
            'name' => $document->original_name,
            'file_name' => $document->name,
            'url' => $document->url,
            'file_path' => $document->file_path,
            'size' => $document->file_size,
            'formatted_size' => $document->formatted_size,
            'mime_type' => $document->mime_type,
            'document_type' => $document->document_type,
            'document_type_name' => $document->document_type_name,
            'category' => $document->category,
            'category_name' => $document->category_name,
            'version' => $document->version,
            'document_date' => $document->document_date,
            'author' => $document->author,
            'is_signed' => $document->is_signed,
            'signature_status' => $document->signature_status,
            'description' => $document->description,
            'uploaded_by' => $document->uploaded_by,
            'created_at' => $document->created_at->format('c'),
            'updated_at' => $document->updated_at->format('c'),
            'is_image' => $document->isImage(),
        ];
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
     * Загрузить документы - альтернативный метод для API с projectId
     */
    public function storeByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->store($request, $project);
    }

    /**
     * Получить список документов - альтернативный метод для API с projectId
     */
    public function indexByProjectId(Request $request, string $projectId)
    {
        $project = Project::findOrFail($projectId);
        return $this->index($request, $project);
    }

    /**
     * Удалить документ - альтернативный метод для API с projectId
     */
    public function destroyByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->destroy($project, $fileId);
    }

    /**
     * Показать документ - альтернативный метод для API с projectId
     */
    public function showByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->show($project, $fileId);
    }

    /**
     * Скачать документ - альтернативный метод для API с projectId
     */
    public function downloadByProjectId(string $projectId, string $fileId)
    {
        $project = Project::findOrFail($projectId);
        return $this->download($project, $fileId);
    }
}
