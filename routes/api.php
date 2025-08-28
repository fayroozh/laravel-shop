<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| مسارات عامة بدون مصادقة
|--------------------------------------------------------------------------
*/

// مصادقة عامة
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'store']);
Route::get('/sanctum/csrf-cookie', fn() => response()->json(['message' => 'CSRF cookie set']));

/*
|--------------------------------------------------------------------------
| Public API for frontend
|--------------------------------------------------------------------------
*/
Route::prefix('frontend')->group(function () {
    // التصنيفات
    Route::get('categories', [CategoryController::class, 'apiIndex']);

    // المنتجات (قائمة + تفاصيل)  👈 تم إضافة apiShow هنا
    Route::get('products', [ProductController::class, 'apiIndex']);
    Route::get('products/{product}', [ProductController::class, 'apiShow']);
    // المستخدمون للواجهة
    Route::get('users', [UserController::class, 'apiIndex']);

});
// POST لإنشاء طلب جديد
Route::post('/frontend/orders', [OrderController::class, 'placeOrder']);

// GET لعرض الطلبات الحالية للمستخدم
Route::get('/frontend/orders', [OrderController::class, 'getOrdersByUser']);
/*
|--------------------------------------------------------------------------
| تسجيل دخول الأدمن (خارج الحماية)
|--------------------------------------------------------------------------
*/
Route::post('/admin/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-theme', [UserController::class, 'updateTheme']);
    Route::post('/frontend/orders', [OrderController::class, 'placeOrder']);
    Route::get('/frontend/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::get('/frontend/users/{user}/orders', [OrderController::class, 'getOrdersByUser']);

    // للإدارة/الداخلي
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/auth/me', fn(Request $request) => response()->json($request->user()));
});

/*
|--------------------------------------------------------------------------
| تسجيل دخول الأدمن (خارج الحماية)
|--------------------------------------------------------------------------
*/
Route::post('/admin/login', [AuthController::class, 'login']);