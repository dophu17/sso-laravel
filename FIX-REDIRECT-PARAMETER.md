# 🔧 Fix Redirect Parameter Issue

## 📋 Vấn đề

Login thành công nhưng không redirect về client app mặc dù có parameter `redirect`

---

## ✅ Đã fix

### 1. LoginController.php ✅

**Đã thêm:**
- ✅ Lấy `redirect` parameter từ request
- ✅ Validate redirect URL (chỉ allow `*.balocco-local.info`)
- ✅ Logging để debug
- ✅ Redirect về client sau login

```php
// Get redirect URL
$redirectUrl = $request->input('redirect');

// Validate (security)
$parsedUrl = parse_url($redirectUrl);
if (isset($parsedUrl['host']) && str_ends_with($parsedUrl['host'], 'balocco-local.info')) {
    return redirect($redirectUrl);  // ← Redirect về client!
}
```

---

### 2. login.blade.php ✅

**Đã thêm:**
- ✅ Hidden input để preserve `redirect` parameter
- ✅ Visual indicator (green box) để user biết sẽ redirect về đâu

```blade
@if(request()->has('redirect'))
    <input type="hidden" name="redirect" value="{{ request()->get('redirect') }}">
    
    <div class="bg-green-50 ...">
        Sau khi login, bạn sẽ được redirect về: {{ request()->get('redirect') }}
    </div>
@endif
```

---

## 🔄 Flow mới

### Client A redirect đến Auth Server:

```
User visit: https://patent-monitor.balocco-local.info/dashboard
    ↓
Middleware: Not logged in
    ↓
Redirect: https://auth.balocco-local.info/login?redirect=https://patent-monitor.balocco-local.info/dashboard
    ↓
Login form shows: "Sau khi login, bạn sẽ được redirect về: https://..."
    ↓
User submit login
    ↓
LoginController:
  - Validate credentials ✅
  - Create session ✅
  - Get redirect parameter ✅
  - Validate redirect URL ✅
  - Redirect về client ✅
    ↓
https://patent-monitor.balocco-local.info/dashboard
    ↓
✅ Client A shows dashboard (Auth::check() = true)
```

---

## 🧪 Testing

### Test 1: Direct Login (no redirect)

```
Visit: https://auth.balocco-local.info/login
Login: email + password
Result: Redirect to home (default)
```

---

### Test 2: Login với redirect parameter

```
Visit: https://auth.balocco-local.info/login?redirect=https://patent-monitor.balocco-local.info/dashboard

Expected:
1. Login form shows green box: "Sau khi login, bạn sẽ được redirect về: https://patent-monitor..."
2. Submit login
3. ✅ Redirect về: https://patent-monitor.balocco-local.info/dashboard
```

---

### Test 3: Client middleware redirect

```
Client A middleware:
redirect('https://auth.balocco-local.info/login?redirect=' . urlencode($request->url()))
```

**Full flow:**
```
1. User visit: https://patent-monitor.balocco-local.info/dashboard
2. Middleware redirect: https://auth.balocco-local.info/login?redirect=https%3A%2F%2Fpatent-monitor...
3. Login form
4. Submit
5. ✅ Redirect về: https://patent-monitor.balocco-local.info/dashboard
```

---

## 🐛 Troubleshooting

### Issue 1: "Không redirect về client"

**Check logs:**
```bash
tail -f storage/logs/laravel.log
```

Look for:
```
SSO Login - Redirecting back to client
```

hoặc:
```
SSO Login - Invalid redirect URL
```

---

### Issue 2: "Invalid redirect URL"

**Nguyên nhân:** URL không pass validation

**Check:**
- Host có end with `balocco-local.info` không?
- URL có đúng format không?

**Debug:**
```php
// Add trong LoginController
\Log::info('Debug redirect', [
    'redirect_url' => $redirectUrl,
    'parsed' => parse_url($redirectUrl),
    'host' => parse_url($redirectUrl, PHP_URL_HOST),
    'ends_with' => str_ends_with(parse_url($redirectUrl, PHP_URL_HOST), 'balocco-local.info')
]);
```

---

### Issue 3: "Redirect parameter not received"

**Check form:**
```blade
<!-- Must have hidden input -->
@if(request()->has('redirect'))
    <input type="hidden" name="redirect" value="{{ request()->get('redirect') }}">
@endif
```

**Debug controller:**
```php
\Log::info('Login request', [
    'has_redirect' => $request->has('redirect'),
    'redirect_value' => $request->input('redirect'),
    'all_inputs' => $request->all(),
]);
```

---

## ✅ Checklist

- [x] LoginController lấy `redirect` parameter ✅
- [x] LoginController validate redirect URL ✅
- [x] LoginController redirect về client ✅
- [x] Login form có hidden input `redirect` ✅
- [x] Login form hiển thị redirect destination ✅
- [x] Logging để debug ✅

---

## 📝 Example Usage

### Client A Middleware:

```php
public function handle($request, Closure $next)
{
    if (!Auth::check()) {
        // Build redirect URL
        $loginUrl = 'https://auth.balocco-local.info/login';
        $redirectUrl = $request->url(); // URL hiện tại của client
        
        return redirect($loginUrl . '?redirect=' . urlencode($redirectUrl));
    }

    return $next($request);
}
```

### Flow:

```
1. User: https://patent-monitor.balocco-local.info/dashboard
2. Middleware redirect:
   https://auth.balocco-local.info/login?redirect=https%3A%2F%2Fpatent-monitor.balocco-local.info%2Fdashboard
3. Login form shows redirect destination
4. User login
5. ✅ Redirect back: https://patent-monitor.balocco-local.info/dashboard
```

---

## 🎯 Test Commands

### Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### View logs:
```bash
tail -f storage/logs/laravel.log
```

### Test URL manually:
```
https://auth.balocco-local.info/login?redirect=https://patent-monitor.balocco-local.info/dashboard
```

---

## 🎉 Should Work Now!

Login với redirect parameter → Redirect về client sau login ✅

---

**Updated:** 2025-10-15

