<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OAuth\AuthorizationController;
use App\Http\Controllers\UserManagementController;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/login/success', [LoginController::class, 'showLoginSuccess'])->name('login.success');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logoutGet'])->name('logout.get');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/success', [RegisterController::class, 'showRegisterSuccess'])->name('register.success');

// OAuth Authorization Routes
Route::get('/oauth/authorize-custom', [AuthorizationController::class, 'authorize'])->name('oauth.authorize');
Route::post('/oauth/approve', [AuthorizationController::class, 'approveAuthorization'])->name('oauth.approve');

// User Management Routes
Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
Route::get('/users/{user}/token', [UserManagementController::class, 'showToken'])->name('users.show.token');
Route::get('/users/{user}/tokens', [UserManagementController::class, 'showTokens'])->name('users.tokens');

// SSO Session Verification (Web route for easy testing)
Route::get('/sso/verify', function (\Illuminate\Http\Request $request) {
    $sessionToken = $request->query('session_token') ?? $request->query('sso_session');
    $callbackUrl = $request->query('callback');
    
    // If no session token, redirect to login
    if (!$sessionToken) {
        // If callback URL is provided, redirect to login with callback
        if ($callbackUrl) {
            return redirect()->route('login', ['callback' => $callbackUrl]);
        }
        
        // Otherwise show error
        return response()->json([
            'authenticated' => false,
            'message' => 'Session token required. Use: ?session_token=XXX or ?callback=URL to login'
        ], 400);
    }
    
    // Get session data from cache
    $sessionData = \Cache::get('sso_session_' . $sessionToken);
    
    if (!$sessionData) {
        // If session not found and callback provided, redirect to login
        if ($callbackUrl) {
            return redirect()->route('login', ['callback' => $callbackUrl]);
        }
        
        return response()->json([
            'authenticated' => false,
            'message' => 'Session not found or expired'
        ], 404);
    }
    
    // Return session data
    return response()->json([
        'authenticated' => true,
        'user_id' => $sessionData['user_id'],
        'user_name' => $sessionData['user_name'],
        'user_email' => $sessionData['user_email'],
        'jwt_token' => $sessionData['jwt_token'],
        'login_time' => $sessionData['login_time'] ?? $sessionData['register_time'] ?? null,
    ]);
});
