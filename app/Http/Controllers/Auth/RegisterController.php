<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm(Request $request)
    {
        $client_id = $request->get('client_id');
        $redirect_uri = $request->get('redirect_uri');
        $state = $request->get('state');
        
        return view('auth.register', compact('client_id', 'redirect_uri', 'state'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        
        // Create JWT token for this registration
        $tokenResult = $user->createToken('Registration Token', ['*']);
        $jwtToken = $tokenResult->accessToken; // JWT string
        
        // Store registration info in session
        $registerInfo = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'jwt_token' => $jwtToken,
            'register_time' => now(),
        ];

        // Check if callback URL is provided
        if ($request->has('callback')) {
            $callbackUrl = $request->get('callback');
            
            // Create a session token for callback verification
            $sessionToken = \Illuminate\Support\Str::random(64);
            
            // Store registration session in cache (shared across domains)
            \Cache::put('sso_session_' . $sessionToken, [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'jwt_token' => $jwtToken,
                'register_time' => now()->toIso8601String(),
                'authenticated' => true,
            ], now()->addMinutes(5)); // 5 minutes expiry
            
            // Log registration with callback URL
            \App\Models\LoginLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'user_name' => $user->name,
                'callback_url' => $callbackUrl,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => 'register',
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

        // Show token page after registration
        return redirect()->route('register.success')
            ->with('register_info', $registerInfo);
    }
    
    /**
     * Show registration success page with JWT token
     */
    public function showRegisterSuccess()
    {
        $registerInfo = session('register_info');
        
        if (!$registerInfo) {
            return redirect()->route('home');
        }
        
        $user = Auth::user();
        
        return view('auth.register-success', compact('user', 'registerInfo'));
    }
}
