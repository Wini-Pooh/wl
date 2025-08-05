<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class StorageLimitService
{
    /**
     * Проверить лимит хранилища для проекта
     */
    public function checkProjectStorageLimit(User $user, int $projectId = null): bool
    {
        $subscription = $user->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            return false;
        }
        
        $plan = $subscription->subscriptionPlan;
        $limitMb = $plan->project_storage_limit_mb ?? 0;
        
        if ($limitMb <= 0) {
            return false;
        }
        
        // Если проверяем конкретный проект
        if ($projectId) {
            $project = Project::find($projectId);
            if (!$project) {
                return false;
            }
            
            // Проверяем доступ к проекту
            if ($user->isPartner() && $project->partner_id !== $user->id) {
                return false;
            } elseif ($user->isEmployee() && $project->partner_id !== $user->employeeProfile?->partner_id) {
                return false;
            }
            
            $currentSizeMb = $this->getProjectStorageSize($project);
            return $currentSizeMb < $limitMb;
        }
        
        return true;
    }
    
    /**
     * Получить размер хранилища проекта в мегабайтах
     */
    public function getProjectStorageSize(Project $project): float
    {
        $totalSize = 0;
        
        // Получаем все файлы проекта
        $projectPath = "projects/{$project->id}";
        
        if (!Storage::exists($projectPath)) {
            return 0;
        }
        
        $files = Storage::allFiles($projectPath);
        
        foreach ($files as $file) {
            $totalSize += Storage::size($file);
        }
        
        // Размер проектных документов
        $documents = $project->documents();
        foreach ($documents as $document) {
            if ($document->file_path && Storage::exists($document->file_path)) {
                $totalSize += Storage::size($document->file_path);
            }
        }
        
        // Размер файлов дизайна
        $designFiles = $project->designFiles();
        foreach ($designFiles as $designFile) {
            if ($designFile->file_path && Storage::exists($designFile->file_path)) {
                $totalSize += Storage::size($designFile->file_path);
            }
        }
        
        // Размер фотографий проекта
        $photos = $project->photos();
        foreach ($photos as $photo) {
            if ($photo->file_path && Storage::exists($photo->file_path)) {
                $totalSize += Storage::size($photo->file_path);
            }
        }
        
        // Размер схем проекта
        $schemes = $project->schemes();
        foreach ($schemes as $scheme) {
            if ($scheme->file_path && Storage::exists($scheme->file_path)) {
                $totalSize += Storage::size($scheme->file_path);
            }
        }
        
        // Конвертируем в мегабайты
        return round($totalSize / 1024 / 1024, 2);
    }
    
    /**
     * Получить оставшееся место в хранилище проекта
     */
    public function getRemainingProjectStorage(Project $project): float
    {
        // Получаем пользователя-партнера для этого проекта
        $partner = User::find($project->partner_id);
        if (!$partner) {
            return 0;
        }
        
        $subscription = $partner->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            return 0;
        }
        
        $plan = $subscription->subscriptionPlan;
        $limitMb = $plan->project_storage_limit_mb ?? 0;
        $usedMb = $this->getProjectStorageSize($project);
        
        return max(0, $limitMb - $usedMb);
    }
    
    /**
     * Получить процент использования хранилища проекта
     */
    public function getProjectStorageUsagePercentage(Project $project): float
    {
        // Получаем пользователя-партнера для этого проекта
        $partner = User::find($project->partner_id);
        if (!$partner) {
            return 100;
        }
        
        $subscription = $partner->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            return 100;
        }
        
        $plan = $subscription->subscriptionPlan;
        $limitMb = $plan->project_storage_limit_mb ?? 1;
        $usedMb = $this->getProjectStorageSize($project);
        
        return min(100, ($usedMb / $limitMb) * 100);
    }
    
    /**
     * Проверить, можно ли загрузить файл в проект
     */
    public function canUploadFile(Project $project, int $fileSizeBytes): bool
    {
        $fileSizeMb = $fileSizeBytes / 1024 / 1024;
        $remainingMb = $this->getRemainingProjectStorage($project);
        
        return $fileSizeMb <= $remainingMb;
    }
    
    /**
     * Получить информацию о хранилище для всех проектов пользователя
     */
    public function getUserProjectsStorageInfo(User $user): array
    {
        $subscription = $user->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            return [];
        }
        
        // Получаем проекты с учетом новой логики доступа
        $projects = \App\Helpers\ProjectAccessHelper::getAccessibleProjects($user);
        
        $storageInfo = [];
        
        foreach ($projects as $project) {
            $storageInfo[$project->id] = [
                'project_name' => $project->name ?? 'Проект #' . $project->id,
                'used_mb' => $this->getProjectStorageSize($project),
                'limit_mb' => $subscription->subscriptionPlan->project_storage_limit_mb ?? 0,
                'remaining_mb' => $this->getRemainingProjectStorage($project),
                'usage_percentage' => $this->getProjectStorageUsagePercentage($project),
            ];
        }
        
        return $storageInfo;
    }
    
    /**
     * Очистить кэш размеров файлов (если используется кэширование)
     */
    public function clearStorageCache(Project $project): void
    {
        // Здесь можно добавить очистку кэша, если он используется
        // Cache::forget("project_storage_size_{$project->id}");
    }
    
    /**
     * Получить общую информацию об использовании хранилища пользователем
     */
    public function getUserStorageInfo(User $user): array
    {
        $subscription = $user->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            return [
                'current' => 0,
                'limit' => 0,
                'current_formatted' => '0 МБ',
                'limit_formatted' => '0 МБ',
                'percentage' => 0,
                'available' => 0,
                'available_formatted' => '0 МБ'
            ];
        }
        
        // Получаем все проекты пользователя
        if ($user->isPartner()) {
            $projects = Project::where('partner_id', $user->id)->get();
        } elseif ($user->isEmployee()) {
            $projects = Project::where('partner_id', $user->employeeProfile?->partner_id)->get();
        } else {
            $projects = collect();
        }
        
        // Подсчитываем общее использование
        $totalUsedMb = 0;
        foreach ($projects as $project) {
            $totalUsedMb += $this->getProjectStorageSize($project);
        }
        
        $limitMb = $subscription->subscriptionPlan->project_storage_limit_mb ?? 0;
        $availableMb = max(0, $limitMb - $totalUsedMb);
        $percentage = $limitMb > 0 ? min(100, ($totalUsedMb / $limitMb) * 100) : 0;
        
        return [
            'current' => $totalUsedMb,
            'limit' => $limitMb,
            'current_formatted' => round($totalUsedMb, 2) . ' МБ',
            'limit_formatted' => $limitMb . ' МБ',
            'percentage' => round($percentage, 1),
            'available' => $availableMb,
            'available_formatted' => round($availableMb, 2) . ' МБ'
        ];
    }
    
    /**
     * Проверить приближение к лимиту хранилища
     */
    public function checkStorageLimitWarning(User $user): ?string
    {
        $usage = $this->getUserStorageInfo($user);
        
        if ($usage['percentage'] >= 90) {
            return "Внимание! Использовано {$usage['percentage']}% от лимита хранилища. Доступно: {$usage['available_formatted']}";
        } elseif ($usage['percentage'] >= 80) {
            return "Использовано {$usage['percentage']}% от лимита хранилища. Доступно: {$usage['available_formatted']}";
        }
        
        return null;
    }
}
