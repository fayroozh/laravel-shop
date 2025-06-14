<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;

// مسارات عامة بدون مصادقة
Route::middleware('api')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
});

// مسارات مصادقة المستخدم (عادي)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/profile', [UserController::class, 'updateProfile']);
    Route::get('user/orders', [OrderController::class, 'userOrders']);
    Route::get('user/feedback', [FeedbackController::class, 'userFeedback']);
    
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('my-orders', function (Request $request) {
        return $request->user()->orders()->with('product')->get();
    });
    
    Route::post('feedback', [FeedbackController::class, 'store']);
    Route::get('my-feedback', function (Request $request) {
        return $request->user()->feedback()->latest()->get();
    });
    
    Route::post('logout', [AuthController::class, 'logout']);
});

// مسارات مصادقة مسؤول (admin)
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('dashboard/stats', function () {
        return [
            'products_count' => \App\Models\Product::count(),
            'orders_count' => \App\Models\Order::count(),
            'feedback_count' => \App\Models\Feedback::count(),
            'employees_count' => \App\Models\Employee::count(),
            'suppliers_count' => \App\Models\Supplier::count(),
        ];
    });

    // موارد الإدارة
    Route::apiResource('users', UserController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);

    // إدارة الطلبات والملاحظات
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('feedback', [FeedbackController::class, 'index']);

    // إدارة الصور
    Route::post('products/{product}/images', [ProductImageController::class, 'store']);
    
    // تعديل المنتجات والتصنيفات
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
});
