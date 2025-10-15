@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - SSO Server')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Hồ sơ cá nhân</h1>
                <p class="text-gray-600 mt-2">Quản lý thông tin tài khoản và theo dõi trạng thái đăng nhập</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Về trang chủ
                </a>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h2>
                <p class="text-gray-600">SSO Server Dashboard</p>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800">
                <strong>✅ You're logged in via Session Sharing SSO!</strong>
            </p>
            <p class="text-sm text-green-700 mt-1">
                Your session is automatically shared across all apps on *.balocco-local.info
            </p>
        </div>
    </div>

    <!-- Session Sharing Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="font-semibold text-blue-900 mb-3">
            🔐 Session Sharing Active
        </h3>
        <p class="text-blue-800 mb-2">
            Your login session is stored in a shared database and automatically available to:
        </p>
        <ul class="list-disc list-inside text-blue-700 space-y-1 ml-4">
            <li>Auth Server (auth.balocco-local.info)</li>
            <li>Patent Monitor (patent-monitor.balocco-local.info)</li>
            <li>Bookcase (bookcase.balocco-local.info)</li>
            <li>Any future apps on *.balocco-local.info</li>
        </ul>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="font-medium">Có lỗi xảy ra:</span>
            </div>
            <ul class="list-disc list-inside ml-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">
        
        <!-- Profile Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-blue-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($user->role === 'admin') bg-red-100 text-red-800 
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($user->role ?? 'member') }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tham gia:</span>
                            <span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Cập nhật:</span>
                            <span class="font-medium">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Xác thực email:</span>
                            <span class="font-medium text-green-600">
                                @if($user->email_verified_at)
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Đã xác thực
                                @else
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Chưa xác thực
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Cập nhật thông tin</h3>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Thông tin cơ bản</h4>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   required>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Đổi mật khẩu</h4>
                        <p class="text-sm text-gray-600">Để đổi mật khẩu, hãy nhập mật khẩu hiện tại và mật khẩu mới.</p>
                        
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu hiện tại</label>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Nhập mật khẩu hiện tại">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới</label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Nhập mật khẩu mới">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('home') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Hủy
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('home') }}" class="block p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900">Home</p>
                        <p class="text-sm text-gray-600">Back to homepage</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('logout.get') }}" class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg transition">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-red-900">Logout</p>
                        <p class="text-sm text-red-700">Logout from all apps</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Debug Info (Development only) -->
    @if(config('app.debug'))
    <details class="mt-6 bg-gray-100 rounded-lg p-4">
        <summary class="cursor-pointer font-semibold text-gray-700">
            🐛 Debug Information (Development Mode)
        </summary>
        <div class="mt-3 text-xs">
            <div class="bg-white rounded p-3 mb-2">
                <strong>Session ID:</strong> {{ $stats['session_id'] }}
            </div>
            <div class="bg-white rounded p-3 mb-2">
                <strong>Auth Check:</strong> {{ $stats['is_authenticated'] ? '✅ Authenticated' : '❌ Not authenticated' }}
            </div>
            <div class="bg-white rounded p-3 mb-2">
                <strong>Session Driver:</strong> {{ $stats['session_driver'] }}
            </div>
            <div class="bg-white rounded p-3 mb-2">
                <strong>Session Domain:</strong> {{ $stats['session_domain'] }}
            </div>
            <div class="bg-white rounded p-3">
                <strong>Session Cookie:</strong> {{ $stats['session_cookie'] }}
            </div>
        </div>
    </details>
    @endif

    <!-- Security Tips -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">💡 Mẹo bảo mật</h3>
        <ul class="space-y-2 text-sm text-blue-800">
            <li class="flex items-start">
                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Sử dụng mật khẩu mạnh với ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt
            </li>
            <li class="flex items-start">
                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Không chia sẻ thông tin đăng nhập với bất kỳ ai
            </li>
            <li class="flex items-start">
                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Đăng xuất khỏi tất cả thiết bị nếu bạn nghi ngờ tài khoản bị xâm nhập
            </li>
        </ul>
    </div>

</div>
@endsection
