# 🔐 SSO Laravel - Single Sign-On System

## 📋 Overview

Hệ thống **SSO (Single Sign-On)** cho phép user **login 1 lần** tại Auth Server, sau đó tự động đăng nhập vào tất cả các ứng dụng con mà không cần nhập thông tin lại.

---

## 🌐 System Architecture

```
Auth Server:     https://auth.balocco-local.info
                 ↓
        ┌────────┴────────┐
        │                 │
  Client A:         Client B:
  Patent Monitor    Bookcase
```

**Login 1 lần → Dùng nhiều apps! 🚀**

---

## ⚡ Quick Start

### 🎯 Cho Developers muốn integrate SSO:

**Đọc ngay:** [`docs/SSO-SUBDOMAIN-INTEGRATION.md`](docs/SSO-SUBDOMAIN-INTEGRATION.md)

File này có:
- ✅ Code examples cho Patent Monitor
- ✅ Code examples cho Bookcase
- ✅ Step-by-step implementation
- ✅ Testing guide
- ✅ Troubleshooting

---

## 📚 Documentation

### 🎓 Learning Path:

1. **[`SSO-SETUP.md`](SSO-SETUP.md)** - Tổng quan hệ thống
2. **[`docs/SSO-SIMPLE-FLOW.md`](docs/SSO-SIMPLE-FLOW.md)** - Hiểu SSO flow
3. **[`docs/SSO-SUBDOMAIN-INTEGRATION.md`](docs/SSO-SUBDOMAIN-INTEGRATION.md)** ⭐ - Integrate vào app
4. **[`QUICK-REFERENCE.md`](QUICK-REFERENCE.md)** - Quick lookup

---

## 🔄 How It Works

### Scenario 1: Login lần đầu

```
User → Patent Monitor
    → Check login with Auth Server
    → Not logged in → Login form
    → Login success → Patent Monitor ✅
```

### Scenario 2: Truy cập app khác (đã login)

```
User → Bookcase
    → Check login with Auth Server
    → ✅ Already logged in! (from Patent Monitor)
    → Bookcase ✅ (NO LOGIN REQUIRED!)
```

**🎉 Magic of SSO!**

---

## 🔌 API Endpoints

### Check Login
```http
GET /api/sso/verify-session?callback={URL}
```

**Response:**
- Đã login: `302 Redirect` → `callback?sso_session=TOKEN`
- Chưa login: `302 Redirect` → `/login?callback=...`

---

### Verify Token
```http
POST /api/sso/verify-session
Content-Type: application/x-www-form-urlencoded

sso_session=TOKEN
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "user_name": "John Doe",
    "user_email": "john@example.com",
    "jwt_token": "...",
    "authenticated": true
  }
}
```

---

## 💻 Client Integration (Simple)

```php
// 1. Check login
if (!isset($_SESSION['user'])) {
    $callback = 'https://your-app.com/sso-callback.php';
    header('Location: https://auth.balocco-local.info/api/sso/verify-session?callback=' . urlencode($callback));
    exit;
}

// 2. In sso-callback.php, verify token
$data = /* POST to /api/sso/verify-session */;
$_SESSION['user'] = $data['data'];

// 3. Done!
echo "Welcome, " . $_SESSION['user']['user_name'];
```

**Full code:** [`docs/SSO-SUBDOMAIN-INTEGRATION.md`](docs/SSO-SUBDOMAIN-INTEGRATION.md)

---

## 📁 Project Structure

```
sso-laravel/
│
├── app/Http/Controllers/Api/
│   └── SessionController.php          ← SSO logic
│
├── routes/
│   ├── api.php                        ← API routes
│   └── web.php                        ← Web routes
│
├── docs/
│   ├── README.md                      ← Docs index
│   ├── SSO-SUBDOMAIN-INTEGRATION.md   ← ⭐ Integration guide
│   └── SSO-SIMPLE-FLOW.md             ← Flow documentation
│
├── SSO-SETUP.md                       ← System overview
├── QUICK-REFERENCE.md                 ← Quick reference
└── README-SSO.md                      ← This file
```

---

## 🔧 Configuration

### Auth Server (.env)

```env
APP_URL=https://auth.balocco-local.info
SESSION_DOMAIN=.balocco-local.info     # Có dấu chấm!
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CACHE_DRIVER=redis
```

---

## ✅ Features

- ✅ Single Sign-On (login 1 lần, dùng nhiều apps)
- ✅ Token-based authentication
- ✅ JWT token support
- ✅ Session management
- ✅ Login activity logging
- ✅ Subdomain support
- ✅ HTTPS ready
- ✅ Redis caching

---

## 🎯 Use Cases

### ✅ Perfect for:
- Internal applications
- Multiple web apps in same organization
- Microservices architecture
- Subdomain-based apps

### Example:
- Patent Monitor (patent management)
- Bookcase (book management)
- HR System
- CRM System
- ... tất cả dùng chung 1 Auth Server!

---

## 🚀 Getting Started

### For Developers:

**Step 1:** Đọc tài liệu
```
docs/SSO-SUBDOMAIN-INTEGRATION.md
```

**Step 2:** Implement trong app của bạn
- Copy code examples
- Thay đổi config
- Test

**Step 3:** Deploy
- Enable HTTPS
- Configure session
- Test cross-app SSO

---

## 🐛 Common Issues

### "Session not shared between subdomains"
```env
# Fix: Add dot before domain
SESSION_DOMAIN=.balocco-local.info
```

### "Token expired"
```
# Token có hiệu lực 5 phút
# Solution: Verify token ngay sau khi nhận được
```

### "CORS error"
```php
// config/cors.php
'allowed_origins_patterns' => [
    '/^https:\/\/.*\.balocco-local\.info$/',
],
```

**Full troubleshooting:** [`docs/SSO-SUBDOMAIN-INTEGRATION.md`](docs/SSO-SUBDOMAIN-INTEGRATION.md)

---

## 📊 Database

### Tables:
- `users` - User accounts
- `login_logs` - SSO activity tracking
- `oauth_*` - Passport tables

### Cache (Redis):
- `sso_session_{token}` - SSO sessions (5 min TTL)

---

## 🔐 Security

- ✅ HTTPS required
- ✅ Secure cookies
- ✅ Token expiry (5 minutes)
- ✅ JWT tokens
- ✅ Activity logging
- ✅ IP tracking

**Recommended:**
- [ ] Callback URL whitelist
- [ ] Rate limiting
- [ ] 2FA support

---

## 📞 Need Help?

### Documentation:
- **Integration:** [`docs/SSO-SUBDOMAIN-INTEGRATION.md`](docs/SSO-SUBDOMAIN-INTEGRATION.md) ⭐
- **Flow:** [`docs/SSO-SIMPLE-FLOW.md`](docs/SSO-SIMPLE-FLOW.md)
- **Setup:** [`SSO-SETUP.md`](SSO-SETUP.md)
- **Quick Ref:** [`QUICK-REFERENCE.md`](QUICK-REFERENCE.md)

### Debugging:
- Check logs: `storage/logs/laravel.log`
- Check database: `login_logs` table
- Check Redis: `redis-cli KEYS "sso_session_*"`

---

## 🎉 Success Criteria

After implementation, you should be able to:

1. ✅ Login at Auth Server
2. ✅ Open Patent Monitor → Auto logged in
3. ✅ Open Bookcase → Auto logged in
4. ✅ Logout from one app → All apps logged out

**Login 1 lần, dùng tất cả apps! 🚀**

---

## 📈 Next Steps

### Phase 1: Basic Integration ✅
- [x] Auth Server setup
- [x] API endpoints
- [x] Documentation

### Phase 2: Client Integration (Current)
- [ ] Integrate Patent Monitor
- [ ] Integrate Bookcase
- [ ] Test SSO flow

### Phase 3: Production
- [ ] HTTPS setup
- [ ] Security hardening
- [ ] Monitoring
- [ ] Performance tuning

---

## 🏆 Credits

**Tech Stack:**
- Laravel 11
- Laravel Passport (OAuth 2.0)
- Redis (Session cache)
- MySQL (Database)

**Version:** 1.0  
**Updated:** 2025-10-15

---

## 🎯 Quick Links

| Document | Purpose |
|----------|---------|
| [`docs/SSO-SUBDOMAIN-INTEGRATION.md`](docs/SSO-SUBDOMAIN-INTEGRATION.md) | ⭐ Integration guide |
| [`SSO-SETUP.md`](SSO-SETUP.md) | System overview |
| [`QUICK-REFERENCE.md`](QUICK-REFERENCE.md) | Quick lookup |
| [`docs/README.md`](docs/README.md) | Docs index |

---

**🚀 Start integrating now!**

Read [`docs/SSO-SUBDOMAIN-INTEGRATION.md`](docs/SSO-SUBDOMAIN-INTEGRATION.md) to begin.

**Happy SSO! 🎉**

Login 1 lần → Truy cập nhiều nơi!

