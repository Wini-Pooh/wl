<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Проверяем, имеет ли пользователь одну из разрешенных ролей
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Если ни одна роль не подходит, возвращаем ошибку 403
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Недостаточно прав для доступа'], 403);
        }
        abort(403, 'Недостаточно прав для доступа к этой странице');
    }
}
