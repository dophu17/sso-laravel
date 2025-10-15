# 🚀 Session Sharing - Quick Setup (5 phút)

## 📋 Tình huống của bạn

3 Laravel apps cần share session:
- **Auth Server**: `auth.balocco-local.info`
- **Client A**: `patent-monitor.balocco-local.info`  
- **Client B**: `bookcase.balocco-local.info`

**Mục tiêu:** Login ở Client A → Client B và Auth Server đều check thấy đã login

---

## ⚡ Solution: Session Sharing qua Database

**Key:** Dùng **CÙNG DATABASE** và **CÙNG SESSION CONFIG**

---

## 🔧 Setup (Làm cho CẢ 3 apps)

### Bước 1: Session table

Chạy trong **MỖI app**:

```bash
php artisan session:table
php artisan migrate
```

---

### Bước 2: Config .env

**QUAN TRỌNG:** Config GIỐNG NHAU cho cả 3 apps!

```env
# Database - PHẢI CÙNG DATABASE!
DB_DATABASE=sso_shared  # ← Cùng tên cho cả 3 apps

# Session Config - GIỐNG NHAU!
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info  # ← CÓ DẤU CHẤM ĐẦU!
SESSION_COOKIE=balocco_session      # ← Cùng tên cookie
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true          # Nếu dùng HTTPS
SESSION_SAME_SITE=lax
```

---

### Bước 3: Clear cache

Chạy trong **MỖI app**:

```bash
php artisan config:clear
php artisan cache:clear
```

---

### Bước 4: Auth Server - Đã OK! ✅

`LoginController.php` đã có logic đúng:

```php
if (Auth::attempt($credentials)) {
    $request->session()->regenerate();  // ← Session tự động shared
    // ...
}
```

**KHÔNG CẦN SỬA GÌ!**

---

### Bước 5: Client A & B - Tạo Middleware

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
            return redirect('https://auth.balocco-local.info/login?redirect=' . urlencode($request->url()));
        }

        return $next($request);
    }
}
```

---

### Bước 6: Register Middleware

File: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'shared.auth' => \App\Http\Middleware\CheckSharedSession::class,
    ]);
})
```

---

### Bước 7: Protected Routes

File: `routes/web.php`

```php
Route::middleware(['shared.auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    });
});
```

---

### Bước 8: Dashboard View

File: `resources/views/dashboard.blade.php`

```blade
<h1>Welcome, {{ Auth::user()->name }}</h1>
<p>Email: {{ Auth::user()->email }}</p>
<p>✅ Logged in via shared session!</p>
```

---

## ✅ Test Flow

### Test 1: Login ở Auth Server

```
1. Visit: https://auth.balocco-local.info/login
2. Login với email/password
3. ✅ Dashboard shows user info
```

**Check database:**
```sql
SELECT * FROM sso_shared.sessions;
-- Có 1 row với user_id của user vừa login
```

---

### Test 2: Auto-login ở Client A

```
1. Visit: https://patent-monitor.balocco-local.info/dashboard
2. ✅ Dashboard shows user info (KHÔNG CẦN LOGIN!)
```

---

### Test 3: Auto-login ở Client B

```
1. Visit: https://bookcase.balocco-local.info/dashboard
2. ✅ Dashboard shows user info (KHÔNG CẦN LOGIN!)
```

---

## 🔄 How it Works

```
1. User login ở Auth Server
   ↓
2. Laravel tạo session, lưu vào database
   Table: sessions
   Row: id, user_id, payload, ...
   ↓
3. Cookie được set với domain=.balocco-local.info
   Name: balocco_session
   Domain: .balocco-local.info (share cho tất cả subdomain)
   ↓
4. User truy cập Client A
   ↓
5. Client A đọc cookie → Query database sessions
   ↓
6. Found session → Auth::check() = true
   ↓
7. ✅ Dashboard shows user info
```

**Tất cả apps đều đọc/ghi vào CÙNG `sessions` table!**

---

## 🐛 Common Issues

### Issue: "Session not shared"

**Check:**
```env
# Phải có dấu chấm đầu!
SESSION_DOMAIN=.balocco-local.info

# Phải cùng tên cookie
SESSION_COOKIE=balocco_session

# Phải cùng database
DB_DATABASE=sso_shared
```

**Verify:**
```sql
-- Check sessions table
SELECT * FROM sso_shared.sessions;

-- Check có user_id không?
SELECT * FROM sso_shared.sessions WHERE user_id IS NOT NULL;
```

---

### Issue: "Auth::check() returns false"

**Debug:**

```php
// Trong middleware, thêm debug
dd([
    'session_id' => session()->getId(),
    'auth_check' => Auth::check(),
    'user' => Auth::user(),
    'session_data' => session()->all(),
]);
```

**Check:**
- Cookie có được gửi không? (Browser DevTools → Network → Headers)
- Database có session không?
- User model có đúng không?

---

## ✅ Checklist

### For Each App:

- [ ] Run `php artisan session:table`
- [ ] Run `php artisan migrate`
- [ ] Update `.env`:
  - [ ] `DB_DATABASE=sso_shared` (cùng database)
  - [ ] `SESSION_DRIVER=database`
  - [ ] `SESSION_DOMAIN=.balocco-local.info` (có dấu chấm)
  - [ ] `SESSION_COOKIE=balocco_session` (cùng tên)
- [ ] Run `php artisan config:clear`

### For Auth Server:
- [ ] LoginController có `Auth::attempt()` ✅ (đã có)
- [ ] Test login works

### For Client A & B:
- [ ] Create `CheckSharedSession` middleware
- [ ] Register middleware
- [ ] Protected routes với `shared.auth`
- [ ] Create dashboard view
- [ ] Test auto-login

---

## 📊 Database Check

```sql
-- Check sessions table structure
DESC sso_shared.sessions;

-- Check active sessions
SELECT id, user_id, last_activity 
FROM sso_shared.sessions 
WHERE user_id IS NOT NULL;

-- Check specific user session
SELECT * 
FROM sso_shared.sessions 
WHERE user_id = 1;
```

---

## 🎯 Summary

### ✅ Đã có sẵn:
- Auth Server với LoginController ✅
- Database migrations ✅

### ❌ Cần làm:
1. Config `.env` cho cả 3 apps (giống nhau)
2. Chạy migrations
3. Tạo middleware cho Client A & B
4. Test!

**Time:** ~5-10 phút

**Result:** Login 1 lần → Tất cả apps thấy login! 🎉

---

## 📖 Full Guide

Xem chi tiết: **`docs/SESSION-SHARING-GUIDE.md`**

---

**Updated:** 2025-10-15

