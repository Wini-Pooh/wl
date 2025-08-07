<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeSubscriptionInheritance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }
        
        try {
            // Проверяем, является ли пользователь сотрудником
            if ($user->isEmployee()) {
                $partner = $user->getPartner();
                
                if ($partner && $partner->activeSubscription) {
                    // Сотрудник наследует подписку партнера - продолжаем
                    return $next($request);
                } else {
                    // У партнера нет подписки - сообщаем сотруднику
                    return redirect()->route('dashboard')
                        ->with('warning', 'Ваш партнер не имеет активной подписки. Обратитесь к партнеру для решения этого вопроса.');
                }
            }
            
            // Для партнеров проверяем собственную подписку
            if (!$user->activeSubscription) {
                // Если это запрос к страницам подписки, разрешаем
                if ($request->is('subscriptions*')) {
                    return $next($request);
                }
                
                // Иначе перенаправляем на выбор тарифа
                return redirect()->route('subscriptions.index')
                    ->with('info', 'Выберите тарифный план для начала работы с системой.');
            }
        } catch (\Exception $e) {
            // Если база данных недоступна, пропускаем проверку
            return $next($request);
        }
        
        return $next($request);
    }
}
