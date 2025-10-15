# 🔐 Laravel SSO for Clients - Complete Guide

## 📋 System Overview

### ✅ Auth Server (Đã sẵn sàng)
```
URL: https://auth.balocco-local.info
Status: ✅ Ready
Features:
- User authentication
- SSO session management
- API endpoints
- Token generation
```

### 📦 Client Apps (Cần integrate)
```
1. Patent Monitor: https://patent-monitor.balocco-local.info
2. Bookcase: https://bookcase.balocco-local.info

Tech Stack: Laravel 11
```

---

## 🎯 Mục tiêu

**Login 1 lần ở Auth Server → Tự động login vào Patent Monitor & Bookcase**

---

## 🚀 Quick Start (3 bước chính)

### 1️⃣ Generate SSO Client Files

Chạy trong **Auth Server**:

```bash
php artisan sso:generate-client-files
```

Files được tạo tại: `storage/sso-client-files/`

---

### 2️⃣ Copy & Configure trong Client App

```bash
# Copy files
cp storage/sso-client-files/SSOService.php → app/Services/
cp storage/sso-client-files/SSOController.php → app/Http/Controllers/Auth/
cp storage/sso-client-files/SSOAuthenticate.php → app/Http/Middleware/
cp storage/sso-client-files/sso.php → config/
```

**Update `.env`:**
```env
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://patent-monitor.balocco-local.info/sso/callback
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
```

**Register middleware** (`bootstrap/app.php`):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'sso.auth' => \App\Http\Middleware\SSOAuthenticate::class,
    ]);
})
```

**Add routes** (`routes/web.php`):
```php
Route::get('/sso/callback', [SSOController::class, 'callback']);
Route::get('/sso/logout', [SSOController::class, 'logout']);

Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

---

### 3️⃣ Test

```bash
# Visit protected route
https://patent-monitor.balocco-local.info/dashboard

# Expected:
→ Redirect to Auth Server
→ Login form
→ After login, redirect back
→ ✅ Dashboard shows user info
```

---

## 📚 Documentation

### 📖 Detailed Guides:

1. **[LARAVEL-SSO-QUICK-START.md](LARAVEL-SSO-QUICK-START.md)** ⭐
   - Step-by-step setup
   - Code examples
   - Testing guide
   - **BẮT ĐẦU TỪ ĐÂY!**

2. **[docs/LARAVEL-CLIENT-INTEGRATION.md](docs/LARAVEL-CLIENT-INTEGRATION.md)**
   - Complete integration guide
   - Full code for all files
   - Blade templates
   - Security recommendations

3. **[docs/LARAVEL-SSO-SETUP-COMMANDS.md](docs/LARAVEL-SSO-SETUP-COMMANDS.md)**
   - Command usage
   - File generation details

---

## 🔄 SSO Flow

### First Login (Patent Monitor):
```
1. User → Patent Monitor /dashboard
2. Middleware: Not logged in
3. Redirect → Auth Server /api/sso/verify-session
4. Auth Server: Not logged in
5. Redirect → /login
6. User login
7. Auth Server create token
8. Redirect → Patent Monitor /sso/callback?sso_session=TOKEN
9. SSOController verify token
10. Store user in session
11. ✅ Redirect /dashboard with user info
```

### Auto-Login (Bookcase):
```
1. User → Bookcase /dashboard
2. Middleware: Not logged in
3. Redirect → Auth Server /api/sso/verify-session
4. Auth Server: ✅ Already logged in! (from Patent Monitor)
5. Auth Server create token immediately
6. Redirect → Bookcase /sso/callback?sso_session=TOKEN
7. SSOController verify token
8. Store user in session
9. ✅ Redirect /dashboard with user info
```

**🎉 No login required in second app!**

---

## 📁 Files Generated

Chạy `php artisan sso:generate-client-files` tạo ra:

```
storage/sso-client-files/
├── SSOService.php          ← Service để gọi SSO API
├── SSOController.php       ← Handle SSO callback
├── SSOAuthenticate.php     ← Middleware check authentication
├── sso.php                 ← Config file
├── .env.sso.example        ← Env example
└── README.md               ← Quick guide
```

### SSOService.php
- `getCheckUrl()` - Get URL để check SSO
- `verifyToken()` - Verify session token
- `storeUserSession()` - Lưu user vào session
- `getUser()` - Get user từ session
- `isAuthenticated()` - Check đã login chưa
- `getLogoutUrl()` - Get URL logout

### SSOController.php
- `callback()` - Handle redirect từ Auth Server
- `logout()` - Handle logout

### SSOAuthenticate Middleware
- Check authentication
- Redirect to SSO nếu chưa login

---

## 🔧 Configuration

### Patent Monitor (.env):
```env
APP_NAME="Patent Monitor"
APP_URL=https://patent-monitor.balocco-local.info

SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://patent-monitor.balocco-local.info/sso/callback

SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

### Bookcase (.env):
```env
APP_NAME="Bookcase"
APP_URL=https://bookcase.balocco-local.info

SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://bookcase.balocco-local.info/sso/callback

SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

**Quan trọng:** `SESSION_DOMAIN=.balocco-local.info` (có dấu chấm đầu)

---

## 💻 Code Example

### Protect route với SSO:

```php
// routes/web.php
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = session('sso_user');
        return view('dashboard', compact('user'));
    });
    
    Route::get('/profile', function () {
        $user = session('sso_user');
        return view('profile', compact('user'));
    });
});
```

### Access user info trong controller:

```php
public function index()
{
    $user = session('sso_user');
    
    // $user = [
    //     'id' => 1,
    //     'name' => 'John Doe',
    //     'email' => 'john@example.com',
    //     'jwt_token' => '...',
    //     'login_time' => '...',
    //     'authenticated' => true,
    // ]
    
    return view('dashboard', compact('user'));
}
```

### Access user info trong Blade:

```blade
@if(session('sso_user'))
    <p>Welcome, {{ session('sso_user')['name'] }}</p>
    <p>Email: {{ session('sso_user')['email'] }}</p>
    <a href="{{ route('sso.logout') }}">Logout</a>
@endif
```

---

## ✅ Checklist

### For Each Client (Patent Monitor / Bookcase):

#### Setup:
- [ ] Run `php artisan sso:generate-client-files` in Auth Server
- [ ] Copy files to client app
- [ ] Create directories if needed: `app/Services/`, `app/Http/Controllers/Auth/`
- [ ] Update `.env` with SSO config
- [ ] Register middleware in `bootstrap/app.php`
- [ ] Add routes in `routes/web.php`
- [ ] Create dashboard view
- [ ] Run `php artisan cache:clear`
- [ ] Run `php artisan config:clear`

#### Testing:
- [ ] Visit `/dashboard` → redirects to Auth Server
- [ ] Login at Auth Server
- [ ] Redirects back to client app
- [ ] Dashboard shows user info
- [ ] Logout works
- [ ] Login again works

#### Cross-App SSO:
- [ ] Login at Patent Monitor
- [ ] Visit Bookcase `/dashboard`
- [ ] ✅ Auto-login (no credentials required)

---

## 🐛 Common Issues

### 1. "Session not shared between apps"

**Reason:** `SESSION_DOMAIN` không đúng

**Fix:**
```env
SESSION_DOMAIN=.balocco-local.info  # Phải có dấu chấm đầu!
```

### 2. "Redirect loop"

**Reason:** SSO callback route có middleware `sso.auth`

**Fix:** Đảm bảo routes SSO **KHÔNG** có middleware:
```php
// ❌ Wrong
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/sso/callback', ...);  // Wrong!
});

// ✅ Correct
Route::get('/sso/callback', [SSOController::class, 'callback']);
```

### 3. "Token verification failed"

**Check:**
1. Auth Server có chạy không?
2. URL trong `.env` đúng không?
3. Check logs: `storage/logs/laravel.log`
4. Test API manually:
   ```bash
   curl "https://auth.balocco-local.info/api/sso/verify-session?callback=http://test.com"
   ```

### 4. "Class SSOService not found"

**Fix:**
```bash
composer dump-autoload
php artisan clear-compiled
php artisan cache:clear
```

---

## 📊 Testing Matrix

| Step | Patent Monitor | Bookcase | Expected |
|------|----------------|----------|----------|
| 1. Clear sessions | ✅ | ✅ | Clean start |
| 2. Visit /dashboard | Redirect to Auth | - | Login form |
| 3. Login | Success | - | Redirect back |
| 4. Check /dashboard | ✅ User info | - | Shows profile |
| 5. Visit Bookcase | - | No redirect | Auto login! |
| 6. Check /dashboard | - | ✅ User info | Shows profile |
| 7. Logout from Patent | Logged out | - | Redirect |
| 8. Visit Bookcase | - | Redirect | Need login |

---

## 🔐 Security

### Implemented:
- ✅ Session tokens (random 64 chars)
- ✅ Token expiry (5 minutes)
- ✅ JWT tokens for API access
- ✅ HTTPS support
- ✅ Secure cookies
- ✅ Activity logging

### Recommended for Production:
- [ ] Callback URL whitelist
- [ ] Rate limiting
- [ ] IP-based restrictions
- [ ] 2FA support
- [ ] Session timeout alerts
- [ ] Audit logs

---

## 📈 Monitoring

### Logs to check:

**Auth Server:**
```sql
SELECT * FROM login_logs 
WHERE action = 'sso_verify' 
ORDER BY login_at DESC;
```

**Client Apps:**
```bash
tail -f storage/logs/laravel.log
```

**Redis (if used):**
```bash
redis-cli KEYS "sso_session_*"
```

---

## 🎯 Next Steps After Basic Setup

### 1. Customize UI
- Update layouts
- Add navigation
- Custom dashboard

### 2. Add More Features
- User profile page
- Settings page
- Access control based on roles

### 3. Production Preparation
- HTTPS certificates
- Environment variables
- Monitoring setup
- Backup strategy

---

## 💡 Tips

### Use Dependency Injection:

```php
// In controller
use App\Services\SSOService;

public function __construct(
    protected SSOService $ssoService
) {}

public function index()
{
    if ($this->ssoService->isAuthenticated()) {
        $user = $this->ssoService->getUser();
        return view('dashboard', compact('user'));
    }
}
```

### Create Helper Functions:

```php
// app/Helpers/sso_helpers.php
function sso_user() {
    return app(\App\Services\SSOService::class)->getUser();
}

// Use in views:
{{ sso_user()['name'] }}
```

### Custom Middleware Logic:

```php
// Allow specific routes without SSO
public function handle(Request $request, Closure $next): Response
{
    // Allow public routes
    if ($request->is('/', 'about', 'contact')) {
        return $next($request);
    }

    // Check SSO for other routes
    if ($this->ssoService->isAuthenticated()) {
        return $next($request);
    }

    return redirect($this->ssoService->getCheckUrl());
}
```

---

## 📞 Support

### Documentation:
- **Quick Start:** `LARAVEL-SSO-QUICK-START.md` ⭐
- **Full Guide:** `docs/LARAVEL-CLIENT-INTEGRATION.md`
- **Commands:** `docs/LARAVEL-SSO-SETUP-COMMANDS.md`

### Debugging:
1. Check logs: `storage/logs/laravel.log`
2. Test API manually with curl
3. Check session in Redis/database
4. Enable debug mode temporarily

---

## ✨ Summary

### What you have:
- ✅ Auth Server với SSO API
- ✅ Command để generate client files
- ✅ Complete documentation
- ✅ Working examples

### What you need to do:
1. Generate files
2. Copy to client apps
3. Configure
4. Test

**Time:** 15-20 minutes per client app

**Result:** Login 1 lần, dùng tất cả apps! 🎉

---

**Version:** 1.0  
**Updated:** 2025-10-15  
**For:** Laravel 11 Clients (Patent Monitor & Bookcase)

---

**🚀 Ready to integrate? Start with `LARAVEL-SSO-QUICK-START.md`!**

