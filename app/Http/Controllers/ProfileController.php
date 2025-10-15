<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user profile page with dashboard info
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get session statistics
        $stats = [
            'session_id' => session()->getId(),
            'session_driver' => config('session.driver'),
            'session_domain' => config('session.domain') ?: '(empty)',
            'session_cookie' => config('session.cookie'),
            'is_authenticated' => Auth::check(),
            'login_time' => now(),
        ];
        
        return view('profile', compact('user', 'stats'));
    }

    /**
     * Update the user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
            }
            
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Thông tin đã được cập nhật thành công!');
    }
}
