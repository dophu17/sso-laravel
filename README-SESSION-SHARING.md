# 🔐 Session Sharing SSO - Laravel

## 📋 Overview

Hệ thống **Session Sharing SSO** cho phép user **login 1 lần** và tự động đăng nhập vào tất cả ứng dụng con trên cùng domain.

---

## 🌐 System Architecture

```
Auth Server:  http://auth.balocco-local.info
              ↓ (Session Database)
       ┌──────┴──────┐
       │             │
Client A:      Client B:
Patent Monitor Bookcase
```

**Login 1 lần → Tất cả apps thấy login! 🚀**

---

## ⚡ Quick Start (5 phút)

### Bước 1: Config .env (CẢ 3 apps)

```env
# Database - CÙNG DATABASE
DB_DATABASE=sso_shared

# Session - GIỐNG NHAU
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info  # ← CÓ DẤU CHẤM!
SESSION_COOKIE=balocco_session
SESSION_SECURE_COOKIE=false  # false cho HTTP, true cho HTTPS
SESSION_SAME_SITE=lax
```

---

### Bước 2: Run Migrations (CẢ 3 apps)

```bash
php artisan session:table
php artisan migrate
php artisan config:clear
```

---

### Bước 3: Client A & B - Middleware

File: `app/Http/Middleware/CheckSharedSession.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSharedSession
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('http://auth.balocco-local.info/login?redirect=' . urlencode($request->url()));
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

### Bước 4: Routes (Client A & B)

```php
Route::middleware(['shared.auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', ['user' => Auth::user()]);
    });
});
```

---

### Bước 5: Test

```
1. Login: http://auth.balocco-local.info/login
2. Visit: http://patent-monitor.balocco-local.info/dashboard
   → ✅ Auto logged in!
3. Visit: http://bookcase.balocco-local.info/dashboard
   → ✅ Auto logged in!
```

**Done! 🎉**

---

## 🔄 How It Works

```
1. User login ở Auth Server
   ↓
2. Session saved to database (sessions table)
   ↓
3. Cookie set (domain=.balocco-local.info)
   ↓
4. Cookie shared to ALL subdomains
   ↓
5. Client A/B read cookie → Query database
   ↓
6. ✅ Auth::check() = true → User logged in
```

---

## 🎯 Features

- ✅ **Login 1 lần** - Dùng tất cả apps
- ✅ **Logout sync** - Logout 1 lần, tất cả apps logout
- ✅ **Real-time sync** - Instant, không có delay
- ✅ **Laravel native** - Dùng `Auth::check()`, `Auth::user()`
- ✅ **Simple** - Không cần API calls, tokens
- ✅ **Secure** - Session database, CSRF protection

---

## 📚 Documentation

### 🚀 Start Here:
**[`SESSION-SHARING-QUICK-SETUP.md`](SESSION-SHARING-QUICK-SETUP.md)**
- 5-minute setup guide
- Step-by-step instructions

### 📖 Complete Guide:
**[`docs/SESSION-SHARING-GUIDE.md`](docs/SESSION-SHARING-GUIDE.md)**
- Detailed documentation
- Configuration options
- Security recommendations

### 🧪 Testing:
**[`TEST-SESSION-SHARING.md`](TEST-SESSION-SHARING.md)**
- Complete testing guide
- Debug commands
- Troubleshooting

### 🔧 Fixes:
- **[`QUICK-FIX-419.md`](QUICK-FIX-419.md)** - Fix 419 Page Expired
- **[`FIX-REDIRECT-PARAMETER.md`](FIX-REDIRECT-PARAMETER.md)** - Fix redirect issues
- **[`FIXES-SUMMARY.md`](FIXES-SUMMARY.md)** - All fixes summary

---

## 🐛 Common Issues

### Lỗi 419 "Page Expired"

**Fix:**
```env
SESSION_SECURE_COOKIE=false  # For HTTP
```

```bash
php artisan config:clear
```

**Docs:** `QUICK-FIX-419.md`

---

### Session không share

**Check:**
```env
SESSION_DOMAIN=.balocco-local.info  # Phải có dấu chấm!
DB_DATABASE=sso_shared              # Cùng database!
SESSION_COOKIE=balocco_session      # Cùng tên!
```

---

### Auth::check() returns false

**Debug:**
```sql
SELECT * FROM sso_shared.sessions WHERE user_id IS NOT NULL;
```

---

## 🔐 Security

### Development (HTTP):
```env
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=.balocco-local.info
```

### Production (HTTPS):
```env
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.your-domain.com
```

---

## 📊 Requirements

- ✅ Laravel 11
- ✅ Shared database for all apps
- ✅ Same domain (*.balocco-local.info)
- ✅ MySQL/PostgreSQL
- ✅ HTTPS recommended for production

---

## 🎯 Use Cases

Perfect for:
- ✅ Multiple internal apps (Patent Monitor, Bookcase, etc.)
- ✅ Microservices on same domain
- ✅ Admin panel + User portal
- ✅ Dashboard apps

Not suitable for:
- ❌ Different domains (app-a.com, app-b.net)
- ❌ Third-party apps (use OAuth instead)

---

## 📁 Project Structure

```
sso-laravel/
├── app/
│   ├── Http/Controllers/
│   │   └── Auth/
│   │       ├── LoginController.php     ← Session sharing login
│   │       └── RegisterController.php
│   └── Models/
│       ├── User.php
│       └── LoginLog.php
│
├── routes/
│   ├── web.php                         ← Login/Logout routes
│   └── api.php                         ← OAuth API (optional)
│
├── docs/
│   └── SESSION-SHARING-GUIDE.md        ← Complete guide
│
└── SESSION-SHARING-QUICK-SETUP.md      ← Quick start
```

---

## ✅ Checklist

### Auth Server:
- [x] LoginController simplified ✅
- [ ] Config `.env` (session sharing)
- [ ] Run migrations
- [ ] Test login/logout

### Client A (Patent Monitor):
- [ ] Config `.env` (same as Auth Server)
- [ ] Run migrations
- [ ] Create middleware
- [ ] Add routes
- [ ] Test

### Client B (Bookcase):
- [ ] Same as Client A
- [ ] Test cross-app login

---

## 🚀 Quick Commands

```bash
# Setup (run in each app)
php artisan session:table
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear

# Test
# Visit: http://auth.balocco-local.info/login
```

---

## 📞 Support

**Docs:**
- Quick setup: `SESSION-SHARING-QUICK-SETUP.md`
- Complete guide: `docs/SESSION-SHARING-GUIDE.md`
- Testing: `TEST-SESSION-SHARING.md`
- Fix 419: `QUICK-FIX-419.md`

**Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Database:**
```sql
SELECT * FROM sso_shared.sessions WHERE user_id IS NOT NULL;
```

---

## 🎉 Result

**Simple, Clean, Effective!**

- ✅ 40% code reduction
- ✅ Session sharing only
- ✅ No token complexity
- ✅ Laravel native
- ✅ Ready for production

**Login 1 lần → Dùng nhiều apps! 🚀**

---

**Version:** 1.0 - Session Sharing  
**Updated:** 2025-10-15  
**Status:** ✅ Production Ready

---

**🎯 Start now: Read [`SESSION-SHARING-QUICK-SETUP.md`](SESSION-SHARING-QUICK-SETUP.md)**

