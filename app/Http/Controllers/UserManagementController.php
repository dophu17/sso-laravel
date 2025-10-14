<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;

class UserManagementController extends Controller
{
    /**
     * Show create user form
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store new user and create tokens
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,member',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            // Create Personal Access Token
            $tokenResult = $user->createToken('Postman API Token', ['*']);
            
            // IMPORTANT: Get JWT token IMMEDIATELY
            // accessToken contains the JWT string - this is what works with Postman!
            $jwtToken = $tokenResult->accessToken; // This is a JWT string!
            
            // Prepare token info to pass to view
            $tokenInfo = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'jwt_token' => $jwtToken,  // JWT token for Postman - THIS WORKS!
                'created_at' => now(),
            ];

            // Store in session to display on next page
            return redirect()->route('users.show.token', $user->id)
                ->with('token_info', $tokenInfo);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show token immediately after creation
     * This is the ONLY place where plainTextToken is available
     */
    public function showToken($userId)
    {
        $user = User::findOrFail($userId);
        
        // Get token info from session (includes plainTextToken)
        $tokenInfo = session('token_info');
        
        if (!$tokenInfo) {
            return redirect()->route('home')
                ->with('error', 'Token information not found. Please create user again.');
        }
        
        return view('users.token-created', compact('user', 'tokenInfo'));
    }
    
    /**
     * Show user tokens (old tokens, without plainTextToken)
     */
    public function showTokens($userId)
    {
        $user = User::findOrFail($userId);
        
        // Get all active Personal Access Tokens
        $personalTokens = Token::where('user_id', $user->id)
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.tokens', compact('user', 'personalTokens'));
    }
}

