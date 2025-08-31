<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProductImageController;

/*
|--------------------------------------------------------------------------
| Public routes without authentication
|--------------------------------------------------------------------------
*/

// Public authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'store']);
Route::get('/sanctum/csrf-cookie', fn() => response()->json(['message' => 'CSRF cookie set']));

/*
|--------------------------------------------------------------------------
| Public API for frontend
|--------------------------------------------------------------------------
*/
Route::prefix('frontend')->group(function () {
    // Categories
    Route::get('categories', [CategoryController::class, 'apiIndex']);

    // Products (list + details)
    Route::get('products', [ProductController::class, 'apiIndex']);
    Route::get('products/{product}', [ProductController::class, 'apiShow']);
    // Users for the frontend
    Route::get('users', [UserController::class, 'apiIndex']);

});
// POST to create a new order
Route::post('/frontend/orders', [OrderController::class, 'placeOrder']);

// GET to view the current user's orders
Route::get('/frontend/orders', [OrderController::class, 'getOrdersByUser']);

// Feedback routes
Route::post('/frontend/feedback', [FeedbackController::class, 'store']);
Route::get('/frontend/feedback', [FeedbackController::class, 'apiIndex']);

/*
|--------------------------------------------------------------------------
| Admin login (unprotected)
|--------------------------------------------------------------------------
*/
Route::post('/admin/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-theme', [UserController::class, 'updateTheme']);
    Route::post('/frontend/orders', [OrderController::class, 'placeOrder']);
    Route::get('/frontend/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::get('/frontend/users/{user}/orders', [OrderController::class, 'getOrdersByUser']);

    // Products
    Route::post('products', [ProductController::class, 'store'])->name('api.products.store');
    Route::post('products/{product}', [ProductController::class, 'update'])->name('api.products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('api.products.destroy');

    // Product Images
    Route::post('products/{product}/images', [ProductImageController::class, 'store'])->name('api.products.images.store');
    Route::delete('product-images/{image}', [ProductImageController::class, 'destroy'])->name('api.product-images.destroy');


    // For admin/internal use
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/auth/me', fn(Request $request) => response()->json($request->user()));
});

/*
|--------------------------------------------------------------------------
| Admin login (unprotected)
|--------------------------------------------------------------------------
*/
Route::post('/admin/login', [AuthController::class, 'login']);