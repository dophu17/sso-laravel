# 🔐 Laravel Client SSO Integration

## 📋 Hướng dẫn integrate SSO cho Laravel Clients

Hướng dẫn này dành cho:
- **Patent Monitor** (patent-monitor.balocco-local.info)
- **Bookcase** (bookcase.balocco-local.info)

---

## 🚀 Quick Start

### Bước 1: Copy các files sau vào Client App

```
app/
├── Http/
│   ├── Middleware/
│   │   └── SSOAuthenticate.php          ← Middleware check SSO
│   └── Controllers/
│       └── Auth/
│           └── SSOController.php        ← Handle SSO callback
└── Services/
    └── SSOService.php                   ← Service gọi SSO API
```

### Bước 2: Config

Thêm vào `.env`:
```env
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://patent-monitor.balocco-local.info/sso/callback
APP_NAME="Patent Monitor"
```

### Bước 3: Register middleware

Trong `bootstrap/app.php` (Laravel 11):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'sso.auth' => \App\Http\Middleware\SSOAuthenticate::class,
    ]);
})
```

### Bước 4: Add routes

Trong `routes/web.php`:
```php
use App\Http\Controllers\Auth\SSOController;

// SSO routes
Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');
Route::get('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');

// Protected routes
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ... other protected routes
});
```

---

## 📄 File 1: SSOService.php

Tạo file `app/Services/SSOService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSOService
{
    protected $ssoServer;
    protected $callbackUrl;

    public function __construct()
    {
        $this->ssoServer = config('app.sso_server', env('SSO_SERVER'));
        $this->callbackUrl = config('app.sso_callback_url', env('SSO_CALLBACK_URL'));
    }

    /**
     * Get SSO check URL
     * Redirect user to this URL to check SSO login status
     */
    public function getCheckUrl(): string
    {
        return $this->ssoServer . '/api/sso/verify-session?callback=' . urlencode($this->callbackUrl);
    }

    /**
     * Verify SSO session token and get user info
     * 
     * @param string $sessionToken
     * @return array|null
     */
    public function verifyToken(string $sessionToken): ?array
    {
        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post($this->ssoServer . '/api/sso/verify-session', [
                    'sso_session' => $sessionToken
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success']) {
                    return $data['data'];
                }
            }

            Log::error('SSO verification failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('SSO verification exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get SSO logout URL
     */
    public function getLogoutUrl(): string
    {
        return $this->ssoServer . '/logout?callback=' . urlencode($this->callbackUrl);
    }

    /**
     * Store user info in session
     */
    public function storeUserSession(array $userData): void
    {
        session([
            'sso_user' => [
                'id' => $userData['user_id'],
                'name' => $userData['user_name'],
                'email' => $userData['user_email'],
                'jwt_token' => $userData['jwt_token'] ?? null,
                'login_time' => $userData['login_time'] ?? now()->toIso8601String(),
                'authenticated' => true,
            ]
        ]);
    }

    /**
     * Get user from session
     */
    public function getUser(): ?array
    {
        return session('sso_user');
    }

    /**
     * Check if user is logged in
     */
    public function isAuthenticated(): bool
    {
        $user = $this->getUser();
        return $user && isset($user['authenticated']) && $user['authenticated'];
    }

    /**
     * Clear user session
     */
    public function clearSession(): void
    {
        session()->forget('sso_user');
    }
}
```

---

## 📄 File 2: SSOController.php

Tạo file `app/Http/Controllers/Auth/SSOController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SSOController extends Controller
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Handle SSO callback
     * Called after user logs in at SSO Server
     */
    public function callback(Request $request)
    {
        // Check for SSO session token
        $sessionToken = $request->query('sso_session');
        
        if (!$sessionToken) {
            Log::warning('SSO callback without session token');
            return redirect('/')->with('error', 'SSO authentication failed: No session token');
        }

        // Verify token with SSO Server
        $userData = $this->ssoService->verifyToken($sessionToken);

        if (!$userData) {
            Log::error('SSO token verification failed', [
                'token' => substr($sessionToken, 0, 20) . '...'
            ]);
            return redirect('/')->with('error', 'SSO authentication failed: Invalid token');
        }

        // Store user info in session
        $this->ssoService->storeUserSession($userData);

        Log::info('SSO login successful', [
            'user_id' => $userData['user_id'],
            'user_email' => $userData['user_email']
        ]);

        // Redirect to intended page or dashboard
        return redirect()->intended('/dashboard')->with('success', 'Đăng nhập thành công!');
    }

    /**
     * Handle SSO logout
     * Logout from this app and redirect to SSO Server logout
     */
    public function logout(Request $request)
    {
        $user = $this->ssoService->getUser();
        
        // Clear local session
        $this->ssoService->clearSession();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user) {
            Log::info('SSO logout', [
                'user_id' => $user['id'],
                'user_email' => $user['email']
            ]);
        }

        // Redirect to SSO Server logout
        return redirect($this->ssoService->getLogoutUrl());
    }
}
```

---

## 📄 File 3: SSOAuthenticate Middleware

Tạo file `app/Http/Middleware/SSOAuthenticate.php`:

```php
<?php

namespace App\Http\Middleware;

use App\Services\SSOService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SSOAuthenticate
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is already authenticated in session
        if ($this->ssoService->isAuthenticated()) {
            // User is logged in, continue
            return $next($request);
        }

        // Not authenticated, redirect to SSO Server
        return redirect($this->ssoService->getCheckUrl());
    }
}
```

---

## 📄 File 4: Config (optional)

Tạo file `config/sso.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSO Server URL
    |--------------------------------------------------------------------------
    |
    | The URL of your SSO authentication server
    |
    */
    'server' => env('SSO_SERVER', 'https://auth.balocco-local.info'),

    /*
    |--------------------------------------------------------------------------
    | SSO Callback URL
    |--------------------------------------------------------------------------
    |
    | The URL where SSO server will redirect back after authentication
    |
    */
    'callback_url' => env('SSO_CALLBACK_URL', env('APP_URL') . '/sso/callback'),

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | The session key used to store SSO user data
    |
    */
    'session_key' => 'sso_user',

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout in seconds when communicating with SSO server
    |
    */
    'timeout' => 10,

    /*
    |--------------------------------------------------------------------------
    | Allowed Domains (optional)
    |--------------------------------------------------------------------------
    |
    | List of allowed SSO server domains for security
    |
    */
    'allowed_domains' => [
        'auth.balocco-local.info',
    ],
];
```

Sau đó update `SSOService.php` để dùng config:

```php
public function __construct()
{
    $this->ssoServer = config('sso.server');
    $this->callbackUrl = config('sso.callback_url');
}
```

---

## 🔧 Configuration

### Patent Monitor (.env)

```env
APP_NAME="Patent Monitor"
APP_URL=https://patent-monitor.balocco-local.info

# SSO Configuration
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://patent-monitor.balocco-local.info/sso/callback

# Session
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

### Bookcase (.env)

```env
APP_NAME="Bookcase"
APP_URL=https://bookcase.balocco-local.info

# SSO Configuration
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://bookcase.balocco-local.info/sso/callback

# Session
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

---

## 📝 Routes Setup

### routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SSOController;
use App\Http\Controllers\DashboardController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// SSO routes (không cần middleware)
Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');
Route::get('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');

// Protected routes (cần SSO authentication)
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    
    // Add your protected routes here
});
```

---

## 🎨 Blade Templates

### resources/views/layouts/app.blade.php

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <span class="brand">{{ config('app.name') }}</span>
            
            @if(session('sso_user'))
                <div class="user-menu">
                    <span>Welcome, {{ session('sso_user')['name'] }}</span>
                    <a href="{{ route('sso.logout') }}">Logout</a>
                </div>
            @endif
        </div>
    </nav>

    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
```

### resources/views/dashboard.blade.php

```blade
@extends('layouts.app')

@section('content')
<div class="dashboard">
    <h1>Dashboard</h1>
    
    @if(session('sso_user'))
        <div class="user-info">
            <h2>User Information</h2>
            <p><strong>ID:</strong> {{ session('sso_user')['id'] }}</p>
            <p><strong>Name:</strong> {{ session('sso_user')['name'] }}</p>
            <p><strong>Email:</strong> {{ session('sso_user')['email'] }}</p>
            <p><strong>Login Time:</strong> {{ session('sso_user')['login_time'] }}</p>
        </div>
    @endif

    <div class="content">
        <h2>Welcome to {{ config('app.name') }}</h2>
        <p>You are logged in via SSO!</p>
    </div>
</div>
@endsection
```

---

## 🧪 Testing

### Test Flow:

1. **Clear all sessions:**
```bash
php artisan cache:clear
php artisan session:clear  # if available
```

2. **Access Patent Monitor:**
```
https://patent-monitor.balocco-local.info/dashboard
```

3. **Expected flow:**
```
1. Middleware check → not logged in
2. Redirect to: https://auth.balocco-local.info/api/sso/verify-session?callback=...
3. SSO Server check → not logged in
4. Redirect to: /login
5. Login form → enter credentials
6. After login → redirect to callback with token
7. SSOController verify token → store user in session
8. Redirect to /dashboard
9. ✅ Dashboard shows user info
```

4. **Test auto-login in Bookcase:**
```
https://bookcase.balocco-local.info/dashboard
```

5. **Expected:**
```
1. Middleware check → not logged in
2. Redirect to SSO Server
3. SSO check → ✅ Already logged in! (from Patent Monitor)
4. Redirect to callback with token
5. Verify token → store user
6. ✅ Dashboard shows user info (NO LOGIN REQUIRED!)
```

---

## 🐛 Debugging

### Add logging in middleware:

```php
public function handle(Request $request, Closure $next): Response
{
    Log::info('SSO Middleware Check', [
        'path' => $request->path(),
        'authenticated' => $this->ssoService->isAuthenticated(),
        'session_user' => session('sso_user'),
    ]);

    if ($this->ssoService->isAuthenticated()) {
        return $next($request);
    }

    Log::info('Redirecting to SSO', [
        'check_url' => $this->ssoService->getCheckUrl()
    ]);

    return redirect($this->ssoService->getCheckUrl());
}
```

### View logs:

```bash
tail -f storage/logs/laravel.log
```

---

## 🔐 Security

### Add CSRF exception for SSO callback

Trong `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        '/sso/callback',  // SSO callback không cần CSRF
    ]);
})
```

### Validate callback URL

Trong `SSOService.php`:

```php
public function verifyToken(string $sessionToken): ?array
{
    // Add validation
    $allowedDomains = config('sso.allowed_domains', []);
    $serverHost = parse_url($this->ssoServer, PHP_URL_HOST);
    
    if (!in_array($serverHost, $allowedDomains)) {
        Log::error('SSO server not in allowed domains', [
            'server' => $serverHost
        ]);
        return null;
    }

    // ... rest of code
}
```

---

## ✅ Checklist

### Patent Monitor:
- [ ] Copy `SSOService.php`
- [ ] Copy `SSOController.php`
- [ ] Copy `SSOAuthenticate.php`
- [ ] Create `config/sso.php`
- [ ] Update `.env` với SSO config
- [ ] Register middleware trong `bootstrap/app.php`
- [ ] Add routes trong `routes/web.php`
- [ ] Update blade templates
- [ ] Test SSO flow
- [ ] Test logout

### Bookcase:
- [ ] Same as Patent Monitor
- [ ] Test auto-login từ Patent Monitor

---

## 🎯 Helper Functions

### Add helper trong `app/Helpers/sso_helpers.php`:

```php
<?php

if (!function_exists('sso_user')) {
    /**
     * Get current SSO user
     */
    function sso_user(): ?array
    {
        return session('sso_user');
    }
}

if (!function_exists('sso_authenticated')) {
    /**
     * Check if user is authenticated via SSO
     */
    function sso_authenticated(): bool
    {
        $user = sso_user();
        return $user && isset($user['authenticated']) && $user['authenticated'];
    }
}

if (!function_exists('sso_logout_url')) {
    /**
     * Get SSO logout URL
     */
    function sso_logout_url(): string
    {
        $ssoService = app(\App\Services\SSOService::class);
        return $ssoService->getLogoutUrl();
    }
}
```

Autoload trong `composer.json`:

```json
"autoload": {
    "files": [
        "app/Helpers/sso_helpers.php"
    ]
}
```

Chạy:
```bash
composer dump-autoload
```

Sử dụng:
```blade
@if(sso_authenticated())
    <p>Welcome, {{ sso_user()['name'] }}</p>
    <a href="{{ sso_logout_url() }}">Logout</a>
@endif
```

---

**Updated:** 2025-10-15  
**Version:** 1.0  
**For:** Laravel 11 Clients (Patent Monitor & Bookcase)

