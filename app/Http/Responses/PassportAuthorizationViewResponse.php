<?php

namespace App\Http\Responses;

use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Laravel\Passport\ClientRepository;

class PassportAuthorizationViewResponse implements AuthorizationViewResponse
{
    protected $parameters = [];

    public function withParameters(array $parameters = []): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function toResponse($request)
    {
        // AUTO-APPROVE: Tự động approve authorization
        // Tạo form auto-submit để approve ngay lập tức
        
        $authToken = $this->parameters['authToken'] ?? null;
        
        return response()->view('oauth.auto-approve', [
            'authToken' => $authToken,
            'state' => $request->get('state'),
            'clientId' => $request->get('client_id'),
            'redirectUri' => $request->get('redirect_uri'),
            'responseType' => $request->get('response_type'),
            'scope' => $request->get('scope', ''),
        ]);
    }
}

