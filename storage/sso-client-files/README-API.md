# 🔐 SSO API Endpoints

## 📋 **Available Endpoints**

### **1. Verify Session** 
```
POST/GET /api/sso/verify-session
```
**Purpose**: Verify if user is authenticated via session cookie

**Headers**:
```
Cookie: your_session_name=session_id_here
Content-Type: application/json
```

**Response - Success** (200):
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "user_name": "John Doe",
    "user_email": "john@example.com",
    "user_role": "admin",
    "authenticated": true,
    "login_time": "2024-01-15T10:30:00Z",
    "session_id": "abc123..."
  }
}
```

**Response - Not Authenticated** (401):
```json
{
  "success": false,
  "message": "Not authenticated",
  "error_code": "NOT_AUTHENTICATED"
}
```

### **2. Check Session** (Lightweight)
```
GET /api/sso/check-session
```
**Purpose**: Quick check if session is valid (no user data)

**Response**:
```json
{
  "success": true,
  "authenticated": true,
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### **3. Get User Info**
```
GET /api/sso/user
```
**Purpose**: Get detailed user information (requires authentication)

**Response**:
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "user_name": "John Doe",
    "user_email": "john@example.com",
    "user_role": "admin",
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  }
}
```

### **4. Server Info**
```
GET /api/sso/server-info
```
**Purpose**: Get SSO server configuration information

**Response**:
```json
{
  "success": true,
  "data": {
    "server_name": "Laravel SSO Server",
    "version": "1.0.0",
    "session_cookie_name": "laravel_session",
    "session_domain": ".your-domain.com",
    "login_url": "http://auth.your-domain.com/login",
    "logout_url": "http://auth.your-domain.com/logout",
    "verify_endpoint": "http://auth.your-domain.com/api/sso/verify-session",
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

## 🚀 **NodeJS Integration Example**

### **Installation**:
```bash
npm install express axios cookie
```

### **Basic Usage**:
```javascript
const axios = require('axios');
const cookie = require('cookie');

class SSOService {
    constructor(ssoServer, sessionCookieName) {
        this.ssoServer = ssoServer;
        this.sessionCookieName = sessionCookieName;
    }

    async checkAuth(req) {
        const cookies = cookie.parse(req.headers.cookie || '');
        const sessionId = cookies[this.sessionCookieName];
        
        if (!sessionId) {
            return { authenticated: false };
        }

        try {
            const response = await axios.post(`${this.ssoServer}/api/sso/verify-session`, {}, {
                headers: {
                    'Cookie': `${this.sessionCookieName}=${sessionId}`,
                    'Content-Type': 'application/json'
                }
            });

            return {
                authenticated: response.data.success,
                user: response.data.data
            };
        } catch (error) {
            return { authenticated: false };
        }
    }
}
```

### **Express Middleware**:
```javascript
const ssoService = new SSOService('https://auth.your-domain.com', 'laravel_session');

async function requireAuth(req, res, next) {
    const authResult = await ssoService.checkAuth(req);
    
    if (authResult.authenticated) {
        req.user = authResult.user;
        next();
    } else {
        res.redirect(`${ssoService.ssoServer}/login?redirect=${encodeURIComponent(req.originalUrl)}`);
    }
}

// Usage
app.get('/dashboard', requireAuth, (req, res) => {
    res.json({ message: `Welcome ${req.user.user_name}!` });
});
```

## 🔧 **Environment Configuration**

### **SSO Server (.env)**:
```env
SESSION_COOKIE=laravel_session
SESSION_DOMAIN=.your-domain.com
SESSION_DRIVER=database
```

### **NodeJS Client (.env)**:
```env
SSO_SERVER=https://auth.your-domain.com
SESSION_COOKIE=laravel_session
```

## 🔄 **Flow Diagram**

```
NodeJS App → Check Session Cookie → Call SSO API → Verify in Database → Return User Data
     ↓              ↓                    ↓              ↓                    ↓
  Request    Parse Cookie         POST /verify     Auth::check()      JSON Response
```

## 🛡️ **Security Notes**

1. **Cookie Domain**: Must be set to `.your-domain.com` for subdomain sharing
2. **Session Storage**: Sessions stored in database for cross-application access
3. **HTTPS**: Use HTTPS in production for secure cookie transmission
4. **Validation**: Always validate redirect URLs to prevent open redirects

## 📝 **Error Codes**

- `NOT_AUTHENTICATED`: User not logged in or session expired
- `INTERNAL_ERROR`: Server error during verification
- `INVALID_SESSION`: Session cookie malformed or invalid

## 🧪 **Testing**

Use the provided NodeJS example:
```bash
cd storage/sso-client-files/
npm install
SSO_SERVER=http://localhost:8000 npm start
```

Visit `http://localhost:3000` to test the integration.
