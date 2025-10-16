@extends('layouts.app')

@section('title', 'Đăng nhập - SSO Server')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Đăng nhập</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            @if(request()->has('redirect'))
                <input type="hidden" name="redirect" value="{{ request()->get('redirect') }}">
                
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm font-medium">🔗 SSO Login (Session Sharing)</p>
                    <p class="text-xs mt-1">Sau khi login, bạn sẽ được redirect về: <code class="bg-green-100 px-1 rounded">{{ request()->get('redirect') }}</code></p>
                </div>
            @endif
            
            @if(request()->has('client_id'))
                <input type="hidden" name="client_id" value="{{ request()->get('client_id') }}">
                <input type="hidden" name="redirect_uri" value="{{ request()->get('redirect_uri') }}">
                <input type="hidden" name="state" value="{{ request()->get('state') }}">
                
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm">Ứng dụng bên ngoài đang yêu cầu truy cập vào tài khoản của bạn.</p>
                </div>
            @endif

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required autofocus>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mật khẩu</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Đăng nhập
            </button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center">
            <div class="flex-1 border-t border-gray-300"></div>
            <span class="px-4 text-gray-500 text-sm">hoặc</span>
            <div class="flex-1 border-t border-gray-300"></div>
        </div>

        <!-- Google Login Button -->
        <a href="{{ route('auth.google', request()->all()) }}" 
           class="w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Đăng nhập bằng Google
        </a>

        <div class="mt-6 text-center space-y-3">
            <p class="text-gray-600">
                <a href="{{ route('password.forgot', request()->all()) }}" class="text-orange-600 hover:text-orange-800 inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                    </svg>
                    Quên mật khẩu?
                </a>
            </p>
            
            <p class="text-gray-600">Chưa có tài khoản? 
                <a href="{{ route('register', request()->all()) }}" class="text-blue-600 hover:text-blue-800">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>
@endsection

