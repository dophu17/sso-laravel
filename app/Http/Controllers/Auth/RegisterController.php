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
        $redirect = $request->get('redirect'); // For session sharing redirect
        
        return view('auth.register', compact('client_id', 'redirect_uri', 'state', 'redirect'));
    }

    /**
     * Handle registration request
     * 
     * Session Sharing Approach:
     * - Create user and auto-login
     * - Session automatically shared across all subdomains
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

        // Auto-login after registration
        Auth::login($user);
        $request->session()->regenerate();
        
        // Log registration
        \App\Models\LoginLog::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'user_name' => $user->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => 'register',
            'status' => 'success',
            'login_at' => now(),
        ]);
        
        // Store registration info for success page
        $registerInfo = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'register_time' => now(),
        ];

        // Check if redirect URL is provided (from client apps)
        $redirectUrl = $request->input('redirect');
        
        if ($redirectUrl) {
            // Validate redirect URL (security: only allow same domain)
            $parsedUrl = parse_url($redirectUrl);
            
            if (isset($parsedUrl['host']) && str_ends_with($parsedUrl['host'], 'balocco-local.info')) {
                \Illuminate\Support\Facades\Log::info('SSO Register - Redirecting back to client', [
                    'user_id' => $user->id,
                    'redirect_url' => $redirectUrl
                ]);
                return redirect($redirectUrl);
            } else {
                \Illuminate\Support\Facades\Log::warning('SSO Register - Invalid redirect URL', [
                    'redirect_url' => $redirectUrl,
                    'parsed_host' => $parsedUrl['host'] ?? 'none'
                ]);
            }
        }

        // Default: redirect to profile (since user is already logged in)
        return redirect()->intended(route('profile'));
    }
    
}

