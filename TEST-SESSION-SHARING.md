# 🧪 Test Session Sharing - Complete Guide

## 📋 Setup trước khi test

### 1. Config .env cho CẢ 3 apps

**Auth Server** (`auth.balocco-local.info`):
```env
APP_NAME="Auth Server"
APP_URL=http://auth.balocco-local.info

DB_DATABASE=sso_shared
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_SAME_SITE=lax
```

**Patent Monitor** (`patent-monitor.balocco-local.info`):
```env
APP_NAME="Patent Monitor"
APP_URL=http://patent-monitor.balocco-local.info

DB_DATABASE=sso_shared  # ← Cùng database!
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_SAME_SITE=lax
```

**Bookcase** (`bookcase.balocco-local.info`):
```env
APP_NAME="Bookcase"
APP_URL=http://bookcase.balocco-local.info

DB_DATABASE=sso_shared  # ← Cùng database!
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_SAME_SITE=lax
```

---

### 2. Run migrations (CẢ 3 apps)

```bash
php artisan session:table
php artisan migrate
php artisan config:clear
php artisan cache:clear
```

---

### 3. Patent Monitor & Bookcase - Middleware

File: `app/Http/Middleware/CheckSharedSession.php`

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
        if (!Auth::check()) {
            // Redirect to Auth Server với redirect parameter
            $authServerUrl = 'http://auth.balocco-local.info/login';
            $redirectUrl = $request->url();
            
            return redirect($authServerUrl . '?redirect=' . urlencode($redirectUrl));
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

### 4. Routes (Patent Monitor & Bookcase)

```php
Route::middleware(['shared.auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
});
```

---

## 🧪 Test Scenarios

### Test 1: Login ở Auth Server

```
1. Clear all cookies (DevTools → Application → Clear storage)
2. Visit: http://auth.balocco-local.info/login
3. Login: admin@example.com / password
4. ✅ Redirect to home
5. Check database:
   SELECT * FROM sso_shared.sessions WHERE user_id IS NOT NULL;
   → Should have 1 row
```

---

### Test 2: Auto-login ở Patent Monitor

```
1. (Đã login ở Auth Server)
2. Visit: http://patent-monitor.balocco-local.info/dashboard
3. Expected flow:
   - Middleware check: Auth::check()
   - Query database: sessions table
   - ✅ Found session! Display dashboard
4. Check user info displayed correctly
```

---

### Test 3: Auto-login ở Bookcase

```
1. (Đã login ở Auth Server)
2. Visit: http://bookcase.balocco-local.info/dashboard
3. ✅ Auto logged in! Dashboard displayed
```

---

### Test 4: Login từ Client A

```
1. Clear all cookies
2. Visit: http://patent-monitor.balocco-local.info/dashboard
3. Expected flow:
   - Middleware: Not logged in
   - Redirect: http://auth.balocco-local.info/login?redirect=http://patent-monitor.balocco-local.info/dashboard
   - Login form shows: "Sau khi login, bạn sẽ được redirect về: http://patent-monitor..."
   - Submit login
   - ✅ Redirect back to: http://patent-monitor.balocco-local.info/dashboard
4. Check dashboard displays user info
```

---

### Test 5: Cross-app check

```
1. (Đã login ở Patent Monitor)
2. Visit: http://bookcase.balocco-local.info/dashboard
3. ✅ Auto logged in! No login required
4. Visit: http://auth.balocco-local.info/
5. ✅ Shows logged in status
```

---

### Test 6: Logout sync

```
1. Logout ở Patent Monitor
2. Visit: http://bookcase.balocco-local.info/dashboard
3. → Redirect to login (not logged in)
4. Visit: http://auth.balocco-local.info/
5. → Shows not logged in
6. ✅ Logout synced across all apps!
```

---

## 🐛 Debug Guide

### Check Session Cookie

**Browser DevTools → Application → Cookies:**

Should see:
```
Name: balocco_session
Value: (long string)
Domain: .balocco-local.info  ← Có dấu chấm!
Path: /
Secure: ☐ (unchecked for HTTP)
HttpOnly: ☑
SameSite: Lax
```

---

### Check Database Sessions

```sql
-- Check sessions table
SELECT * FROM sso_shared.sessions;

-- Check active user sessions
SELECT id, user_id, ip_address, last_activity 
FROM sso_shared.sessions 
WHERE user_id IS NOT NULL 
ORDER BY last_activity DESC;

-- Decode payload (optional)
SELECT id, user_id, 
       FROM_UNIXTIME(last_activity) as last_active_time
FROM sso_shared.sessions 
WHERE user_id = 1;
```

---

### Check Logs

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Look for:
# - "SSO Login - Redirecting back to client"
# - "SSO Login - Invalid redirect URL"
```

---

### Debug Middleware

Add debug trong `CheckSharedSession`:

```php
public function handle($request, Closure $next)
{
    \Log::info('Middleware Check', [
        'app' => config('app.name'),
        'url' => $request->url(),
        'auth_check' => Auth::check(),
        'session_id' => session()->getId(),
        'user_id' => Auth::id(),
    ]);

    if (!Auth::check()) {
        return redirect('http://auth.balocco-local.info/login?redirect=' . urlencode($request->url()));
    }

    return $next($request);
}
```

---

## 📊 Testing Checklist

### Auth Server:
- [ ] Config `.env` với session sharing settings
- [ ] Run `php artisan session:table` + `migrate`
- [ ] Run `php artisan config:clear`
- [ ] Test login works
- [ ] Check sessions table có data
- [ ] Check cookie được set đúng

### Patent Monitor:
- [ ] Config `.env` (same as Auth Server)
- [ ] Run migrations
- [ ] Create `CheckSharedSession` middleware
- [ ] Register middleware
- [ ] Add protected routes
- [ ] Create dashboard view
- [ ] Test: Visit /dashboard → redirect to Auth Server
- [ ] Test: Login → redirect back to /dashboard
- [ ] Test: Dashboard shows user info

### Bookcase:
- [ ] Same as Patent Monitor
- [ ] Test: Auto-login từ Patent Monitor

---

## 🎯 Expected Results

### ✅ Success Indicators:

1. **Cookie shared:**
   - Domain: `.balocco-local.info`
   - Same cookie name across all apps

2. **Database single session:**
   - Only 1 row in sessions table
   - All apps read from same row

3. **Auto-login works:**
   - Login once at any app
   - All other apps see logged in

4. **Logout sync:**
   - Logout at any app
   - All apps see logged out

5. **Redirect works:**
   - Login from Client A
   - Redirects back to Client A after login

---

## 🔍 Common Issues

### Issue: "Session not found"

**Check:**
```bash
# View sessions table
mysql -u root -e "SELECT * FROM sso_shared.sessions WHERE user_id IS NOT NULL;"
```

---

### Issue: "Cookie not shared"

**Check:**
```env
SESSION_DOMAIN=.balocco-local.info  # Must have dot!
SESSION_COOKIE=balocco_session      # Same name!
```

---

### Issue: "Auth::check() returns false"

**Debug:**
```php
// Add to middleware
dd([
    'session_id' => session()->getId(),
    'auth_check' => Auth::check(),
    'auth_user' => Auth::user(),
    'session_data' => session()->all(),
    'cookie' => $_COOKIE['balocco_session'] ?? 'not found',
]);
```

---

## 🎉 Success!

When all tests pass:
- ✅ Login 1 lần → Tất cả apps thấy login
- ✅ Logout 1 lần → Tất cả apps logout
- ✅ Redirect parameter works
- ✅ Real-time sync

**Session Sharing hoạt động hoàn hảo! 🚀**

---

## 📚 Troubleshooting Docs

- **`QUICK-FIX-419.md`** - Fix lỗi 419 Page Expired
- **`FIX-REDIRECT-PARAMETER.md`** - Fix redirect issues
- **`SESSION-SHARING-QUICK-SETUP.md`** - Setup guide
- **`docs/SESSION-SHARING-GUIDE.md`** - Complete guide

---

**Updated:** 2025-10-15

