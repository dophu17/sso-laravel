# 🔐 Laravel Session Sharing SSO

Hệ thống Single Sign-On sử dụng **Session Sharing** - Đăng nhập 1 lần, truy cập nhiều ứng dụng.

---

## 🌐 Hệ thống

```
Auth Server:  http://auth.balocco-local.info
Client A:     http://patent-monitor.balocco-local.info  (Hệ thống giám sát bằng sáng chế)
Client B:     http://bookcase.balocco-local.info        (Hệ thống quản lý sách)
```

**Đăng nhập 1 lần → Tất cả ứng dụng đều thấy đã đăng nhập! 🚀**

---

## ⚡ Cài đặt nhanh

### 1. Auth Server (Đã sẵn sàng!)

**Cấu hình .env:**
```env
DB_DATABASE=sso_shared
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_SECURE_COOKIE=false
```

**Chạy migrations:**
```bash
php artisan session:table
php artisan migrate
php artisan config:clear
```

**Khởi động:**
```bash
php artisan serve
# Truy cập: http://auth.balocco-local.info
```

### 2. Client Apps (10 phút mỗi app)

**Cấu hình .env cho mỗi client:**
```env
DB_DATABASE=sso_shared  # Cùng database với Auth Server
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_SECURE_COOKIE=false
```

**Chạy migrations:**
```bash
php artisan session:table
php artisan migrate
```

**Tạo middleware CheckSharedSession:**
```php
// app/Http/Middleware/CheckSharedSession.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckSharedSession
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra session trong database
        $sessionId = $request->cookie('balocco_session');
        
        if ($sessionId) {
            $session = DB::table('sessions')
                ->where('id', $sessionId)
                ->where('last_activity', '>', now()->subMinutes(5)->timestamp)
                ->first();
                
            if ($session && $session->user_id) {
                // Tự động đăng nhập user
                $user = \App\Models\User::find($session->user_id);
                if ($user) {
                    Auth::login($user);
                }
            }
        }
        
        return $next($request);
    }
}
```

**Đăng ký middleware trong bootstrap/app.php:**
```php
$middleware->web(append: [
    \App\Http\Middleware\CheckSharedSession::class,
]);
```

---

## 🎯 Tính năng

- ✅ **Chia sẻ phiên đăng nhập** - Đăng nhập 1 lần, tự động đăng nhập tất cả ứng dụng
- ✅ **Quản lý người dùng** - Danh sách người dùng với trạng thái trực tuyến/ngoại tuyến
- ✅ **Đồng bộ thời gian thực** - Đăng xuất được đồng bộ tự động
- ✅ **Hỗ trợ redirect** - Đăng nhập/đăng xuất redirect về client
- ✅ **Theo dõi hoạt động** - Giám sát đăng nhập/đăng xuất
- ✅ **Laravel Native** - Sử dụng `Auth::check()`, `Auth::user()`

---

## 🔄 Cách hoạt động

```
Đăng nhập ở bất kỳ app → Session lưu vào database
                     → Cookie chia sẻ (.balocco-local.info)
                     → Tất cả apps: Auth::check() = true
                     → ✅ Tự động đăng nhập!
```

---

## 🚀 Sử dụng

### Đăng nhập từ Client App
```php
// Trong client app, redirect đến Auth Server
return redirect('http://auth.balocco-local.info/login?redirect=' . urlencode($redirectUrl));
```

### Kiểm tra đăng nhập
```php
// Trong bất kỳ app nào
if (Auth::check()) {
    $user = Auth::user();
    echo "Xin chào, " . $user->name;
}
```

### Đăng xuất
```php
// Đăng xuất khỏi tất cả apps
Auth::logout();
return redirect('http://auth.balocco-local.info/logout?redirect=' . urlencode($redirectUrl));
```

---

## 🐛 Xử lý lỗi thường gặp

### Lỗi 419 "Page Expired"
```env
SESSION_SECURE_COOKIE=false
```
```bash
php artisan config:clear
```

### Session không chia sẻ
- Kiểm tra `SESSION_DOMAIN=.balocco-local.info`
- Kiểm tra cùng database `sso_shared`
- Kiểm tra cookie name `balocco_session`

### Redirect không hoạt động
- Kiểm tra parameter `redirect` trong URL
- Kiểm tra validation redirect URL trong controller

---

## 📊 Công nghệ sử dụng

- **Laravel 11** - Framework chính
- **MySQL/PostgreSQL** - Database chung
- **Session Driver: Database** - Lưu trữ phiên
- **Cookie Domain Sharing** - Chia sẻ cookie

---

## 📁 Cấu trúc thư mục

```
sso-laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── HomeController.php          # Trang chủ + quản lý user
│   │   ├── UserAdminController.php     # Quản lý user (admin)
│   │   ├── ProfileController.php       # Hồ sơ cá nhân
│   │   └── Auth/
│   │       ├── LoginController.php     # Đăng nhập
│   │       └── RegisterController.php  # Đăng ký
│   ├── Models/
│   │   ├── User.php                    # Model người dùng
│   │   └── LoginLog.php                # Log đăng nhập
│   └── Http/Middleware/
│       └── CheckSharedSession.php      # Middleware client (tạo riêng)
├── resources/views/
│   ├── home.blade.php                  # Trang chủ
│   ├── profile.blade.php               # Hồ sơ cá nhân
│   ├── users/edit.blade.php            # Chỉnh sửa user
│   └── auth/
│       ├── login.blade.php             # Đăng nhập
│       └── register.blade.php          # Đăng ký
├── storage/sso-client-files/           # Template cho client apps
└── README.md                           # Tài liệu này
```

---

## 🎯 Quy trình triển khai

1. **Auth Server** - Đã sẵn sàng ✅
2. **Client A** - Cài đặt theo hướng dẫn (10 phút)
3. **Client B** - Cài đặt theo hướng dẫn (10 phút)
4. **Test** - Kiểm tra đăng nhập/logout
5. **Deploy** - Triển khai production

**Thời gian:** ~30 phút tổng cộng

---

## 🔧 Bảo trì

### Backup database
```bash
mysqldump sso_shared > backup.sql
```

### Xóa session cũ
```sql
DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(NOW() - INTERVAL 1 DAY);
```

### Monitor logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📞 Hỗ trợ

- **Tài liệu:** File README.md này
- **Vấn đề:** Kiểm tra phần "Xử lý lỗi thường gặp"
- **Template:** Xem thư mục `storage/sso-client-files/`

---

**Phiên bản:** 1.0 - Session Sharing  
**Cập nhật:** 2025-10-15  
**Ngôn ngữ:** Tiếng Việt

---

**🚀 Bắt đầu ngay: Cài đặt Client Apps theo hướng dẫn trên!**