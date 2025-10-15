# ⚡ Quick Fix - Lỗi 419 Page Expired

## 🎯 Fix ngay (1 phút)

### Nguyên nhân phổ biến nhất:

Bạn đang dùng **HTTP** (không phải HTTPS) nhưng `SESSION_SECURE_COOKIE=true`

---

## ✅ Solution (Chạy ngay):

```bash
# 1. Update .env - Thêm dòng này
echo "" >> .env
echo "# Fix 419 Error" >> .env
echo "SESSION_SECURE_COOKIE=false" >> .env

# 2. Clear cache
php artisan config:clear
php artisan cache:clear

# 3. Test lại
```

**Hoặc edit manual:**

File: `.env`
```env
SESSION_SECURE_COOKIE=false
```

Sau đó:
```bash
php artisan config:clear
```

---

## 🔍 Check thêm

### 1. Check form có @csrf không:

File: `resources/views/auth/login.blade.php`

```blade
<form method="POST" action="{{ route('login') }}">
    @csrf  <!-- ← PHẢI CÓ dòng này! -->
    
    <input type="email" name="email">
    <input type="password" name="password">
    <button type="submit">Login</button>
</form>
```

---

### 2. Config cho Session Sharing:

File: `.env`
```env
# Session
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=false  # ← False cho HTTP
SESSION_DOMAIN=.balocco-local.info  # ← Có dấu chấm
SESSION_COOKIE=balocco_session
SESSION_SAME_SITE=lax

# Database - Cùng database cho cả 3 apps
DB_DATABASE=sso_shared
```

---

### 3. Run migrations:

```bash
php artisan session:table
php artisan migrate
```

---

## 🐛 Debug

### Check config:

```bash
php artisan tinker
```

```php
config('session.secure');  // Should be false for HTTP
config('session.domain');  // Should be .balocco-local.info
config('session.driver');  // Should be database
```

---

### Check logs:

```bash
tail -f storage/logs/laravel.log
```

---

## 📋 Checklist

- [ ] `.env` có `SESSION_SECURE_COOKIE=false`
- [ ] Run `php artisan config:clear`
- [ ] Login form có `@csrf`
- [ ] Test login lại

---

## 🎉 Should Work Now!

Visit: `http://auth.balocco-local.info/login`

Login → Should redirect to dashboard ✅

---

**Read full guide:** `FIX-419-ERROR.md`

