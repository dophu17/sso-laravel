# 🔐 SSO Laravel - Single Sign-On System

Complete SSO (Single Sign-On) system built with Laravel 11 and Passport, featuring JWT tokens, callback URLs, and cross-domain session management.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## ✨ Features

- 🔐 **JWT Token Authentication** - Secure token-based authentication
- 🌐 **Cross-Domain Support** - Works across different domains and ports
- 🔄 **Callback URL System** - Redirect back to your web after login
- 🎯 **Session Management** - Shared session across multiple applications
- 📊 **Admin Dashboard** - View users, tokens, and OAuth clients
- 🚀 **API Ready** - RESTful API endpoints for integration
- 💻 **Demo Pages** - Ready-to-use demo pages included
- 📝 **Complete Documentation** - 10+ detailed guide documents

---

## 🚀 Quick Start

### Requirements

- PHP 8.2+
- MySQL/MariaDB
- Composer
- Laravel 11

### Installation

```bash
# Clone repository
git clone https://github.com/dophu17/sso-laravel.git
cd sso-laravel

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Generate Passport keys
php artisan passport:keys --force
php artisan passport:client --personal --name="Personal Access Client"

# Start server
php artisan serve
```

Visit: `http://localhost:8000`

---

## 📋 Usage

### 1. Login with Callback

```
http://localhost:8000/login?callback=http://yourweb.com
```

**Flow:**
1. User logs in
2. SSO creates JWT token + session token
3. Redirects to: `http://yourweb.com?sso_session=abc123&status=success`
4. Your web verifies session and creates local session

### 2. Register with Callback

```
http://localhost:8000/register?callback=http://yourweb.com
```

Same flow as login, but for new user registration.

### 3. SSO Verify (Auto Login)

```
http://localhost:8000/sso/verify?callback=http://yourweb.com
```

- If not logged in → Auto redirect to login
- If logged in → Redirect to callback with session token

### 4. Logout with Callback

```
http://localhost:8000/logout?callback=http://yourweb.com
```

- Revokes all user tokens
- Clears SSO session
- Redirects to callback URL

---

## 🔑 Client Web Integration

### Simple Example

```php
<?php
session_start();

// Get SSO session from callback
$ssoSession = $_GET['sso_session'] ?? null;

if ($ssoSession) {
    // Verify with SSO
    $url = "http://localhost:8000/sso/verify?session_token=" . urlencode($ssoSession);
    $response = @file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data['authenticated']) {
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['user_name'] = $data['user_name'];
        $_SESSION['jwt_token'] = $data['jwt_token'];
        $_SESSION['logged_in'] = true;
    }
}

// Display
if (isset($_SESSION['logged_in'])) {
    echo "Welcome, " . $_SESSION['user_name'] . "!";
    echo '<a href="http://localhost:8000/logout?callback=' . urlencode($_SERVER['REQUEST_URI']) . '">Logout</a>';
} else {
    echo '<a href="http://localhost:8000/sso/verify?callback=' . urlencode($_SERVER['REQUEST_URI']) . '">Login with SSO</a>';
}
?>
```

---

## 📡 API Endpoints

### Session Verification

```bash
# GET method
GET /api/sso/verify-session?session_token={token}

# POST method
POST /api/sso/verify-session
Content-Type: application/json
{
  "sso_session": "{token}"
}
```

### User API (with JWT Token)

```bash
GET /api/user
Authorization: Bearer {jwt_token}
```

---

## 🎯 Demo Pages

### 1. Callback Demo (HTML)
```
http://localhost:8000/callback-demo.html
```

### 2. Callback with Session (PHP)
```
http://localhost:8000/callback-with-session.php
```

### 3. Your Web Callback Example
```
http://localhost:8000/your-web-callback.php
```

---

## 📚 Documentation

Complete guides available in repository:

- `SSO_COMPLETE_GUIDE.md` - Complete system guide
- `CALLBACK_URL_FEATURE.md` - Callback URL documentation
- `SSO_SESSION_SOLUTION.md` - Session management guide
- `LOGIN_WITH_JWT_TOKEN.md` - JWT token guide
- `LOGOUT_API_COMPLETE.md` - Logout API documentation

---

## 🎨 Features Overview

### Authentication
- ✅ Login with email/password
- ✅ User registration
- ✅ Auto JWT token generation
- ✅ Token-based API access

### Token Management
- ✅ JWT tokens (6 months validity)
- ✅ Personal Access Tokens
- ✅ OAuth Access Tokens
- ✅ Token revocation on logout

### Session Management
- ✅ SSO session tokens (5 min validity)
- ✅ Cross-domain session support
- ✅ Session verification API
- ✅ Callback URL system

### Admin Dashboard
- ✅ User statistics
- ✅ Active tokens list
- ✅ OAuth clients management
- ✅ Create new users with tokens

---

## 🔧 Configuration

### Default Users

After setup, create demo users:

```php
// User A
Email: user_a@gmail.com
Password: password123

// Admin
Email: admin@test.com
Password: password
```

### OAuth Client

Create OAuth client for testing:

```bash
Client ID: 1c17afc9-6f69-4f42-8a48-7121659977c0
Client Secret: bakH3OwfDwwigTrIbszsPfJG6BiXFUncdoyYy88L
Redirect URI: http://localhost/client-app/callback.php
```

---

## 🧪 Testing

### Test with Postman

```
Method: GET
URL: http://localhost:8000/api/user
Headers:
  Authorization: Bearer {jwt_token}
  Accept: application/json

Expected Response: 200 OK
{
  "success": true,
  "data": {
    "id": 1,
    "name": "User A",
    "email": "user_a@gmail.com"
  }
}
```

### Test SSO Flow

1. **Login:** `http://localhost:8000/sso/verify?callback=http://localhost:8000/callback-demo.html`
2. **Enter credentials:** user_a@gmail.com / password123
3. **Verify redirect** to demo page with session token
4. **Check session** data displayed

---

## 📖 How It Works

### SSO Login Flow

```
┌─────────────┐      ┌─────────────┐      ┌──────────────┐
│  Client Web │      │ SSO Server  │      │  Your Web    │
└─────────────┘      └─────────────┘      └──────────────┘
       │                     │                     │
       │  1. Redirect to     │                     │
       │     /sso/verify     │                     │
       ├────────────────────>│                     │
       │                     │                     │
       │  2. Show login      │                     │
       │     form            │                     │
       │                     │                     │
       │  3. User login      │                     │
       │                     │                     │
       │  4. Create tokens   │                     │
       │     (JWT + session) │                     │
       │                     │                     │
       │  5. Redirect with   │                     │
       │     session token   │                     │
       │<────────────────────┤                     │
       │                     │                     │
       │  6. Verify session  │                     │
       ├────────────────────>│                     │
       │                     │                     │
       │  7. Return user     │                     │
       │     data + JWT      │                     │
       │<────────────────────┤                     │
       │                     │                     │
       │  8. Create session  │                     │
       │     on your web     │                     │
       │                     │                     │
       │  ✅ User logged in! │                     │
       └─────────────────────┴─────────────────────┘
```

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📄 License

This project is open-sourced software licensed under the MIT license.

---

## 👨‍💻 Author

**dophu17**

- GitHub: [@dophu17](https://github.com/dophu17)
- Repository: [sso-laravel](https://github.com/dophu17/sso-laravel)

---

## 🎉 Acknowledgments

- Laravel Framework
- Laravel Passport
- Tailwind CSS (for UI)

---

**Built with ❤️ using Laravel 11 & Passport**
