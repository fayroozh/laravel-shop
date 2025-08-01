<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;

// ✅ توجيه المستخدم مباشرة إلى واجهة React
Route::get('/', fn () => redirect('http://localhost:3000/'));

// ✅ مسارات التحقق
Route::get('/web-check', fn () => 'web route working');
Route::get('/api-test', fn () => view('api-test'));

// ✅ مصادقة المستخدم
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ✅ لوحة تحكم المشرفين (Blade) - محمية بالمصادقة
// Mover estas rutas dentro del grupo de middleware de autenticación
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ✅ التقارير
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/employees', [ReportController::class, 'employees'])->name('employees');
    });

    // ✅ روابط الموارد الرئيسية
    Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
    Route::get('/suppliers', [AdminController::class, 'suppliers'])->name('suppliers');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/feedback', [AdminController::class, 'feedback'])->name('feedback');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/users', [UserController::class, 'index'])->name('users');

    // ✅ منتجات
    // Products Management
    Route::prefix('products')->group(function () {
        Route::post('/store', [ProductController::class, 'store'])->name('products.store');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/search', [ProductController::class, 'search'])->name('products.search');
    });

    // ✅ تصنيفات
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // ✅ موردين
    Route::prefix('suppliers')->group(function () {
        Route::post('/store', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
        Route::get('/search', [SupplierController::class, 'search'])->name('suppliers.search');
    });

    // ✅ موظفين
    Route::prefix('employees')->group(function () {
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/search', [EmployeeController::class, 'search'])->name('employees.search');
    });

    // ✅ الطلبات
    Route::prefix('orders')->group(function () {
        Route::put('/{id}', [OrderController::class, 'update'])->name('orders.update');
    });

    // ✅ تصدير و أنشطة
    Route::get('/export-report/{format}', [AdminController::class, 'exportReport'])->name('export.report');
    Route::get('/activities', [AdminController::class, 'activities'])->name('activities');

    // ✅ المستخدمين
       Route::prefix('users')->name('users.')->group(function () {
           Route::post('/store', [UserController::class, 'store'])->name('store');
           Route::put('/{user}', [UserController::class, 'update'])->name('update');
           Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
           Route::get('/search', [UserController::class, 'search'])->name('search');
       });
    
    

    // ✅ الصلاحيات
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // ✅ الإشعارات
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
        Route::get('/latest', [NotificationController::class, 'getLatestNotifications'])->name('notifications.latest');
    });
    
    // General search
    Route::get('/search', [AdminController::class, 'search'])->name('search');
});

// ✅ تسجيل دخول المشرفين
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

