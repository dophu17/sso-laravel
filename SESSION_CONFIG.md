# 🍪 Session Configuration Guide

## 📋 **Chuẩn hóa Session Cookie**

Hệ thống SSO đã được chuẩn hóa để chỉ sử dụng **`SESSION_COOKIE`** làm single source of truth cho tên cookie phiên.

## 🔧 **Environment Variables**

### **Session Configuration:**
```env
# Laravel Session (Standard)
SESSION_DRIVER=database
SESSION_DOMAIN=.your-domain.com
SESSION_COOKIE=your_session_name
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax

# SSO Configuration (Custom)
SSO_DOMAIN=your-domain.com
SSO_AUTH_URL=http://auth.your-domain.com
SSO_SESSION_DOMAIN=.your-domain.com
```

## 📁 **Files Using SESSION_COOKIE**

### **1. Laravel Core:**
- ✅ **`config/session.php`** - Laravel session configuration
- ✅ **`app/Http/Controllers/Auth/*`** - Controllers sử dụng session

### **2. Client Integration:**
- ✅ **`storage/sso-client-files/sso.php`** - Client config sử dụng `env('SESSION_COOKIE')`

### **3. Documentation:**
- ✅ **`.env.example`** - Template với `SESSION_COOKIE`
- ✅ **`README.md`** - Hướng dẫn sử dụng `SESSION_COOKIE`

## 🚀 **Usage Examples**

### **Server Side (Laravel):**
```php
// Laravel tự động sử dụng SESSION_COOKIE
config('session.cookie') // Returns: your_session_name

// Trong controller
$sessionId = $request->cookie(config('session.cookie'));
```

### **Client Side (sso.php):**
```php
return [
    'server' => env('SSO_SERVER', 'https://auth.your-domain.com'),
    'session_key' => env('SESSION_COOKIE', 'your_session_name'), // ← Uses SESSION_COOKIE
    'timeout' => 10,
];
```

### **Client App Usage:**
```php
// Client app đọc session cookie
$sessionName = config('sso.session_key'); // your_session_name
$sessionId = $request->cookie($sessionName);
```

## 🔄 **Migration Benefits**

### **Before (Confusing):**
```env
SESSION_COOKIE=your_session_name
SSO_SESSION_NAME=your_session_name  # ← Duplicate!
```

### **After (Clean):**
```env
SESSION_COOKIE=your_session_name  # ← Single source of truth
```

## 🛠️ **Configuration Examples**

### **Local Development:**
```env
SESSION_COOKIE=balocco_session
SESSION_DOMAIN=.balocco-local.info
```

### **Production:**
```env
SESSION_COOKIE=company_session
SESSION_DOMAIN=.yourcompany.com
```

## ✅ **Key Benefits**

- ✅ **Single Source of Truth**: Chỉ có `SESSION_COOKIE`
- ✅ **No Duplication**: Loại bỏ `SSO_SESSION_NAME`
- ✅ **Laravel Standard**: Tuân theo chuẩn Laravel
- ✅ **Easy Maintenance**: Dễ quản lý và cấu hình
- ✅ **Client Compatibility**: Client files tự động sync

## 🔍 **Verification**

### **Check Configuration:**
```bash
php artisan tinker
echo 'Session Cookie: ' . config('session.cookie');
echo 'SSO Session Key: ' . config('sso.session_key');
```

### **Expected Output:**
```
Session Cookie: your_session_name
SSO Session Key: your_session_name
```

---

**Bây giờ hệ thống sử dụng `SESSION_COOKIE` làm single source of truth cho tên session cookie!** 🎯
