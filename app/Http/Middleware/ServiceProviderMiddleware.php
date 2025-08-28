<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceProviderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this area.');
        }

        $user = Auth::user();

        // Check if user is a service provider
        if ($user->role !== 'service_provider') {
            return redirect('/')->with('error', 'You do not have service provider access.');
        }

        // Check if user account is active
        if ($user->status !== 'active') {
            return redirect('/')->with('error', 'Your account is not active. Please contact your administrator.');
        }

        // Check if user has a service provider profile
        if (!$user->serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found. Please contact your administrator.');
        }

        // Check if the service provider's company is active
        $serviceProvider = $user->serviceProvider;
        if (!$serviceProvider->company) {
            return redirect('/')->with('error', 'Company profile not found. Please contact your administrator.');
        }

        // Service Providers don't require license approval - they are vendor-managed roles

        return $next($request);
    }
}
