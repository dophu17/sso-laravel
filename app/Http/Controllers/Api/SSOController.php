<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class SSOController extends Controller
{
    /**
     * Verify SSO session for external clients (NodeJS, etc.)
     * 
     * This endpoint allows external applications to verify if a user
     * is authenticated by checking the session cookie
     */
    public function verifySession(Request $request)
    {
        try {
            // Get session cookie name from config
            $sessionCookieName = config('session.cookie');
            
            // Get session ID from cookie
            $sessionId = $request->cookie($sessionCookieName);
            
            Log::info('SSO API Verification Request', [
                'session_cookie_name' => $sessionCookieName,
                'has_session_cookie' => !empty($sessionId),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);

            // Check if user is authenticated
            if (Auth::check()) {
                $user = Auth::user();
                
                $responseData = [
                    'success' => true,
                    'data' => [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'user_role' => $user->role ?? 'user',
                        'authenticated' => true,
                        'login_time' => $user->created_at->toIso8601String(),
                        'session_id' => $sessionId,
                    ]
                ];

                Log::info('SSO API Verification Success', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);

                return response()->json($responseData);

            } else {
                // User not authenticated
                Log::info('SSO API Verification Failed - Not Authenticated', [
                    'session_id' => $sessionId,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated',
                    'error_code' => 'NOT_AUTHENTICATED'
                ], 401);
            }

        } catch (\Exception $e) {
            Log::error('SSO API Verification Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error_code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

    /**
     * Get user information (for authenticated requests)
     */
    public function getUser(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role ?? 'user',
                'created_at' => $user->created_at->toIso8601String(),
                'updated_at' => $user->updated_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * Check if session is valid (lightweight check)
     */
    public function checkSession(Request $request)
    {
        $isAuthenticated = Auth::check();
        
        return response()->json([
            'success' => true,
            'authenticated' => $isAuthenticated,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get SSO server information for client configuration
     */
    public function getServerInfo(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'server_name' => config('app.name'),
                'version' => '1.0.0',
                'session_cookie_name' => config('session.cookie'),
                'session_domain' => config('session.domain'),
                'login_url' => route('login'),
                'logout_url' => route('logout'),
                'verify_endpoint' => route('api.sso.verify'),
                'timestamp' => now()->toIso8601String(),
            ]
        ]);
    }
}
