<?php

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\LocationHierarchyController;
use App\Http\Controllers\Api\ParkingFacilityController;
use App\Http\Controllers\Api\PricingRangeController;
use App\Http\Controllers\Api\StatsAndPricingController;
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

// Stats, AI and Pricing APIs
Route::get('/stats', [StatsAndPricingController::class, 'stats']);
Route::get('/pricing/ranges', [PricingRangeController::class, 'index']);
Route::post('/pricing/estimate', [StatsAndPricingController::class, 'estimate']);
Route::post('/ai/search', [StatsAndPricingController::class, 'aiSearch']);

// Dhaka-Centered Hierarchical Locations (District -> Thana -> Area)
Route::prefix('locations')->group(function () {
    Route::get('/districts', [LocationHierarchyController::class, 'districts']);
    Route::get('/thanas', [LocationHierarchyController::class, 'thanas']);
    Route::get('/areas', [LocationHierarchyController::class, 'areas']);
    Route::get('/coverage', [LocationHierarchyController::class, 'coverage']);
});

// Legacy / Direct locations & vehicle types
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/vehicle-types', [VehicleTypeController::class, 'index']);

// Facilities Search API (Supports Dhaka hierarchy & coordinates)
Route::get('/facilities/search', [ParkingFacilityController::class, 'index']);

// Parking Facilities API v1
Route::prefix('v1')->group(function () {
    Route::get('/facilities', [ParkingFacilityController::class, 'index']);
    Route::get('/facilities/{id}', [ParkingFacilityController::class, 'show']);
});

// Authentication APIs
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::match(['put', 'patch'], '/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
