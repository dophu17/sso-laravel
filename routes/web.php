<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OAuth\AuthorizationController;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logoutGet'])->name('logout.get');

// Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard');
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/success', [RegisterController::class, 'showRegisterSuccess'])->name('register.success');

// OAuth Authorization Routes (Optional - for third-party apps)
Route::get('/oauth/authorize-custom', [AuthorizationController::class, 'authorize'])->name('oauth.authorize');
Route::post('/oauth/approve', [AuthorizationController::class, 'approveAuthorization'])->name('oauth.approve');
