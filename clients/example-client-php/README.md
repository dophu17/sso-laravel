# Example Client Application (Web A)

Đây là ứng dụng client ví dụ để demo tích hợp SSO với Laravel Passport OAuth2 Server.

## Cài đặt

### Bước 1: Tạo OAuth Client trên SSO Server

Truy cập SSO Server và chạy command sau để tạo OAuth client:

```bash
php artisan passport:client
```

Chọn loại client: `Authorization code grant client`
- Redirect URL: `http://localhost:3000/callback.php`

Bạn sẽ nhận được:
- Client ID
- Client Secret

### Bước 2: Cấu hình Client App

Mở file `index.php`, `login.php`, và `callback.php`, thay thế các giá trị sau:

```php
'client_id' => 'YOUR_CLIENT_ID',
'client_secret' => 'YOUR_CLIENT_SECRET',
```

### Bước 3: Chạy Client App

Sử dụng PHP built-in server:

```bash
cd clients/example-client-php
php -S localhost:3000
```

### Bước 4: Test SSO

1. Mở trình duyệt và truy cập: `http://localhost:3000`
2. Click "Đăng nhập với SSO Server"
3. Bạn sẽ được redirect đến SSO Server để login
4. Sau khi login, bạn sẽ được redirect về Client App
5. Thông tin user sẽ được hiển thị

## Cách hoạt động

### OAuth 2.0 Authorization Code Flow (Auto-Redirect)

1. **Auto-Redirect**: User truy cập client → tự động redirect đến SSO Server
2. **User Authentication**: User đăng nhập trên SSO Server
3. **Authorization Grant**: SSO Server redirect về client với authorization code
4. **Token Exchange**: Client exchange authorization code để lấy access token
5. **Success Message**: Hiển thị "✓ Đăng nhập thành công!"
6. **API Access**: Client sử dụng access token để lấy thông tin user

**Lưu ý:** Không cần click button "Đăng nhập" - hệ thống tự động redirect!

## Files

- `index.php` - Trang chủ, tự động redirect nếu chưa login, hiển thị thông báo success/failed
- `callback.php` - Xử lý callback từ SSO Server, exchange code cho token
- `logout.php` - Đăng xuất và clear session
- `login.php` - (Optional) Manual redirect đến SSO Server
- `debug.php` - 🔍 **Debug page** - Xem config, session, test API connection

## Requirements

- PHP 7.4 hoặc cao hơn
- cURL extension
- Session support

## Security Notes

- Luôn sử dụng HTTPS trong production
- Bảo mật Client Secret
- Validate state parameter để prevent CSRF
- Set session timeout hợp lý

