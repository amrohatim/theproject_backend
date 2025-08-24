<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Allow admins to access everything
            if ($user->role === 'admin') {
                return $next($request);
            }

            // Check if user status is pending or declined
            if ($user->status === 'pending') {
                // If it's an API request, return JSON response
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account is pending approval. Please wait for admin review.',
                        'status' => 'pending',
                        'redirect' => route('pending-approval'),
                    ], 403);
                }

                // For web requests, redirect to pending approval page
                return redirect()->route('pending-approval');
            }

            if ($user->status === 'declined') {
                // If it's an API request, return JSON response
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account registration has been declined. Please contact support.',
                        'status' => 'declined',
                        'redirect' => route('registration-declined'),
                    ], 403);
                }

                // For web requests, redirect to declined page
                return redirect()->route('registration-declined');
            }

            if ($user->status === 'inactive') {
                // If it's an API request, return JSON response
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been deactivated. Please contact support.',
                        'status' => 'inactive',
                    ], 403);
                }

                // For web requests, redirect to login with error
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact support.');
            }
        }

        return $next($request);
    }
}
