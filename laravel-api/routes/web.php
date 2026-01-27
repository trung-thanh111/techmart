<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\V1\EmailVerification\EmailVerificationController;

Route::get('/', function () {
    return view('welcome');
});
// // Email verification routes
Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verifyFromEmail'])
    ->name('email.verify');
