<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Include Admin Routes
require __DIR__ . '/api/admin.php';

// Include Client Routes
require __DIR__ . '/api/client.php';
