<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // التحقق من أن المستخدم هو مسؤول أو موظف
            if ($user->isAdmin() || $user->isEmployee()) {
                $request->session()->regenerate();
                return redirect()->intended('admin/dashboard');
            }
            
            // إذا لم يكن مسؤولاً، قم بتسجيل خروجه
            Auth::logout();
        }

        return back()->withErrors([
            'email' => 'بيانات الاعتماد المقدمة غير صحيحة أو ليس لديك صلاحيات كافية.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}