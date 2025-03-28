<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // Check if user is logged in and has role 'gym'
        if ($user && $user->hasRole('Gym')) {
            // If user is inactive, log them out and redirect to login
            if ($user->status == '2') {
                Auth::logout();
                return redirect()->route('admin.login')->with('error', 'You are blocked. Contact the admin.');
            }
        }

        return $next($request);
    }
}
