<?php

namespace App\Http\Middleware;

use App\Services\SSOService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SSOAuthenticate
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->ssoService->isAuthenticated()) {
            return $next($request);
        }

        return redirect($this->ssoService->getCheckUrl());
    }
}