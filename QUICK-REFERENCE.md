# 🚀 SSO Quick Reference

## 📋 System Info

```
Auth Server:  https://auth.balocco-local.info
Client A:     https://patent-monitor.balocco-local.info  (Patent Monitor)
Client B:     https://bookcase.balocco-local.info        (Bookcase)
```

---

## 🔌 API Endpoints

### Check Login & Get Token
```
GET https://auth.balocco-local.info/api/sso/verify-session?callback={URL}

→ If logged in:  Redirect to callback?sso_session=TOKEN
→ If not:        Redirect to /login?callback={URL}
```

### Verify Token
```
POST https://auth.balocco-local.info/api/sso/verify-session
Body: sso_session=TOKEN

→ Returns user info (JSON)
```

---

## 💻 Client Integration (3 steps)

### 1. Check if logged in
```php
if (!isset($_SESSION['user'])) {
    $callback = 'https://your-app.com/sso-callback.php';
    header('Location: https://auth.balocco-local.info/api/sso/verify-session?callback=' . urlencode($callback));
    exit;
}
```

### 2. Handle callback (sso-callback.php)
```php
if (isset($_GET['sso_session'])) {
    $ch = curl_init('https://auth.balocco-local.info/api/sso/verify-session');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['sso_session' => $_GET['sso_session']]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $data = json_decode(curl_exec($ch), true);
    
    if ($data['success']) {
        $_SESSION['user'] = $data['data'];
        header('Location: /');
        exit;
    }
}
```

### 3. Display user
```php
echo "Welcome, " . $_SESSION['user']['user_name'];
```

---

## 🔧 Auth Server Config

### .env
```env
APP_URL=https://auth.balocco-local.info
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CACHE_DRIVER=redis
```

---

## 📚 Full Documentation

- **Integration Guide:** `docs/SSO-SUBDOMAIN-INTEGRATION.md`
- **Setup Overview:** `SSO-SETUP.md`
- **Flow Details:** `docs/SSO-SIMPLE-FLOW.md`

---

## 🐛 Quick Debug

### Check session in Redis:
```bash
redis-cli KEYS "sso_session_*"
```

### View SSO logs:
```sql
SELECT * FROM login_logs WHERE action='sso_verify' ORDER BY login_at DESC LIMIT 10;
```

### Test endpoint:
```bash
curl "https://auth.balocco-local.info/api/sso/verify-session?callback=http://test.com"
```

---

**🎯 Start here:** `docs/SSO-SUBDOMAIN-INTEGRATION.md`

