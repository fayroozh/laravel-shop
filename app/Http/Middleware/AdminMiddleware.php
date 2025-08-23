<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // التحقق من وجود token في الكوكيز
        if ($token = $request->cookie('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'غير مصرح'], 401)
                : redirect()->route('login');
        }

        $user = Auth::user();
        
        // التحقق من الصلاحيات
        if (!$user->is_admin && !$user->is_employee_role) {
            return $request->expectsJson()
                ? response()->json(['message' => 'غير مصرح لك بالوصول'], 403)
                : redirect()->back()->with('error', 'غير مصرح لك بالوصول');
        }

        return $next($request);
    }
}
