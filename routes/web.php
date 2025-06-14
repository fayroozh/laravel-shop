<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
// https://github.com/fayroozh/laravel-shop.git
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Check if web is working
Route::get('/web-check', function () {
    return 'web route working';
});

// API test page
Route::get('/api-test', function () {
    return view('api-test');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
    
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
    
            // إنشاء توكن للمستخدم
            $token = $user->createToken('auth-token')->plainTextToken;
            
            if ($user->is_admin || $user->is_employee_role) {
                // للإداريين والموظفين: توجيه مباشر للوحة التحكم
                return redirect()->route('admin.dashboard');
            } else {
                // للمستخدمين العاديين: توجيه للصفحة الرئيسية React مع التوكن
                return redirect('/?token=' . $token . '&user=' . urlencode(json_encode([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                    'is_employee' => $user->is_employee_role
                ])));
            }
        }
    
        return back()->withErrors([
            'email' => 'Invalid login credentials.'
        ]);
    })->name('login.submit');
});

// Admin Routes (Blade templates)
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Reports Routes
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('/employees', [ReportController::class, 'employees'])->name('reports.employees');
            Route::get('/customers', [ReportController::class, 'customers'])->name('reports.customers');
        });

        // Resource Routes
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees.index');
        Route::get('/suppliers', [AdminController::class, 'suppliers'])->name('suppliers.index');
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::get('/feedback', [AdminController::class, 'feedback'])->name('feedback.index');
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
        Route::get('/products', [AdminController::class, 'products'])->name('products.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        
        // Products CRUD
        Route::prefix('products')->group(function () {
            Route::post('/store', [ProductController::class, 'store'])->name('products.store');
            Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        });

        // Categories CRUD
        Route::prefix('categories')->group(function () {
            Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });

        // Suppliers CRUD
        Route::prefix('suppliers')->group(function () {
            Route::post('/store', [SupplierController::class, 'store'])->name('suppliers.store');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
            Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
        });

        // Employees CRUD
        Route::prefix('employees')->group(function () {
            Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        });

        // Orders Management
        Route::prefix('orders')->group(function () {
            Route::put('/{id}', [OrderController::class, 'update'])->name('orders.update');
        });

        // Export and Activities
        Route::get('/export-report/{format}', [AdminController::class, 'exportReport'])->name('export.report');
        Route::get('/activities', [AdminController::class, 'activities'])->name('activities.index');

        // Users Management
        Route::prefix('users')->group(function () {
            Route::post('/', [UserController::class, 'store'])->name('users.store');
            Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Roles Management
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('roles.index');
            Route::post('/', [RoleController::class, 'store'])->name('roles.store');
            Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
            Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
            Route::get('/latest', [NotificationController::class, 'getLatestNotifications'])->name('notifications.latest');
        });
    });
