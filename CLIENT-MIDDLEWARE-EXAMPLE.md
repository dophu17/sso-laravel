# 🔐 Client Middleware Example - Session Sharing SSO

## 📋 Middleware cho Client A & B

Hướng dẫn tạo middleware đầy đủ cho Patent Monitor và Bookcase

---

## 📄 File: CheckSharedSession.php

**Location:** `app/Http/Middleware/CheckSharedSession.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSharedSession
{
    /**
     * Handle an incoming request.
     * 
     * Checks if user is authenticated via shared session
     * If not, redirects to Auth Server with redirect parameter
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via shared session
        if (Auth::check()) {
            return $next($request);
        }

        // Not authenticated, redirect to Auth Server
        $authServerUrl = 'http://auth.balocco-local.info/login';
        $currentUrl = $request->url(); // Current URL của client app
        
        // Build redirect URL với parameter
        return redirect($authServerUrl . '?redirect=' . urlencode($currentUrl));
    }
}
```

---

## 📄 File: LogoutController.php (Optional)

**Location:** `app/Http/Controllers/Auth/LogoutController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle logout request
     * 
     * Logout và redirect về Auth Server để clear session
     * Sau đó Auth Server redirect về lại client
     */
    public function logout(Request $request)
    {
        // Client logout thì redirect về Auth Server logout
        $authServerUrl = 'http://auth.balocco-local.info/logout';
        $currentUrl = $request->url();
        $homeUrl = url('/'); // Homepage của client app
        
        // Redirect to Auth Server logout với redirect parameter
        return redirect($authServerUrl . '?redirect=' . urlencode($homeUrl));
    }
}
```

---

## 🔧 Setup trong Client Apps

### 1. Register Middleware

File: `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register SSO middleware
        $middleware->alias([
            'shared.auth' => \App\Http\Middleware\CheckSharedSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

### 2. Routes

File: `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LogoutController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Logout route (optional - nếu dùng LogoutController)
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// Protected routes - require SSO authentication
Route::middleware(['shared.auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
    
    // Add your other protected routes here
    Route::get('/profile', function () {
        $user = Auth::user();
        return view('profile', compact('user'));
    })->name('profile');
});
```

---

## 🔄 Flow hoàn chỉnh

### Login Flow:

```
User visit: http://patent-monitor.balocco-local.info/dashboard
    ↓
Middleware: Auth::check() = false
    ↓
Redirect: http://auth.balocco-local.info/login?redirect=http://patent-monitor.balocco-local.info/dashboard
    ↓
User login at Auth Server
    ↓
LoginController validates redirect URL
    ↓
Redirect back: http://patent-monitor.balocco-local.info/dashboard
    ↓
✅ Patent Monitor dashboard displayed
```

---

### Logout Flow:

```
User click logout: http://patent-monitor.balocco-local.info/logout
    ↓
LogoutController
    ↓
Redirect: http://auth.balocco-local.info/logout?redirect=http://patent-monitor.balocco-local.info/
    ↓
Auth Server logout (destroys session)
    ↓
Redirect back: http://patent-monitor.balocco-local.info/
    ↓
✅ Patent Monitor homepage (not logged in)
```

---

## 📝 Dashboard View Example

File: `resources/views/dashboard.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }} - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">{{ config('app.name') }}</h1>
                
                <div class="flex items-center gap-4">
                    <span class="text-gray-700">Welcome, {{ $user->name }}</span>
                    <a href="{{ route('logout') }}" class="text-red-600 hover:text-red-800">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
            
            <div class="bg-gray-50 rounded p-4">
                <h3 class="font-semibold mb-2">User Information:</h3>
                <p><strong>ID:</strong> {{ $user->id }}</p>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ $user->role ?? 'member' }}</p>
            </div>

            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded">
                <p class="text-green-800">
                    ✅ <strong>Session Sharing Active!</strong>
                    You're logged in via shared session from Auth Server.
                </p>
            </div>
        </div>
    </main>
</body>
</html>
```

---

## 🧪 Testing

### Test Login Flow:

```
1. Visit: http://patent-monitor.balocco-local.info/dashboard
2. → Redirect: http://auth.balocco-local.info/login?redirect=http://patent-monitor...
3. Login at Auth Server
4. → Redirect back: http://patent-monitor.balocco-local.info/dashboard
5. ✅ Dashboard displayed
```

---

### Test Logout Flow:

```
1. Click logout: http://patent-monitor.balocco-local.info/logout
2. → Redirect: http://auth.balocco-local.info/logout?redirect=http://patent-monitor.balocco-local.info/
3. Auth Server logout (destroy session)
4. → Redirect back: http://patent-monitor.balocco-local.info/
5. ✅ Homepage displayed (not logged in)
6. Visit Bookcase → Not logged in (session destroyed)
```

---

### Test Cross-App:

```
1. Login at Patent Monitor
2. Visit Bookcase → ✅ Auto logged in
3. Logout at Bookcase → Redirect to Auth Server → Redirect back to Bookcase
4. Visit Patent Monitor → Not logged in (session destroyed)
5. ✅ Logout synced across all apps
```

---

## 📊 Complete Example

### Patent Monitor

**Config (.env):**
```env
APP_NAME="Patent Monitor"
APP_URL=http://patent-monitor.balocco-local.info

DB_DATABASE=sso_shared
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

**Middleware:** `CheckSharedSession.php` (code ở trên)

**Logout Controller:** `LogoutController.php` (code ở trên)

**Routes:**
```php
Route::get('/logout', [LogoutController::class, 'logout']);

Route::middleware(['shared.auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard', ['user' => Auth::user()]));
});
```

---

### Bookcase

**Same as Patent Monitor**, chỉ thay config:
```env
APP_NAME="Bookcase"
APP_URL=http://bookcase.balocco-local.info
```

---

## 🎯 Summary

### Login:
```
Client → Auth Server login?redirect=CLIENT_URL
       → Login
       → Redirect back to CLIENT_URL
```

### Logout:
```
Client → Auth Server logout?redirect=CLIENT_URL
       → Logout (destroy session)
       → Redirect back to CLIENT_URL
```

### Session Check:
```php
Auth::check()  // True if session exists in database
```

---

## ✅ Benefits

- ✅ **Symmetric flow** - Login và logout giống nhau
- ✅ **User-friendly** - Redirect về lại client sau logout
- ✅ **Clean UX** - User không bị "lost" sau logout
- ✅ **Logged activity** - Track cả login và logout với redirect URL

---

## 📚 Documentation

- **Setup:** `SESSION-SHARING-QUICK-SETUP.md`
- **Testing:** `TEST-SESSION-SHARING.md`
- **Middleware:** `CLIENT-MIDDLEWARE-EXAMPLE.md` (this file)

---

**Updated:** 2025-10-15  
**Version:** 1.0

