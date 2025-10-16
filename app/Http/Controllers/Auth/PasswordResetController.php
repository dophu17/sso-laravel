<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Mail\PasswordResetMail;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotPasswordForm(Request $request)
    {
        $redirect = $request->get('redirect');
        $client_id = $request->get('client_id');
        $redirect_uri = $request->get('redirect_uri');
        $state = $request->get('state');
        
        return view('auth.forgot-password', compact('redirect', 'client_id', 'redirect_uri', 'state'));
    }

    /**
     * Send password reset email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email này không tồn tại trong hệ thống.',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email này không tồn tại trong hệ thống.']);
        }

        // Generate reset token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addMinutes(60); // Token expires in 1 hour

        // Store or update token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send email
        try {
            Mail::to($user)->send(new PasswordResetMail($user, $token, $request->get('redirect')));
            
            return back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn!');
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Show the reset password form
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->get('email');
        $redirect = $request->get('redirect');
        
        // Verify token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', Carbon::now()->subHours(1)) // Token valid for 1 hour
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return redirect()->route('password.forgot')->withErrors(['token' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }

        return view('auth.reset-password', compact('token', 'email', 'redirect'));
    }

    /**
     * Reset the password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $email = $request->email;
        $token = $request->token;

        // Verify token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', Carbon::now()->subHours(1))
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return back()->withErrors(['token' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }

        // Update user password
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Người dùng không tồn tại.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Log the password reset
        \App\Models\LoginLog::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'user_name' => $user->name,
            'callback_url' => $request->get('redirect'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => 'password_reset',
            'status' => 'success',
            'login_at' => now(),
        ]);

        // If redirect URL is provided, redirect back to client
        $redirectUrl = $request->input('redirect');
        if ($redirectUrl) {
            return redirect($redirectUrl)->with('success', 'Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập với mật khẩu mới.');
        }

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập với mật khẩu mới.');
    }
}