<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SessionController extends Controller
{
    /**
     * Verify SSO session token and return user data (POST method)
     * 
     * This allows callback URLs to verify login status
     */
    public function verifySession(Request $request)
    {
        $sessionToken = $request->input('sso_session');
        
        if (!$sessionToken) {
            return response()->json([
                'success' => false,
                'message' => 'SSO session token is required'
            ], 400);
        }
        
        // Get session data from cache
        $sessionData = Cache::get('sso_session_' . $sessionToken);
        
        if (!$sessionData) {
            return response()->json([
                'success' => false,
                'message' => 'SSO session not found or expired'
            ], 404);
        }
        
        // Return session data (including JWT token)
        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $sessionData['user_id'],
                'user_name' => $sessionData['user_name'],
                'user_email' => $sessionData['user_email'],
                'jwt_token' => $sessionData['jwt_token'],
                'login_time' => $sessionData['login_time'] ?? $sessionData['register_time'] ?? null,
                'authenticated' => $sessionData['authenticated'],
            ]
        ]);
    }
    
    /**
     * Verify SSO session token (GET method - simpler for quick checks)
     */
    public function verifySessionGet(Request $request)
    {
        $sessionToken = $request->query('session_token') ?? $request->query('sso_session');
        
        if (!$sessionToken) {
            return response()->json([
                'authenticated' => false,
                'message' => 'SSO session token is required'
            ], 400);
        }
        
        // Get session data from cache
        $sessionData = Cache::get('sso_session_' . $sessionToken);
        
        if (!$sessionData) {
            return response()->json([
                'authenticated' => false,
                'message' => 'SSO session not found or expired'
            ], 404);
        }
        
        // Return session data
        return response()->json(array_merge(
            ['authenticated' => true],
            $sessionData
        ));
    }
}

