# 🚀 SSO Simple Flow - Login 1 lần, truy cập nhiều nơi

## 📖 Tổng quan

Flow SSO đơn giản cho phép user **login 1 lần trên SSO Server**, sau đó có thể truy cập **nhiều ứng dụng khác nhau** mà không cần login lại.

---

## 🔄 Flow hoạt động

```
User → Client App A
         ↓
    Gọi SSO Server: /api/sso/verify-session?callback=...
         ↓
    ┌─────────────────┐
    │ SSO Server      │
    │ Check login?    │
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
    Callback URL + ?sso_session=TOKEN
                ↓
    Client verify token → Lấy user info
                ↓
    ✅ DONE! User đã login vào Client App A
    
    
User → Client App B
         ↓
    Gọi SSO Server: /api/sso/verify-session?callback=...
         ↓
    ✅ Đã login rồi! (từ App A)
         ↓
    Tạo token → Redirect ngay
         ↓
    ✅ DONE! Không cần login lại!
```

---

## 🛠️ Implementation cho Client

### Bước 1: Redirect đến SSO Server để check login

```php
<?php
// index.php - Client App

session_start();

// Config
$ssoServer = 'http://localhost:8000';
$callbackUrl = 'http://your-app.com/callback.php';

// Nếu chưa có user trong session → check SSO
if (!isset($_SESSION['user'])) {
    // Redirect đến SSO Server để check login
    $ssoCheckUrl = $ssoServer . '/api/sso/verify-session?callback=' . urlencode($callbackUrl);
    header('Location: ' . $ssoCheckUrl);
    exit;
}

// Đã có user → hiển thị app
echo "Welcome, " . $_SESSION['user']['name'];
?>
```

---

### Bước 2: Xử lý callback và verify token

```php
<?php
// callback.php - Xử lý khi SSO Server redirect về

session_start();

$ssoServer = 'http://localhost:8000';

// Nhận token từ SSO Server
if (isset($_GET['sso_session'])) {
    $sessionToken = $_GET['sso_session'];
    
    // Verify token với SSO Server
    $verifyUrl = $ssoServer . '/api/sso/verify-session';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'sso_session' => $sessionToken
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        
        if ($data['success']) {
            // Lưu user info vào session
            $_SESSION['user'] = [
                'id' => $data['data']['user_id'],
                'name' => $data['data']['user_name'],
                'email' => $data['data']['user_email'],
                'jwt_token' => $data['data']['jwt_token'],
            ];
            
            // Redirect về trang chủ
            header('Location: index.php');
            exit;
        }
    }
    
    // Verify failed
    die('SSO verification failed');
}

die('No SSO session token');
?>
```

---

## 📡 API Endpoints

### 1. Check Login & Redirect

```
GET /api/sso/verify-session?callback={URL}
```

**Tham số:**
- `callback` (required): URL để redirect về sau khi check login

**Response:**
- Nếu đã login: `302 Redirect` → `callback?sso_session=TOKEN`
- Nếu chưa login: `302 Redirect` → `/login?callback=...`

**Example:**
```
GET /api/sso/verify-session?callback=http://app-a.com/callback.php

→ Redirect: http://app-a.com/callback.php?sso_session=abc123xyz...
```

---

### 2. Verify Token & Get User Info

```
POST /api/sso/verify-session
```

**Body:**
```json
{
  "sso_session": "abc123xyz..."
}
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

## ✅ Flow đầy đủ Example

### Scenario: User truy cập 3 ứng dụng

#### Lần 1: Truy cập App A (chưa login)

```
1. User → http://app-a.com
2. App A check session → không có
3. App A redirect: /api/sso/verify-session?callback=http://app-a.com/callback.php
4. SSO Server check → chưa login
5. SSO redirect: /login?callback=http://app-a.com/callback.php
6. User login trên SSO Server
7. SSO tạo token → redirect: http://app-a.com/callback.php?sso_session=TOKEN
8. App A verify token → lưu user info
9. ✅ App A hiển thị: "Welcome John!"
```

#### Lần 2: Truy cập App B (đã login ở App A)

```
1. User → http://app-b.com
2. App B check session → không có
3. App B redirect: /api/sso/verify-session?callback=http://app-b.com/callback.php
4. SSO Server check → ✅ ĐÃ LOGIN! (từ App A)
5. SSO tạo token → redirect ngay: http://app-b.com/callback.php?sso_session=TOKEN2
6. App B verify token → lưu user info
7. ✅ App B hiển thị: "Welcome John!" (KHÔNG CẦN LOGIN LẠI!)
```

#### Lần 3: Truy cập App C (đã login ở App A, B)

```
1. User → http://app-c.com
2. App C check session → không có
3. App C redirect: /api/sso/verify-session?callback=http://app-c.com/callback.php
4. SSO Server check → ✅ ĐÃ LOGIN!
5. SSO tạo token → redirect: http://app-c.com/callback.php?sso_session=TOKEN3
6. App C verify token → lưu user info
7. ✅ App C hiển thị: "Welcome John!" (KHÔNG CẦN LOGIN LẠI!)
```

**🎉 Login 1 lần ở SSO Server → Truy cập được 3 apps!**

---

## 🔐 Security Notes

### Token Expiry
- Session token có hiệu lực **5 phút**
- Client phải verify token ngay sau khi nhận được
- Sau khi verify, lưu user info vào session local (không lưu token)

### Session Management
- SSO Server lưu session trong cache (Redis/Memcached)
- Mỗi lần verify tạo token mới
- Token chỉ dùng 1 lần (one-time token)

### Best Practices
1. ✅ Always use HTTPS in production
2. ✅ Validate callback URL (whitelist)
3. ✅ Set session timeout appropriate
4. ✅ Log all SSO activities
5. ✅ Implement rate limiting

---

## 🐛 Troubleshooting

### Vấn đề: "Token expired"

**Nguyên nhân:** Token chỉ có hiệu lực 5 phút

**Giải pháp:**
- Verify token ngay sau khi nhận được (trong callback)
- Không delay hoặc cache token

---

### Vấn đề: "Session not found"

**Nguyên nhân:** 
- Token đã được dùng rồi
- Token đã expire
- Cache server down

**Giải pháp:**
- Redirect user về SSO để login lại
- Check cache server (Redis/Memcached)

---

### Vấn đề: "Redirect loop"

**Nguyên nhân:** Callback URL không đúng hoặc không xử lý token

**Giải pháp:**
- Check callback URL đúng format
- Check callback.php có xử lý `$_GET['sso_session']`
- Check không redirect lại SSO sau khi đã có token

---

## 📊 Monitoring

### Logs to track:

1. **SSO verification logs** (`login_logs` table):
   - `action = 'sso_verify'`
   - Track: user_id, callback_url, session_token, timestamp

2. **Failed verifications:**
   - Token not found
   - Token expired
   - Invalid callback URL

3. **Usage metrics:**
   - Number of SSO verifications per day
   - Number of unique apps using SSO
   - Average time between verifications

---

## 🚀 Quick Start Checklist

### Setup SSO Server:
- [x] Install Laravel Passport
- [x] Configure cache (Redis/Memcached)
- [x] Enable session handling
- [x] Configure CORS if needed

### Setup Client App:
- [ ] Add redirect to SSO on index page
- [ ] Create callback.php to handle token
- [ ] Implement verify token API call
- [ ] Store user info in session
- [ ] Test login flow
- [ ] Test SSO across multiple apps

---

## 📚 Code Examples

### Example 1: Minimal Client (All-in-one file)

```php
<?php
session_start();

$ssoServer = 'http://localhost:8000';
$thisUrl = 'http://localhost:3000/index.php';

// Handle callback with token
if (isset($_GET['sso_session']) && !isset($_SESSION['user'])) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ssoServer . '/api/sso/verify-session');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'sso_session' => $_GET['sso_session']
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = json_decode(curl_exec($ch), true);
    
    if ($response['success']) {
        $_SESSION['user'] = $response['data'];
        header('Location: index.php');
        exit;
    }
}

// Check if logged in
if (!isset($_SESSION['user'])) {
    // Redirect to SSO
    header('Location: ' . $ssoServer . '/api/sso/verify-session?callback=' . urlencode($thisUrl));
    exit;
}

// Display app
?>
<!DOCTYPE html>
<html>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user']['user_name']) ?>!</h1>
    <p>Email: <?= htmlspecialchars($_SESSION['user']['user_email']) ?></p>
    <a href="?logout">Logout</a>
</body>
</html>
```

---

## ✅ Summary

**SSO Simple Flow = Login 1 lần, dùng nhiều nơi**

- ✅ Đơn giản, dễ implement
- ✅ User experience tốt (không cần login nhiều lần)
- ✅ Bảo mật với JWT token
- ✅ Session management tự động
- ✅ Support multiple applications

**Perfect for:**
- Internal applications
- Microservices architecture  
- Multiple web apps trong cùng tổ chức

---

**Updated:** 2025-10-15  
**Version:** 1.0

