<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| مسارات عامة بدون مصادقة
|--------------------------------------------------------------------------
*/

// مصادقة عامة
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'store']);
Route::get('/sanctum/csrf-cookie', fn() => response()->json(['message' => 'CSRF cookie set']));


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/frontend/products', [ProductController::class, 'apiIndex']);

// عرض التصنيفات والمنتجات (قراءة فقط)
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// المستخدم
Route::get('/user', fn(Request $request) => $request->user());
Route::get('user/profile', [UserController::class, 'profile']);
Route::put('user/profile', [UserController::class, 'updateProfile']);

// الطلبات
Route::get('user/orders', [OrderController::class, 'userOrders']);
Route::post('orders', [OrderController::class, 'store']);
Route::get(
    'my-orders',
    fn(Request $request) =>
    $request->user()->orders()->with('product')->get()
);

// التعليقات
Route::get('user/feedback', [FeedbackController::class, 'userFeedback']);
Route::post('feedback', [FeedbackController::class, 'store']);

// تسجيل الخروج
Route::post('logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| مسارات الأدمن (محمية)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| تسجيل دخول الأدمن (خارج الحماية)
|--------------------------------------------------------------------------
*/

Route::post('/admin/login', [AuthController::class, 'login']);