<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ProjectAccessHelper;
use App\Models\Project;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Если пользователь не авторизован, пропускаем middleware
        if (!$user) {
            return $next($request);
        }

        // Получаем ID проекта из параметров маршрута
        $projectId = $request->route('project');
        
        // Если это модель Project, получаем ID
        if ($projectId instanceof Project) {
            $project = $projectId;
        } else {
            // Если это просто ID, загружаем проект
            $project = Project::find($projectId);
        }

        // Если проект не найден, пропускаем middleware (обработается в контроллере)
        if (!$project) {
            return $next($request);
        }

        // Проверяем доступ через helper
        if (!ProjectAccessHelper::canAccessProject($user, $project)) {
            abort(403, 'У вас нет доступа к этому проекту');
        }

        return $next($request);
    }
}
