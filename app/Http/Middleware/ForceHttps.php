<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, находимся ли мы в production окружении или установлен флаг принудительного HTTPS
        if ((app()->environment('production') || env('APP_FORCE_HTTPS', false)) && !$request->secure()) {
            // Проверяем заголовки от прокси-сервера (например, Cloudflare, Load Balancer)
            $isSecure = $request->server('HTTPS') === 'on' ||
                       $request->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
                       $request->server('HTTP_X_FORWARDED_SSL') === 'on' ||
                       $request->server('SERVER_PORT') == 443;

            if (!$isSecure) {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }
}
