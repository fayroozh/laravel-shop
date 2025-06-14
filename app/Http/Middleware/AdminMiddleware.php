<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Check for token in cookie (from React)
        if ($request->hasCookie('laravel_token')) {
            $token = $request->cookie('laravel_token');
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        if (!auth()->user()->is_admin && !auth()->user()->is_employee_role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied'], 403);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}

