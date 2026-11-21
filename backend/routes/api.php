<?php

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\VehicleTypeController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'Parkly API is running successfully.',
    ]);
});

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'healthy',
        'platform' => 'Parkly API',
    ]);
});

// Homepage and Location APIs
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/vehicle-types', [VehicleTypeController::class, 'index']);

// Authentication APIs
Route::prefix('v1/auth')->group(function () {
    // Public authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected authentication routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::match(['put', 'patch'], '/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

