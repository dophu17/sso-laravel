<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\LoginLog;

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
     * SSO Check Login - Login 1 lần, truy cập nhiều nơi
     * 
     * Flow:
     * 1. Client gọi: /sso/verify?callback=URL
     * 2. Nếu đã login → tạo session token → redirect về callback
     * 3. Nếu chưa login → redirect đến /login
     * 4. Login xong → tự động redirect về callback với token
     * 5. Client verify token → lấy user info → Done!
     */
    public function verifySessionGet(Request $request)
    {
        $callbackUrl = $request->query('callback');
        
        // Kiểm tra nếu user đã authenticated (SSO - đã login ở nơi khác)
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Tạo JWT token cho session này (User model has HasApiTokens trait)
            $tokenResult = $user->createToken('SSO Session Token', ['*']);
            $jwtToken = $tokenResult->accessToken;
            
            // Tạo session token (dùng để verify)
            $sessionToken = Str::random(64);
            
            // Lưu session data vào cache (5 phút)
            Cache::put('sso_session_' . $sessionToken, [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'jwt_token' => $jwtToken,
                'login_time' => now()->toIso8601String(),
                'authenticated' => true,
            ], now()->addMinutes(5));
            
            // Log SSO verification
            LoginLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'user_name' => $user->name,
                'callback_url' => $callbackUrl,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => 'sso_verify',
                'status' => 'success',
                'session_token' => $sessionToken,
                'login_at' => now(),
            ]);
            
            // Redirect về callback với token
            if ($callbackUrl) {
                $separator = parse_url($callbackUrl, PHP_URL_QUERY) ? '&' : '?';
                return redirect($callbackUrl . $separator . 'sso_session=' . $sessionToken);
            }
            
            // Nếu không có callback, trả về JSON với token
            return response()->json([
                'authenticated' => true,
                'sso_session' => $sessionToken,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);
        }
        
        // Chưa login → redirect đến trang login với callback
        if ($callbackUrl) {
            return redirect()->route('login', ['callback' => $callbackUrl]);
        }
        
        // Không có callback → trả về lỗi
        return response()->json([
            'authenticated' => false,
            'message' => 'Not authenticated. Please provide callback URL to login.',
            'example' => '/api/sso/verify-session?callback=http://your-app/callback'
        ], 401);
    }
}

