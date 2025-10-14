<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use Illuminate\Http\Response;

class AuthorizationController extends Controller
{
    protected $clients;

    public function __construct(ClientRepository $clients)
    {
        $this->clients = $clients;
    }

    /**
     * Show OAuth authorization page
     */
    public function authorize(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login', [
                'client_id' => $request->get('client_id'),
                'redirect_uri' => $request->get('redirect_uri'),
                'state' => $request->get('state'),
            ]);
        }

        $clientId = $request->get('client_id');
        $client = $this->clients->find($clientId);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        return view('oauth.authorize', [
            'client' => $client,
            'request' => $request,
        ]);
    }

    /**
     * Handle authorization approval
     */
    public function approveAuthorization(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Forward to Passport's authorization server
        return redirect('/oauth/authorize?' . http_build_query($request->all()));
    }
}
