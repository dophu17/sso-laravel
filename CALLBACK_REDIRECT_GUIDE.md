# 🔗 CALLBACK REDIRECT GUIDE

## ✅ **CALLBACK FEATURE ĐÃ ĐƯỢC CẬP NHẬT!**

```
✅ Login với ?callback parameter
✅ Register với ?callback parameter
✅ Auto redirect về callback URL với JWT token
✅ Demo page để test
```

---

## 🎯 **CÁCH TEST:**

### **Step 1: Test với Demo Page**
```
1. Go to: http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html

2. Login:
   Email: user_a@gmail.com
   Password: password123

3. Click "Đăng nhập"

4. Expected:
   → Redirect to: http://localhost:8000/callback-demo.html?status=success&user_id=1&user_name=User+A&jwt_token=...
   → Demo page hiển thị user info
   → JWT token displayed
```

### **Step 2: Test với URL khác**
```
1. Setup web server trên port 8001:
   - Tạo file index.php hoặc index.html
   - Start server: php -S 127.0.0.1:8001

2. Login URL:
   http://localhost:8000/login?callback=http://127.0.0.1:8001

3. Login thành công

4. Expected:
   → Redirect to: http://127.0.0.1:8001?status=success&user_id=1&jwt_token=...
```

---

## 🔍 **VẤN ĐỀ VỀ SESSION:**

### **Vấn đề:**
```
❌ Login ở http://127.0.0.1:8000
❌ Session được lưu ở domain 127.0.0.1:8000
❌ Redirect về http://127.0.0.1:8001
❌ Session KHÔNG tồn tại ở 127.0.0.1:8001 (khác port = khác domain)
```

### **Giải pháp:**
```
✅ Sử dụng JWT Token thay vì Session
✅ JWT token được truyền qua URL
✅ Web 8001 lưu JWT token vào localStorage hoặc session
✅ Web 8001 dùng JWT token để verify user qua API
```

---

## 💡 **GIẢI THÍCH SESSION & DOMAIN:**

### **Session hoạt động như thế nào:**
```
Session được lưu theo domain và port:

http://127.0.0.1:8000 → Session A (SSO Server)
http://127.0.0.1:8001 → Session B (Your Web) ← KHÁC SESSION!

→ Không thể share session giữa các domain/port khác nhau
→ Đây là security feature của browser
```

### **Giải pháp SSO đúng cách:**
```
1. User login ở SSO Server (port 8000)
2. SSO tạo JWT token
3. Redirect về Your Web (port 8001) với JWT token
4. Your Web lưu JWT token vào localStorage/session
5. Your Web dùng JWT token để call API verify user
6. API trả về thông tin user
7. Your Web tạo session riêng cho user

→ Mỗi web có session riêng
→ Nhưng tất cả dùng chung JWT token để verify
```

---

## 🚀 **IMPLEMENTATION CHO WEB CỦA BẠN:**

### **Tạo file callback handler trên port 8001:**

**File: index.php (trên port 8001)**
```php
<?php
session_start();

// Parse callback parameters
$status = $_GET['status'] ?? null;
$userId = $_GET['user_id'] ?? null;
$userName = $_GET['user_name'] ?? null;
$userEmail = $_GET['user_email'] ?? null;
$jwtToken = $_GET['jwt_token'] ?? null;

if ($status === 'success' && $jwtToken) {
    // Save JWT token to YOUR web's session
    $_SESSION['sso_jwt_token'] = $jwtToken;
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_email'] = $userEmail;
    $_SESSION['logged_in'] = true;
    
    // Verify token with SSO API
    $ch = curl_init('http://localhost:8000/api/user');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $jwtToken,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $userData = json_decode($response, true);
        echo "<!DOCTYPE html>";
        echo "<html>";
        echo "<head><title>Login Success</title></head>";
        echo "<body>";
        echo "<h1>✅ Login Successful!</h1>";
        echo "<p>Welcome, " . htmlspecialchars($userData['data']['name']) . "!</p>";
        echo "<p>Email: " . htmlspecialchars($userData['data']['email']) . "</p>";
        echo "<p>You are now logged in on port 8001!</p>";
        echo "<hr>";
        echo "<h2>Session Data (Port 8001):</h2>";
        echo "<pre>" . print_r($_SESSION, true) . "</pre>";
        echo "</body>";
        echo "</html>";
    } else {
        echo "Token verification failed!";
    }
} else {
    echo "No callback data received. Please login first.";
}
?>
```

**Chạy server:**
```bash
php -S 127.0.0.1:8001
```

**Test:**
```
http://localhost:8000/login?callback=http://127.0.0.1:8001
→ Login
→ Redirect to http://127.0.0.1:8001?status=success&jwt_token=...
→ Port 8001 tạo session riêng
→ User logged in ở port 8001!
```

---

## 🔧 **DEBUG REDIRECT ISSUE:**

### **Check 1: Xem login có nhận callback không**
```bash
# Check Laravel logs khi login:
tail -f storage/logs/laravel.log

# Hoặc add debug vào LoginController:
\Log::info('Callback URL: ' . $request->get('callback'));
```

### **Check 2: Test redirect trực tiếp**
```bash
# Test script để verify redirect logic
php -r "
echo 'Testing redirect logic...' . PHP_EOL;
\$callbackUrl = 'http://127.0.0.1:8001';
\$params = ['status' => 'success', 'user_id' => 1];
\$separator = parse_url(\$callbackUrl, PHP_URL_QUERY) ? '&' : '?';
\$redirectUrl = \$callbackUrl . \$separator . http_build_query(\$params);
echo 'Redirect URL: ' . \$redirectUrl . PHP_EOL;
"
```

---

## 💡 **GIẢI THÍCH CROSS-DOMAIN SESSION:**

### **Tại sao session không share được:**
```
Browser Security Rules:

http://127.0.0.1:8000 → Cookie domain: 127.0.0.1:8000
http://127.0.0.1:8001 → Cookie domain: 127.0.0.1:8001

→ 2 cookies khác nhau!
→ 2 sessions khác nhau!
→ KHÔNG THỂ share session!
```

### **SSO Solution:**
```
1. User login ở SSO Server (8000)
   → Session A created on 8000

2. SSO tạo JWT token
   → Token is stateless (không cần session)

3. Redirect to Your Web (8001) với JWT token
   → Pass token qua URL

4. Your Web (8001) nhận JWT token
   → Save to Session B on 8001
   → Session B is INDEPENDENT from Session A

5. Your Web verify user với JWT token
   → Call API: GET /api/user với Bearer token
   → API trả về user info
   → Your Web tạo session cho user

→ Result: User "logged in" ở cả 2 webs
→ Nhưng mỗi web có session riêng
→ Cả 2 đều dùng JWT token để verify
```

---

## 🎊 **COMPLETE SOLUTION:**

### **File 1: SSO Login (port 8000)**
```
Already done! ✅
- Login form accepts ?callback
- Creates JWT token
- Redirects to callback URL with token
```

### **File 2: Your Web Callback (port 8001)**
```php
<?php
session_start();

// Receive JWT token from SSO
$jwtToken = $_GET['jwt_token'] ?? null;

if ($jwtToken) {
    // Save to YOUR web's session
    $_SESSION['sso_token'] = $jwtToken;
    $_SESSION['logged_in'] = true;
    
    // Verify with API
    $ch = curl_init('http://localhost:8000/api/user');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $jwtToken,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    
    if ($data['success']) {
        // Save user info to YOUR session
        $_SESSION['user'] = $data['data'];
        
        // Now user is logged in on YOUR web!
        echo "Welcome, " . $_SESSION['user']['name'];
    }
}
?>
```

---

## 🚀 **QUICK TEST STEPS:**

### **Setup:**
```bash
# Terminal 1: SSO Server
cd c:\xampp\htdocs\sso-laravel
php artisan serve --port=8000

# Terminal 2: Your Web
mkdir c:\xampp\htdocs\your-web
cd c:\xampp\htdocs\your-web
# Create index.php (code above)
php -S 127.0.0.1:8001
```

### **Test:**
```
1. Go to: http://localhost:8000/login?callback=http://127.0.0.1:8001

2. Login: user_a@gmail.com / password123

3. Should redirect to: http://127.0.0.1:8001?status=success&jwt_token=...

4. Your web (8001) receives token and creates its own session

5. User is now logged in on port 8001!
```

---

## 🎉 **CONCLUSION:**

### **Câu hỏi:**

> "Tại sao sau khi redirect về URL callback, tôi vẫn giữ trạng thái login được không?"

### **Answer:**
> **CÓ THỂ!** ✅
> 
> Nhưng không phải "giữ" session, mà là:
> 1. SSO tạo JWT token
> 2. Your web nhận JWT token qua URL
> 3. Your web lưu JWT token vào session riêng của nó
> 4. Your web verify user qua API với JWT token
> 5. Your web tạo session riêng cho user
> 
> → Mỗi web có session riêng
> → Nhưng cả 2 đều authenticated qua JWT token!

---

**HÃY TẠO FILE CALLBACK HANDLER VÀ TEST!** 🚀

**Chi tiết:** `CALLBACK_REDIRECT_GUIDE.md` 💪
