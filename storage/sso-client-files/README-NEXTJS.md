# 🚀 NextJS SSO Client Integration

## 📋 **Overview**

NextJS client có thể sử dụng các API endpoints SSO để authenticate với Laravel SSO server. Hỗ trợ cả **client-side** và **server-side** authentication.

## 🛠️ **Installation**

```bash
cd nextjs-example/
npm install
```

## ⚙️ **Configuration**

Tạo file `.env.local`:
```env
NEXT_PUBLIC_SSO_SERVER=http://localhost:8000
NEXT_PUBLIC_SESSION_COOKIE=laravel_session
```

## 🏗️ **Architecture**

### **1. SSO Service** (`lib/sso.js`)
- ✅ Universal authentication service (client + server)
- ✅ Automatic cookie parsing
- ✅ API integration với Laravel SSO
- ✅ Error handling và logging

### **2. React Hook** (`hooks/useAuth.js`)
- ✅ `useAuth()` hook cho components
- ✅ Authentication context provider
- ✅ `withAuth()` HOC cho protected pages
- ✅ Loading states và error handling

### **3. Middleware** (`middleware.js`)
- ✅ Automatic route protection
- ✅ Server-side authentication check
- ✅ Redirect to SSO login
- ✅ User headers injection

## 🔄 **Authentication Methods**

### **Method 1: Middleware Protection**
```javascript
// middleware.js automatically protects routes
const protectedRoutes = ['/dashboard', '/profile', '/admin'];

// Users are automatically redirected to SSO login
// if not authenticated on protected routes
```

### **Method 2: React Hook**
```javascript
import { useAuth } from '../hooks/useAuth';

function MyComponent() {
    const { user, authenticated, loading, login, logout } = useAuth();
    
    if (loading) return <div>Loading...</div>;
    
    if (!authenticated) {
        return <button onClick={() => login()}>Login</button>;
    }
    
    return <div>Welcome {user.user_name}!</div>;
}
```

### **Method 3: HOC Protection**
```javascript
import { withAuth } from '../hooks/useAuth';

function ProtectedPage() {
    return <div>This page requires authentication</div>;
}

export default withAuth(ProtectedPage);
```

### **Method 4: Server-Side (getServerSideProps)**
```javascript
export async function getServerSideProps(context) {
    const ssoService = (await import('../lib/sso')).default;
    
    const authResult = await ssoService.checkAuth(context);
    
    if (!authResult.authenticated) {
        return {
            redirect: {
                destination: ssoService.getLoginUrl(context.resolvedUrl),
                permanent: false,
            },
        };
    }
    
    return {
        props: {
            user: authResult.user,
        },
    };
}
```

## 🎯 **Usage Examples**

### **1. Public Page with Auth Check**
```javascript
// pages/index.js
import { useAuth } from '../hooks/useAuth';

export default function Home() {
    const { user, authenticated, loading } = useAuth();
    
    if (loading) return <div>Loading...</div>;
    
    return (
        <div>
            {authenticated ? (
                <h1>Welcome back, {user.user_name}!</h1>
            ) : (
                <h1>Please log in</h1>
            )}
        </div>
    );
}
```

### **2. Protected Page (Middleware)**
```javascript
// pages/dashboard.js - Automatically protected by middleware
export default function Dashboard() {
    return <div>Protected dashboard content</div>;
}
```

### **3. Protected Page (HOC)**
```javascript
// pages/profile.js
import { withAuth } from '../hooks/useAuth';

function Profile() {
    return <div>Profile page</div>;
}

export default withAuth(Profile);
```

### **4. API Route with Auth**
```javascript
// pages/api/protected-data.js
import ssoService from '../../lib/sso';

export default async function handler(req, res) {
    const authResult = await ssoService.checkAuth({
        headers: req.headers
    });
    
    if (!authResult.authenticated) {
        return res.status(401).json({ error: 'Not authenticated' });
    }
    
    res.json({
        message: `Hello ${authResult.user.user_name}`,
        user: authResult.user
    });
}
```

## 🔄 **Authentication Flow**

```
1. User visits NextJS page
   ↓
2. Middleware checks authentication
   ↓
3a. If authenticated → Allow access
   ↓
3b. If not authenticated → Redirect to SSO login
   ↓
4. User logs in at Laravel SSO server
   ↓
5. SSO server sets session cookie (.domain.com)
   ↓
6. Redirect back to NextJS app
   ↓
7. NextJS reads cookie → API call → User authenticated ✅
```

## 🛡️ **Security Features**

- ✅ **Cookie Domain Sharing**: `.your-domain.com`
- ✅ **Server-Side Verification**: API calls to SSO server
- ✅ **Automatic Redirects**: Seamless login/logout flow
- ✅ **Protected Routes**: Middleware-based protection
- ✅ **Error Handling**: Graceful fallbacks

## 🚀 **Running the Example**

```bash
# Start Laravel SSO Server
cd /path/to/sso-laravel
php artisan serve # http://localhost:8000

# Start NextJS Client (in another terminal)
cd nextjs-example/
npm run dev # http://localhost:3000
```

## 📁 **File Structure**

```
nextjs-example/
├── lib/
│   └── sso.js              # SSO service library
├── hooks/
│   └── useAuth.js          # React authentication hooks
├── pages/
│   ├── _app.js             # App with AuthProvider
│   ├── index.js            # Home page (public)
│   ├── dashboard.js        # Protected page (middleware)
│   └── profile.js          # Protected page (SSR)
├── middleware.js           # NextJS middleware
├── package.json           # Dependencies
└── env.example            # Environment variables
```

## 🔧 **Advanced Configuration**

### **Custom Protected Routes**
```javascript
// middleware.js
const protectedRoutes = [
    '/dashboard',
    '/profile',
    '/admin',
    '/settings',
    // Add more routes as needed
];
```

### **Role-Based Access**
```javascript
// In your components
const { user } = useAuth();

if (user.user_role === 'admin') {
    // Show admin content
}
```

### **Custom Login/Logout URLs**
```javascript
// In your components
const { login, logout } = useAuth();

// Custom redirect URLs
login('/dashboard');
logout('/');
```

## 🎯 **Key Benefits**

- ✅ **Universal Authentication**: Works on both client and server
- ✅ **Automatic Protection**: Middleware handles route protection
- ✅ **React Integration**: Hooks and HOCs for easy use
- ✅ **SSR Support**: Server-side authentication checks
- ✅ **Type Safety**: Can be extended with TypeScript
- ✅ **Performance**: Efficient cookie-based authentication

**NextJS client hoàn toàn có thể sử dụng API endpoints SSO với nhiều cách tích hợp linh hoạt!** 🚀
