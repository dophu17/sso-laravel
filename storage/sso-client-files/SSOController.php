<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SSOController extends Controller
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Handle SSO callback
     */
    public function callback(Request $request)
    {
        $sessionToken = $request->query('sso_session');
        
        if (!$sessionToken) {
            Log::warning('SSO callback without session token');
            return redirect('/')->with('error', 'SSO authentication failed: No session token');
        }

        $userData = $this->ssoService->verifyToken($sessionToken);

        if (!$userData) {
            Log::error('SSO token verification failed');
            return redirect('/')->with('error', 'SSO authentication failed: Invalid token');
        }

        $this->ssoService->storeUserSession($userData);

        Log::info('SSO login successful', [
            'user_id' => $userData['user_id'],
            'user_email' => $userData['user_email']
        ]);

        return redirect()->intended('/dashboard')->with('success', 'Đăng nhập thành công!');
    }

    /**
     * Handle SSO logout
     */
    public function logout(Request $request)
    {
        $user = $this->ssoService->getUser();
        
        $this->ssoService->clearSession();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user) {
            Log::info('SSO logout', ['user_id' => $user['id']]);
        }

        return redirect($this->ssoService->getLogoutUrl());
    }
}