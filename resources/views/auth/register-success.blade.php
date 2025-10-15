@extends('layouts.app')

@section('title', 'Đăng ký thành công')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    
    <!-- Success Card -->
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        
        <!-- Success Icon -->
        <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <!-- Success Message -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            ✅ Đăng ký thành công!
        </h1>
        
        <p class="text-lg text-gray-600 mb-8">
            Tài khoản của bạn đã được tạo thành công.
        </p>

        <!-- User Info -->
        @if(session('register_info'))
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h3 class="font-semibold text-gray-900 mb-4">Thông tin tài khoản:</h3>
            <div class="space-y-2">
                <div class="flex">
                    <span class="text-gray-600 w-24">Tên:</span>
                    <span class="font-medium">{{ session('register_info')['user_name'] }}</span>
                </div>
                <div class="flex">
                    <span class="text-gray-600 w-24">Email:</span>
                    <span class="font-medium">{{ session('register_info')['user_email'] }}</span>
                </div>
                <div class="flex">
                    <span class="text-gray-600 w-24">ID:</span>
                    <span class="font-medium">{{ session('register_info')['user_id'] }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- SSO Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-left">
                    <h4 class="font-semibold text-blue-900 mb-2">Session Sharing SSO</h4>
                    <p class="text-sm text-blue-800">
                        Tài khoản này có thể đăng nhập vào tất cả ứng dụng trên <code class="bg-blue-100 px-1 rounded">*.balocco-local.info</code> 
                        mà không cần đăng nhập lại.
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center gap-4">
            <a href="{{ route('login') }}" 
               class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow">
                Đăng nhập ngay
            </a>
            <a href="{{ route('home') }}" 
               class="px-8 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                Về trang chủ
            </a>
        </div>
    </div>

    <!-- Next Steps -->
    <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="font-semibold text-green-900 mb-3">📋 Các bước tiếp theo:</h3>
        <ol class="list-decimal list-inside text-green-800 space-y-2">
            <li>Đăng nhập vào hệ thống</li>
            <li>Truy cập vào các ứng dụng khác (Patent Monitor, Bookcase)</li>
            <li>Session của bạn sẽ tự động shared - không cần đăng nhập lại!</li>
        </ol>
    </div>

</div>
@endsection
