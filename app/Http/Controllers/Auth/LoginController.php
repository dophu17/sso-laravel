<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        $redirect = $request->get('redirect'); // For session sharing redirect
        
        return view('auth.login', compact('client_id', 'redirect_uri', 'state', 'redirect'));
    }

    /**
     * Handle login request
     * 
     * Session Sharing Approach:
     * - Auth::attempt() creates session in database
     * - Session automatically shared across all subdomains via SESSION_DOMAIN
     * - Cookie domain from SESSION_DOMAIN shares session to all apps
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Regenerate session for security
            $request->session()->regenerate();
            
            // Session is now automatically shared across all subdomains!
            // All apps can use Auth::check() to verify login status
            
            $user = Auth::user();
            
            // Check if redirect URL is provided (from client apps)
            $redirectUrl = $request->input('redirect');
            
            // Log successful login
            \App\Models\LoginLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'user_name' => $user->name,
                'callback_url' => $redirectUrl, // Store redirect URL
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => 'login',
                'status' => 'success',
                'login_at' => now(),
            ]);

            // If redirect URL is provided (from client apps)
            if ($redirectUrl) {
                // Validate redirect URL (security: only allow same domain)
                $parsedUrl = parse_url($redirectUrl);
                
                if (isset($parsedUrl['host']) && str_ends_with($parsedUrl['host'], env('SSO_DOMAIN'))) {
                    Log::info('SSO Login - Redirecting back to client', [
                        'user_id' => $user->id,
                        'redirect_url' => $redirectUrl
                    ]);
                    
                    return redirect($redirectUrl);
                } else {
                    Log::warning('SSO Login - Invalid redirect URL', [
                        'redirect_url' => $redirectUrl,
                        'parsed_host' => $parsedUrl['host'] ?? 'none'
                    ]);
                }
            }


            // Default: redirect to profile or home
            return redirect()->intended(route('profile'));
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }


    /**
     * Handle logout request
     * 
     * Session Sharing Approach:
     * - Auth::logout() destroys session in database
     * - Session automatically removed from all subdomains
     * - All apps will see user as logged out
     * - Supports redirect parameter to redirect back to client after logout
     */
    public function logout(Request $request)
    {
        $user = null;
        
        if (Auth::check()) {
            $user = Auth::user();
        }
        
        // Get redirect URL (from client apps)
        $redirectUrl = $request->input('redirect') ?? $request->query('redirect');
        
        // Logout (destroys session in database)
        Auth::logout();
        
        // Invalidate session - this affects all subdomains!
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Log logout
        if ($user) {
            \App\Models\LoginLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'user_name' => $user->name,
                'callback_url' => $redirectUrl, // Store redirect URL
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => 'logout',
                'status' => 'success',
                'login_at' => now(),
            ]);
        }

        // If redirect URL is provided (from client apps)
        if ($redirectUrl) {
            // Validate redirect URL (security: only allow same domain)
            $parsedUrl = parse_url($redirectUrl);
            
            if (isset($parsedUrl['host']) && str_ends_with($parsedUrl['host'], env('SSO_DOMAIN'))) {
                Log::info('SSO Logout - Redirecting back to client', [
                    'user_id' => $user->id ?? null,
                    'redirect_url' => $redirectUrl
                ]);
                
                return redirect($redirectUrl)->with('status', 'Đã đăng xuất thành công!');
            } else {
                Log::warning('SSO Logout - Invalid redirect URL', [
                    'redirect_url' => $redirectUrl,
                    'parsed_host' => $parsedUrl['host'] ?? 'none'
                ]);
            }
        }

        return redirect('/')->with('status', 'Đã đăng xuất thành công!');
    }
    
    /**
     * Handle GET logout request (for easy links)
     */
    public function logoutGet(Request $request)
    {
        return $this->logout($request);
    }
}
