# 🎯 SSO VERIFY ENDPOINT - READY!

## ✅ **ENDPOINT ĐÃ CÓ SẴN!**

```
✅ GET /sso/verify?session_token={token}
✅ GET /api/sso/verify-session?session_token={token}
✅ POST /api/sso/verify-session (body: {"sso_session": "..."})
```

---

## 🚀 **CÁCH SỬ DỤNG:**

### **Method 1: Web Route (Đơn giản nhất)**
```
GET http://localhost:8000/sso/verify?session_token={token}

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

### **Method 2: API Route (GET)**
```
GET http://localhost:8000/api/sso/verify-session?session_token={token}

Response: Same as above
```

### **Method 3: API Route (POST)**
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
    "authenticated": true,
    "user_id": 1,
    ...
  }
}
```

---

## 🎯 **COMPLETE FLOW:**

### **Step 1: Login với Callback**
```
http://localhost:8000/login?callback=http://127.0.0.1:8001

Login: user_a@gmail.com / password123

Redirect to:
http://127.0.0.1:8001?sso_session=abc123xyz&status=success
```

### **Step 2: Verify Session**
```php
// YOUR web (port 8001)
$ssoSession = $_GET['sso_session'];

// Call SSO to verify
$url = "http://localhost:8000/sso/verify?session_token=$ssoSession";
$response = file_get_contents($url);
$data = json_decode($response, true);

if ($data['authenticated']) {
    // User is authenticated!
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['user_name'] = $data['user_name'];
    $_SESSION['jwt_token'] = $data['jwt_token'];
    $_SESSION['logged_in'] = true;
    
    echo "Welcome, " . $_SESSION['user_name'];
}
```

---

## 💻 **CLIENT WEB SIMPLE EXAMPLE:**

```php
<?php
// File: index.php on port 8001
session_start();

$ssoSession = $_GET['sso_session'] ?? null;

if ($ssoSession && !isset($_SESSION['logged_in'])) {
    // Verify with SSO
    $url = "http://localhost:8000/sso/verify?session_token=" . urlencode($ssoSession);
    $response = @file_get_contents($url);
    
    if ($response) {
        $data = json_decode($response, true);
        
        if ($data['authenticated']) {
            // Create session
            $_SESSION = $data;
            $_SESSION['logged_in'] = true;
        }
    }
}

// Check if logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo "<h1>Welcome, " . htmlspecialchars($_SESSION['user_name']) . "!</h1>";
    echo "<p>Email: " . htmlspecialchars($_SESSION['user_email']) . "</p>";
    echo "<p>JWT Token: " . htmlspecialchars(substr($_SESSION['jwt_token'], 0, 50)) . "...</p>";
    echo "<a href='?logout=1'>Logout</a>";
    
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: /');
        exit;
    }
} else {
    echo "<h1>Not logged in</h1>";
    echo "<a href='http://localhost:8000/login?callback=" . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . "'>Login with SSO</a>";
}
?>
```

---

## 🎊 **TEST ENDPOINTS:**

### **Test Verify Endpoint:**
```bash
# Test without token (should fail):
http://localhost:8000/sso/verify

Response: 400 Bad Request
{
  "authenticated": false,
  "message": "Session token required"
}

# Test with invalid token:
http://localhost:8000/sso/verify?session_token=invalid123

Response: 404 Not Found
{
  "authenticated": false,
  "message": "Session not found or expired"
}

# Test with valid token (after login):
http://localhost:8000/sso/verify?session_token=abc123xyz

Response: 200 OK
{
  "authenticated": true,
  "user_id": 1,
  "user_name": "User A",
  "jwt_token": "eyJ0eXAi...",
  ...
}
```

---

## 🎉 **CONCLUSION:**

### **Câu hỏi:**

> **"tại sao trả về 404 khi truy cập /sso/verify?"**

### **Answer:**
> **ĐÃ FIX!** ✅
> 
> Route `/sso/verify` đã được tạo!
> 
> **Usage:**
> ```
> GET /sso/verify?session_token={token}
> ```

---

## 🚀 **TEST NGAY:**

```
1. Login:
   http://localhost:8000/login?callback=http://localhost:8000/callback-with-session.php

2. Login: user_a@gmail.com / password123

3. Redirect với sso_session parameter

4. Verify works! ✅
```

---

**ENDPOINT SẴN SÀNG!** 🎉

**Chi tiết:** `SSO_VERIFY_ENDPOINT.md` 💪
