# 🔐 Session Sharing Guide - Subdomain SSO

## 📋 Yêu cầu

Bạn có 3 Laravel apps trên cùng domain:
- **Auth Server**: `auth.balocco-local.info`
- **Client A**: `patent-monitor.balocco-local.info`
- **Client B**: `bookcase.balocco-local.info`

**Mục tiêu:** Login ở Client A → Client B và Auth Server đều thấy đã login

---

## 🎯 Solution: Session Sharing

Dùng **shared session** giữa các subdomain qua:
1. **SESSION_DOMAIN=.balocco-local.info** (có dấu chấm)
2. **SESSION_DRIVER=database** (shared database)
3. **Same session cookie** cho tất cả subdomain

---

## ⚙️ Configuration cho TẤT CẢ 3 apps

### 1. Database Session Migration

Chạy trong **TẤT CẢ 3 apps** (Auth, Client A, Client B):

```bash
php artisan session:table
php artisan migrate
```

---

### 2. Config .env (TẤT CẢ 3 apps)

**QUAN TRỌNG:** Cấu hình GIỐNG NHAU cho cả 3 apps:

```env
# App specific
APP_NAME="Auth Server"  # hoặc "Patent Monitor", "Bookcase"
APP_URL=https://auth.balocco-local.info  # hoặc subdomain tương ứng

# Database (PHẢI CÙNG DATABASE)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sso_shared  # ← CÙNG DATABASE CHO CẢ 3 APPS
DB_USERNAME=root
DB_PASSWORD=

# Session Configuration (GIỐNG NHAU CHO CẢ 3 APPS)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_DOMAIN=.balocco-local.info  # ← CÓ DẤU CHẤM ĐẦU!
SESSION_SECURE_COOKIE=true  # Nếu dùng HTTPS
SESSION_SAME_SITE=lax
SESSION_COOKIE=balocco_session  # ← CÙNG TÊN COOKIE
```

---

### 3. Config session.php (TẤT CẢ 3 apps)

File: `config/session.php`

Đảm bảo có:

```php
'driver' => env('SESSION_DRIVER', 'database'),
'domain' => env('SESSION_DOMAIN'),
'cookie' => env('SESSION_COOKIE', 'balocco_session'),
'secure' => env('SESSION_SECURE_COOKIE', false),
'same_site' => env('SESSION_SAME_SITE', 'lax'),
```

---

## 📝 Auth Server (auth.balocco-local.info)

### LoginController (đã có - KHÔNG CẦN SỬA)

File: `app/Http/Controllers/Auth/LoginController.php`

```php
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        // Session automatically shared to all subdomains
        // via SESSION_DOMAIN=.balocco-local.info
        
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
}
```

### Check Login Middleware

Tạo `app/Http/Middleware/CheckAuthenticated.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('https://auth.balocco-local.info/login');
        }

        return $next($request);
    }
}
```

Register trong `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'check.auth' => \App\Http\Middleware\CheckAuthenticated::class,
    ]);
})
```

---

## 📝 Client A & B (patent-monitor, bookcase)

### Middleware Check Session

Tạo `app/Http/Middleware/CheckSharedSession.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSharedSession
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated via shared session
        if (!Auth::check()) {
            // Redirect to Auth Server for login
            return redirect('https://auth.balocco-local.info/login?redirect=' . urlencode($request->url()));
        }

        return $next($request);
    }
}
```

Register trong `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'shared.auth' => \App\Http\Middleware\CheckSharedSession::class,
    ]);
})
```

---

### Routes (Client A & B)

File: `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Protected routes - check shared session
Route::middleware(['shared.auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
    
    // Other protected routes...
});

// Logout
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('https://auth.balocco-local.info/logout');
})->name('logout');
```

---

### Dashboard View (Client A & B)

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
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">{{ config('app.name') }}</h1>
            
            @auth
                <div class="flex items-center gap-4">
                    <span>Welcome, {{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="text-red-600">Logout</a>
                </div>
            @endauth
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
            
            @auth
                <div class="bg-gray-50 rounded p-4">
                    <h3 class="font-semibold mb-2">User Information:</h3>
                    <p><strong>ID:</strong> {{ Auth::user()->id }}</p>
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                </div>

                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded">
                    <p class="text-green-800">
                        ✅ <strong>Shared Session Active!</strong> 
                        You're logged in via shared session from Auth Server.
                    </p>
                </div>
            @endauth
        </div>
    </main>
</body>
</html>
```

---

## 🔄 Flow Hoạt Động

### 1. Login ở Auth Server:

```
User → https://auth.balocco-local.info/login
    ↓
Enter credentials
    ↓
LoginController::login()
    ↓
Auth::attempt() → Success
    ↓
Session saved to database (sessions table)
    ↓
Cookie set with domain=.balocco-local.info
    ↓
✅ Cookie được share cho TẤT CẢ subdomain
```

### 2. Truy cập Client A (patent-monitor):

```
User → https://patent-monitor.balocco-local.info/dashboard
    ↓
Middleware CheckSharedSession
    ↓
Auth::check() → Check session from database
    ↓
✅ User found! (from shared session)
    ↓
Display dashboard with user info
```

### 3. Truy cập Client B (bookcase):

```
User → https://bookcase.balocco-local.info/dashboard
    ↓
Middleware CheckSharedSession
    ↓
Auth::check() → Check session from database
    ↓
✅ User found! (from shared session)
    ↓
Display dashboard with user info
```

**🎉 Tất cả đều dùng CÙNG SESSION từ database!**

---

## 🗄️ Database Structure

### sessions table:

```sql
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Cả 3 apps đều đọc/ghi vào table này!**

---

## ✅ Testing

### Test 1: Login ở Auth Server

```
1. Clear all cookies
2. Visit: https://auth.balocco-local.info/login
3. Login với email/password
4. ✅ Dashboard shows user info
5. Check cookie: Name=balocco_session, Domain=.balocco-local.info
6. Check database: sessions table có 1 row mới
```

### Test 2: Auto-login ở Client A

```
1. (Đã login ở Auth Server)
2. Visit: https://patent-monitor.balocco-local.info/dashboard
3. ✅ Dashboard shows user info (KHÔNG CẦN LOGIN LẠI!)
4. Check database: sessions table vẫn chỉ có 1 row
```

### Test 3: Auto-login ở Client B

```
1. (Đã login ở Auth Server)
2. Visit: https://bookcase.balocco-local.info/dashboard
3. ✅ Dashboard shows user info (KHÔNG CẦN LOGIN LẠI!)
4. Check database: sessions table vẫn chỉ có 1 row
```

### Test 4: Logout

```
1. Logout ở Client A
2. Visit Client B → Chưa login (session đã bị xóa)
3. Visit Auth Server → Chưa login (session đã bị xóa)
```

---

## 🐛 Troubleshooting

### Issue 1: "Session not shared"

**Check:**
```env
SESSION_DOMAIN=.balocco-local.info  # Phải có dấu chấm đầu!
SESSION_COOKIE=balocco_session      # Cùng tên cho cả 3 apps
SESSION_DRIVER=database             # Phải là database, không phải file
```

**Verify:**
```bash
# Check database sessions table
SELECT * FROM sessions;

# Check cookie trong browser
# Domain phải là .balocco-local.info
```

---

### Issue 2: "Database not shared"

**Check:**
```env
# Cả 3 apps phải dùng CÙNG DATABASE
DB_DATABASE=sso_shared  # Same for all 3 apps
```

---

### Issue 3: "Auth::check() returns false"

**Check:**
1. Session table có data không?
2. Cookie có được gửi không? (check Network tab)
3. User model có đúng không?
4. Guards config đúng không?

---

## 🔐 Security Notes

### 1. HTTPS Required

```env
SESSION_SECURE_COOKIE=true  # Cookie chỉ gửi qua HTTPS
```

### 2. CORS (nếu cần)

Nếu có API calls giữa các subdomain, cần config CORS:

```php
// config/cors.php
'allowed_origins_patterns' => [
    '/^https:\/\/.*\.balocco-local\.info$/',
],
```

### 3. CSRF Protection

Laravel tự động handle CSRF với session. Đảm bảo:
- Forms có `@csrf`
- AJAX requests có CSRF token

---

## 📊 Summary

### ✅ Advantages của Session Sharing:
- Đơn giản, dễ implement
- Không cần API calls
- True SSO (1 session cho tất cả apps)
- Laravel native support

### ⚠️ Requirements:
- Cùng parent domain (*.balocco-local.info)
- Shared database
- HTTPS recommended
- Same session config

### 🎯 Result:
**Login 1 lần → Tất cả apps thấy login ngay lập tức!**

---

## ✅ Checklist

### For Each App (Auth Server, Client A, Client B):

- [ ] Run `php artisan session:table`
- [ ] Run `php artisan migrate`
- [ ] Update `.env` với session config
- [ ] Set `SESSION_DOMAIN=.balocco-local.info`
- [ ] Set `SESSION_DRIVER=database`
- [ ] Set `SESSION_COOKIE=balocco_session`
- [ ] Set `DB_DATABASE=sso_shared` (cùng database)
- [ ] Create middleware `CheckSharedSession`
- [ ] Register middleware
- [ ] Update routes với middleware
- [ ] Create dashboard view
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Clear config: `php artisan config:clear`
- [ ] Test login flow

---

**Updated:** 2025-10-15  
**Version:** 1.0

