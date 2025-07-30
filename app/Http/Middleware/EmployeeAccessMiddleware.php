<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeAccessMiddleware
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
        
        // Админ имеет доступ ко всему
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Партнер имеет доступ к своим данным
        if ($user->isPartner() && in_array('partner', $roles)) {
            return $next($request);
        }
        
        // Проверяем роли сотрудников
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                // Если это сотрудник, сметчик или прораб, проверяем связь с партнером
                if (($user->isEmployee() || $user->isEstimator() || $user->isForeman()) && 
                    in_array($role, ['employee', 'estimator', 'foreman'])) {
                    $employeeProfile = $user->employeeProfile;
                    if ($employeeProfile && $employeeProfile->status === 'active') {
                        // Добавляем информацию о партнере в запрос
                        $request->attributes->set('employee_partner_id', $employeeProfile->partner_id);
                        $request->attributes->set('employee_profile', $employeeProfile);
                        $request->attributes->set('partner_data_access', true);
                        return $next($request);
                    }
                }
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
