@extends('layouts.app')

@section('title', 'Token Created - ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Success Message -->
    <div class="bg-green-100 border-2 border-green-500 rounded-lg p-6 mb-6">
        <div class="flex items-center mb-4">
            <svg class="w-12 h-12 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h1 class="text-2xl font-bold text-green-900">✅ User Created Successfully!</h1>
                <p class="text-green-700">Token đã được tạo và sẵn sàng sử dụng</p>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">👤 Thông tin User</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-gray-600">Tên:</span>
                <span class="font-medium ml-2">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-gray-600">Email:</span>
                <span class="font-medium ml-2">{{ $user->email }}</span>
            </div>
        </div>
    </div>

    <!-- Token Display - MOST IMPORTANT -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-4 border-blue-500 rounded-lg p-8 mb-6">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">🔑 API TOKEN</h2>
            <p class="text-lg text-red-600 font-semibold">⚠️ QUAN TRỌNG: Token này chỉ hiển thị MỘT LẦN DUY NHẤT!</p>
            <p class="text-gray-700 mt-2">Copy và lưu lại ngay bây giờ để sử dụng trong Postman</p>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-lg">
            <label class="block text-sm font-bold text-gray-700 mb-3">📋 YOUR JWT API TOKEN (Copy this!):</label>
            <div class="space-y-3">
                <textarea 
                    id="api-token"
                    readonly
                    rows="6"
                    class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg font-mono text-xs bg-gray-50"
                >{{ $tokenInfo['jwt_token'] }}</textarea>
                <button 
                    onclick="copyToken()"
                    class="w-full px-6 py-4 bg-blue-600 text-white font-bold text-lg rounded-lg hover:bg-blue-700 transition shadow-lg"
                >
                    📋 COPY TOKEN TO CLIPBOARD
                </button>
                <p id="copy-status" class="text-green-600 font-semibold text-center hidden">✅ Token copied to clipboard!</p>
            </div>
        </div>
    </div>

    <!-- Postman Guide -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">🚀 Cách sử dụng với Postman</h2>
        
        <div class="space-y-4">
            <!-- Step 1 -->
            <div class="border-l-4 border-blue-500 pl-4">
                <h3 class="font-bold text-gray-900 mb-2">Step 1: Mở Postman</h3>
                <p class="text-gray-700">Tạo request mới</p>
            </div>

            <!-- Step 2 -->
            <div class="border-l-4 border-blue-500 pl-4">
                <h3 class="font-bold text-gray-900 mb-2">Step 2: Cấu hình Request</h3>
                <div class="bg-gray-50 p-3 rounded mt-2">
                    <p><strong>Method:</strong> GET</p>
                    <p><strong>URL:</strong> <code class="bg-gray-200 px-2 py-1 rounded">http://localhost:8000/api/user</code></p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="border-l-4 border-blue-500 pl-4">
                <h3 class="font-bold text-gray-900 mb-2">Step 3: Thêm Headers</h3>
                <div class="bg-gray-50 p-3 rounded mt-2 space-y-1">
                    <p><strong>Authorization:</strong> <code class="bg-gray-200 px-2 py-1 rounded">Bearer {{ substr($tokenInfo['jwt_token'], 0, 30) }}...</code></p>
                    <p><strong>Accept:</strong> <code class="bg-gray-200 px-2 py-1 rounded">application/json</code></p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="border-l-4 border-green-500 pl-4">
                <h3 class="font-bold text-gray-900 mb-2">Step 4: Send Request</h3>
                <p class="text-gray-700">Click "Send" và bạn sẽ nhận được thông tin user!</p>
            </div>
        </div>
    </div>

    <!-- Expected Response -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Expected Response</h2>
        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>{
  "success": true,
  "data": {
    "id": {{ $user->id }},
    "name": "{{ $user->name }}",
    "email": "{{ $user->email }}",
    "created_at": "{{ $user->created_at }}",
    "updated_at": "{{ $user->updated_at }}"
  }
}</code></pre>
    </div>

    <!-- Actions -->
    <div class="flex gap-4">
        <a href="{{ route('home') }}" class="flex-1 bg-gray-600 text-white text-center px-6 py-3 rounded-lg hover:bg-gray-700 font-medium">
            ← Về trang chủ
        </a>
        <button onclick="window.print()" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 font-medium">
            🖨️ In trang này
        </button>
    </div>

    <!-- Warning -->
    <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mt-6">
        <h3 class="font-bold text-yellow-900 mb-2">⚠️ LƯU Ý QUAN TRỌNG:</h3>
        <ul class="list-disc list-inside text-yellow-800 space-y-1">
            <li>Token này chỉ hiển thị <strong>MỘT LẦN DUY NHẤT</strong></li>
            <li>Sau khi rời khỏi trang này, bạn KHÔNG THỂ xem lại token</li>
            <li>Hãy copy và lưu token vào nơi an toàn</li>
            <li>Nếu mất token, bạn phải tạo user mới</li>
        </ul>
    </div>
</div>

<script>
function copyToken() {
    const tokenInput = document.getElementById('api-token');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999); // For mobile
    
    navigator.clipboard.writeText(tokenInput.value).then(function() {
        const status = document.getElementById('copy-status');
        status.classList.remove('hidden');
        
        setTimeout(function() {
            status.classList.add('hidden');
        }, 3000);
    }, function(err) {
        alert('Failed to copy token: ' + err);
    });
}

// Auto-select token on page load
window.addEventListener('DOMContentLoaded', function() {
    const tokenInput = document.getElementById('api-token');
    tokenInput.focus();
    tokenInput.select();
});
</script>
@endsection

