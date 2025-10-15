# 🚀 START HERE - Session Sharing SSO

## 📋 Tình huống

Bạn có 3 Laravel apps cần SSO:
- **Auth Server**: `auth.balocco-local.info`
- **Client A**: `patent-monitor.balocco-local.info`
- **Client B**: `bookcase.balocco-local.info`

**Mục tiêu:** Login 1 lần → Tất cả apps thấy login

---

## ✅ Auth Server - Đã sẵn sàng!

**Status:** ✅ Clean & Ready

**Đã làm:**
- ✅ Xóa tất cả token-based code (~4500+ lines)
- ✅ Simplified controllers
- ✅ Cleaned all views
- ✅ Updated routes
- ✅ Complete documentation

**Cần làm:** Config + test

---

## ⚡ Quick Setup (3 bước)

### 1️⃣ Config .env (CẢ 3 apps)

```env
# Database - CÙNG DATABASE
DB_DATABASE=sso_shared

# Session - GIỐNG NHAU
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info  # ← CÓ DẤU CHẤM!
SESSION_COOKIE=balocco_session
SESSION_SECURE_COOKIE=false  # false cho HTTP
SESSION_SAME_SITE=lax
```

---

### 2️⃣ Run Migrations (CẢ 3 apps)

```bash
php artisan session:table
php artisan migrate
php artisan config:clear
```

---

### 3️⃣ Test Auth Server

```bash
# Visit
http://auth.balocco-local.info/login

# Login với credentials
# Should work! ✅
```

---

## 📚 Documentation

### ⭐ Next Steps:
**[`SESSION-SHARING-QUICK-SETUP.md`](SESSION-SHARING-QUICK-SETUP.md)**
- Complete setup guide
- Client A & B middleware
- Testing guide

### 📖 Other Docs:
- `README-SESSION-SHARING.md` - Main README
- `TEST-SESSION-SHARING.md` - Testing guide
- `QUICK-FIX-419.md` - Fix 419 error
- `ALL-CLEANUP-COMPLETE.md` - Cleanup summary

---

## 🐛 Common Issue: 419 Error

**Quick fix:**
```env
SESSION_SECURE_COOKIE=false
```

```bash
php artisan config:clear
```

**Docs:** `QUICK-FIX-419.md`

---

## 🔄 How It Works

```
Login ở Auth Server
    ↓
Session saved to database
    ↓
Cookie shared (domain=.balocco-local.info)
    ↓
Client A & B: Auth::check() = true
    ↓
✅ Auto logged in!
```

---

## ✅ What's Clean Now

- ✅ No JWT tokens
- ✅ No session tokens
- ✅ No API verification
- ✅ No token management UI
- ✅ Pure session sharing
- ✅ Laravel native only

**4500+ lines removed! 🎉**

---

## 🚀 Next Action

### 1. Fix 419 (nếu có):
```bash
echo "SESSION_SECURE_COOKIE=false" >> .env
php artisan config:clear
```

### 2. Test login:
```
http://auth.balocco-local.info/login
```

### 3. Setup clients:
Read: `SESSION-SHARING-QUICK-SETUP.md`

**Time:** ~30 minutes total

---

**🎯 System is clean and ready! Start with `SESSION-SHARING-QUICK-SETUP.md`**

Login 1 lần → Dùng tất cả apps! 🚀

