/**
 * NodeJS SSO Client Example
 * 
 * This example shows how to integrate with Laravel SSO server
 * from a NodeJS/Express application
 */

const axios = require('axios');
const cookie = require('cookie');
const express = require('express');

class SSOService {
    constructor(config) {
        this.ssoServer = config.ssoServer || 'https://auth.your-domain.com';
        this.sessionCookieName = config.sessionCookieName || 'your_session_name';
        this.timeout = config.timeout || 10000;
    }

    /**
     * Check if user is authenticated by calling SSO API
     */
    async checkAuth(req) {
        try {
            // Parse cookies from request
            const cookies = cookie.parse(req.headers.cookie || '');
            const sessionId = cookies[this.sessionCookieName];

            if (!sessionId) {
                console.log('No session cookie found');
                return { authenticated: false };
            }

            console.log('Checking SSO authentication...', { sessionId: sessionId.substring(0, 10) + '...' });

            // Call SSO server to verify session
            const response = await axios.post(`${this.ssoServer}/api/sso/verify-session`, {
                // Empty body - session info comes from cookie
            }, {
                headers: {
                    'Cookie': `${this.sessionCookieName}=${sessionId}`,
                    'Content-Type': 'application/json',
                    'User-Agent': req.headers['user-agent'] || 'NodeJS-SSO-Client',
                },
                timeout: this.timeout,
            });

            if (response.data.success) {
                console.log('SSO authentication successful', {
                    userId: response.data.data.user_id,
                    userName: response.data.data.user_name
                });
                
                return {
                    authenticated: true,
                    user: response.data.data
                };
            }

            console.log('SSO authentication failed', response.data);
            return { authenticated: false };

        } catch (error) {
            console.error('SSO Check Error:', error.message);
            
            if (error.response) {
                console.error('Response status:', error.response.status);
                console.error('Response data:', error.response.data);
            }
            
            return { authenticated: false };
        }
    }

    /**
     * Get login URL for redirect
     */
    getLoginUrl(redirectUrl) {
        const url = new URL(`${this.ssoServer}/login`);
        url.searchParams.set('redirect', redirectUrl);
        return url.toString();
    }

    /**
     * Get logout URL for redirect
     */
    getLogoutUrl(redirectUrl) {
        const url = new URL(`${this.ssoServer}/logout`);
        url.searchParams.set('redirect', redirectUrl);
        return url.toString();
    }

    /**
     * Get server info
     */
    async getServerInfo() {
        try {
            const response = await axios.get(`${this.ssoServer}/api/sso/server-info`);
            return response.data;
        } catch (error) {
            console.error('Failed to get server info:', error.message);
            return null;
        }
    }
}

// Express.js Middleware
function requireAuth(ssoService) {
    return async (req, res, next) => {
        const authResult = await ssoService.checkAuth(req);
        
        if (authResult.authenticated) {
            req.user = authResult.user;
            next();
        } else {
            // Redirect to SSO login
            const loginUrl = ssoService.getLoginUrl(req.originalUrl);
            console.log('Redirecting to SSO login:', loginUrl);
            res.redirect(loginUrl);
        }
    };
}

// Example Express.js Application
function createApp() {
    const app = express();
    
    // Initialize SSO service
    const ssoService = new SSOService({
        ssoServer: process.env.SSO_SERVER || 'http://localhost:8000',
        sessionCookieName: process.env.SESSION_COOKIE || 'laravel_session',
        timeout: 10000
    });

    // Middleware
    app.use(express.json());
    app.use(express.urlencoded({ extended: true }));

    // Public routes
    app.get('/', (req, res) => {
        res.send(`
            <h1>NodeJS SSO Client</h1>
            <p><a href="/dashboard">Go to Dashboard (Protected)</a></p>
            <p><a href="/server-info">Server Info</a></p>
            <p><a href="/test-auth">Test Auth</a></p>
        `);
    });

    // Server info route
    app.get('/server-info', async (req, res) => {
        try {
            const serverInfo = await ssoService.getServerInfo();
            res.json({
                success: true,
                data: serverInfo
            });
        } catch (error) {
            res.status(500).json({
                success: false,
                error: error.message
            });
        }
    });

    // Test auth route
    app.get('/test-auth', async (req, res) => {
        const authResult = await ssoService.checkAuth(req);
        res.json(authResult);
    });

    // Protected routes
    app.get('/dashboard', requireAuth(ssoService), (req, res) => {
        res.json({
            message: `Welcome to Dashboard, ${req.user.user_name}!`,
            user: req.user,
            timestamp: new Date().toISOString()
        });
    });

    app.get('/profile', requireAuth(ssoService), (req, res) => {
        res.json({
            message: 'User Profile',
            user: req.user
        });
    });

    // Logout route
    app.get('/logout', (req, res) => {
        const logoutUrl = ssoService.getLogoutUrl('/');
        res.redirect(logoutUrl);
    });

    return app;
}

// Export for use in other files
module.exports = {
    SSOService,
    requireAuth,
    createApp
};

// Run example if this file is executed directly
if (require.main === module) {
    const app = createApp();
    const port = process.env.PORT || 3000;
    
    app.listen(port, () => {
        console.log(`NodeJS SSO Client running on http://localhost:${port}`);
        console.log(`SSO Server: ${process.env.SSO_SERVER || 'http://localhost:8000'}`);
        console.log(`Session Cookie: ${process.env.SESSION_COOKIE || 'laravel_session'}`);
    });
}
