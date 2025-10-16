# 🔐 Google OAuth Setup Guide

## ✅ **ĐÃ HOÀN THÀNH:**

### 1. **Laravel Socialite Installation**
- ✅ Cài đặt Laravel Socialite package
- ✅ Cấu hình trong `config/services.php`

### 2. **Database Updates**
- ✅ Thêm `google_id` và `avatar` vào `users` table
- ✅ Cập nhật User model với fillable fields
- ✅ Thêm Google actions vào `login_logs` table

### 3. **Google OAuth Controller**
- ✅ Tạo `GoogleController` với redirect và callback
- ✅ Xử lý existing users và new users
- ✅ Link Google account với existing email
- ✅ Logging Google login activities

### 4. **Routes & Views**
- ✅ Thêm Google OAuth routes
- ✅ Cập nhật login view với Google button
- ✅ Giữ nguyên redirect parameters từ client

## 🔧 **CẦN CẤU HÌNH:**

### **Bước 1: Tạo Google OAuth App**

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project hiện có
3. Enable Google+ API
4. Tạo OAuth 2.0 credentials:
   - Application type: Web application
   - Authorized redirect URIs: `http://auth.balocco-local.info/auth/google/callback`

### **Bước 2: Cấu hình .env**

Thêm vào file `.env`:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://auth.balocco-local.info/auth/google/callback
```

### **Bước 3: Clear Cache**

```bash
php artisan config:clear
php artisan cache:clear
```

## 🚀 **TESTING:**

### **Test Google Login:**

1. **Truy cập login page:**
   ```
   http://auth.balocco-local.info/login
   ```

2. **Click "Đăng nhập bằng Google"**

3. **Với redirect từ client:**
   ```
   http://auth.balocco-local.info/login?redirect=https://client-app.com/dashboard
   ```

### **Test Flow:**

1. **New User:** Google login → Tạo user mới → Redirect
2. **Existing Email:** Google login → Link account → Redirect  
3. **Existing Google ID:** Google login → Login existing user → Redirect

## 🔗 **SSO Integration:**

### **Client App Integration:**

```php
// Trong client app, redirect đến SSO với Google login
$ssoUrl = 'http://auth.balocco-local.info/login?' . http_build_query([
    'redirect' => 'https://client-app.com/dashboard',
    'client_id' => 'your_client_id',
    'redirect_uri' => 'https://client-app.com/callback',
    'state' => 'random_state_string'
]);
```

### **Session Sharing:**

- Google login tạo session giống như login thường
- Session được share qua `SESSION_DOMAIN=.balocco-local.info`
- Client apps có thể access session ngay lập tức

## 📊 **Database Schema:**

### **Users Table:**
```sql
ALTER TABLE users ADD COLUMN google_id VARCHAR(255) UNIQUE AFTER email;
ALTER TABLE users ADD COLUMN avatar VARCHAR(255) AFTER google_id;
```

### **Login Logs Table:**
```sql
ALTER TABLE login_logs MODIFY COLUMN action ENUM(
    'login', 'register', 'logout', 'password_reset', 
    'google_login', 'google_register'
) DEFAULT 'login';
```

## 🛡️ **Security Features:**

### **OAuth Security:**
- ✅ CSRF protection
- ✅ State parameter validation
- ✅ Secure redirect handling
- ✅ Token validation

### **User Security:**
- ✅ Email verification tự động
- ✅ Unique Google ID constraint
- ✅ Avatar URL validation
- ✅ Activity logging

### **Session Security:**
- ✅ Session regeneration
- ✅ Secure cookie settings
- ✅ Cross-subdomain sharing

## 🎯 **Features:**

### **Google Login Features:**
- ✅ **Beautiful UI**: Google-style button với official colors
- ✅ **Account Linking**: Link Google với existing email
- ✅ **Auto Registration**: Tạo user mới từ Google profile
- ✅ **Avatar Support**: Lưu Google profile picture
- ✅ **Email Verification**: Auto verify email từ Google

### **SSO Features:**
- ✅ **Redirect Preservation**: Giữ nguyên client redirect
- ✅ **Session Sharing**: Hoạt động với session sharing
- ✅ **Activity Logging**: Log Google login activities
- ✅ **Error Handling**: Xử lý lỗi OAuth gracefully

## 🔍 **Troubleshooting:**

### **Common Issues:**

1. **"Invalid redirect_uri":**
   - Kiểm tra Google Console settings
   - Đảm bảo redirect URI chính xác

2. **"Access blocked":**
   - Check Google OAuth consent screen
   - Verify domain authorization

3. **"Client ID not found":**
   - Kiểm tra .env configuration
   - Clear config cache

### **Debug Commands:**

```bash
# Check Google config
php artisan tinker
config('services.google')

# Check user with Google ID
User::whereNotNull('google_id')->get()

# Check login logs
LoginLog::where('action', 'google_login')->get()
```

---

**Sau khi cấu hình Google OAuth credentials, hệ thống sẽ hoạt động hoàn hảo!** 🚀
