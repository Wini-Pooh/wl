<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Базовый контроллер для работы с файлами проектов
 * Содержит общие методы для всех файловых операций
 */
abstract class BaseFileController extends Controller
{
    /**
     * Максимальный размер файла (в байтах)
     */
    protected int $maxFileSize = 10 * 1024 * 1024; // 10MB
    
    /**
     * Разрешенные типы файлов
     */
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'application/zip',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
        'image/vnd.dwg',
        'application/acad',
        'application/x-autocad',
        'application/dwg',
        'application/x-dwg',
        'application/x-autocad',
        'image/x-dwg',
        'application/acad',
        'application/x-acad',
        'application/autocad_dwg',
        'image/x-dwg',
    ];

    /**
     * Разрешенные расширения файлов
     */
    protected array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff', 'tif',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf',
        'zip', 'rar', '7z',
        'dwg', 'dxf', 'step', 'stp', 'iges', 'igs',
        'ai', 'eps', 'psd', 'sketch', 'fig'
    ];

    /**
     * Путь к директории файлов
     */
    abstract protected function getFileDirectory(Project $project): string;

    /**
     * Получить категорию файла
     */
    abstract protected function getFileCategory(): string;

    /**
     * Получить дополнительные метаданные файла при загрузке
     */
    protected function getAdditionalFileMetadata(Request $request, $fileName): array
    {
        return [];
    }

    /**
     * Проверка доступа к проекту
     */
    protected function checkProjectAccess(Project $project): void
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        if ($user->hasRole('admin')) {
            return;
        }
        
        if ($user->hasRole('partner')) {
            if ($project->partner_id !== $user->id) {
                abort(403, 'Нет доступа к этому проекту');
            }
            return;
        }
        
        if (($user->hasRole('employee') || $user->hasRole('foreman')) && isset($user->employeeProfile)) {
            if ($project->partner_id !== $user->employeeProfile->partner_id) {
                abort(403, 'Нет доступа к этому проекту');
            }
            return;
        }
        
        if ($user->hasRole('client')) {
            // Для клиентов - проверяем номер телефона пользователя с номером в проекте
            $userPhone = $user->phone ?? $user->email; // fallback на email если нет телефона
            
            if (!$userPhone) {
                abort(403, 'Нет доступа к этому проекту');
            }
            
            // Очищаем номера телефонов от символов для сравнения
            $userPhoneClean = preg_replace('/[^0-9]/', '', $userPhone);
            $projectPhoneClean = preg_replace('/[^0-9]/', '', $project->client_phone);
            
            if ($userPhoneClean === $projectPhoneClean) {
                return;
            }
            
            abort(403, 'Нет доступа к этому проекту');
        }
        
        abort(403, 'Нет доступа к этому проекту');
    }

    /**
     * Получить все файлы проекта с пагинацией и фильтрацией
     */
    public function index(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            // Параметры пагинации и фильтрации
            $page = (int) $request->get('page', 1);
            $perPage = (int) $request->get('per_page', 12);
            $search = $request->get('search');
            $date = $request->get('date');
            $category = $request->get('category');
            $type = $request->get('type');
            $room = $request->get('room');

            // Получаем файлы из директории
            $directory = $this->getFileDirectory($project);
            $files = Storage::disk('public')->files($directory);

            Log::info('Loading files for project', [
                'project_id' => $project->id,
                'directory' => $directory,
                'files_found' => count($files)
            ]);

            $fileList = [];
            
            foreach ($files as $file) {
                $fileInfo = $this->getFileInfo($file);
                
                if ($fileInfo && $this->matchesFilters($fileInfo, $search, $date, $category, $type, $room)) {
                    $fileList[] = $fileInfo;
                }
            }

            // Сортировка по дате создания (новые сначала)
            usort($fileList, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // Расчет пагинации
            $total = count($fileList);
            $totalPages = ceil($total / $perPage);
            $offset = ($page - 1) * $perPage;

            // Получаем файлы для текущей страницы
            $paginatedFiles = array_slice($fileList, $offset, $perPage);

            $pagination = [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_previous' => $page > 1
            ];

            return response()->json([
                'success' => true,
                'files' => $paginatedFiles,
                'pagination' => $pagination,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки файлов'
            ], 500);
        }
    }

    /**
     * Загрузить файлы
     */
    public function store(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);

            $request->validate([
                'files.*' => 'required|file|mimes:' . implode(',', $this->allowedExtensions) . '|max:' . ($this->maxFileSize / 1024),
                'description' => 'nullable|string|max:1000',
                'category' => 'nullable|string|max:100',
                'type' => 'nullable|string|max:100',
                'room' => 'nullable|string|max:100',
            ]);

            $uploadedFiles = [];
            $directory = $this->getFileDirectory($project);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $uploadedFile = $this->uploadFile($file, $directory, $request);
                    if ($uploadedFile) {
                        $uploadedFiles[] = $uploadedFile;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => 'Файлы успешно загружены'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки файлов'
            ], 500);
        }
    }

    /**
     * Показать конкретный файл
     */
    public function show(Project $project, string $fileId)
    {
        try {
            $this->checkProjectAccess($project);

            $directory = $this->getFileDirectory($project);
            $filePath = $directory . '/' . $fileId;

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

            $fileInfo = $this->getFileInfo($filePath);

            return response()->json([
                'success' => true,
                'file' => $fileInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения файла'
            ], 500);
        }
    }

    /**
     * Удалить файл
     */
    public function destroy(Project $project, string $fileId)
    {
        try {
            $this->checkProjectAccess($project);

            $directory = $this->getFileDirectory($project);
            $filePath = $directory . '/' . $fileId;

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Файл не найден'
                ], 404);
            }

            // Удаляем файл и его метаданные
            Storage::disk('public')->delete($filePath);
            $this->deleteFileMetadata($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Файл успешно удален'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка удаления файла'
            ], 500);
        }
    }

    /**
     * Загрузить один файл
     */
    protected function uploadFile($file, string $directory, Request $request): ?array
    {
        try {
            // Проверяем размер файла
            if ($file->getSize() > $this->maxFileSize) {
                throw new \Exception('Файл слишком большой');
            }

            // Проверяем тип файла
            $mimeType = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $this->allowedExtensions)) {
                throw new \Exception('Недопустимый тип файла');
            }

            // Генерируем уникальное имя файла
            $fileName = $this->generateFileName($file);
            $filePath = $directory . '/' . $fileName;

            // Сохраняем файл
            $file->storeAs($directory, $fileName, 'public');

            // Сохраняем метаданные
            $metadata = array_merge([
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $mimeType,
                'extension' => $extension,
                'category' => $this->getFileCategory(),
                'description' => $request->get('description'),
                'type' => $request->get('type'),
                'room' => $request->get('room'),
                'uploaded_at' => now()->toISOString()
            ], $this->getAdditionalFileMetadata($request, $fileName));

            $this->saveFileMetadata($filePath, $metadata);

            return [
                'id' => $fileName,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $mimeType,
                'url' => asset('storage/' . $filePath),
                'created_at' => now()->format('Y-m-d H:i:s'),
                'metadata' => $metadata
            ];

        } catch (\Exception $e) {
            Log::error('Error uploading single file: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Получить информацию о файле
     */
    protected function getFileInfo(string $filePath): ?array
    {
        try {
            if (!Storage::disk('public')->exists($filePath)) {
                return null;
            }

            $fileName = basename($filePath);
            $size = Storage::disk('public')->size($filePath);
            $lastModified = Storage::disk('public')->lastModified($filePath);
            $url = asset('storage/' . $filePath);

            // Получаем метаданные
            $metadata = $this->getFileMetadata($filePath);

            return [
                'id' => $fileName,
                'file_name' => $metadata['original_name'] ?? $fileName,
                'file_path' => $filePath,
                'file_size' => $size,
                'mime_type' => $metadata['mime_type'] ?? $this->guessMimeType($filePath),
                'url' => $url,
                'created_at' => $metadata['uploaded_at'] ?? date('Y-m-d H:i:s', $lastModified),
                'updated_at' => date('Y-m-d H:i:s', $lastModified),
                'category' => $metadata['category'] ?? $this->getFileCategory(),
                'type' => $metadata['type'] ?? null,
                'room' => $metadata['room'] ?? null,
                'description' => $metadata['description'] ?? null,
                'metadata' => $metadata
            ];

        } catch (\Exception $e) {
            Log::error('Error getting file info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Проверить соответствие файла фильтрам
     */
    protected function matchesFilters(array $fileInfo, ?string $search, ?string $date, ?string $category, ?string $type, ?string $room): bool
    {
        // Фильтр по поиску
        if ($search && !str_contains(strtolower($fileInfo['file_name']), strtolower($search))) {
            return false;
        }

        // Фильтр по дате
        if ($date) {
            $fileDate = date('Y-m-d', strtotime($fileInfo['created_at']));
            if ($fileDate !== $date) {
                return false;
            }
        }

        // Фильтр по категории
        if ($category && $fileInfo['category'] !== $category) {
            return false;
        }

        // Фильтр по типу
        if ($type && $fileInfo['type'] !== $type) {
            return false;
        }

        // Фильтр по комнате
        if ($room && $fileInfo['room'] !== $room) {
            return false;
        }

        return true;
    }

    /**
     * Генерировать уникальное имя файла
     */
    protected function generateFileName($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "{$baseName}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Сохранить метаданные файла
     */
    protected function saveFileMetadata(string $filePath, array $metadata): void
    {
        $metadataPath = $filePath . '.meta';
        Storage::disk('public')->put($metadataPath, json_encode($metadata));
    }

    /**
     * Получить метаданные файла
     */
    protected function getFileMetadata(string $filePath): array
    {
        $metadataPath = $filePath . '.meta';
        
        if (Storage::disk('public')->exists($metadataPath)) {
            $content = Storage::disk('public')->get($metadataPath);
            return json_decode($content, true) ?? [];
        }
        
        return [];
    }

    /**
     * Удалить метаданные файла
     */
    protected function deleteFileMetadata(string $filePath): void
    {
        $metadataPath = $filePath . '.meta';
        if (Storage::disk('public')->exists($metadataPath)) {
            Storage::disk('public')->delete($metadataPath);
        }
    }

    /**
     * Определить MIME-тип файла по расширению
     */
    protected function guessMimeType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'dwg' => 'application/acad',
            'dxf' => 'application/dxf',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
