<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Get all users (no error possible here)
        $users = User::orderBy('created_at', 'desc')->get();
        
        // Get active tokens with error handling
        try {
            // Get tokens without eager loading first
            $tokens = Token::where('revoked', false)
                ->where('expires_at', '>', now())
                ->whereNotNull('client_id')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Filter and load relationships manually
            $activeTokens = collect([]);
            foreach ($tokens as $token) {
                try {
                    $user = \App\Models\User::find($token->user_id);
                    $client = \Laravel\Passport\Client::find($token->client_id);
                    
                    if ($user && $client) {
                        $token->user = $user;
                        $token->client = $client;
                        $activeTokens->push($token);
                    }
                } catch (\Exception $e) {
                    // Skip tokens with relationship issues
                    continue;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error loading tokens: ' . $e->getMessage());
            $activeTokens = collect([]);
        }
        
        // Get token statistics
        try {
            $allTokens = Token::count();
            $expiredTokens = Token::where('expires_at', '<', now())->count();
            $revokedTokens = Token::where('revoked', true)->count();
        } catch (\Exception $e) {
            \Log::error('Error loading token stats: ' . $e->getMessage());
            $allTokens = 0;
            $expiredTokens = 0;
            $revokedTokens = 0;
        }
        
        // Get OAuth clients
        try {
            $clients = Client::select('id', 'name', 'redirect_uris', 'created_at')->get();
        } catch (\Exception $e) {
            \Log::error('Error loading clients: ' . $e->getMessage());
            $clients = collect([]);
        }
        
        // Build statistics
        $stats = [
            'total_users' => $users->count(),
            'total_tokens' => $allTokens,
            'active_tokens' => $activeTokens->count(),
            'expired_tokens' => $expiredTokens,
            'revoked_tokens' => $revokedTokens,
            'total_clients' => $clients->count(),
        ];
        
        return view('home', compact('users', 'activeTokens', 'stats', 'clients'));
    }
}

