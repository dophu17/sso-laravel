# 🐘 PHP SSO Client

## 📋 **Overview**

PHP client integration với Laravel SSO Server. Bao gồm các service classes và middleware để tích hợp SSO vào ứng dụng PHP/Laravel.

## 📁 **Files**

- `sso.php` - SSO configuration file
- `SSOService.php` - Main SSO service class
- `SSOController.php` - API controller for session verification
- `SSOAuthenticate.php` - Middleware for authentication
- `README.md` - This file

## 🚀 **Usage**

### **1. SSO Configuration (`sso.php`)**
```php
return [
    'server' => env('SSO_SERVER', 'https://auth.your-domain.com'),
    'session_key' => env('SESSION_COOKIE', 'laravel_session'),
    'timeout' => 10,
];
```

### **2. SSO Service (`SSOService.php`)**
```php
use App\Services\SSOService;

$ssoService = new SSOService();

// Check authentication
if ($ssoService->isAuthenticated()) {
    $user = $ssoService->getUser();
    echo "Welcome " . $user['user_name'];
}

// Verify token
$userData = $ssoService->verifyToken($sessionToken);
```

### **3. Middleware (`SSOAuthenticate.php`)**
```php
// In routes/web.php
Route::middleware('sso.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### **4. API Controller (`SSOController.php`)**
```php
// API endpoints for external clients
POST /api/sso/verify-session  // Verify session
GET  /api/sso/user           // Get user info
GET  /api/sso/server-info    // Server configuration
```

## 🔧 **Integration**

### **Laravel Application:**
1. Copy files vào `app/` directory
2. Register middleware trong `bootstrap/app.php`
3. Add routes trong `routes/api.php`

### **Standalone PHP Application:**
1. Include `SSOService.php`
2. Use service methods để check authentication
3. Redirect users đến SSO server khi cần

## 🎯 **Features**

- ✅ Laravel SSO service integration
- ✅ Session verification API
- ✅ Authentication middleware
- ✅ Configuration management
- ✅ Error handling và logging

## 📚 **Documentation**

Xem `../README-API.md` để biết chi tiết về API endpoints và integration.
