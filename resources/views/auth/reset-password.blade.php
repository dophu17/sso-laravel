@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - SSO Server')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Đặt lại mật khẩu</h2>
            <p class="text-gray-600 mt-2">Tạo mật khẩu mới cho tài khoản của bạn</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            
            @if($redirect)
                <input type="hidden" name="redirect" value="{{ $redirect }}">
                
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm font-medium">🔗 SSO Password Reset</p>
                    <p class="text-xs mt-1">Sau khi đặt lại mật khẩu, bạn sẽ được redirect về: <code class="bg-blue-100 px-1 rounded">{{ $redirect }}</code></p>
                </div>
            @endif

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" id="email" value="{{ $email }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600"
                       readonly>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mật khẩu mới</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="Nhập mật khẩu mới"
                       required>
                <p class="text-xs text-gray-500 mt-1">Tối thiểu 8 ký tự</p>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="Nhập lại mật khẩu mới"
                       required>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Đặt lại mật khẩu
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                <a href="{{ route('login', request()->all()) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Quay lại đăng nhập
                </a>
            </p>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-gray-700 font-medium">Yêu cầu mật khẩu:</p>
                    <ul class="text-xs text-gray-600 mt-1 space-y-1">
                        <li>• Tối thiểu 8 ký tự</li>
                        <li>• Nên bao gồm chữ hoa, chữ thường và số</li>
                        <li>• Tránh sử dụng thông tin cá nhân</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = checkPasswordStrength(password);
    
    // Remove existing indicators
    const existingIndicator = document.querySelector('.password-strength');
    if (existingIndicator) {
        existingIndicator.remove();
    }
    
    if (password.length > 0) {
        const indicator = document.createElement('div');
        indicator.className = 'password-strength mt-2 p-2 rounded text-xs';
        
        if (strength < 2) {
            indicator.className += ' bg-red-100 text-red-700';
            indicator.textContent = 'Mật khẩu yếu';
        } else if (strength < 3) {
            indicator.className += ' bg-yellow-100 text-yellow-700';
            indicator.textContent = 'Mật khẩu trung bình';
        } else {
            indicator.className += ' bg-green-100 text-green-700';
            indicator.textContent = 'Mật khẩu mạnh';
        }
        
        this.parentNode.appendChild(indicator);
    }
});

function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}
</script>
@endsection
