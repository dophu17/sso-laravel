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
                </div>
            </div>
            <a href="{{ route('home') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                ← Về trang chủ
            </a>
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
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Token Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($personalTokens as $token)
                            <tr>
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
                                        $daysLeft = now()->diffInDays($token->expires_at);
                                    @endphp
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                        Active ({{ $daysLeft }} days)
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
