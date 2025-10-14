# 🎉 SSO SYSTEM - COMPLETE GUIDE

## ✅ **HỆ THỐNG HOÀN CHỈNH!**

```
✅ Login/Register → JWT Token
✅ Callback URL support
✅ Session verification API (GET & POST)
✅ Cross-domain authentication
✅ Demo pages ready
✅ Complete documentation
```

---

## 🎯 **2 API ENDPOINTS ĐỂ VERIFY SESSION:**

### **Method 1: GET (Đơn giản hơn)**
```
GET http://localhost:8000/api/sso/verify-session?session_token={token}

Response:
{
  "authenticated": true,
  "user_id": 1,
  "user_name": "User A",
  "user_email": "user_a@gmail.com",
  "jwt_token": "eyJ0eXAi...",
  "login_time": "2025-10-14T..."
}
```

### **Method 2: POST (Theo standard REST)**
```
POST http://localhost:8000/api/sso/verify-session
Content-Type: application/json

{
  "sso_session": "{token}"
}

Response:
{
  "success": true,
  "data": {
    "user_id": 1,
    "user_name": "User A",
    "user_email": "user_a@gmail.com",
    "jwt_token": "eyJ0eXAi...",
    "authenticated": true
  }
}
```

---

## 🚀 **COMPLETE FLOW:**

### **Step 1: User Login với Callback**
```
URL: http://localhost:8000/login?callback=http://127.0.0.1:8001

Flow:
1. User thấy login form với notification "🔗 Login với Callback"
2. User login: user_a@gmail.com / password123
3. SSO tạo:
   - JWT token
   - Session token (64 chars)
   - Lưu vào Cache (5 phút)
4. SSO redirect to: http://127.0.0.1:8001?sso_session=abc123&status=success
```

### **Step 2: Your Web Verify Session**
```php
// YOUR web receives callback
$ssoSession = $_GET['sso_session'];

// Method 1: GET request (simpler)
$url = "http://localhost:8000/api/sso/verify-session?session_token=$ssoSession";
$response = file_get_contents($url);
$data = json_decode($response, true);

// OR Method 2: POST request
$ch = curl_init('http://localhost:8000/api/sso/verify-session');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['sso_session' => $ssoSession]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);

// Check authentication
if ($data['authenticated']) {
    // Save to YOUR session
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['user_name'] = $data['user_name'];
    $_SESSION['user_email'] = $data['user_email'];
    $_SESSION['jwt_token'] = $data['jwt_token'];
    $_SESSION['logged_in'] = true;
    
    echo "Welcome, " . $_SESSION['user_name'];
    // User is now logged in on YOUR web!
}
```

---

## 💻 **CLIENT WEB EXAMPLE:**

### **Simple GET method:**
```php
<?php
session_start();

$ssoSession = $_GET['sso_session'] ?? null;

if ($ssoSession) {
    // Verify với SSO Server (GET method)
    $url = "http://localhost:8000/api/sso/verify-session?session_token=" . urlencode($ssoSession);
    $response = @file_get_contents($url);
    
    if ($response) {
        $data = json_decode($response, true);
        
        if ($data['authenticated']) {
            // Tạo session trên YOUR web
            $_SESSION['user_id'] = $data['user_id'];
            $_SESSION['user_name'] = $data['user_name'];
            $_SESSION['user_email'] = $data['user_email'];
            $_SESSION['jwt_token'] = $data['jwt_token'];
            $_SESSION['logged_in'] = true;
            
            echo "✅ Login thành công!<br>";
            echo "Welcome, " . $_SESSION['user_name'] . "!<br>";
            echo "Email: " . $_SESSION['user_email'];
        }
    }
} else {
    echo "Not logged in. <a href='http://localhost:8000/login?callback=" . urlencode($_SERVER['REQUEST_URI']) . "'>Login</a>";
}
?>
```

---

## 🎊 **3 DEMO PAGES:**

### **1. callback-demo.html**
```
✅ Simple HTML/JavaScript demo
✅ Parse URL parameters
✅ Display user info & JWT token
✅ Test API button

URL: http://localhost:8000/callback-demo.html
Test: http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html
```

### **2. callback-with-session.php**
```
✅ Full PHP example with session
✅ POST method to verify session
✅ Create session on YOUR web
✅ Display session data

URL: http://localhost:8000/callback-with-session.php
Test: http://localhost:8000/login?callback=http://localhost:8000/callback-with-session.php
```

### **3. your-web-callback.php**
```
✅ Complete integration example
✅ Session creation
✅ API verification
✅ Beautiful UI

URL: http://localhost:8000/your-web-callback.php
Test: http://localhost:8000/login?callback=http://localhost:8000/your-web-callback.php
```

---

## 🔧 **API ENDPOINTS SUMMARY:**

### **SSO Server APIs:**
```
GET  /api/sso/verify-session?session_token={token}
POST /api/sso/verify-session (body: {"sso_session": "{token}"})
GET  /api/user (with Bearer JWT token)
GET  /api/user/profile (with Bearer JWT token)
```

---

## 🎯 **TROUBLESHOOTING:**

### **Nếu không redirect:**

**Check 1: Clear cache**
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

**Check 2: Browser**
```
- Clear browser cache completely
- Use incognito mode
- Try different browser
```

**Check 3: Test manual redirect**
```
http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html

Should see: "🔗 Login với Callback" notification
```

**Check 4: Check logs**
```bash
# Add debug in LoginController:
\Log::info('Callback URL: ' . $request->get('callback'));
\Log::info('Redirect URL: ' . $redirectUrl);

# Check logs:
tail -f storage/logs/laravel.log
```

---

## 🎉 **FINAL SUMMARY:**

### **What Works:**

✅ **Login/Register:**
- http://localhost:8000/login → JWT token
- http://localhost:8000/register → JWT token

✅ **Login/Register with Callback:**
- http://localhost:8000/login?callback=URL → Redirect with session token
- http://localhost:8000/register?callback=URL → Redirect with session token

✅ **Session Verification:**
- GET /api/sso/verify-session?session_token=X
- POST /api/sso/verify-session (body: {"sso_session": "X"})

✅ **JWT Token Usage:**
- Postman API testing
- Cross-domain authentication
- Web integration

---

**TEST URL:**
```
http://localhost:8000/login?callback=http://localhost:8000/callback-with-session.php
```

**Login:**
```
user_a@gmail.com / password123
```

**Expected:**
```
→ Redirect to callback URL
→ Session verified
→ User logged in! ✅
```

---

**HỆ THỐNG HOÀN CHỈNH!** 🎉💪
