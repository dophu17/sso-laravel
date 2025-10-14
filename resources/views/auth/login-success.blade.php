@extends('layouts.app')

@section('title', 'Login Success - ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Success Message -->
    <div class="bg-green-100 border-2 border-green-500 rounded-lg p-6 mb-6 animate-pulse">
        <div class="flex items-center mb-4">
            <svg class="w-12 h-12 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h1 class="text-3xl font-bold text-green-900">🎉 Login Successful!</h1>
                <p class="text-green-700 text-lg">Xin chào, {{ $user->name }}!</p>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">👤 Thông tin User</h2>
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
                <span class="text-gray-600">Login lúc:</span>
                <span class="font-medium ml-2">{{ $loginInfo['login_time']->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
    </div>

    <!-- JWT Token Display -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 border-4 border-green-500 rounded-lg p-8 mb-6">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">🔑 YOUR LOGIN JWT TOKEN</h2>
            <p class="text-lg text-green-600 font-semibold">✅ Dùng token này để xác thực trên web của bạn!</p>
            <p class="text-gray-700 mt-2">Token này chứng minh bạn đã login thành công vào SSO Server</p>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-lg">
            <label class="block text-sm font-bold text-gray-700 mb-3">📋 JWT TOKEN (Copy và dùng trên web của bạn):</label>
            <div class="space-y-3">
                <textarea 
                    id="jwt-token"
                    readonly
                    rows="6"
                    class="w-full px-4 py-3 border-2 border-green-300 rounded-lg font-mono text-xs bg-gray-50 focus:border-green-500"
                >{{ $loginInfo['jwt_token'] }}</textarea>
                <button 
                    onclick="copyToken()"
                    class="w-full px-6 py-4 bg-green-600 text-white font-bold text-lg rounded-lg hover:bg-green-700 transition shadow-lg"
                >
                    📋 COPY JWT TOKEN
                </button>
                <p id="copy-status" class="text-green-600 font-semibold text-center hidden">✅ Token copied to clipboard!</p>
            </div>
        </div>
    </div>

    <!-- Usage Guide -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Postman Test -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Test với Postman
            </h3>
            <div class="space-y-2 text-sm">
                <div class="bg-gray-50 p-3 rounded">
                    <p class="font-medium">Method:</p>
                    <code class="text-blue-600">GET</code>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <p class="font-medium">URL:</p>
                    <code class="text-blue-600 text-xs">http://localhost:8000/api/user</code>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <p class="font-medium">Headers:</p>
                    <code class="text-blue-600 text-xs">Authorization: Bearer {token}</code>
                </div>
            </div>
        </div>

        <!-- Your Web App -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
                Dùng trên Web của bạn
            </h3>
            <div class="space-y-3 text-sm text-gray-700">
                <p>✅ Lưu token này trên web của bạn</p>
                <p>✅ Sử dụng để xác thực user đã login</p>
                <p>✅ Gọi API với token để lấy thông tin user</p>
                <p>✅ Token có hiệu lực 6 tháng</p>
            </div>
        </div>
    </div>

    <!-- Expected API Response -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Expected API Response</h2>
        <p class="text-sm text-gray-600 mb-3">Khi gọi <code class="bg-gray-100 px-2 py-1 rounded">GET /api/user</code> với token này:</p>
        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs"><code>{
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

    <!-- Example Integration -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">💻 Example: Sử dụng trên Web của bạn</h3>
        
        <div class="bg-white p-4 rounded mb-4">
            <p class="text-sm text-gray-600 mb-2">JavaScript Example:</p>
            <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code>// Lưu token sau khi login SSO
localStorage.setItem('sso_token', '{{ substr($loginInfo['jwt_token'], 0, 50) }}...');

// Gọi API để lấy thông tin user
fetch('http://localhost:8000/api/user', {
    headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('sso_token'),
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    console.log('User:', data.data);
    // Hiển thị thông tin user trên web của bạn
    document.getElementById('user-name').textContent = data.data.name;
    document.getElementById('user-email').textContent = data.data.email;
});</code></pre>
        </div>

        <div class="bg-white p-4 rounded">
            <p class="text-sm text-gray-600 mb-2">PHP Example:</p>
            <pre class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto"><code>// Lưu token trong session
$_SESSION['sso_token'] = '{{ substr($loginInfo['jwt_token'], 0, 50) }}...';

// Gọi API để verify user
$ch = curl_init('http://localhost:8000/api/user');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['sso_token'],
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$userData = json_decode($response, true);

// Hiển thị thông tin user
echo "Welcome, " . $userData['data']['name'];</code></pre>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-4">
        <a href="{{ route('home') }}" class="flex-1 bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
            → Về trang chủ
        </a>
        <button onclick="window.print()" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 font-medium">
            🖨️ In trang này
        </button>
        <form action="{{ route('logout') }}" method="POST" class="flex-1">
            @csrf
            <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-medium">
                🚪 Đăng xuất
            </button>
        </form>
    </div>

    <!-- Important Notice -->
    <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mt-6">
        <h3 class="font-bold text-yellow-900 mb-2">⚠️ LƯU Ý QUAN TRỌNG:</h3>
        <ul class="list-disc list-inside text-yellow-800 space-y-1 text-sm">
            <li>Token này chỉ hiển thị <strong>MỘT LẦN</strong> ngay sau khi login</li>
            <li>Hãy copy và lưu token vào web của bạn ngay</li>
            <li>Token có hiệu lực 6 tháng</li>
            <li>Sau khi rời khỏi trang này, không thể xem lại token</li>
            <li>Nếu cần token mới, vui lòng đăng xuất và login lại</li>
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
        
        // Auto hide after 3 seconds
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

