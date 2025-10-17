/**
 * NextJS SSO Client Library
 * 
 * This library provides SSO authentication functionality for NextJS applications
 */

class SSOService {
    constructor(config) {
        this.ssoServer = config.ssoServer || 'https://auth.your-domain.com';
        this.sessionCookieName = config.sessionCookieName || 'laravel_session';
        this.apiTimeout = config.timeout || 10000;
    }

    /**
     * Check authentication status
     * Can be used in both client-side and server-side
     */
    async checkAuth(context = null) {
        try {
            let cookies = '';
            
            // Get cookies from different contexts
            if (typeof window !== 'undefined') {
                // Client-side: use document.cookie
                cookies = document.cookie;
            } else if (context && context.req) {
                // Server-side (getServerSideProps): use request headers
                cookies = context.req.headers.cookie || '';
            } else if (context && context.headers) {
                // API route or middleware: use headers directly
                cookies = context.headers.cookie || '';
            }

            if (!cookies) {
                return { authenticated: false };
            }

            // Parse session cookie
            const sessionId = this.parseCookie(cookies, this.sessionCookieName);
            
            if (!sessionId) {
                return { authenticated: false };
            }

            // Call SSO API to verify session
            const response = await fetch(`${this.ssoServer}/api/sso/verify-session`, {
                method: 'POST',
                headers: {
                    'Cookie': `${this.sessionCookieName}=${sessionId}`,
                    'Content-Type': 'application/json',
                },
                credentials: 'include', // Include cookies in request
            });

            if (!response.ok) {
                return { authenticated: false };
            }

            const data = await response.json();
            
            if (data.success) {
                return {
                    authenticated: true,
                    user: data.data
                };
            }

            return { authenticated: false };

        } catch (error) {
            console.error('SSO Check Error:', error);
            return { authenticated: false };
        }
    }

    /**
     * Parse cookie value from cookie string
     */
    parseCookie(cookieString, name) {
        const value = `; ${cookieString}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
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
     * Get server information
     */
    async getServerInfo() {
        try {
            const response = await fetch(`${this.ssoServer}/api/sso/server-info`);
            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Failed to get server info:', error);
            return null;
        }
    }

    /**
     * Redirect to SSO login
     */
    redirectToLogin(redirectUrl) {
        const loginUrl = this.getLoginUrl(redirectUrl);
        
        if (typeof window !== 'undefined') {
            window.location.href = loginUrl;
        }
    }

    /**
     * Redirect to SSO logout
     */
    redirectToLogout(redirectUrl) {
        const logoutUrl = this.getLogoutUrl(redirectUrl);
        
        if (typeof window !== 'undefined') {
            window.location.href = logoutUrl;
        }
    }
}

// Create default instance
const ssoService = new SSOService({
    ssoServer: process.env.NEXT_PUBLIC_SSO_SERVER || 'http://localhost:8000',
    sessionCookieName: process.env.NEXT_PUBLIC_SESSION_COOKIE || 'laravel_session',
});

export default ssoService;
export { SSOService };
