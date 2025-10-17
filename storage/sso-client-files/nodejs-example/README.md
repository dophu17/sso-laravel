# 🟢 NodeJS SSO Client

## 📋 **Overview**

NodeJS client integration với Laravel SSO Server sử dụng API endpoints.

## 🚀 **Quick Start**

### **Installation:**
```bash
npm install
```

### **Configuration:**
Tạo file `.env`:
```env
SSO_SERVER=https://auth.your-domain.com
SESSION_COOKIE=laravel_session
PORT=3000
```

### **Run:**
```bash
npm start
```

## 📁 **Files**

- `nodejs-example.js` - Complete Express.js application với SSO integration
- `package.json` - Dependencies và scripts
- `README.md` - This file

## 🔧 **Usage**

### **Basic SSO Service:**
```javascript
const ssoService = new SSOService({
    ssoServer: 'https://auth.your-domain.com',
    sessionCookieName: 'laravel_session'
});

// Check authentication
const authResult = await ssoService.checkAuth(req);
if (authResult.authenticated) {
    console.log('User:', authResult.user.user_name);
}
```

### **Express Middleware:**
```javascript
app.get('/dashboard', requireAuth(ssoService), (req, res) => {
    res.json({ message: `Welcome ${req.user.user_name}!` });
});
```

## 🎯 **Features**

- ✅ Complete Express.js application
- ✅ SSO authentication middleware
- ✅ Automatic redirect to login/logout
- ✅ User session management
- ✅ Error handling và logging

## 📚 **Documentation**

Xem `../README-API.md` để biết chi tiết về API endpoints.
