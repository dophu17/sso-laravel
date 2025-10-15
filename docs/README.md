# 📚 SSO Documentation

## 🎯 Quick Start

**Bắt đầu từ đây:**
- 📖 [`SSO-SUBDOMAIN-INTEGRATION.md`](SSO-SUBDOMAIN-INTEGRATION.md) - **Integration guide cho Patent Monitor & Bookcase**

---

## 📁 Documents

### 1. Integration Guide
**File:** [`SSO-SUBDOMAIN-INTEGRATION.md`](SSO-SUBDOMAIN-INTEGRATION.md)

**Nội dung:**
- ✅ Hướng dẫn integrate SSO cho Patent Monitor
- ✅ Hướng dẫn integrate SSO cho Bookcase
- ✅ Code examples (index.php, sso-callback.php)
- ✅ Configuration guide
- ✅ Security recommendations
- ✅ Testing guide
- ✅ Troubleshooting
- ✅ Checklist

**Dành cho:** Developers integrate SSO vào client apps

---

### 2. Flow Documentation
**File:** [`SSO-SIMPLE-FLOW.md`](SSO-SIMPLE-FLOW.md)

**Nội dung:**
- ✅ SSO flow diagram
- ✅ API endpoints reference
- ✅ Implementation examples
- ✅ Security notes
- ✅ Monitoring guide

**Dành cho:** Hiểu rõ SSO flow hoạt động như thế nào

---

## 📋 Other Documents

### Root Directory:

**[`SSO-SETUP.md`](../SSO-SETUP.md)**
- Overview của toàn bộ hệ thống
- Domain configuration
- Quick start guide
- Deployment checklist

**[`QUICK-REFERENCE.md`](../QUICK-REFERENCE.md)**
- Quick reference card
- API endpoints
- Code snippets
- Debug commands

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────┐
│        Auth Server (SSO)                │
│   https://auth.balocco-local.info       │
│                                         │
│   - User authentication                │
│   - Session management                 │
│   - Token generation                   │
│   - API: /api/sso/verify-session       │
└──────────────┬──────────────────────────┘
               │
               │ SSO Token
               │
       ┌───────┴────────┐
       │                │
┌──────▼─────┐  ┌──────▼─────┐
│  Client A  │  │  Client B  │
│  Patent    │  │  Bookcase  │
│  Monitor   │  │            │
└────────────┘  └────────────┘
```

---

## 🔄 SSO Flow Summary

1. User → Client App (Patent Monitor)
2. Client check session → Không có
3. Redirect → Auth Server
4. Auth Server check login
   - Đã login → Tạo token → Redirect về
   - Chưa login → Login form → Tạo token → Redirect về
5. Client verify token → Lưu user info
6. ✅ Done!

**User truy cập Client B:**
- Auth Server check → ✅ Đã login!
- Tạo token → Redirect ngay
- ✅ Auto-login!

---

## 📊 API Endpoints

### Check Login
```
GET /api/sso/verify-session?callback={URL}
```

### Verify Token
```
POST /api/sso/verify-session
Body: sso_session={TOKEN}
```

---

## 🎓 Learning Path

1. **Start:** Read [`SSO-SETUP.md`](../SSO-SETUP.md) - Hiểu overview
2. **Understand:** Read [`SSO-SIMPLE-FLOW.md`](SSO-SIMPLE-FLOW.md) - Hiểu flow
3. **Implement:** Follow [`SSO-SUBDOMAIN-INTEGRATION.md`](SSO-SUBDOMAIN-INTEGRATION.md) - Integrate vào app
4. **Reference:** Use [`QUICK-REFERENCE.md`](../QUICK-REFERENCE.md) - Quick lookup

---

## 🔧 Configuration Files

```
sso-laravel/
├── .env                          ← Environment config
├── config/
│   ├── app.php                  ← App config
│   ├── session.php              ← Session config
│   └── cors.php                 ← CORS config
├── routes/
│   ├── api.php                  ← API routes
│   └── web.php                  ← Web routes
└── app/Http/Controllers/Api/
    └── SessionController.php    ← SSO logic
```

---

## 🐛 Troubleshooting

**Common issues:**

1. **Session not shared:**
   - Check: `SESSION_DOMAIN=.balocco-local.info` (có dấu chấm)

2. **CORS error:**
   - Check: `config/cors.php` allowed origins

3. **Token expired:**
   - Token có hiệu lực 5 phút
   - Verify ngay sau khi nhận

4. **Redirect loop:**
   - Check callback URL xử lý token đúng
   - Check không redirect lại SSO sau khi có user

**Full troubleshooting:** See [`SSO-SUBDOMAIN-INTEGRATION.md`](SSO-SUBDOMAIN-INTEGRATION.md#troubleshooting)

---

## 📞 Support

**Need help?**

1. Read full integration guide: [`SSO-SUBDOMAIN-INTEGRATION.md`](SSO-SUBDOMAIN-INTEGRATION.md)
2. Check SSO logs: `storage/logs/laravel.log`
3. Check database logs: `login_logs` table
4. Check Redis: `redis-cli KEYS "sso_session_*"`

---

**Updated:** 2025-10-15  
**Version:** 1.0

