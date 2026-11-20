

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\VehicleTypeController;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'Parkly API is running successfully.',
    ]);
});

// Location APIs
Route::get('/locations', [LocationController::class, 'index']);

// Vehicle Type API
Route::get('/vehicle-types', [VehicleTypeController::class, 'index']);
