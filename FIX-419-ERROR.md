# 🔧 Fix Lỗi 419 "Page Expired"

## 📋 Vấn đề

Sau khi submit login form, báo lỗi:
```
419 | Page Expired
The page has expired due to inactivity.
Please refresh and try again.
```

---

## 🎯 Nguyên nhân

Lỗi 419 trong Laravel thường do:
1. ❌ **CSRF token expired** hoặc không match
2. ❌ **Session configuration** không đúng
3. ❌ **SESSION_SECURE_COOKIE=true** nhưng dùng HTTP (không phải HTTPS)
4. ❌ **SESSION_DOMAIN** không đúng format
5. ❌ **Cache config** chưa clear

---

## ✅ Solutions (Thử theo thứ tự)

### Solution 1: Check SESSION_SECURE_COOKIE

**Vấn đề phổ biến nhất!**

Nếu bạn đang dùng **HTTP** (không phải HTTPS), cần set:

```env
# .env
SESSION_SECURE_COOKIE=false  # ← Đổi thành false nếu dùng HTTP
```

**Hoặc** nếu dùng HTTPS:
```env
SESSION_SECURE_COOKIE=true
```

---

### Solution 2: Check SESSION_DOMAIN

```env
# .env

# Nếu development (localhost hoặc local domain)
SESSION_DOMAIN=.balocco-local.info  # ← Có dấu chấm đầu!

# Hoặc để trống nếu testing single domain
SESSION_DOMAIN=
```

**Quan trọng:** 
- Có dấu chấm đầu (`.balocco-local.info`) cho subdomain sharing
- Hoặc để trống (`SESSION_DOMAIN=`) nếu testing single app

---

### Solution 3: Clear All Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

### Solution 4: Check Session Driver

```env
# .env
SESSION_DRIVER=database  # ← Phải là database cho session sharing

# Hoặc file cho testing đơn giản
SESSION_DRIVER=file
```

**Nếu dùng database:**
```bash
php artisan session:table
php artisan migrate
```

---

### Solution 5: Check Login Form có @csrf

File: `resources/views/auth/login.blade.php`

```blade
<form method="POST" action="{{ route('login') }}">
    @csrf  <!-- ← Phải có dòng này! -->
    
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
```

---

### Solution 6: Disable CSRF cho route (Temporary - Chỉ để test)

**KHÔNG KHUYẾN KHÍCH - CHỈ ĐỂ DEBUG!**

File: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    // Temporary: Disable CSRF for login route to test
    $middleware->validateCsrfTokens(except: [
        '/login',  // ← Chỉ để test, sau đó remove!
    ]);
})
```

**Sau khi test xong, phải REMOVE dòng này!**

---

## 🔧 Recommended Configuration cho Development

### .env (Development - HTTP local):

```env
APP_ENV=local
APP_DEBUG=true

# Session Config
SESSION_DRIVER=file  # ← Đơn giản cho testing
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false  # ← False vì dùng HTTP
SESSION_SAME_SITE=lax
SESSION_DOMAIN=  # ← Để trống cho single app testing

# Database (nếu dùng session database)
DB_DATABASE=sso_laravel
```

---

## 🔧 Recommended Configuration cho Session Sharing

### .env (Session Sharing - Cả 3 apps):

```env
APP_ENV=local
APP_DEBUG=true

# Session Config
SESSION_DRIVER=database  # ← Database cho sharing
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false  # ← False nếu HTTP, true nếu HTTPS
SESSION_SAME_SITE=lax
SESSION_DOMAIN=.balocco-local.info  # ← Có dấu chấm cho subdomain

# Database - PHẢI CÙNG DATABASE
DB_DATABASE=sso_shared
SESSION_COOKIE=balocco_session
```

---

## 🐛 Debug Steps

### Step 1: Check Session Config

```bash
php artisan tinker
```

Trong tinker:
```php
config('session.driver');      // Should be 'database' or 'file'
config('session.domain');       // Check domain
config('session.secure');       // Should be false for HTTP
config('session.same_site');    // Should be 'lax'
```

---

### Step 2: Check Cookie trong Browser

1. Mở DevTools → Application/Storage → Cookies
2. Check cookie name: `laravel_session` hoặc `balocco_session`
3. Check Domain: 
   - Nếu single app: `auth.balocco-local.info`
   - Nếu session sharing: `.balocco-local.info`
4. Check Secure flag:
   - HTTP → Secure = false ✅
   - HTTPS → Secure = true ✅

---

### Step 3: Test Form Submit

**Add debug vào LoginController:**

```php
public function login(Request $request)
{
    // Debug
    \Log::info('Login attempt', [
        'email' => $request->email,
        'csrf_token' => $request->header('X-CSRF-TOKEN'),
        'session_id' => $request->session()->getId(),
    ]);
    
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    
    // ... rest of code
}
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

---

## ✅ Quick Fix (Most Common)

**Thử cái này trước:**

```bash
# 1. Update .env
echo "SESSION_SECURE_COOKIE=false" >> .env

# 2. Clear caches
php artisan config:clear
php artisan cache:clear

# 3. Test lại
# Visit: http://auth.balocco-local.info/login
```

---

## 📊 Checklist

### For Development (HTTP):
- [ ] `SESSION_SECURE_COOKIE=false` ✅
- [ ] `SESSION_DRIVER=file` (hoặc `database`)
- [ ] `SESSION_DOMAIN=` (empty cho single app)
- [ ] Run `php artisan config:clear`
- [ ] Form có `@csrf` directive
- [ ] Test login again

### For Session Sharing (HTTP):
- [ ] `SESSION_SECURE_COOKIE=false` ✅
- [ ] `SESSION_DRIVER=database`
- [ ] `SESSION_DOMAIN=.balocco-local.info` (có dấu chấm)
- [ ] `DB_DATABASE=sso_shared` (cùng database)
- [ ] Run `php artisan session:table` + `migrate`
- [ ] Run `php artisan config:clear`
- [ ] Test login again

### For Production (HTTPS):
- [ ] `SESSION_SECURE_COOKIE=true` ✅
- [ ] `SESSION_DRIVER=database`
- [ ] `SESSION_DOMAIN=.your-domain.com`
- [ ] HTTPS enabled
- [ ] SSL certificate valid

---

## 🔍 Advanced Debug

### Check Session Files (nếu dùng SESSION_DRIVER=file):

```bash
# List session files
ls -la storage/framework/sessions/

# Check last modified
ls -lt storage/framework/sessions/ | head -5
```

### Check Session Table (nếu dùng SESSION_DRIVER=database):

```sql
-- Check sessions table exists
SHOW TABLES LIKE 'sessions';

-- Check structure
DESC sessions;

-- Check active sessions
SELECT id, user_id, last_activity FROM sessions ORDER BY last_activity DESC LIMIT 5;
```

---

## 💡 Common Mistakes

### ❌ Wrong:
```env
SESSION_SECURE_COOKIE=true  # ← But using HTTP
SESSION_DOMAIN=balocco-local.info  # ← Missing dot
```

### ✅ Correct:
```env
SESSION_SECURE_COOKIE=false  # ← For HTTP
SESSION_DOMAIN=.balocco-local.info  # ← Has dot
```

---

## 🎯 Summary

### Most Common Solution:

```env
# .env - Add/Update this line
SESSION_SECURE_COOKIE=false
```

Then:
```bash
php artisan config:clear
```

**Try login again! ✅**

---

## 📞 Still Not Working?

### Check Laravel Logs:
```bash
tail -f storage/logs/laravel.log
```

### Check PHP Errors:
```bash
tail -f /path/to/php_error.log
```

### Check Session Permission:
```bash
# For file driver
chmod -R 775 storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions
```

---

**Updated:** 2025-10-15

