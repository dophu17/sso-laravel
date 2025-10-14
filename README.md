# 🔐 SSO Laravel - Hệ Thống Đăng Nhập Tập Trung

Hệ thống SSO (Single Sign-On) hoàn chỉnh được xây dựng với Laravel 11 và Passport, hỗ trợ JWT token, callback URL, quản lý session liên domain và theo dõi hoạt động đăng nhập.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## ✨ Tính Năng

- 🔐 **Xác thực bằng JWT Token** - Bảo mật cao với token
- 🌐 **Hỗ trợ Cross-Domain** - Hoạt động trên nhiều domain/port khác nhau
- 🔄 **Hệ thống Callback URL** - Tự động chuyển về trang web của bạn sau khi đăng nhập
- 🎯 **Quản lý Session** - Chia sẻ session giữa nhiều ứng dụng
- 📊 **Bảng điều khiển Admin** - Xem users, tokens và hoạt động đăng nhập
- 📝 **Lịch sử đăng nhập** - Theo dõi tất cả login/register/logout với callback URLs
- 👥 **Hệ thống phân quyền** - Vai trò Admin và Member
- 🚀 **API sẵn sàng** - RESTful API endpoints để tích hợp
- 💻 **Trang Demo** - Các trang demo sẵn sàng sử dụng

---

## 🚀 Bắt Đầu Nhanh

### Yêu Cầu

- PHP 8.2+
- MySQL/MariaDB
- Composer
- Laravel 11

### Cài Đặt

```bash
# Clone repository
git clone https://github.com/dophu17/sso-laravel.git
cd sso-laravel

# Cài đặt dependencies
composer install

# Thiết lập môi trường
cp .env.example .env
php artisan key:generate

# Cấu hình database trong .env
DB_DATABASE=ten_database
DB_USERNAME=username
DB_PASSWORD=password

# Chạy migrations
php artisan migrate

# Tạo dữ liệu mẫu
php artisan db:seed

# Tạo Passport keys
php artisan passport:keys --force
php artisan passport:client --personal --name="Personal Access Client"

# Khởi động server
php artisan serve
```

Truy cập: `http://localhost:8000`

---

## 👤 Tài Khoản Mặc Định

Sau khi chạy `php artisan db:seed`:

```
Tài khoản Admin:
Email: admin@gmail.com
Password: password123
Vai trò: admin

Tài khoản Demo:
Email: user_a@gmail.com
Password: password123
Vai trò: member
```

---

## 📋 Cách Sử Dụng

### 1. Đăng nhập với Callback

```
http://localhost:8000/login?callback=http://yourweb.com
```

**Quy trình:**
1. User đăng nhập tại SSO server
2. SSO tạo JWT token + session token
3. Chuyển hướng về: `http://yourweb.com?sso_session=abc123&status=success`
4. Web của bạn xác thực session qua API
5. Tạo session local trên web của bạn

### 2. Đăng ký với Callback

```
http://localhost:8000/register?callback=http://yourweb.com
```

Quy trình tương tự đăng nhập, nhưng dành cho user mới.

### 3. SSO Verify (Tự động đăng nhập)

```
http://localhost:8000/sso/verify?callback=http://yourweb.com
```

- Nếu chưa đăng nhập → Tự động chuyển đến trang login
- Nếu đã đăng nhập → Chuyển về callback với session token

### 4. Đăng xuất với Callback

```
http://localhost:8000/logout?callback=http://yourweb.com
```

- Thu hồi tất cả tokens của user
- Xóa SSO session
- Ghi log hoạt động đăng xuất
- Chuyển về callback URL

---

## 🔑 Tích Hợp Vào Web Client

### Ví dụ PHP đơn giản

```php
<?php
session_start();

// Lấy SSO session từ callback
$ssoSession = $_GET['sso_session'] ?? null;

if ($ssoSession) {
    // Xác thực với SSO API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/api/sso/verify-session");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['sso_session' => $ssoSession]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if ($data['success']) {
        $_SESSION['user_id'] = $data['data']['user_id'];
        $_SESSION['user_name'] = $data['data']['user_name'];
        $_SESSION['user_email'] = $data['data']['user_email'];
        $_SESSION['jwt_token'] = $data['data']['jwt_token'];
        $_SESSION['logged_in'] = true;
        
        // Redirect về URL sạch
        header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}

// Hiển thị
if (isset($_SESSION['logged_in'])) {
    echo "Chào mừng, " . htmlspecialchars($_SESSION['user_name']) . "!<br>";
    echo "Email: " . htmlspecialchars($_SESSION['user_email']) . "<br>";
    echo '<a href="http://localhost:8000/logout?callback=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '">Đăng xuất</a>';
} else {
    echo '<a href="http://localhost:8000/sso/verify?callback=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '">Đăng nhập với SSO</a>';
}
?>
```

---

## 📡 API Endpoints

### 1. Xác thực SSO Session

**Phương thức GET:**
```bash
GET /api/sso/verify-session?session_token={token}

Response:
{
  "authenticated": true,
  "user_id": 1,
  "user_name": "Admin",
  "user_email": "admin@gmail.com",
  "jwt_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "login_time": "2025-10-14T16:35:21+00:00"
}
```

**Phương thức POST:**
```bash
POST /api/sso/verify-session
Content-Type: application/json

{
  "sso_session": "{token}"
}

Response:
{
  "success": true,
  "data": {
    "user_id": 1,
    "user_name": "Admin",
    "user_email": "admin@gmail.com",
    "jwt_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "authenticated": true
  }
}
```

### 2. User API (Cần xác thực)

**Lấy thông tin user hiện tại:**
```bash
GET /api/user
Authorization: Bearer {jwt_token}
Accept: application/json

Response:
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin",
    "email": "admin@gmail.com",
    "role": "admin",
    "email_verified_at": "2025-10-14T16:00:00.000000Z",
    "created_at": "2025-10-14T16:00:00.000000Z"
  }
}
```

**Lấy profile user:**
```bash
GET /api/user/profile
Authorization: Bearer {jwt_token}
Accept: application/json

Response:
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin",
    "email": "admin@gmail.com",
    "bio": "This is a sample bio for Admin",
    "registered_at": "2025-10-14T16:00:00.000000Z"
  }
}
```

---

## 🎯 Tính Năng Bảng Điều Khiển

### Trang chủ (`/`)

**Thẻ thống kê:**
- Tổng số Users
- Tổng số Tokens
- Tokens đang hoạt động

**Các mục:**
1. **Danh sách Users** - Tất cả users đã đăng ký với vai trò
2. **Hoạt động đăng nhập** - 10 sự kiện login/register/logout gần nhất
   - Loại hành động (Login/Register/Logout)
   - Callback URL
   - Địa chỉ IP
   - Thời gian
3. **Tokens đang hoạt động** - Tất cả tokens chưa hết hạn, chưa bị thu hồi
   - Thông tin user
   - Token ID
   - Ngày tạo/hết hạn
   - Số ngày còn lại

### Quản lý User

**Tạo User mới (`/users/create`)**
- Tạo user với JWT token tự động
- Phân quyền (Admin/Member)
- Token hiển thị ngay để test với Postman

**Xem Tokens của User (`/users/{id}/tokens`)**
- Thống kê user (Tổng Tokens, Tokens hoạt động, Số lần đăng nhập)
- Danh sách API Tokens
- Lịch sử hoạt động đăng nhập

---

## 📊 Hệ Thống Ghi Log Đăng Nhập

### Những gì được ghi lại

Mọi hành động login/register/logout đều được ghi với:
- User ID, Email, Tên
- Callback URL (nếu có)
- Địa chỉ IP
- User Agent (Thông tin trình duyệt)
- Loại hành động (login/register/logout)
- Trạng thái (success/failed)
- Session token
- Thời gian

### Nơi xem

1. **Bảng điều khiển Trang chủ** - 10 hoạt động gần nhất
2. **Trang Tokens của User** - Lịch sử hoạt động theo user

### Bảng Database

```sql
login_logs:
- id
- user_id (foreign key)
- email
- user_name
- callback_url
- ip_address
- user_agent
- action (enum: login, register, logout)
- status (enum: success, failed)
- session_token
- login_at
- created_at
- updated_at
```

---

## 🎨 Hệ Thống Phân Quyền

### Các vai trò

- **admin** - Toàn quyền (huy hiệu đỏ)
- **member** - User thông thường (huy hiệu xanh)

### Hiển thị vai trò

Vai trò được hiển thị bằng huy hiệu màu trong toàn hệ thống:
- Danh sách users
- Trang tokens của user
- Hoạt động đăng nhập

---

## 🧪 Kiểm Tra

### Test với Postman

**Bước 1: Tạo User và lấy Token**
1. Truy cập: `http://localhost:8000/users/create`
2. Điền form và submit
3. Copy JWT token từ trang thành công

**Bước 2: Test API**
```
Method: GET
URL: http://localhost:8000/api/user
Headers:
  Authorization: Bearer {paste_jwt_token_here}
  Accept: application/json

Kết quả mong đợi: 200 OK với dữ liệu user
```

### Test quy trình SSO

**Bước 1: Đăng nhập với Callback**
```
http://localhost:8000/login?callback=http://localhost:8000/callback-demo.html
```

**Bước 2: Nhập thông tin đăng nhập**
- Email: `admin@gmail.com`
- Password: `password123`

**Bước 3: Kiểm tra Redirect**
- Sẽ chuyển về callback URL
- URL chứa các tham số `sso_session` và `status`

**Bước 4: Kiểm tra Dashboard**
- Truy cập `http://localhost:8000`
- Xem mục "Login Activity"
- Sẽ thấy sự kiện đăng nhập với callback URL

---

## 🔧 Cấu Hình

### Thời gian hết hạn Token

Mặc định token hết hạn sau **6 tháng** (182 ngày).

Để thay đổi, chỉnh sửa `app/Providers/AppServiceProvider.php`:

```php
Passport::tokensExpireIn(now()->addDays(182));
Passport::refreshTokensExpireIn(now()->addDays(365));
Passport::personalAccessTokensExpireIn(now()->addDays(182));
```

### Thời gian hết hạn Session Token

SSO session tokens hết hạn sau **5 phút**.

Để thay đổi, chỉnh sửa login/register controllers:

```php
\Cache::put('sso_session_' . $sessionToken, [...], now()->addMinutes(5));
```

---

## 📖 Cách Hoạt Động

### Sơ đồ quy trình SSO Login

```
┌─────────────┐      ┌─────────────┐      ┌──────────────┐
│  Client Web │      │ SSO Server  │      │  Your Web    │
└─────────────┘      └─────────────┘      └──────────────┘
       │                     │                     │
       │  1. Redirect đến    │                     │
       │     /sso/verify     │                     │
       ├────────────────────>│                     │
       │                     │                     │
       │  2. Kiểm tra trạng  │                     │
       │     thái đăng nhập  │                     │
       │                     │                     │
       │  3. Hiện form login │                     │
       │     (nếu chưa login)│                     │
       │                     │                     │
       │  4. User đăng nhập  │                     │
       │                     │                     │
       │  5. Tạo tokens      │                     │
       │     - JWT token     │                     │
       │     - Session token │                     │
       │                     │                     │
       │  6. Ghi log vào DB  │                     │
       │                     │                     │
       │  7. Redirect với    │                     │
       │     session token   │                     │
       │     callback?sso_   │                     │
       │     session=XXX     │                     │
       │<────────────────────┤                     │
       │                     │                     │
       │  8. Nhận session    │                     │
       │     token           │                     │
       ├─────────────────────┼────────────────────>│
       │                     │                     │
       │  9. Xác thực session│                     │
       │     /api/sso/       │                     │
       │     verify-session  │                     │
       │<────────────────────┼─────────────────────┤
       │                     │                     │
       │  10. Trả về dữ liệu │                     │
       │      user + JWT     │                     │
       ├─────────────────────┼────────────────────>│
       │                     │                     │
       │  11. Tạo session    │                     │
       │      local          │                     │
       │                     │                     │
       │  ✅ Đã đăng nhập!   │                     │
       └─────────────────────┴─────────────────────┘
```

### Các loại Token

1. **JWT Token (Access Token)**
   - Có hiệu lực 6 tháng
   - Dùng để xác thực API
   - Lưu trong bảng `oauth_access_tokens`
   - Ví dụ: `eyJ0eXAiOiJKV1QiLCJhbGc...`

2. **Session Token**
   - Có hiệu lực 5 phút
   - Dùng để xác thực callback
   - Lưu trong Laravel cache
   - Sử dụng 1 lần
   - Ví dụ: `abc123def456...`

---

## 🎯 Trang Demo

Các trang demo có sẵn để test:

1. **`/callback-demo.html`** - Trang callback HTML đơn giản
2. **`/callback-with-session.php`** - Callback PHP với session
3. **`/your-web-callback.php`** - Ví dụ tích hợp đầy đủ

---

## 🛠️ Xử Lý Lỗi

### Lỗi: Token hết hạn ngay lập tức

**Giải pháp:**
1. Xóa tất cả sessions: `DELETE FROM sessions;`
2. Xóa browser cache (Ctrl+Shift+Delete)
3. Khởi động lại Laravel server

### Lỗi: 401 Unauthenticated với token hợp lệ

**Giải pháp:**
1. Kiểm tra `config/auth.php` có `api` guard:
```php
'api' => [
    'driver' => 'passport',
    'provider' => 'users',
    'hash' => false,
],
```
2. Đảm bảo token được gửi trong header: `Authorization: Bearer {token}`

### Lỗi: Callback không hoạt động

**Giải pháp:**
1. Kiểm tra callback URL đã được encode đúng
2. Xác minh SSO session token có trong URL
3. Kiểm tra session token chưa hết hạn (5 phút)

---

## 📁 Cấu Trúc Project

```
sso-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── SessionController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── HomeController.php
│   │   │   └── UserManagementController.php
│   │   └── Responses/
│   │       └── PassportAuthorizationViewResponse.php
│   ├── Models/
│   │   ├── LoginLog.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_oauth_*_tables.php
│   │   ├── ..._add_role_to_users_table.php
│   │   └── ..._create_login_logs_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── login-success.blade.php
│       │   └── register-success.blade.php
│       ├── users/
│       │   ├── create.blade.php
│       │   ├── token-created.blade.php
│       │   └── tokens.blade.php
│       └── home.blade.php
├── routes/
│   ├── api.php
│   └── web.php
└── public/
    ├── callback-demo.html
    ├── callback-with-session.php
    └── your-web-callback.php
```

---

## 🤝 Đóng Góp

Mọi đóng góp đều được chào đón! Vui lòng tạo Pull Request.

---

## 📄 License

Dự án này được phân phối dưới giấy phép MIT.

---

## 👨‍💻 Tác Giả

**dophu17**

- GitHub: [@dophu17](https://github.com/dophu17)
- Repository: [sso-laravel](https://github.com/dophu17/sso-laravel)

---

## 🎉 Cảm Ơn

- Laravel Framework
- Laravel Passport
- Tailwind CSS (cho giao diện)

---

**Được xây dựng với ❤️ sử dụng Laravel 11 & Passport**
