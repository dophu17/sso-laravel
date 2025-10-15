# 🔐 SSO Setup - Balocco Local Environment

## 📋 System Overview

```
┌─────────────────────────────────────────────────────────┐
│                 SSO Architecture                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌────────────────────────────────────────────┐         │
│  │     Auth Server (SSO)                      │         │
│  │  https://auth.balocco-local.info           │         │
│  │  - Central authentication                  │         │
│  │  - User management                         │         │
│  │  - Session management                      │         │
│  └─────────────┬──────────────────────────────┘         │
│                │                                         │
│                │ SSO Token                               │
│                │                                         │
│       ┌────────┴────────┐                               │
│       │                 │                               │
│  ┌────▼────┐      ┌────▼────┐                          │
│  │Client A │      │Client B │                          │
│  │Patent   │      │Bookcase │                          │
│  │Monitor  │      │         │                          │
│  └─────────┘      └─────────┘                          │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 🌐 Domains

### Auth Server (SSO)
```
Domain: https://auth.balocco-local.info
Role:   Central authentication & user management
Tech:   Laravel 11 + Passport
```

### Client A - Patent Monitor
```
Domain: https://patent-monitor.balocco-local.info
Role:   Patent monitoring application
Auth:   SSO via Auth Server
```

### Client B - Bookcase
```
Domain: https://bookcase.balocco-local.info
Role:   Book management application
Auth:   SSO via Auth Server
```

---

## ✅ Đã implement

### Auth Server:
- ✅ User authentication (login/logout)
- ✅ SSO session management
- ✅ Token generation & verification
- ✅ API endpoints for SSO
- ✅ Login activity logging
- ✅ JWT token support

### API Endpoints:
```
GET  /api/sso/verify-session?callback={URL}  - Check login & redirect
POST /api/sso/verify-session                 - Verify token & get user info
```

---

## 🔄 SSO Flow

### Scenario 1: User chưa login

```
1. User → https://patent-monitor.balocco-local.info
2. Patent Monitor check session → không có
3. Redirect: https://auth.balocco-local.info/api/sso/verify-session?callback=...
4. Auth Server check → chưa login
5. Redirect: /login
6. User nhập thông tin login
7. Auth Server tạo token → redirect về Patent Monitor với token
8. Patent Monitor verify token → lưu user info
9. ✅ Patent Monitor hiển thị: "Welcome John Doe!"
```

### Scenario 2: User đã login ở Client A, truy cập Client B

```
1. User → https://bookcase.balocco-local.info
2. Bookcase check session → không có
3. Redirect: https://auth.balocco-local.info/api/sso/verify-session?callback=...
4. Auth Server check → ✅ Đã login rồi! (từ Patent Monitor)
5. Auth Server tạo token → redirect ngay về Bookcase với token
6. Bookcase verify token → lưu user info
7. ✅ Bookcase hiển thị: "Welcome John Doe!" (KHÔNG CẦN LOGIN LẠI!)
```

**🎉 Result: Login 1 lần → Dùng nhiều apps!**

---

## 📝 Integration Guide

### For Patent Monitor & Bookcase:

**Đọc file:** `docs/SSO-SUBDOMAIN-INTEGRATION.md`

File này bao gồm:
- ✅ Implementation guide chi tiết
- ✅ Code examples (sso-callback.php)
- ✅ Security recommendations
- ✅ Testing guide
- ✅ Troubleshooting
- ✅ Configuration checklist

---

## 🔧 Configuration

### Auth Server (.env)

```env
APP_URL=https://auth.balocco-local.info
APP_ENV=local

# Session config for subdomain sharing
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

# Cache for SSO sessions
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sso_laravel
DB_USERNAME=root
DB_PASSWORD=
```

**Quan trọng:**
- `SESSION_DOMAIN=.balocco-local.info` (có dấu chấm đầu)
- `SESSION_SECURE_COOKIE=true` (cần HTTPS)
- `CACHE_DRIVER=redis` (recommended cho SSO)

---

## 🚀 Quick Start

### 1. Start Auth Server

```bash
cd c:\xampp\htdocs\sso-laravel
php artisan serve --host=auth.balocco-local.info --port=8000
```

Hoặc với Apache/Nginx, configure virtual host:
```apache
<VirtualHost *:443>
    ServerName auth.balocco-local.info
    DocumentRoot "C:/xampp/htdocs/sso-laravel/public"
    
    SSLEngine on
    SSLCertificateFile "path/to/cert.pem"
    SSLCertificateKeyFile "path/to/key.pem"
    
    <Directory "C:/xampp/htdocs/sso-laravel/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 2. Setup Patent Monitor & Bookcase

Trong mỗi client app, implement SSO check:

```php
// index.php hoặc middleware
if (!isset($_SESSION['user'])) {
    $ssoServer = 'https://auth.balocco-local.info';
    $callback = 'https://' . $_SERVER['HTTP_HOST'] . '/sso-callback.php';
    header('Location: ' . $ssoServer . '/api/sso/verify-session?callback=' . urlencode($callback));
    exit;
}
```

### 3. Test Flow

1. Clear all sessions/cookies
2. Mở `https://patent-monitor.balocco-local.info`
3. → Redirect to Auth Server → Login
4. → Redirect về Patent Monitor → ✅ Logged in
5. Mở `https://bookcase.balocco-local.info`
6. → ✅ **Auto-login!** (không cần nhập thông tin lại)

---

## 📊 Database Tables

### `users` - User accounts
```sql
- id
- name
- email
- password
- role
- created_at
- updated_at
```

### `login_logs` - SSO activity tracking
```sql
- id
- user_id
- email
- user_name
- callback_url
- ip_address
- user_agent
- action (login, logout, sso_verify)
- status (success, failed)
- session_token
- login_at
```

### Cache: SSO Sessions
```
Key: sso_session_{token}
Value: {
  user_id: 1,
  user_name: "John Doe",
  user_email: "john@example.com",
  jwt_token: "...",
  login_time: "2025-10-15T10:30:00Z",
  authenticated: true
}
TTL: 5 minutes
```

---

## 🔐 Security

### Implemented:
- ✅ Session token (random 64 chars)
- ✅ Token expiry (5 minutes)
- ✅ JWT token for API access
- ✅ HTTPS required
- ✅ Secure cookies
- ✅ Activity logging

### Recommended:
- [ ] Callback URL whitelist
- [ ] Rate limiting
- [ ] CSRF protection
- [ ] 2FA support
- [ ] Session timeout configuration
- [ ] IP-based restrictions

---

## 🐛 Common Issues

### Issue 1: "Session not shared"

**Check:**
```env
SESSION_DOMAIN=.balocco-local.info  # Có dấu chấm!
```

### Issue 2: "CORS error"

**Fix:** Configure `config/cors.php`:
```php
'allowed_origins_patterns' => [
    '/^https:\/\/.*\.balocco-local\.info$/',
],
```

### Issue 3: "Token expired"

**Reason:** Token có hiệu lực 5 phút

**Fix:** Verify token ngay sau khi nhận được

---

## 📚 Documentation

### Main Docs:
- **`docs/SSO-SUBDOMAIN-INTEGRATION.md`** ⭐ - Integration guide cho Patent Monitor & Bookcase
- **`docs/SSO-SIMPLE-FLOW.md`** - SSO flow documentation
- **`SSO-SETUP.md`** - This file (overview)

### Code:
- **`app/Http/Controllers/Api/SessionController.php`** - SSO logic
- **`routes/api.php`** - API routes
- **`routes/web.php`** - Web routes

---

## 📈 Monitoring

### Check active sessions:
```bash
redis-cli KEYS "sso_session_*"
```

### View SSO logs:
```sql
SELECT * FROM login_logs 
WHERE action = 'sso_verify' 
ORDER BY login_at DESC 
LIMIT 50;
```

### Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

---

## ✅ Deployment Checklist

### Auth Server:
- [ ] HTTPS enabled
- [ ] SSL certificate valid
- [ ] Redis configured
- [ ] Database migrated
- [ ] Passport keys generated
- [ ] Environment variables set
- [ ] Logging configured
- [ ] Backup configured

### Client Apps:
- [ ] SSO integration implemented
- [ ] sso-callback.php created
- [ ] Session management
- [ ] Error handling
- [ ] HTTPS enabled
- [ ] Testing completed

---

## 🎯 Next Steps

1. **Implement SSO in Patent Monitor:**
   - Follow guide: `docs/SSO-SUBDOMAIN-INTEGRATION.md`
   - Test login flow
   - Test auto-login from other apps

2. **Implement SSO in Bookcase:**
   - Same as Patent Monitor
   - Test cross-app SSO

3. **Security hardening:**
   - Add callback URL whitelist
   - Implement rate limiting
   - Add monitoring

4. **Optional enhancements:**
   - Remember me feature
   - 2FA support
   - Role-based access control
   - Session management UI

---

**Status:** ✅ Auth Server Ready  
**Version:** 1.0  
**Updated:** 2025-10-15

---

**🚀 Ready to integrate!**

Follow `docs/SSO-SUBDOMAIN-INTEGRATION.md` để integrate SSO vào Patent Monitor và Bookcase.

