<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SessionController;

// Public API routes - SSO Session Verification
Route::post('/sso/verify-session', [SessionController::class, 'verifySession']);
Route::get('/sso/verify-session', [SessionController::class, 'verifySessionGet']);

// Protected API routes (require OAuth token)
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'me']);
    Route::get('/user/profile', [UserController::class, 'profile']);
});

