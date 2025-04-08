<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\AdminManager\AdminController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\ProductManagement\ProductController;
use App\Http\Controllers\Admin\ProductManagement\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthAdminController::class, 'login']);
    
    // Protected admin routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthAdminController::class, 'profile']);
        Route::post('/logout', [AuthAdminController::class, 'logout']);
        
        // Admin Manager routes - only accessible to manager role
        Route::prefix('manager')->middleware(\App\Http\Middleware\AdminManagerMiddleware::class)->group(function () {
            // Admin management routes
            Route::controller(AdminController::class)->group(function () {
                Route::get('/admins', 'index');
                Route::post('/admins', 'store');
                Route::get('/admins/{id}', 'show');
                Route::put('/admins/{id}', 'update');
                Route::delete('/admins/{id}', 'destroy');
            });
            
            // Dashboard routes
            Route::controller(\App\Http\Controllers\Admin\Dashboard\DashboardController::class)->prefix('dashboard')->group(function () {
                Route::get('/statistics', 'getStatistics');
                Route::get('/recent-activity', 'getRecentActivity');
            });
        });
        
        // Seller Admin routes - only accessible to seller role
        Route::prefix('seller')->middleware(\App\Http\Middleware\SellerAdminMiddleware::class)->group(function () {
            // Product Management routes
            Route::prefix('products')->controller(\App\Http\Controllers\Admin\ProductManagement\ProductController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });
            
            // Category Management routes
            Route::prefix('categories')->controller(\App\Http\Controllers\Admin\ProductManagement\CategoryController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });
        });
    });
});
