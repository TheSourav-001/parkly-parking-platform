<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Google OAuth 2.0 Web Routes
Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});
