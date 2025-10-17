# 🚀 SSO Client Integration Files

## 📁 **Client Examples**

Thư mục này chứa các ví dụ tích hợp SSO cho các platform khác nhau:

### **🟢 NodeJS Client** (`nodejs-example/`)
- Complete Express.js application với SSO integration
- Authentication middleware và service classes
- Automatic redirect handling

### **⚛️ NextJS Client** (`nextjs-example/`)
- Complete NextJS application với SSO integration
- Hỗ trợ cả client-side và server-side authentication
- React hooks và HOCs cho authentication

### **🐘 PHP/Laravel Client** (`php-example/`)
- SSO service classes cho Laravel applications
- API controller cho session verification
- Middleware cho route protection

## 🚀 **Quick Start**

### **NodeJS:**
```bash
cd nodejs-example/
npm install
npm start
```

### **NextJS:**
```bash
cd nextjs-example/
npm install
npm run dev
```

### **PHP/Laravel:**
```bash
cd php-example/
# Copy files vào Laravel app theo hướng dẫn trong README
```

## 📚 **Documentation**

- `README-API.md` - API endpoints documentation
- `README-NEXTJS.md` - NextJS integration guide
- `nodejs-example/README.md` - NodeJS client guide
- `php-example/README.md` - PHP client guide

## 🎯 **Features**

- ✅ **Multiple Platforms**: NodeJS, NextJS, PHP/Laravel
- ✅ **API Endpoints**: Session verification APIs
- ✅ **Authentication Helpers**: Middleware và services
- ✅ **Configuration Templates**: Ready-to-use configs
- ✅ **Complete Documentation**: Step-by-step guides

## 📖 **API Endpoints**

Tất cả client examples sử dụng các API endpoints sau:

- `POST /api/sso/verify-session` - Verify user session
- `GET /api/sso/user` - Get user information
- `GET /api/sso/server-info` - Get server configuration
- `GET /api/sso/check-session` - Quick session check

Xem `README-API.md` để biết chi tiết về API usage.