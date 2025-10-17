<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSOService
{
    protected $ssoServer;
    protected $callbackUrl;

    public function __construct()
    {
        $this->ssoServer = config('sso.server', env('SSO_SERVER'));
        $this->callbackUrl = config('sso.callback_url', env('SSO_CALLBACK_URL'));
    }

    /**
     * Get SSO check URL
     */
    public function getCheckUrl(): string
    {
        return $this->ssoServer . '/api/sso/verify-session?callback=' . urlencode($this->callbackUrl);
    }

    /**
     * Verify SSO session token
     */
    public function verifyToken(string $sessionToken): ?array
    {
        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post($this->ssoServer . '/api/sso/verify-session', [
                    'sso_session' => $sessionToken
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success']) {
                    return $data['data'];
                }
            }

            Log::error('SSO verification failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('SSO verification exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get SSO logout URL
     */
    public function getLogoutUrl(): string
    {
        return $this->ssoServer . '/logout?callback=' . urlencode($this->callbackUrl);
    }

    /**
     * Store user in session
     */
    public function storeUserSession(array $userData): void
    {
        session([
            'sso_user' => [
                'id' => $userData['user_id'],
                'name' => $userData['user_name'],
                'email' => $userData['user_email'],
                'jwt_token' => $userData['jwt_token'] ?? null,
                'login_time' => $userData['login_time'] ?? now()->toIso8601String(),
                'authenticated' => true,
            ]
        ]);
    }

    /**
     * Get user from session
     */
    public function getUser(): ?array
    {
        return session('sso_user');
    }

    /**
     * Check if authenticated
     */
    public function isAuthenticated(): bool
    {
        $user = $this->getUser();
        return $user && isset($user['authenticated']) && $user['authenticated'];
    }

    /**
     * Clear session
     */
    public function clearSession(): void
    {
        session()->forget('sso_user');
    }
}