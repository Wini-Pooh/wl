<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckFeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $feature
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $feature)
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
        
        // Проверяем доступ к функции
        if (!$user->hasFeatureAccess($feature)) {
            $featureNames = [
                'estimates' => 'смет',
                'documents' => 'документов',
                'projects' => 'проектов',
                'analytics' => 'аналитики',
                'employees' => 'управления сотрудниками',
                'online_training' => 'онлайн обучения',
            ];
            
            $featureName = $featureNames[$feature] ?? $feature;
            
            return redirect()->back()
                ->with('warning', "Доступ к разделу {$featureName} не включен в ваш тарифный план. Обновите подписку для получения доступа.")
                ->withInput();
        }
        
        return $next($request);
    }
}
