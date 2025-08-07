<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MockAuth
{
    /**
     * Handle an incoming request for testing without database
     */
    public function handle(Request $request, Closure $next)
    {
        // Если пользователь не авторизован и база данных недоступна
        if (!Auth::check()) {
            try {
                // Пытаемся получить пользователя из базы данных
                $user = User::first();
            } catch (\Exception $e) {
                // Если база недоступна, создаем мок-пользователя
                $mockUser = new User();
                $mockUser->id = 1;
                $mockUser->name = 'Тестовый пользователь';
                $mockUser->email = 'test@example.com';
                $mockUser->phone = '+7 (000) 000-00-00';
                
                // Авторизуем мок-пользователя
                Auth::login($mockUser);
            }
        }

        return $next($request);
    }
}
