<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SSOController;

// SSO API routes (for external client verification)
Route::prefix('sso')->group(function () {
    Route::post('/verify-session', [SSOController::class, 'verifySession'])->name('api.sso.verify');
    Route::get('/verify-session', [SSOController::class, 'verifySession']); // Support both GET and POST
    Route::get('/check-session', [SSOController::class, 'checkSession'])->name('api.sso.check');
    Route::get('/server-info', [SSOController::class, 'getServerInfo'])->name('api.sso.info');
});

// Protected API routes (require authentication)
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'me']);
    Route::get('/user/profile', [UserController::class, 'profile']);
});

// SSO User info route (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/sso/user', [SSOController::class, 'getUser'])->name('api.sso.user');
});

