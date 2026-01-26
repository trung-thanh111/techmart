<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EmailVerification\EmailVerificationController;

Route::get('/', function () {
    return view('welcome');
});
// // Email verification routes
Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verifyFromEmail'])
    ->name('email.verify');

// route fallback nuxtapp 
// Fallback to Nuxt UI (SPA) for any unmatched routes
Route::fallback(function () {
    $path = public_path('nuxt-ui/index.html');

    if (file_exists($path)) {
        return response()->file($path);
    }

    // Fallback if build not found (dev mode or missing build)
    return response("Nuxt UI build not found at {$path}", 404);
});
