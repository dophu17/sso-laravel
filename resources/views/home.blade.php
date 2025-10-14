@extends('layouts.app')

@section('title', 'SSO Server - Trang chủ')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Hệ thống Single Sign-On (SSO)</h1>
        <p class="text-xl text-gray-600">Đăng nhập một lần, truy cập mọi nơi</p>
        
        <div class="mt-6">
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tạo User Mới với Tokens
            </a>
        </div>
    </div>

    {{-- Success/Error Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @auth
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Xin chào, {{ Auth::user()->name }}!</h2>
                <p class="text-gray-600 mb-4">Bạn đã đăng nhập thành công vào hệ thống SSO</p>
                <div class="bg-gray-50 rounded p-4 text-left">
                    <p class="text-sm text-gray-600"><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-600"><strong>Tài khoản được tạo:</strong> {{ Auth::user()->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    @endauth

    {{-- Statistics --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tổng Users</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Tokens</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_tokens'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Tokens</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_tokens'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Users List --}}
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Danh sách Users</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Tokens</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $userActiveTokens = $activeTokens->where('user_id', $user->id)->count();
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($userActiveTokens > 0) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                    {{ $userActiveTokens }} token(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('users.tokens', $user->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Xem Tokens →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Chưa có users nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Active Tokens --}}
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Active Tokens (Đang hoạt động)</h2>
            <p class="text-sm text-gray-600 mt-1">Các token chưa hết hạn và chưa bị thu hồi</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token ID (first 20 chars)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tạo lúc</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hết hạn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian còn lại</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activeTokens as $token)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 text-xs font-bold">{{ $token->user ? strtoupper(substr($token->user->name, 0, 1)) : '?' }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $token->user->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $token->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $token->client->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ substr($token->client_id ?? '', 0, 20) }}...</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ substr($token->id, 0, 20) }}...</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $token->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $token->expires_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $daysLeft = now()->diffInDays($token->expires_at);
                                    $hoursLeft = now()->diffInHours($token->expires_at) % 24;
                                @endphp
                                <span class="text-sm 
                                    @if($daysLeft > 7) text-green-600 
                                    @elseif($daysLeft > 3) text-yellow-600 
                                    @else text-orange-600 
                                    @endif">
                                    {{ $daysLeft }} ngày {{ $hoursLeft }}h
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Chưa có token nào đang active</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activeTokens->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Tổng <strong>{{ $activeTokens->count() }}</strong> token(s) đang hoạt động
                </p>
            </div>
        @endif
    </div>


    {{-- Features Grid --}}
    <div class="grid md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Bảo mật cao</h3>
            <p class="text-gray-600 text-sm">Sử dụng OAuth 2.0 và Laravel Passport để đảm bảo an toàn tuyệt đối cho tài khoản của bạn</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nhanh chóng</h3>
            <p class="text-gray-600 text-sm">Đăng nhập một lần, truy cập tất cả các ứng dụng được kết nối mà không cần đăng nhập lại</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Dễ dàng tích hợp</h3>
            <p class="text-gray-600 text-sm">API đơn giản và tài liệu đầy đủ giúp bạn tích hợp SSO vào ứng dụng chỉ trong vài phút</p>
        </div>
    </div>

    {{-- Documentation --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hướng dẫn tích hợp</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
            <li>Đăng ký ứng dụng OAuth Client với SSO Server</li>
            <li>Lấy Client ID và Client Secret</li>
            <li>Tích hợp OAuth 2.0 authorization code flow vào ứng dụng của bạn</li>
            <li>Sử dụng access token để truy cập API và lấy thông tin user</li>
        </ol>
        <div class="mt-4">
            <a href="#documentation" class="text-blue-600 hover:text-blue-800 font-medium">Xem tài liệu đầy đủ →</a>
        </div>
    </div>
</div>
@endsection
