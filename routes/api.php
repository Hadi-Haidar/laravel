<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\AdminManager\AdminController;

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
            // Import controller at the top of the file for better readability
            Route::controller(AdminController::class)->group(function () {
                Route::get('/admins', 'index');
                Route::post('/admins', 'store');
                Route::get('/admins/{id}', 'show');
                Route::put('/admins/{id}', 'update');
                Route::delete('/admins/{id}', 'destroy');
            });
        });
    });
});
