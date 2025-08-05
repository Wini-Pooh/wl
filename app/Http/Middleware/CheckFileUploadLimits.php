<?php

namespace App\Http\Middleware;

use App\Services\StorageLimitService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckFileUploadLimits
{
    protected $storageLimitService;

    public function __construct(StorageLimitService $storageLimitService)
    {
        $this->storageLimitService = $storageLimitService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Админы не ограничены
        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        // Проверяем подписку
        $subscription = $user->activeSubscription;
        if (!$subscription) {
            return redirect()->back()
                ->with('error', 'У вас нет активной подписки для загрузки файлов.');
        }

        // Если есть файлы для загрузки
        if ($request->hasFile('file') || $request->hasFile('files')) {
            $files = [];
            
            // Собираем все файлы
            if ($request->hasFile('file')) {
                $files[] = $request->file('file');
            }
            
            if ($request->hasFile('files')) {
                $uploadedFiles = $request->file('files');
                if (is_array($uploadedFiles)) {
                    $files = array_merge($files, $uploadedFiles);
                } else {
                    $files[] = $uploadedFiles;
                }
            }

            // Проверяем каждый файл
            $totalSize = 0;
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $totalSize += $file->getSize();
                }
            }

            // Получаем проект, если он указан
            $projectId = $request->route('project') ? $request->route('project')->id : null;
            
            if ($projectId) {
                $project = \App\Models\Project::find($projectId);
                
                if ($project && !$this->storageLimitService->canUploadFile($project, $totalSize)) {
                    $fileSizeMb = round($totalSize / 1024 / 1024, 2);
                    $remainingMb = $this->storageLimitService->getRemainingProjectStorage($project);
                    
                    return redirect()->back()
                        ->with('error', "Недостаточно места для загрузки файлов. Размер файлов: {$fileSizeMb} МБ, доступно: {$remainingMb} МБ");
                }
            }
        }

        return $next($request);
    }
}
