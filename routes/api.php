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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ReportController;

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
    // إحصائيات لوحة التحكم
    Route::get('dashboard/stats', function () {
        return [
            'products_count' => \App\Models\Product::count(),
            'orders_count' => \App\Models\Order::count(),
            'feedback_count' => \App\Models\Feedback::count(),
            'employees_count' => \App\Models\Employee::count(),
            'suppliers_count' => \App\Models\Supplier::count(),
        ];
    });
    
    // مسارات المنتجات
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    
    // مسارات التصنيفات
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    
    // مسارات للطلبات
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}', [OrderController::class, 'update']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']);
    
    // مسارات للتعليقات
    Route::get('feedback', [FeedbackController::class, 'index']);
    Route::delete('feedback/{id}', [FeedbackController::class, 'destroy']);
    
    // مسارات للمستخدمين
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    
    // مسارات للموظفين
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::post('employees', [EmployeeController::class, 'store']);
    Route::get('employees/{id}', [EmployeeController::class, 'show']);
    Route::put('employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);
    
    // مسارات للتقارير
    Route::get('reports/sales', [ReportController::class, 'sales']);
    Route::get('reports/inventory', [ReportController::class, 'inventory']);
    Route::get('reports/customers', [ReportController::class, 'customers']);
    Route::get('reports/employees', [ReportController::class, 'employees']);
    
    // تصدير التقارير
    Route::get('reports/export/{type}/{format}', [AdminController::class, 'exportReport']);
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
