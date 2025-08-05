<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Отобразить список доступных тарифных планов
     */
    public function index()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        $currentSubscription = Auth::user()->activeSubscription;
        
        return view('subscriptions.index', compact('plans', 'currentSubscription'));
    }
    
    /**
     * Показать детали тарифного плана
     */
    public function show(SubscriptionPlan $plan)
    {
        $currentSubscription = Auth::user()->activeSubscription;
        
        return view('subscriptions.show', compact('plan', 'currentSubscription'));
    }
    
    /**
     * Страница выбора периода подписки
     */
    public function selectPeriod(SubscriptionPlan $plan)
    {
        $currentSubscription = Auth::user()->activeSubscription;
        
        return view('subscriptions.select-period', compact('plan', 'currentSubscription'));
    }
    
    /**
     * Подписаться на тарифный план
     */
    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'billing_period' => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
        ]);
        
        $user = Auth::user();
        
        // Проверяем, есть ли уже активная подписка
        $currentSubscription = $user->activeSubscription;
        
        try {
            DB::beginTransaction();
            
            // Если есть активная подписка, отменяем её
            if ($currentSubscription) {
                $currentSubscription->cancel();
            }
            
            // Создаем новую подписку
            $price = $plan->getPriceForPeriod($request->billing_period);
            $expiresAt = $request->billing_period === 'yearly' 
                ? now()->addYear() 
                : now()->addMonth();
                
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'last_payment_at' => now(),
                'next_payment_at' => $request->billing_period === 'yearly' 
                    ? $expiresAt->copy()->subDays(7)
                    : $expiresAt->copy()->subDays(3),
                'status' => 'active',
                'billing_period' => $request->billing_period,
                'paid_amount' => $price,
                'payment_method' => $request->payment_method,
                'auto_renewal' => $request->has('auto_renewal'),
            ]);
            
            // Обновляем счетчики ресурсов
            $subscription->updateResourceCounters();
            
            DB::commit();
            
            return redirect()->route('subscriptions.success')
                ->with('success', 'Подписка успешно оформлена!');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withErrors(['error' => 'Произошла ошибка при оформлении подписки. Попробуйте снова.'])
                ->withInput();
        }
    }
    
    /**
     * Страница успешной подписки
     */
    public function success()
    {
        $subscription = Auth::user()->activeSubscription;
        
        if (!$subscription) {
            return redirect()->route('subscriptions.index');
        }
        
        return view('subscriptions.success', compact('subscription'));
    }
    
    /**
     * Страница управления подпиской
     */
    public function manage()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription()->with('subscriptionPlan')->first();
        
        if (!$subscription) {
            return redirect()->route('subscriptions.index')
                ->with('info', 'У вас нет активной подписки.');
        }
        
        // Получаем информацию о хранилище
        $storageService = app(\App\Services\StorageLimitService::class);
        $storageInfo = $storageService->getUserStorageInfo($user);
        
        return view('subscription.manage', compact('subscription', 'storageInfo'));
    }
    
    /**
     * Изменить тарифный план
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'new_plan_id' => 'required|exists:subscription_plans,id',
            'billing_period' => 'required|in:monthly,yearly',
        ]);
        
        $user = Auth::user();
        $currentSubscription = $user->activeSubscription;
        $newPlan = SubscriptionPlan::findOrFail($request->new_plan_id);
        
        if (!$currentSubscription) {
            return back()->withErrors(['error' => 'У вас нет активной подписки.']);
        }
        
        try {
            DB::beginTransaction();
            
            // Отменяем текущую подписку
            $currentSubscription->cancel();
            
            // Создаем новую подписку
            $price = $newPlan->getPriceForPeriod($request->billing_period);
            $expiresAt = $request->billing_period === 'yearly' 
                ? now()->addYear() 
                : now()->addMonth();
                
            $newSubscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $newPlan->id,
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'last_payment_at' => now(),
                'status' => 'active',
                'billing_period' => $request->billing_period,
                'paid_amount' => $price,
                'auto_renewal' => $currentSubscription->auto_renewal,
            ]);
            
            // Обновляем счетчики ресурсов
            $newSubscription->updateResourceCounters();
            
            DB::commit();
            
            return redirect()->route('subscriptions.manage')
                ->with('success', 'Тарифный план успешно изменен!');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => 'Произошла ошибка при смене тарифного плана.']);
        }
    }
    
    /**
     * Отменить подписку
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return back()->withErrors(['error' => 'У вас нет активной подписки.']);
        }
        
        $subscription->cancel();
        
        return redirect()->route('subscriptions.index')
            ->with('success', 'Подписка отменена. Доступ сохранится до окончания оплаченного периода.');
    }
    
    /**
     * Возобновить подписку
     */
    public function resume(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()
            ->where('status', 'cancelled')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
            
        if (!$subscription) {
            return back()->withErrors(['error' => 'Нет подписки для возобновления.']);
        }
        
        $subscription->status = 'active';
        $subscription->auto_renewal = true;
        $subscription->next_payment_at = $subscription->billing_period === 'yearly' 
            ? $subscription->expires_at->copy()->subDays(7)
            : $subscription->expires_at->copy()->subDays(3);
        $subscription->save();
        
        return redirect()->route('subscriptions.manage')
            ->with('success', 'Подписка успешно возобновлена!');
    }
    
    /**
     * Обновить счетчики использования ресурсов
     */
    public function updateResourceCounters()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        
        if ($subscription) {
            $subscription->updateResourceCounters();
        }
        
        return response()->json(['success' => true]);
    }
}
