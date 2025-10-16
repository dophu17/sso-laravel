# 🔧 Environment Configuration Guide

## 📋 **Environment Variables Overview**

Hệ thống SSO đã được refactor để sử dụng environment variables thay vì hardcoded values. Điều này giúp dễ dàng chuyển đổi giữa local development và production deployment.

## 🌍 **Environment Variables**

### **Core Application**
```env
APP_NAME="Auth Server"
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000
```

### **SSO Configuration**
```env
# Main domain (without subdomain)
SSO_DOMAIN=your-domain.com

# Auth server URL
SSO_AUTH_URL=http://auth.your-domain.com

# Session sharing configuration
SSO_SESSION_DOMAIN=.your-domain.com
SSO_SESSION_NAME=your_session_name
```

### **Database Configuration**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sso_laravel
DB_USERNAME=root
DB_PASSWORD=your_password
```

### **Session Configuration**
```env
SESSION_DRIVER=database
SESSION_DOMAIN=.your-domain.com
SESSION_COOKIE=your_session_name
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax
```

### **Mail Configuration**
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=ssl
MAIL_FROM="${APP_NAME}"
```

### **Google OAuth Configuration**
```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://auth.your-domain.com/auth/google/callback
```

## 🏠 **Local Development Setup**

### **File: .env**
```env
APP_NAME="SSO Auth Server"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Local domain setup
SSO_DOMAIN=balocco-local.info
SSO_AUTH_URL=http://auth.balocco-local.info
SSO_SESSION_DOMAIN=.balocco-local.info
SSO_SESSION_NAME=balocco_session

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sso_laravel
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_DOMAIN=.balocco-local.info
SESSION_COOKIE=balocco_session
SESSION_LIFETIME=120

# Mail (for password reset)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=dophu17@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=ssl

# Google OAuth
GOOGLE_CLIENT_ID=your_local_google_client_id
GOOGLE_CLIENT_SECRET=your_local_google_client_secret
GOOGLE_REDIRECT_URI=http://auth.balocco-local.info/auth/google/callback
```

### **Local URLs:**
- Auth Server: `http://auth.balocco-local.info`
- Client App 1: `http://patent-monitor.balocco-local.info`
- Client App 2: `http://bookcase.balocco-local.info`

## 🚀 **Production Setup**

### **File: .env**
```env
APP_NAME="SSO Auth Server"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://auth.yourcompany.com

# Production domain setup
SSO_DOMAIN=yourcompany.com
SSO_AUTH_URL=https://auth.yourcompany.com
SSO_SESSION_DOMAIN=.yourcompany.com
SSO_SESSION_NAME=company_session

# Production Database
DB_CONNECTION=mysql
DB_HOST=your-db-host.com
DB_PORT=3306
DB_DATABASE=sso_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Session (HTTPS)
SESSION_DRIVER=database
SESSION_DOMAIN=.yourcompany.com
SESSION_COOKIE=company_session
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

# Production Mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.yourcompany.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourcompany.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls

# Production Google OAuth
GOOGLE_CLIENT_ID=your_production_google_client_id
GOOGLE_CLIENT_SECRET=your_production_google_client_secret
GOOGLE_REDIRECT_URI=https://auth.yourcompany.com/auth/google/callback
```

### **Production URLs:**
- Auth Server: `https://auth.yourcompany.com`
- Client App 1: `https://patent-monitor.yourcompany.com`
- Client App 2: `https://bookcase.yourcompany.com`

## 🔄 **Migration Checklist**

### **From Local to Production:**

1. **Update Domain Variables:**
   ```env
   SSO_DOMAIN=balocco-local.info → yourcompany.com
   SSO_AUTH_URL=http://auth.balocco-local.info → https://auth.yourcompany.com
   SSO_SESSION_DOMAIN=.balocco-local.info → .yourcompany.com
   ```

2. **Update Database:**
   ```env
   DB_HOST=127.0.0.1 → your-db-host.com
   DB_DATABASE=sso_laravel → sso_production
   ```

3. **Update Mail Settings:**
   ```env
   MAIL_HOST=smtp.gmail.com → smtp.yourcompany.com
   MAIL_USERNAME=dophu17@gmail.com → noreply@yourcompany.com
   ```

4. **Update Google OAuth:**
   - Create new OAuth app for production domain
   - Update `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`
   - Update `GOOGLE_REDIRECT_URI`

5. **Enable HTTPS:**
   ```env
   SESSION_SECURE_COOKIE=true
   APP_URL=https://auth.yourcompany.com
   ```

## 🛠️ **Configuration Files Updated**

### **Files Modified:**
- ✅ `.env.example` - Template với placeholder values
- ✅ `config/services.php` - Google OAuth redirect URL
- ✅ `app/Http/Controllers/Auth/LoginController.php` - Domain validation
- ✅ `README.md` - Documentation với placeholder domains
- ✅ `GOOGLE_OAUTH_SETUP.md` - Setup guide với placeholder domains

### **Key Changes:**
- ✅ **Hardcoded domains removed** from all files
- ✅ **Environment variables** used throughout
- ✅ **Placeholder values** in documentation
- ✅ **Flexible configuration** for different environments

## 🔍 **Validation**

### **Check Domain Configuration:**
```bash
# Check current configuration
php artisan tinker
echo 'SSO_DOMAIN: ' . env('SSO_DOMAIN');
echo 'SSO_AUTH_URL: ' . env('SSO_AUTH_URL');
echo 'SESSION_DOMAIN: ' . env('SESSION_DOMAIN');
```

### **Test Session Sharing:**
```bash
# Clear config cache after changes
php artisan config:clear
php artisan cache:clear

# Test login flow
# 1. Login at auth.your-domain.com
# 2. Check session in database
# 3. Visit client.your-domain.com
# 4. Verify Auth::check() returns true
```

## 📝 **Notes**

- ✅ **Security**: All sensitive values are in environment variables
- ✅ **Flexibility**: Easy to switch between environments
- ✅ **Documentation**: Clear examples for local and production
- ✅ **Maintenance**: Single place to update domain configurations

**Bây giờ bạn có thể dễ dàng deploy từ local sang production chỉ bằng cách thay đổi environment variables!** 🚀
