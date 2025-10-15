<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application homepage
     * 
     * Simple homepage for Session Sharing SSO
     * Shows user management for admins
     */
    public function index()
    {
        // Get all users with recent login info
        $users = User::orderBy('created_at', 'desc')->get();
        
        // Get statistics
        $stats = [
            'total_users' => $users->count(),
            'active_sessions' => DB::table('sessions')->whereNotNull('user_id')->count(),
            'total_logins' => \App\Models\LoginLog::where('action', 'login')->count(),
            'recent_logins' => \App\Models\LoginLog::where('action', 'login')
                ->orderBy('login_at', 'desc')
                ->take(5)
                ->get(),
        ];
        
        return view('home', compact('users', 'stats'));
    }
}

