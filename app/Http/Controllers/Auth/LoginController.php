<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(Request $request)
    {
        $client_id = $request->get('client_id');
        $redirect_uri = $request->get('redirect_uri');
        $state = $request->get('state');
        
        return view('auth.login', compact('client_id', 'redirect_uri', 'state'));
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Get authenticated user
            $user = Auth::user();
            
            // Create JWT token for this login session
            $tokenResult = $user->createToken('Login Session Token', ['*']);
            $jwtToken = $tokenResult->accessToken; // JWT string
            
            // Store token info in session
            $loginInfo = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'jwt_token' => $jwtToken,
                'login_time' => now(),
            ];

            // Check if callback URL is provided
            if ($request->has('callback')) {
                $callbackUrl = $request->get('callback');
                
                // Create a session token for callback verification
                $sessionToken = \Illuminate\Support\Str::random(64);
                
                // Store login session in cache (shared across domains)
                \Cache::put('sso_session_' . $sessionToken, [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'jwt_token' => $jwtToken,
                    'login_time' => now()->toIso8601String(),
                    'authenticated' => true,
                ], now()->addMinutes(5)); // 5 minutes expiry
                
                // Log login with callback URL
                \App\Models\LoginLog::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'user_name' => $user->name,
                    'callback_url' => $callbackUrl,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'action' => 'login',
                    'status' => 'success',
                    'session_token' => $sessionToken,
                    'login_at' => now(),
                ]);
                
                // Build callback URL with session token
                $params = [
                    'sso_session' => $sessionToken,
                    'status' => 'success',
                ];
                
                // Add query parameters to callback URL
                $separator = parse_url($callbackUrl, PHP_URL_QUERY) ? '&' : '?';
                $redirectUrl = $callbackUrl . $separator . http_build_query($params);
                
                return redirect($redirectUrl);
            }

            // Redirect to OAuth authorization page if parameters exist
            if ($request->has('client_id') && $request->has('redirect_uri')) {
                return redirect()->route('oauth.authorize', [
                    'client_id' => $request->get('client_id'),
                    'redirect_uri' => $request->get('redirect_uri'),
                    'response_type' => 'code',
                    'state' => $request->get('state'),
                    'scope' => '',
                ]);
            }

            // Show token page after login
            return redirect()->route('login.success')
                ->with('login_info', $loginInfo);
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Show login success page with JWT token
     */
    public function showLoginSuccess()
    {
        $loginInfo = session('login_info');
        
        if (!$loginInfo) {
            return redirect()->route('home');
        }
        
        $user = Auth::user();
        
        return view('auth.login-success', compact('user', 'loginInfo'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $callbackUrl = $request->query('callback');
        
        // Revoke all active tokens for this user
        if (Auth::check()) {
            $user = Auth::user();
            
            // Revoke all tokens
            \Laravel\Passport\Token::where('user_id', $user->id)
                ->where('revoked', false)
                ->update(['revoked' => true]);
        }
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // If callback URL is provided, redirect there
        if ($callbackUrl) {
            // Log logout with callback URL
            \App\Models\LoginLog::create([
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? 'unknown',
                'user_name' => $user->name ?? null,
                'callback_url' => $callbackUrl,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => 'logout',
                'status' => 'success',
                'login_at' => now(),
            ]);
            
            $params = [
                'status' => 'logged_out',
                'message' => 'Successfully logged out from SSO',
            ];
            
            $separator = parse_url($callbackUrl, PHP_URL_QUERY) ? '&' : '?';
            $redirectUrl = $callbackUrl . $separator . http_build_query($params);
            
            return redirect($redirectUrl);
        }

        return redirect('/');
    }
    
    /**
     * Handle GET logout request (for easy links)
     */
    public function logoutGet(Request $request)
    {
        return $this->logout($request);
    }
}
