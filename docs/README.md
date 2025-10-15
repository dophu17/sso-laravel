# 📚 Session Sharing SSO - Documentation

## 🎯 Overview

**Session Sharing SSO** cho phép user **login 1 lần** tại Auth Server, sau đó tự động đăng nhập vào tất cả apps trên cùng domain.

---

## 📖 Documents

### 1. Session Sharing Guide
**File:** [`SESSION-SHARING-GUIDE.md`](SESSION-SHARING-GUIDE.md)

**Nội dung:**
- ✅ Complete session sharing guide
- ✅ Configuration chi tiết
- ✅ Implementation examples
- ✅ Security recommendations
- ✅ Testing guide
- ✅ Troubleshooting

**Dành cho:** Developers muốn hiểu chi tiết session sharing

---

## 📋 Root Documents

### Quick Start:
**[`SESSION-SHARING-QUICK-SETUP.md`](../SESSION-SHARING-QUICK-SETUP.md)** ⭐
- 5-minute setup guide
- Step-by-step cho cả 3 apps
- **BẮT ĐẦU TỪ ĐÂY!**

### Testing:
**[`TEST-SESSION-SHARING.md`](../TEST-SESSION-SHARING.md)**
- Complete testing guide
- Debug commands
- Success criteria

### Fixes:
- **[`QUICK-FIX-419.md`](../QUICK-FIX-419.md)** - Fix 419 error
- **[`FIX-REDIRECT-PARAMETER.md`](../FIX-REDIRECT-PARAMETER.md)** - Fix redirect
- **[`FIXES-SUMMARY.md`](../FIXES-SUMMARY.md)** - All fixes

### Info:
- **[`LOGINCONTROLLER-UPDATED.md`](../LOGINCONTROLLER-UPDATED.md)** - LoginController changes
- **[`CLEANUP-SUMMARY.md`](../CLEANUP-SUMMARY.md)** - Cleanup summary
- **[`README-SESSION-SHARING.md`](../README-SESSION-SHARING.md)** - Main README

---

## 🏗️ Architecture

```
┌──────────────────────────────────────────┐
│       Auth Server (SSO)                  │
│    auth.balocco-local.info               │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │  LoginController                   │ │
│  │  - Auth::attempt()                 │ │
│  │  - Session sharing via database    │ │
│  └────────────────────────────────────┘ │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │  Database: sessions table          │ │
│  │  - Shared across all subdomains    │ │
│  └────────────────────────────────────┘ │
└──────────────┬───────────────────────────┘
               │
        Session Cookie
     (domain=.balocco-local.info)
               │
      ┌────────┴────────┐
      │                 │
┌─────▼──────┐    ┌────▼──────┐
│  Client A  │    │  Client B │
│  Patent    │    │  Bookcase │
│  Monitor   │    │           │
│            │    │           │
│ Auth::check│    │ Auth::check│
└────────────┘    └───────────┘
```

---

## 🔄 Session Sharing Flow

```
1. User login ở Auth Server
   ↓
2. Session saved to database
   ↓
3. Cookie set (domain=.balocco-local.info)
   ↓
4. All subdomains share same cookie
   ↓
5. Client A/B: Auth::check() → Query database
   ↓
6. ✅ User logged in!
```

---

## 🎓 Learning Path

1. **Start:** [`SESSION-SHARING-QUICK-SETUP.md`](../SESSION-SHARING-QUICK-SETUP.md) - Quick setup
2. **Understand:** [`SESSION-SHARING-GUIDE.md`](SESSION-SHARING-GUIDE.md) - Deep dive
3. **Test:** [`TEST-SESSION-SHARING.md`](../TEST-SESSION-SHARING.md) - Testing
4. **Troubleshoot:** [`QUICK-FIX-419.md`](../QUICK-FIX-419.md) - Common issues

---

## 📞 Need Help?

### Common Issues:
- **419 Error:** Read [`QUICK-FIX-419.md`](../QUICK-FIX-419.md)
- **Redirect not working:** Read [`FIX-REDIRECT-PARAMETER.md`](../FIX-REDIRECT-PARAMETER.md)
- **Session not shared:** Read [`SESSION-SHARING-GUIDE.md`](SESSION-SHARING-GUIDE.md)

### Debug:
```bash
# Logs
tail -f storage/logs/laravel.log

# Database
SELECT * FROM sso_shared.sessions WHERE user_id IS NOT NULL;

# Config
php artisan tinker
config('session.domain');
```

---

**Updated:** 2025-10-15  
**Version:** 1.0 - Session Sharing Only

