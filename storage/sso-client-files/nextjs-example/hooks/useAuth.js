/**
 * React Hook for SSO Authentication
 * 
 * Provides authentication state and methods for NextJS components
 */

import { useState, useEffect, useContext, createContext } from 'react';
import ssoService from '../lib/sso';

// Create Auth Context
const AuthContext = createContext();

// Auth Provider Component
export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [authenticated, setAuthenticated] = useState(false);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        checkAuth();
    }, []);

    const checkAuth = async () => {
        try {
            setLoading(true);
            const authResult = await ssoService.checkAuth();
            
            if (authResult.authenticated) {
                setUser(authResult.user);
                setAuthenticated(true);
            } else {
                setUser(null);
                setAuthenticated(false);
            }
        } catch (error) {
            console.error('Auth check failed:', error);
            setUser(null);
            setAuthenticated(false);
        } finally {
            setLoading(false);
        }
    };

    const login = (redirectUrl) => {
        ssoService.redirectToLogin(redirectUrl || window.location.href);
    };

    const logout = (redirectUrl) => {
        ssoService.redirectToLogout(redirectUrl || window.location.origin);
    };

    const value = {
        user,
        authenticated,
        loading,
        checkAuth,
        login,
        logout,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
}

// Hook to use auth context
export function useAuth() {
    const context = useContext(AuthContext);
    
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    
    return context;
}

// HOC for protected pages
export function withAuth(WrappedComponent) {
    return function AuthenticatedComponent(props) {
        const { authenticated, loading, login } = useAuth();
        const [mounted, setMounted] = useState(false);

        useEffect(() => {
            setMounted(true);
        }, []);

        useEffect(() => {
            if (mounted && !loading && !authenticated) {
                login();
            }
        }, [mounted, loading, authenticated, login]);

        if (!mounted || loading) {
            return <div>Loading...</div>;
        }

        if (!authenticated) {
            return <div>Redirecting to login...</div>;
        }

        return <WrappedComponent {...props} />;
    };
}
