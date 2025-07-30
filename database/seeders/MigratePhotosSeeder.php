<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MigratePhotosSeeder extends Seeder
{
    /**
     * Миграция всех фотографий из файловой системы в базу данных.
     */
    public function run(): void
    {
        $projects = Project::all();
        $totalMigrated = 0;
        $totalErrors = 0;
        
        foreach ($projects as $project) {
            $this->command->info("Мигрируем фотографии для проекта ID: {$project->id}");
            
            // Получаем все файлы из директории проекта
            $photos = Storage::disk('public')->files("projects/{$project->id}/photos");
            $migratedForProject = 0;
            $errorsForProject = 0;
            
            foreach ($photos as $photoPath) {
                // Пропускаем файлы метаданных
                if (str_ends_with($photoPath, '.meta.json')) {
                    continue;
                }
                
                $filename = basename($photoPath);
                $metaPath = "{$photoPath}.meta.json";
                
                // Проверяем, существует ли уже запись в базе данных
                $existing = ProjectPhoto::where('project_id', $project->id)
                                       ->where('filename', $filename)
                                       ->first();
                
                // Если запись уже существует, пропускаем
                if ($existing) {
                    continue;
                }
                
                // Получаем метаданные, если они существуют
                $metadata = [];
                if (Storage::disk('public')->exists($metaPath)) {
                    $metadata = json_decode(Storage::disk('public')->get($metaPath), true) ?? [];
                }
                
                try {
                    // Создаем запись в базе данных
                    ProjectPhoto::create([
                        'project_id' => $project->id,
                        'filename' => $filename,
                        'original_name' => $metadata['originalName'] ?? $filename,
                        'path' => $photoPath,
                        'category' => $metadata['category'] ?? 'Без категории',
                        'comment' => $metadata['comment'] ?? '',
                        'photo_date' => $metadata['photoDate'] ?? now()->format('Y-m-d'),
                        'file_size' => $metadata['fileSize'] ?? null,
                        'mime_type' => $metadata['mimeType'] ?? null,
                        'file_hash' => $metadata['fileHash'] ?? null,
                    ]);
                    
                    $migratedForProject++;
                    $totalMigrated++;
                } catch (\Exception $e) {
                    Log::error('Error migrating photo:', [
                        'error' => $e->getMessage(),
                        'file' => $filename,
                        'project_id' => $project->id
                    ]);
                    $errorsForProject++;
                    $totalErrors++;
                }
            }
            
            $this->command->info("Для проекта {$project->id}: перенесено {$migratedForProject} фото, ошибок: {$errorsForProject}");
        }
        
        $this->command->info("Миграция завершена. Всего перенесено {$totalMigrated} фото. Всего ошибок: {$totalErrors}");
    }
}
