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
            
            @if(request()->has('callback'))
                <input type="hidden" name="callback" value="{{ request()->get('callback') }}">
                
                <div class="bg-purple-50 border border-purple-200 text-purple-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm font-medium">🔗 Login với Callback (Token-based)</p>
                    <p class="text-xs mt-1">Sau khi login, bạn sẽ được redirect về: <code class="bg-purple-100 px-1 rounded">{{ request()->get('callback') }}</code></p>
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

        <div class="mt-4 text-center">
            <p class="text-gray-600">Chưa có tài khoản? 
                <a href="{{ route('register', request()->all()) }}" class="text-blue-600 hover:text-blue-800">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>
@endsection

