@extends('layouts.app')

@section('title', 'User Tokens - ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- User Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 text-2xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full mt-1
                        @if($user->role === 'admin') bg-red-100 text-red-800 @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst($user->role ?? 'member') }}
                    </span>
                </div>
            </div>
            <a href="{{ route('home') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                ← Về trang chủ
            </a>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="grid md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-sm text-blue-600 font-medium">Total Tokens</div>
            <div class="text-2xl font-bold text-blue-900">{{ $personalTokens->count() }}</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="text-sm text-green-600 font-medium">Active Tokens</div>
            <div class="text-2xl font-bold text-green-900">{{ $personalTokens->where('revoked', false)->where('expires_at', '>', now())->count() }}</div>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <div class="text-sm text-purple-600 font-medium">Login Count</div>
            <div class="text-2xl font-bold text-purple-900">
                @php
                    $loginCount = \App\Models\LoginLog::where('user_id', $user->id)
                        ->where('action', 'login')
                        ->count();
                @endphp
                {{ $loginCount }}
            </div>
        </div>
        <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
            <div class="text-sm text-orange-600 font-medium">Member Since</div>
            <div class="text-lg font-bold text-orange-900">{{ $user->created_at->format('d/m/Y') }}</div>
        </div>
    </div>

    <!-- Warning -->
    <div class="bg-yellow-50 border border-yellow-400 rounded-lg p-4 mb-6">
        <h3 class="font-bold text-yellow-900 mb-2">⚠️ LƯU Ý:</h3>
        <p class="text-yellow-800">Token plaintext chỉ hiển thị <strong>MỘT LẦN</strong> ngay sau khi tạo user. Trang này chỉ hiển thị token IDs (đã hash) không thể dùng được.</p>
    </div>

    <!-- Personal Access Tokens -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">🔑 API Tokens</h2>
            <p class="text-sm text-gray-600 mt-1">Danh sách tokens đã tạo (token IDs - không thể dùng trực tiếp)</p>
        </div>
        <div class="p-6">
            @if($personalTokens->count() > 0)
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Token ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Token Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($personalTokens as $token)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ substr($token->id, 0, 20) }}...</code>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-900">{{ $token->name }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $token->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $token->expires_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $daysLeft = (int) now()->diffInDays($token->expires_at);
                                        $isActive = !$token->revoked && $token->expires_at > now();
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded 
                                        {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        @if($isActive)
                                            ✓ Active ({{ $daysLeft }} days)
                                        @else
                                            ✗ Expired
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-8">Chưa có token nào</p>
            @endif
        </div>
    </div>

    <!-- Login Activity -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">📊 Login Activity</h2>
            <p class="text-sm text-gray-600 mt-1">Recent login/register/logout history</p>
        </div>
        <div class="overflow-x-auto">
            @php
                $userLogs = \App\Models\LoginLog::where('user_id', $user->id)
                    ->orderBy('login_at', 'desc')
                    ->limit(10)
                    ->get();
            @endphp
            
            @if($userLogs->count() > 0)
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Callback URL</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Browser</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($userLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full 
                                        @if($log->action === 'login') bg-green-100 text-green-800
                                        @elseif($log->action === 'register') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->callback_url)
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded block">{{ Str::limit($log->callback_url, 40) }}</code>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    @php
                                        $ua = $log->user_agent ?? '';
                                        if (str_contains($ua, 'Chrome')) echo 'Chrome';
                                        elseif (str_contains($ua, 'Firefox')) echo 'Firefox';
                                        elseif (str_contains($ua, 'Safari')) echo 'Safari';
                                        elseif (str_contains($ua, 'Edge')) echo 'Edge';
                                        else echo Str::limit($ua, 20);
                                    @endphp
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $log->login_at->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-gray-500">Chưa có activity nào</div>
            @endif
        </div>
        @if($userLogs->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Hiển thị <strong>{{ $userLogs->count() }}</strong> hoạt động gần nhất
                </p>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">💡 Làm sao để lấy token?</h3>
        <p class="text-gray-700 mb-4">Token plaintext (dùng trong Postman) chỉ hiển thị <strong>NGAY SAU KHI TẠO USER</strong>.</p>
        
        <div class="bg-white p-4 rounded">
            <p class="font-medium text-gray-900 mb-2">Để có token mới:</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-700">
                <li>Về trang chủ</li>
                <li>Click "Tạo User Mới với Tokens"</li>
                <li>Tạo user mới</li>
                <li>Token plaintext sẽ hiển thị NGAY sau khi tạo</li>
                <li>Copy và lưu token đó</li>
            </ol>
        </div>
    </div>

    <!-- Action -->
    <div class="mt-6">
        <a href="{{ route('home') }}" class="block w-full bg-gray-600 text-white text-center px-6 py-3 rounded-lg hover:bg-gray-700 font-medium">
            ← Về trang chủ
        </a>
    </div>
</div>
@endsection
