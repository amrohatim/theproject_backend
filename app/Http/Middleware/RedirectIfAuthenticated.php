<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Debug logging
                \Illuminate\Support\Facades\Log::info('🔍 RedirectIfAuthenticated middleware triggered', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'request_url' => $request->url(),
                ]);

                if ($user->role === 'admin') {
                    \Illuminate\Support\Facades\Log::info('🔄 Redirecting to admin dashboard');
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'vendor') {
                    \Illuminate\Support\Facades\Log::info('🔄 Redirecting to vendor dashboard');
                    return redirect()->route('vendor.dashboard');
                } elseif ($user->role === 'provider') {
                    \Illuminate\Support\Facades\Log::info('🔄 Redirecting to provider dashboard');
                    return redirect()->route('provider.dashboard');
                } elseif ($user->role === 'merchant') {
                    \Illuminate\Support\Facades\Log::info('🔄 Redirecting to merchant dashboard');
                    return redirect()->route('merchant.dashboard');
                } elseif ($user->role === 'service_provider') {
                    \Illuminate\Support\Facades\Log::info('🔄 Redirecting to service provider dashboard');
                    return redirect()->route('service-provider.dashboard');
                } elseif ($user->role === 'products_manager') {
                    \Illuminate\Support\Facades\Log::info('🔄 Redirecting to products manager dashboard');
                    return redirect()->route('products-manager.dashboard');
                } else {
                    \Illuminate\Support\Facades\Log::info('🔄 Redirecting to home page - unknown role', [
                        'role' => $user->role
                    ]);
                    return redirect('/');
                }
            }
        }

        return $next($request);
    }
}
