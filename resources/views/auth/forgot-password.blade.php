@extends('layouts.app')

@section('title', 'Quên mật khẩu - SSO Server')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Quên mật khẩu?</h2>
            <p class="text-gray-600 mt-2">Nhập email của bạn để nhận link đặt lại mật khẩu</p>
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

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            @if(request()->has('redirect'))
                <input type="hidden" name="redirect" value="{{ request()->get('redirect') }}">
                
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm font-medium">🔗 SSO Password Reset</p>
                    <p class="text-xs mt-1">Sau khi đặt lại mật khẩu, bạn sẽ được redirect về: <code class="bg-blue-100 px-1 rounded">{{ request()->get('redirect') }}</code></p>
                </div>
            @endif
            
            @if(request()->has('client_id'))
                <input type="hidden" name="client_id" value="{{ request()->get('client_id') }}">
                <input type="hidden" name="redirect_uri" value="{{ request()->get('redirect_uri') }}">
                <input type="hidden" name="state" value="{{ request()->get('state') }}">
            @endif

            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Nhập email của bạn"
                       required autofocus>
                <p class="text-xs text-gray-500 mt-1">Chúng tôi sẽ gửi link đặt lại mật khẩu đến email này</p>
            </div>

            <button type="submit" class="w-full bg-orange-600 text-white py-2 px-4 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Gửi link đặt lại mật khẩu
            </button>
        </form>

        <div class="mt-6 text-center space-y-3">
            <p class="text-gray-600">
                <a href="{{ route('login', request()->all()) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Quay lại đăng nhập
                </a>
            </p>
            
            <p class="text-gray-600">
                Chưa có tài khoản? 
                <a href="{{ route('register', request()->all()) }}" class="text-blue-600 hover:text-blue-800">Đăng ký ngay</a>
            </p>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-gray-700 font-medium">Lưu ý:</p>
                    <ul class="text-xs text-gray-600 mt-1 space-y-1">
                        <li>• Link đặt lại mật khẩu có hiệu lực trong 60 phút</li>
                        <li>• Kiểm tra cả hộp thư spam nếu không nhận được email</li>
                        <li>• Liên hệ quản trị viên nếu gặp vấn đề</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
