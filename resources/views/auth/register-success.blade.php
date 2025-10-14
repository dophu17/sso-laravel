@extends('layouts.app')

@section('title', 'Registration Success - ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Success Message -->
    <div class="bg-gradient-to-r from-green-100 to-blue-100 border-2 border-green-500 rounded-lg p-6 mb-6">
        <div class="flex items-center mb-4">
            <svg class="w-16 h-16 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h1 class="text-3xl font-bold text-green-900">🎉 Đăng ký thành công!</h1>
                <p class="text-green-700 text-xl">Chào mừng {{ $user->name }} đến với SSO Server!</p>
                <p class="text-gray-600 mt-2">Tài khoản của bạn đã được tạo và kích hoạt</p>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">👤 Thông tin tài khoản</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-gray-600">ID:</span>
                <span class="font-medium ml-2">{{ $user->id }}</span>
            </div>
            <div>
                <span class="text-gray-600">Tên:</span>
                <span class="font-medium ml-2">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-gray-600">Email:</span>
                <span class="font-medium ml-2">{{ $user->email }}</span>
            </div>
            <div>
                <span class="text-gray-600">Đăng ký lúc:</span>
                <span class="font-medium ml-2">{{ $registerInfo['register_time']->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
    </div>

    <!-- JWT Token Display -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-4 border-blue-500 rounded-lg p-8 mb-6">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">🔑 YOUR JWT API TOKEN</h2>
            <p class="text-lg text-blue-600 font-semibold">✅ Token đã được tạo tự động!</p>
            <p class="text-red-600 font-semibold mt-2">⚠️ Token này chỉ hiển thị MỘT LẦN DUY NHẤT!</p>
            <p class="text-gray-700 mt-2">Copy và lưu lại ngay để sử dụng trên web của bạn</p>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-lg">
            <label class="block text-sm font-bold text-gray-700 mb-3">📋 JWT TOKEN (Copy this!):</label>
            <div class="space-y-3">
                <textarea 
                    id="jwt-token"
                    readonly
                    rows="6"
                    class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg font-mono text-xs bg-gray-50"
                >{{ $registerInfo['jwt_token'] }}</textarea>
                <button 
                    onclick="copyToken()"
                    class="w-full px-6 py-4 bg-blue-600 text-white font-bold text-lg rounded-lg hover:bg-blue-700 transition shadow-lg"
                >
                    📋 COPY JWT TOKEN
                </button>
                <p id="copy-status" class="text-green-600 font-semibold text-center hidden">✅ Token copied to clipboard!</p>
            </div>
        </div>
    </div>

    <!-- Quick Start Guide -->
    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <!-- Postman -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-2xl">📮</span>
                </div>
                <h3 class="font-bold text-gray-900">Postman</h3>
            </div>
            <div class="text-sm text-gray-700 space-y-2">
                <p><strong>Method:</strong> GET</p>
                <p><strong>URL:</strong></p>
                <code class="text-xs bg-gray-100 px-2 py-1 rounded block">http://localhost:8000/api/user</code>
                <p class="mt-2"><strong>Header:</strong></p>
                <code class="text-xs bg-gray-100 px-2 py-1 rounded block">Authorization: Bearer {token}</code>
            </div>
        </div>

        <!-- JavaScript -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-2xl">⚡</span>
                </div>
                <h3 class="font-bold text-gray-900">JavaScript</h3>
            </div>
            <div class="text-sm text-gray-700">
                <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>fetch('/api/user', {
  headers: {
    'Authorization': 
      'Bearer ' + token
  }
})</code></pre>
            </div>
        </div>

        <!-- PHP -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-2xl">🐘</span>
                </div>
                <h3 class="font-bold text-gray-900">PHP</h3>
            </div>
            <div class="text-sm text-gray-700">
                <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>curl_setopt($ch, 
  CURLOPT_HTTPHEADER, [
    'Authorization: 
     Bearer ' . $token
  ]
);</code></pre>
            </div>
        </div>
    </div>

    <!-- Expected Response -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Expected API Response</h2>
        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm"><code>{
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
        <a href="{{ route('home') }}" class="flex-1 bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
            🏠 Về trang chủ
        </a>
        <button onclick="window.print()" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 font-medium">
            🖨️ In trang này
        </button>
    </div>

    <!-- Important Notice -->
    <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mt-6">
        <h3 class="font-bold text-yellow-900 mb-2">⚠️ LƯU Ý QUAN TRỌNG:</h3>
        <ul class="list-disc list-inside text-yellow-800 space-y-1">
            <li>Token này chỉ hiển thị <strong>MỘT LẦN DUY NHẤT</strong></li>
            <li>Sau khi rời khỏi trang này, bạn KHÔNG THỂ xem lại token</li>
            <li>Hãy copy và lưu token vào nơi an toàn ngay</li>
            <li>Token có hiệu lực 6 tháng</li>
            <li>Nếu mất token, đăng xuất và login lại để có token mới</li>
        </ul>
    </div>
</div>

<script>
function copyToken() {
    const tokenInput = document.getElementById('jwt-token');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999);
    
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
    const tokenInput = document.getElementById('jwt-token');
    tokenInput.focus();
    tokenInput.select();
});
</script>
@endsection

