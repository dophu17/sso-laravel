# 🚀 Laravel SSO - Quick Start Guide

## 📋 Tổng quan

Hệ thống SSO cho 3 Laravel apps:
- ✅ **Auth Server**: `auth.balocco-local.info` (đã sẵn sàng)
- 📦 **Patent Monitor**: `patent-monitor.balocco-local.info` (cần integrate)
- 📦 **Bookcase**: `bookcase.balocco-local.info` (cần integrate)

---

## ⚡ Quick Setup cho Patent Monitor & Bookcase

### Bước 1: Generate SSO Client Files

Chạy trong **Auth Server**:

```bash
cd /path/to/auth-server
php artisan sso:generate-client-files
```

**Output:**
```
✅ Files generated successfully!
📁 Location: C:\xampp\htdocs\sso-laravel\storage\sso-client-files
```

---

### Bước 2: Copy files vào Patent Monitor

```bash
cd storage/sso-client-files

# Copy vào Patent Monitor
cp SSOService.php /path/to/patent-monitor/app/Services/
cp SSOController.php /path/to/patent-monitor/app/Http/Controllers/Auth/
cp SSOAuthenticate.php /path/to/patent-monitor/app/Http/Middleware/
cp sso.php /path/to/patent-monitor/config/
```

---

### Bước 3: Config Patent Monitor

#### File: `.env`

Thêm/update các dòng sau:

```env
APP_NAME="Patent Monitor"
APP_URL=https://patent-monitor.balocco-local.info

# SSO Configuration
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://patent-monitor.balocco-local.info/sso/callback

# Session Configuration
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

---

#### File: `bootstrap/app.php`

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
            'sso.auth' => \App\Http\Middleware\SSOAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

#### File: `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SSOController;

// Public route
Route::get('/', function () {
    return view('welcome');
});

// SSO routes
Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');
Route::get('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');

// Protected routes
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Add your other protected routes here
});
```

---

#### File: `resources/views/dashboard.blade.php`

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
                
                @if(session('sso_user'))
                    <div class="flex items-center gap-4">
                        <span>Welcome, {{ session('sso_user')['name'] }}</span>
                        <a href="{{ route('sso.logout') }}" class="text-red-600">Logout</a>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('sso_user'))
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
                
                <div class="bg-gray-50 rounded p-4">
                    <h3 class="font-semibold mb-2">User Information:</h3>
                    <p><strong>ID:</strong> {{ session('sso_user')['id'] }}</p>
                    <p><strong>Name:</strong> {{ session('sso_user')['name'] }}</p>
                    <p><strong>Email:</strong> {{ session('sso_user')['email'] }}</p>
                    <p><strong>Login Time:</strong> {{ session('sso_user')['login_time'] }}</p>
                </div>

                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded">
                    <p class="text-green-800">
                        ✅ <strong>SSO Active!</strong> You're logged in via SSO Server.
                    </p>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
```

---

### Bước 4: Test Patent Monitor

```bash
cd /path/to/patent-monitor

# Clear cache
php artisan cache:clear
php artisan config:clear

# Start server (if needed)
php artisan serve --host=patent-monitor.balocco-local.info --port=8001
```

**Test:**
1. Mở browser: `https://patent-monitor.balocco-local.info/dashboard`
2. → Redirect to Auth Server login
3. → Login với email/password
4. → Redirect về Patent Monitor
5. ✅ Dashboard hiển thị user info

---

### Bước 5: Repeat cho Bookcase

Làm y hệt như Patent Monitor, chỉ thay đổi:

```env
APP_NAME="Bookcase"
APP_URL=https://bookcase.balocco-local.info
SSO_CALLBACK_URL=https://bookcase.balocco-local.info/sso/callback
```

---

### Bước 6: Test SSO Cross-Apps

1. ✅ **Login ở Patent Monitor**
   - Visit: `https://patent-monitor.balocco-local.info/dashboard`
   - Login với credentials
   - ✅ Dashboard shows user info

2. ✅ **Auto-login ở Bookcase**
   - Visit: `https://bookcase.balocco-local.info/dashboard`
   - ✅ **NO LOGIN REQUIRED!** Dashboard shows user info automatically

**🎉 Success! SSO hoạt động!**

---

## 🔄 Flow Summary

```
User → Patent Monitor /dashboard
    ↓
Middleware check → Not logged in
    ↓
Redirect: auth.balocco-local.info/api/sso/verify-session?callback=...
    ↓
Auth Server check → Not logged in
    ↓
Redirect: /login
    ↓
User login
    ↓
Auth Server tạo token
    ↓
Redirect: patent-monitor.balocco-local.info/sso/callback?sso_session=TOKEN
    ↓
SSOController verify token
    ↓
Store user in session
    ↓
✅ Redirect to /dashboard
```

**Second app (Bookcase):**
```
User → Bookcase /dashboard
    ↓
Middleware check → Not logged in
    ↓
Redirect: auth.balocco-local.info/api/sso/verify-session?callback=...
    ↓
Auth Server check → ✅ Already logged in!
    ↓
Auth Server tạo token ngay
    ↓
Redirect: bookcase.balocco-local.info/sso/callback?sso_session=TOKEN
    ↓
✅ Auto logged in!
```

---

## 📁 File Structure

### Patent Monitor / Bookcase:

```
app/
├── Services/
│   └── SSOService.php              ← SSO service
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   │       └── SSOController.php   ← Handle SSO callback
│   └── Middleware/
│       └── SSOAuthenticate.php     ← Check SSO authentication
config/
└── sso.php                         ← SSO config
```

---

## 🐛 Troubleshooting

### Issue: "Session not shared"

**Fix .env:**
```env
SESSION_DOMAIN=.balocco-local.info  # Có dấu chấm đầu!
```

### Issue: "Redirect loop"

**Check:**
- SSO callback route không có middleware `sso.auth`
- Routes đúng format

### Issue: "Token verification failed"

**Check:**
- Auth Server có chạy không
- URL trong `.env` đúng không
- Check logs: `storage/logs/laravel.log`

---

## ✅ Checklist

### Patent Monitor:
- [ ] Copy SSO files
- [ ] Update `.env`
- [ ] Register middleware in `bootstrap/app.php`
- [ ] Add routes in `routes/web.php`
- [ ] Create dashboard view
- [ ] Clear cache
- [ ] Test login flow
- [ ] Test dashboard shows user info

### Bookcase:
- [ ] Lày hệt Patent Monitor
- [ ] Test auto-login từ Patent Monitor

---

## 📚 Documentation

### Main Docs:
- **`docs/LARAVEL-CLIENT-INTEGRATION.md`** - Full integration guide
- **`docs/LARAVEL-SSO-SETUP-COMMANDS.md`** - Command usage
- **`README-SSO.md`** - System overview

### Quick Reference:
- **`QUICK-REFERENCE.md`** - API endpoints & snippets

---

## 🎯 Next Steps

### After basic setup works:

1. **Customize views:**
   - Add your own layouts
   - Customize dashboard
   - Add navigation

2. **Add protected routes:**
   - Wrap routes với `sso.auth` middleware
   - Access user info: `session('sso_user')`

3. **Production:**
   - Enable HTTPS
   - Update session config
   - Add monitoring

---

## 💡 Helper Functions (Optional)

Create `app/Helpers/sso_helpers.php`:

```php
<?php

function sso_user(): ?array {
    return session('sso_user');
}

function sso_authenticated(): bool {
    $user = sso_user();
    return $user && ($user['authenticated'] ?? false);
}
```

Autoload in `composer.json`:

```json
"autoload": {
    "files": ["app/Helpers/sso_helpers.php"]
}
```

Then run:
```bash
composer dump-autoload
```

Use in views:
```blade
@if(sso_authenticated())
    <p>Welcome, {{ sso_user()['name'] }}</p>
@endif
```

---

## 🚀 Summary

### What you need to do:

1. ✅ Run: `php artisan sso:generate-client-files` (in Auth Server)
2. ✅ Copy files to Patent Monitor & Bookcase
3. ✅ Update `.env` in each client
4. ✅ Register middleware
5. ✅ Add routes
6. ✅ Test!

**Time:** ~15 minutes per client app

**Result:** Login 1 lần, dùng tất cả apps! 🎉

---

**Updated:** 2025-10-15  
**Version:** 1.0

