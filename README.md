# 🔐 Laravel Session Sharing SSO

Single Sign-On system dùng **Session Sharing** - Login 1 lần, truy cập nhiều ứng dụng.

---

## 🌐 System

```
Auth Server:  http://auth.balocco-local.info
Client A:     http://patent-monitor.balocco-local.info  (Patent Monitor)
Client B:     http://bookcase.balocco-local.info        (Bookcase)
```

**Login 1 lần → Tất cả apps thấy login! 🚀**

---

## ⚡ Quick Start

### Auth Server (Đã sẵn sàng!)

**1. Config .env:**
```env
DB_DATABASE=sso_shared
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_SECURE_COOKIE=false
```

**2. Migrations:**
```bash
php artisan session:table
php artisan migrate
php artisan config:clear
```

**3. Test:**
```bash
php artisan serve
# Visit: http://auth.balocco-local.info
```

---

### Client A & B Setup (10 phút)

**Follow:** [`SESSION-SHARING-QUICK-SETUP.md`](SESSION-SHARING-QUICK-SETUP.md)

---

## 🎯 Features

- ✅ **Session Sharing** - Login 1 lần, auto-login tất cả apps
- ✅ **User Management** - Danh sách users với online/offline status
- ✅ **Real-time Sync** - Logout sync tự động
- ✅ **Redirect Support** - Login/logout redirect về client
- ✅ **Activity Tracking** - Monitor login/logout
- ✅ **Laravel Native** - Dùng `Auth::check()`, `Auth::user()`

---

## 🔄 How It Works

```
Login ở bất kỳ app → Session saved to database
                   → Cookie shared (.balocco-local.info)
                   → All apps: Auth::check() = true
                   → ✅ Auto logged in!
```

---

## 📚 Documentation

### 🚀 Quick Start:
- **[`START-HERE.md`](START-HERE.md)** - Bắt đầu từ đây
- **[`SESSION-SHARING-QUICK-SETUP.md`](SESSION-SHARING-QUICK-SETUP.md)** - Setup đầy đủ

### 📖 Guides:
- **[`docs/SESSION-SHARING-GUIDE.md`](docs/SESSION-SHARING-GUIDE.md)** - Complete guide
- **[`CLIENT-MIDDLEWARE-EXAMPLE.md`](CLIENT-MIDDLEWARE-EXAMPLE.md)** - Client examples
- **[`TEST-SESSION-SHARING.md`](TEST-SESSION-SHARING.md)** - Testing

### 🔧 Troubleshooting:
- **[`QUICK-FIX-419.md`](QUICK-FIX-419.md)** - Fix 419 error
- **[`FIX-REDIRECT-PARAMETER.md`](FIX-REDIRECT-PARAMETER.md)** - Fix redirect

---

## 🐛 Common Issue

### Lỗi 419 "Page Expired"

**Quick fix:**
```env
SESSION_SECURE_COOKIE=false
```

```bash
php artisan config:clear
```

---

## 📊 Tech Stack

- Laravel 11
- MySQL/PostgreSQL (shared database)
- Session Driver: Database
- Laravel Passport (OAuth - optional)

---

## 🎯 Next Steps

1. **Read:** [`START-HERE.md`](START-HERE.md)
2. **Setup:** [`SESSION-SHARING-QUICK-SETUP.md`](SESSION-SHARING-QUICK-SETUP.md)
3. **Test:** Login flow
4. **Deploy:** Client A & B

**Time:** ~30 minutes total

---

## ✅ Status

- ✅ Auth Server: Ready
- 📦 Client A: Needs setup (10 min)
- 📦 Client B: Needs setup (10 min)

---

## 📞 Support

**Documentation:** See `/docs` folder  
**Issues:** Check `QUICK-FIX-419.md`  
**Examples:** See `CLIENT-MIDDLEWARE-EXAMPLE.md`

---

**Version:** 1.0 - Session Sharing  
**Updated:** 2025-10-15

---

**🚀 Start now: Read [`START-HERE.md`](START-HERE.md)!**
