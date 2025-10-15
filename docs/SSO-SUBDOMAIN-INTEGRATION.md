# 🔐 SSO Integration Guide - Subdomain Setup

## 📋 Thông tin hệ thống

### SSO Server (Auth)
```
URL: https://auth.balocco-local.info
Role: Central authentication server
```

### Client Applications
```
Client A: https://patent-monitor.balocco-local.info
Client B: https://bookcase.balocco-local.info
```

**Goal:** Login 1 lần ở Auth Server → Tự động login vào Patent Monitor và Bookcase

---

## 🔄 SSO Flow

```
User → Patent Monitor
         ↓
    Check session local → Không có
         ↓
    Redirect: https://auth.balocco-local.info/api/sso/verify-session?callback=...
         ↓
    ┌─────────────────┐
    │  Auth Server    │
    │  Check login?   │
    └─────────────────┘
         ↓
    ┌────────────┬────────────┐
    │ Đã login   │ Chưa login │
    └────────────┴────────────┘
         ↓              ↓
    Tạo token      Redirect /login
         ↓              ↓
    Redirect       Login form
    callback           ↓
    + token       Submit login
         ↓              ↓
         └──────────────┘
                ↓
    https://patent-monitor.balocco-local.info/sso-callback?sso_session=TOKEN
                ↓
    Patent Monitor verify token → Lấy user info
                ↓
    ✅ DONE! Patent Monitor logged in
    
    
User → Bookcase (lần 2)
         ↓
    Redirect: https://auth.balocco-local.info/api/sso/verify-session?callback=...
         ↓
    ✅ Đã login rồi! (từ Patent Monitor)
         ↓
    Tạo token → Redirect ngay
         ↓
    ✅ DONE! Bookcase auto-login (không cần nhập thông tin lại!)
```

---

## 🛠️ Implementation Guide

### 1. Patent Monitor (Client A)

#### File: `index.php` hoặc `middleware/SSOAuth.php`

```php
<?php
session_start();

// Configuration
$config = [
    'sso_server' => 'https://auth.balocco-local.info',
    'callback_url' => 'https://patent-monitor.balocco-local.info/sso-callback',
    'app_name' => 'Patent Monitor',
];

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    session_destroy();
    
    // Optional: Logout from SSO Server
    header('Location: ' . $config['sso_server'] . '/logout?callback=' . urlencode($config['callback_url']));
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    // Not logged in → Redirect to SSO Server
    $ssoCheckUrl = $config['sso_server'] . '/api/sso/verify-session?callback=' . urlencode($config['callback_url']);
    header('Location: ' . $ssoCheckUrl);
    exit;
}

// User is logged in → Continue with app
$user = $_SESSION['user'];
?>
```

---

#### File: `sso-callback.php`

```php
<?php
session_start();

// Configuration
$config = [
    'sso_server' => 'https://auth.balocco-local.info',
    'home_url' => 'https://patent-monitor.balocco-local.info',
];

// Check if we received SSO session token
if (!isset($_GET['sso_session'])) {
    die('No SSO session token provided');
}

$sessionToken = $_GET['sso_session'];

// Verify token with SSO Server
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['sso_server'] . '/api/sso/verify-session');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'sso_session' => $sessionToken
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Enable SSL verification in production

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    if (isset($data['success']) && $data['success']) {
        // Store user info in session
        $_SESSION['user'] = [
            'id' => $data['data']['user_id'],
            'name' => $data['data']['user_name'],
            'email' => $data['data']['user_email'],
            'jwt_token' => $data['data']['jwt_token'],
            'login_time' => $data['data']['login_time'],
        ];
        
        // Optional: Log SSO login
        error_log("SSO Login: User {$data['data']['user_id']} logged in to Patent Monitor");
        
        // Redirect to home page
        header('Location: ' . $config['home_url'] . '?sso_login=success');
        exit;
    }
}

// Verification failed
error_log("SSO Verification Failed: HTTP Code $httpCode, Response: $response");
die('SSO verification failed. Please try again.');
?>
```

---

### 2. Bookcase (Client B)

**Tương tự như Patent Monitor**, chỉ thay đổi config:

```php
$config = [
    'sso_server' => 'https://auth.balocco-local.info',
    'callback_url' => 'https://bookcase.balocco-local.info/sso-callback',
    'app_name' => 'Bookcase',
];
```

---

## 🔧 Auth Server Configuration

### File: `config/cors.php` (nếu dùng)

Cho phép CORS từ các subdomain:

```php
'allowed_origins' => [
    'https://patent-monitor.balocco-local.info',
    'https://bookcase.balocco-local.info',
],

'allowed_origins_patterns' => [
    '/^https:\/\/.*\.balocco-local\.info$/', // Allow all balocco-local.info subdomains
],
```

---

### File: `.env`

```env
APP_URL=https://auth.balocco-local.info
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_DRIVER=redis  # Recommended for SSO
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Quan trọng:** `SESSION_DOMAIN=.balocco-local.info` (có dấu chấm đầu) để share session giữa các subdomain.

---

## 📡 API Endpoints

### 1. Check Login & Get Token

```
GET https://auth.balocco-local.info/api/sso/verify-session?callback={URL}
```

**Tham số:**
- `callback` (required): URL để redirect về sau khi check login

**Response:**
- Nếu đã login: `302 Redirect` → `callback?sso_session=TOKEN`
- Nếu chưa login: `302 Redirect` → `/login?callback=...`

**Example:**
```
GET /api/sso/verify-session?callback=https://patent-monitor.balocco-local.info/sso-callback

→ Redirect: https://patent-monitor.balocco-local.info/sso-callback?sso_session=abc123...
```

---

### 2. Verify Token

```
POST https://auth.balocco-local.info/api/sso/verify-session
Content-Type: application/x-www-form-urlencoded

sso_session=abc123...
```

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "user_name": "John Doe",
    "user_email": "john@example.com",
    "jwt_token": "eyJ0eXAiOiJKV1Qi...",
    "login_time": "2025-10-15T10:30:00Z",
    "authenticated": true
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "SSO session not found or expired"
}
```

---

## 🔐 Security Recommendations

### 1. HTTPS Required
```
✅ https://auth.balocco-local.info
✅ https://patent-monitor.balocco-local.info
✅ https://bookcase.balocco-local.info
```

### 2. Session Configuration
```env
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true     # Chỉ gửi qua HTTPS
SESSION_SAME_SITE=lax          # CSRF protection
SESSION_LIFETIME=120           # 2 hours
```

### 3. Callback URL Whitelist

Trong `SessionController.php`, thêm validation:

```php
private function isValidCallbackUrl($url) {
    $allowedDomains = [
        'patent-monitor.balocco-local.info',
        'bookcase.balocco-local.info',
    ];
    
    $host = parse_url($url, PHP_URL_HOST);
    return in_array($host, $allowedDomains);
}
```

Sử dụng:

```php
public function verifySessionGet(Request $request)
{
    $callbackUrl = $request->query('callback');
    
    // Validate callback URL
    if ($callbackUrl && !$this->isValidCallbackUrl($callbackUrl)) {
        return response()->json([
            'error' => 'Invalid callback URL'
        ], 400);
    }
    
    // ... rest of code
}
```

---

## 🧪 Testing

### Test Flow:

1. **Test Patent Monitor (lần đầu - chưa login):**
```
1. Mở: https://patent-monitor.balocco-local.info
2. → Redirect to Auth Server
3. → Login form
4. → Nhập thông tin login
5. → Redirect về Patent Monitor với token
6. → Patent Monitor verify token
7. ✅ Patent Monitor: "Welcome John Doe!"
```

2. **Test Bookcase (đã login ở Patent Monitor):**
```
1. Mở: https://bookcase.balocco-local.info
2. → Redirect to Auth Server
3. → ✅ Đã login rồi! (từ Patent Monitor)
4. → Tạo token và redirect ngay
5. → Bookcase verify token
6. ✅ Bookcase: "Welcome John Doe!" (không cần login lại!)
```

3. **Test Logout:**
```
1. Logout ở Patent Monitor
2. Mở Bookcase
3. → Chưa login → redirect to login form
4. → Cần login lại
```

---

## 🐛 Troubleshooting

### Vấn đề 1: "Session not shared between subdomains"

**Nguyên nhân:** `SESSION_DOMAIN` không đúng

**Giải pháp:**
```env
# .env
SESSION_DOMAIN=.balocco-local.info  # Có dấu chấm đầu!
```

---

### Vấn đề 2: "CORS error"

**Nguyên nhân:** Auth Server không cho phép request từ subdomain

**Giải pháp:**
```php
// config/cors.php
'allowed_origins_patterns' => [
    '/^https:\/\/.*\.balocco-local\.info$/',
],
```

---

### Vấn đề 3: "Token expired"

**Nguyên nhân:** Token chỉ có hiệu lực 5 phút

**Giải pháp:**
- Verify token ngay sau khi nhận được (trong sso-callback.php)
- Không cache hoặc delay việc verify

---

### Vấn đề 4: "Redirect loop"

**Nguyên nhân:** Callback URL không xử lý token đúng

**Giải pháp:**
- Check sso-callback.php có xử lý `$_GET['sso_session']`
- Check không redirect lại SSO sau khi đã có user trong session
- Add logging để trace flow

---

## 📊 Monitoring

### Logs cần theo dõi:

1. **SSO verification logs** (database: `login_logs`):
```sql
SELECT * FROM login_logs 
WHERE action = 'sso_verify' 
ORDER BY login_at DESC 
LIMIT 100;
```

2. **Failed verifications:**
```sql
SELECT * FROM login_logs 
WHERE status = 'failed' 
AND action = 'sso_verify'
ORDER BY login_at DESC;
```

3. **Active sessions:**
```bash
# Redis
redis-cli KEYS "sso_session_*"
redis-cli TTL "sso_session_abc123..."
```

---

## 📝 Implementation Checklist

### Auth Server (auth.balocco-local.info):
- [x] SSO endpoints implemented
- [ ] HTTPS enabled
- [ ] SESSION_DOMAIN configured
- [ ] CORS configured
- [ ] Callback URL whitelist
- [ ] Redis cache configured
- [ ] Logging enabled

### Patent Monitor (patent-monitor.balocco-local.info):
- [ ] SSO check on index.php
- [ ] sso-callback.php created
- [ ] Session management
- [ ] Logout functionality
- [ ] Error handling
- [ ] HTTPS enabled

### Bookcase (bookcase.balocco-local.info):
- [ ] SSO check on index.php
- [ ] sso-callback.php created
- [ ] Session management
- [ ] Logout functionality
- [ ] Error handling
- [ ] HTTPS enabled

---

## 🚀 Quick Start Code

### Minimal Integration (All-in-one file)

```php
<?php
// index.php - Patent Monitor hoặc Bookcase
session_start();

// Config
$ssoServer = 'https://auth.balocco-local.info';
$thisUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$callbackUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/sso-callback.php';

// Handle SSO callback
if (isset($_GET['sso_session']) && !isset($_SESSION['user'])) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ssoServer . '/api/sso/verify-session');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['sso_session' => $_GET['sso_session']]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    if ($response['success']) {
        $_SESSION['user'] = $response['data'];
        header('Location: /');
        exit;
    }
}

// Check login
if (!isset($_SESSION['user'])) {
    header('Location: ' . $ssoServer . '/api/sso/verify-session?callback=' . urlencode($callbackUrl));
    exit;
}

// Display app
echo "Welcome, " . htmlspecialchars($_SESSION['user']['user_name']) . "!";
?>
```

---

## 📚 Reference

- **API Documentation:** `docs/SSO-SIMPLE-FLOW.md`
- **Server Implementation:** `app/Http/Controllers/Api/SessionController.php`
- **Routes:** `routes/api.php`

---

**Updated:** 2025-10-15  
**Version:** 1.0  
**For:** Balocco Local Development Environment

---

**🎉 Happy SSO Integration!**

Login 1 lần ở Auth Server → Tự động login vào Patent Monitor & Bookcase!

