<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    /**
     * Show edit user form (admin only)
     */
    public function edit(User $user)
    {
        // Check if current user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Prevent editing other admins
        if ($user->role === 'admin') {
            return redirect()->route('home')->with('error', 'Không thể chỉnh sửa tài khoản admin khác.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update user (admin only)
     */
    public function update(Request $request, User $user)
    {
        // Check if current user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        // Prevent editing other admins
        if ($user->role === 'admin') {
            return redirect()->route('home')->with('error', 'Không thể chỉnh sửa tài khoản admin khác.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:member,admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        \Illuminate\Support\Facades\Log::info('Admin updated user', [
            'admin_id' => Auth::id(),
            'updated_user_id' => $user->id,
            'updated_user_email' => $user->email
        ]);

        return redirect()->route('home')->with('success', "User '{$user->name}' đã được cập nhật thành công!");
    }

    /**
     * Delete user (admin only)
     */
    public function destroy(User $user)
    {
        // Check if current user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        // Prevent deleting other admins
        if ($user->role === 'admin') {
            return redirect()->route('home')->with('error', 'Không thể xóa tài khoản admin khác.');
        }

        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return redirect()->route('home')->with('error', 'Không thể xóa chính mình.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        \Illuminate\Support\Facades\Log::info('Admin deleted user', [
            'admin_id' => Auth::id(),
            'deleted_user_id' => $user->id,
            'deleted_user_email' => $user->email
        ]);

        $user->delete();

        return redirect()->route('home')->with('success', "User '{$userName}' đã được xóa thành công!");
    }
}