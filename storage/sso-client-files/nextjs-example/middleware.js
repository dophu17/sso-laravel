/**
 * NextJS Middleware for SSO Authentication
 * 
 * This middleware runs on every request and can redirect users to login
 * if they're not authenticated on protected routes
 */

import { NextResponse } from 'next/server';
import ssoService from './lib/sso';

// Define protected routes (require authentication)
const protectedRoutes = [
    '/dashboard',
    '/profile',
    '/admin',
];

// Define public routes (no authentication required)
const publicRoutes = [
    '/',
    '/login',
    '/about',
];

export async function middleware(request) {
    const { pathname } = request.nextUrl;

    // Skip middleware for API routes, static files, etc.
    if (
        pathname.startsWith('/api/') ||
        pathname.startsWith('/_next/') ||
        pathname.startsWith('/favicon.ico') ||
        pathname.includes('.')
    ) {
        return NextResponse.next();
    }

    // Check if route is protected
    const isProtectedRoute = protectedRoutes.some(route => 
        pathname.startsWith(route)
    );

    const isPublicRoute = publicRoutes.some(route => 
        pathname === route || pathname.startsWith(route)
    );

    // If it's a protected route, check authentication
    if (isProtectedRoute) {
        try {
            const authResult = await ssoService.checkAuth({
                headers: request.headers
            });

            if (!authResult.authenticated) {
                // Redirect to SSO login with return URL
                const loginUrl = ssoService.getLoginUrl(request.url);
                return NextResponse.redirect(loginUrl);
            }

            // Add user info to headers for use in pages
            const response = NextResponse.next();
            response.headers.set('x-user-id', authResult.user.user_id.toString());
            response.headers.set('x-user-name', authResult.user.user_name);
            response.headers.set('x-user-email', authResult.user.user_email);
            
            return response;

        } catch (error) {
            console.error('Middleware auth check failed:', error);
            
            // On error, redirect to login
            const loginUrl = ssoService.getLoginUrl(request.url);
            return NextResponse.redirect(loginUrl);
        }
    }

    // For public routes or other routes, just continue
    return NextResponse.next();
}

export const config = {
    matcher: [
        /*
         * Match all request paths except for the ones starting with:
         * - api (API routes)
         * - _next/static (static files)
         * - _next/image (image optimization files)
         * - favicon.ico (favicon file)
         */
        '/((?!api|_next/static|_next/image|favicon.ico).*)',
    ],
};
