<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\LoginLog;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle(Request $request)
    {
        // Store redirect parameters in session for later use
        $request->session()->put('oauth_redirect', $request->get('redirect'));
        $request->session()->put('oauth_client_id', $request->get('client_id'));
        $request->session()->put('oauth_redirect_uri', $request->get('redirect_uri'));
        $request->session()->put('oauth_state', $request->get('state'));
        
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists with this Google ID
            $existingUser = User::where('google_id', $googleUser->getId())->first();
            
            if ($existingUser) {
                // User exists with Google ID
                Auth::login($existingUser);
                
                // Log the login
                $this->logLogin($existingUser, $request, 'google_login');
                
            } else {
                // Check if user exists with same email
                $userByEmail = User::where('email', $googleUser->getEmail())->first();
                
                if ($userByEmail) {
                    // Link Google account to existing user
                    $userByEmail->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                    ]);
                    
                    Auth::login($userByEmail);
                    
                    // Log the login
                    $this->logLogin($userByEmail, $request, 'google_login');
                    
                } else {
                    // Create new user
                    $newUser = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'password' => Hash::make(uniqid()), // Random password for Google users
                        'role' => 'user',
                        'email_verified_at' => now(),
                    ]);
                    
                    Auth::login($newUser);
                    
                    // Log the login
                    $this->logLogin($newUser, $request, 'google_register');
                }
            }
            
            // Redirect based on stored parameters
            $redirectUrl = $this->getRedirectUrl($request);
            
            return redirect($redirectUrl);
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
            
            return redirect()->route('login')
                ->withErrors(['error' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.']);
        }
    }
    
    /**
     * Log the login activity
     */
    private function logLogin($user, $request, $action)
    {
        LoginLog::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'user_name' => $user->name,
            'callback_url' => $request->session()->get('oauth_redirect'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => $action,
            'status' => 'success',
            'login_at' => now(),
        ]);
    }
    
    /**
     * Get redirect URL from session or default
     */
    private function getRedirectUrl($request)
    {
        $redirectUrl = $request->session()->get('oauth_redirect');
        
        // Clear OAuth session data
        $request->session()->forget([
            'oauth_redirect',
            'oauth_client_id', 
            'oauth_redirect_uri',
            'oauth_state'
        ]);
        
        if ($redirectUrl) {
            return $redirectUrl;
        }
        
        return route('home');
    }
}