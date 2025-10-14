# 🎉 CALLBACK URL FEATURE - COMPLETE!

## ✅ **TÍNH NĂNG CALLBACK HOÀN THÀNH!**

```
✅ Login với callback parameter
✅ Register với callback parameter
✅ Auto redirect về callback URL
✅ JWT token được truyền qua URL
✅ User info được truyền qua URL
✅ Demo page để test callback
```

---

## 🎯 **CÁCH SỬ DỤNG:**

### **Login với Callback:**
```
URL: http://localhost:8000/login?callback=http://127.0.0.1:8001

Flow:
1. User truy cập URL trên
2. Thấy thông báo "Login với Callback"
3. Nhập: user_a@gmail.com / password123
4. Submit
5. Redirect về: http://127.0.0.1:8001?status=success&user_id=1&user_name=User+A&user_email=user_a@gmail.com&jwt_token=eyJ0eXAi...&login_time=2025-10-14T...
```

### **Register với Callback:**
```
URL: http://localhost:8000/register?callback=http://127.0.0.1:8001

Flow:
1. User truy cập URL trên
2. Thấy thông báo "Đăng ký với Callback"
3. Điền thông tin đăng ký
4. Submit
5. Redirect về: http://127.0.0.1:8001?status=success&user_id=12&user_name=New+User&user_email=new@example.com&jwt_token=eyJ0eXAi...&register_time=2025-10-14T...
```

---

## 📋 **CALLBACK PARAMETERS:**

### **Query Parameters được trả về:**
```
status: success
user_id: {id}
user_name: {name}
user_email: {email}
jwt_token: {jwt_string}
login_time: {iso8601_datetime}  (for login)
register_time: {iso8601_datetime}  (for register)
```

### **Example Callback URL:**
```
http://127.0.0.1:8001?status=success&user_id=1&user_name=User+A&user_email=user_a%40gmail.com&jwt_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...&login_time=2025-10-14T14:05:32.000000Z
```

---

## 🚀 **DEMO PAGE:**

### **Test Callback với Demo Page:**
```
1. Open demo page:
   http://localhost:8000/callback-demo.html

2. Test login:
   http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html
   
3. Login: user_a@gmail.com / password123

4. Redirect về demo page với:
   ✅ User info displayed
   ✅ JWT token displayed
   ✅ Copy button
   ✅ Test API button
```

### **Demo Page Features:**
```
✅ Parse URL parameters automatically
✅ Display user info
✅ Display JWT token
✅ Copy token button
✅ Test API button
✅ Show API response
✅ Beautiful UI
```

---

## 💻 **YOUR WEB INTEGRATION:**

### **Example: Redirect to Your Web**
```
Your web URL: http://yourweb.com/sso-callback

SSO Login URL:
http://localhost:8000/login?callback=http://yourweb.com/sso-callback

After login:
→ Redirect to: http://yourweb.com/sso-callback?status=success&user_id=1&user_name=User+A&user_email=user_a@gmail.com&jwt_token=eyJ0eXAi...
```

### **JavaScript to Parse Callback:**
```javascript
// On your web's callback page
const urlParams = new URLSearchParams(window.location.search);

// Get data
const status = urlParams.get('status');
const userId = urlParams.get('user_id');
const userName = urlParams.get('user_name');
const userEmail = urlParams.get('user_email');
const jwtToken = urlParams.get('jwt_token');

if (status === 'success' && jwtToken) {
    // Save token
    localStorage.setItem('sso_token', jwtToken);
    
    // Display user info
    console.log('Logged in as:', userName);
    
    // Verify token with API
    fetch('http://localhost:8000/api/user', {
        headers: {
            'Authorization': 'Bearer ' + jwtToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('User verified:', data);
        // Show user dashboard
    });
}
```

### **PHP to Parse Callback:**
```php
// On your web's callback page
$status = $_GET['status'] ?? null;
$userId = $_GET['user_id'] ?? null;
$userName = $_GET['user_name'] ?? null;
$userEmail = $_GET['user_email'] ?? null;
$jwtToken = $_GET['jwt_token'] ?? null;

if ($status === 'success' && $jwtToken) {
    // Save token in session
    $_SESSION['sso_token'] = $jwtToken;
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_email'] = $userEmail;
    
    // Verify token with API
    $ch = curl_init('http://localhost:8000/api/user');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $jwtToken,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    
    if ($data['success']) {
        echo "Welcome, " . $data['data']['name'];
        // Show user dashboard
    }
}
```

---

## 🔧 **TECHNICAL DETAILS:**

### **LoginController:**
```php
// Check for callback parameter
if ($request->has('callback')) {
    $callbackUrl = $request->get('callback');
    
    // Build URL with parameters
    $params = [
        'status' => 'success',
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_email' => $user->email,
        'jwt_token' => $jwtToken,
        'login_time' => now()->toIso8601String(),
    ];
    
    $separator = parse_url($callbackUrl, PHP_URL_QUERY) ? '&' : '?';
    $redirectUrl = $callbackUrl . $separator . http_build_query($params);
    
    return redirect($redirectUrl);
}
```

### **Views Updated:**
```
✅ resources/views/auth/login.blade.php
   - Hidden input for callback
   - Info box showing callback URL

✅ resources/views/auth/register.blade.php
   - Hidden input for callback
   - Info box showing callback URL
```

---

## 🎊 **COMPLETE FLOWS:**

### **Flow 1: Login without Callback**
```
http://localhost:8000/login
→ Login
→ /login/success (internal page)
→ Display JWT token
```

### **Flow 2: Login with Callback**
```
http://localhost:8000/login?callback=http://yourweb.com/callback
→ Login
→ Redirect to: http://yourweb.com/callback?status=success&jwt_token=...
```

### **Flow 3: Register without Callback**
```
http://localhost:8000/register
→ Register
→ /register/success (internal page)
→ Display JWT token
```

### **Flow 4: Register with Callback**
```
http://localhost:8000/register?callback=http://yourweb.com/callback
→ Register
→ Redirect to: http://yourweb.com/callback?status=success&jwt_token=...
```

---

## 🎯 **TEST EXAMPLES:**

### **Test 1: Demo Page (Login)**
```
URL: http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html

Login: user_a@gmail.com / password123

Result:
→ Redirect to callback-demo.html
→ User info displayed
→ JWT token displayed
→ Copy button works
→ Test API button works
```

### **Test 2: Demo Page (Register)**
```
URL: http://localhost:8000/register?callback=http://localhost:8000/callback-demo.html

Register: New User / newuser@test.com / password123

Result:
→ Redirect to callback-demo.html
→ User info displayed
→ JWT token displayed
→ Works perfectly
```

### **Test 3: External URL**
```
URL: http://localhost:8000/login?callback=http://127.0.0.1:8001

Login: user_a@gmail.com / password123

Result:
→ Redirect to http://127.0.0.1:8001?status=success&...
→ Your web receives all parameters
→ Parse and use JWT token
```

---

## 📊 **CALLBACK URL STRUCTURE:**

### **Base URL:**
```
http://yourweb.com/callback
```

### **With Parameters:**
```
http://yourweb.com/callback?
  status=success&
  user_id=1&
  user_name=User+A&
  user_email=user_a@gmail.com&
  jwt_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...&
  login_time=2025-10-14T14:05:32.000000Z
```

### **URL Encoded:**
```
Spaces → +
@ → %40
Special chars → %XX
```

---

## 🎉 **CONCLUSION:**

### **Câu hỏi của bạn:**

> **"Ở màn hình login, trường hợp URL có param ?callback thì sau khi login thành công, sẽ redirect về link callback, kết quả trả về thành công kèm JWT như bình thường"**

### **Answer:**
> **DONE!** ✅
> 
> - Login/Register với ?callback parameter
> - Auto redirect về callback URL
> - JWT token + user info trong URL
> - Demo page để test
> - Integration examples (JS & PHP)

---

## 🚀 **QUICK TEST:**

```bash
# Test with demo page:
http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html

# Login:
user_a@gmail.com / password123

# Result:
→ Beautiful demo page
→ User info displayed
→ JWT token ready
→ Test API button works
```

---

**CALLBACK FEATURE HOÀN CHỈNH!** 🎉

**Test ngay:** 
```
http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html
```

**Chi tiết:** `CALLBACK_URL_FEATURE.md` 💪
