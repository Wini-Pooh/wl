<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $resource
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $resource)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Проверяем, есть ли активная подписка
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return redirect()->route('subscriptions.index')
                ->with('warning', 'Для использования этой функции необходима активная подписка.');
        }
        
        // Проверяем лимит ресурса
        if (!$user->checkResourceLimit($resource)) {
            $resourceNames = [
                'active_projects' => 'активных проектов',
                'employees' => 'сотрудников',
                'right_hand_employees' => 'правых рук',
                'estimate_templates' => 'шаблонов смет',
            ];
            
            $resourceName = $resourceNames[$resource] ?? $resource;
            
            return redirect()->back()
                ->with('warning', "Достигнут лимит {$resourceName} для вашего тарифного плана. Обновите подписку для увеличения лимитов.")
                ->withInput();
        }
        
        return $next($request);
    }
}
