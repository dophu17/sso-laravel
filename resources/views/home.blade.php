@extends('layouts.app')

@section('title', 'SSO Server - Session Sharing')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-100 rounded-full mb-6">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Hệ thống SSO Server
        </h1>
        <p class="text-xl text-gray-600 mb-2">
            Chia sẻ phiên đăng nhập - Đăng nhập 1 lần, truy cập nhiều nơi
        </p>
        <p class="text-gray-500">
            auth.balocco-local.info
        </p>
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
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-8">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('status'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-lg mb-8">
            {{ session('status') }}
        </div>
    @endif

    <!-- User Status -->
    @auth
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Chào mừng, {{ Auth::user()->name }}!</h2>
                        <p class="text-gray-600">Bạn đã đăng nhập qua hệ thống chia sẻ phiên</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('profile') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Xem hồ sơ
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Chưa đăng nhập</h2>
                <p class="text-gray-600 mb-6">Vui lòng đăng nhập để truy cập hệ thống</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Đăng ký
                    </a>
                </div>
            </div>
        </div>
    @endauth

    <!-- Features Grid -->
    <div class="grid md:grid-cols-3 gap-6 mb-12">
        <!-- Feature 1 -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chia sẻ phiên đăng nhập</h3>
            <p class="text-gray-600">
                Đăng nhập một lần, phiên đăng nhập tự động được chia sẻ qua database đến tất cả ứng dụng trên *.balocco-local.info
            </p>
        </div>

        <!-- Feature 2 -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Đồng bộ thời gian thực</h3>
            <p class="text-gray-600">
                Đăng nhập và đăng xuất được đồng bộ ngay lập tức giữa tất cả ứng dụng - không có độ trễ
            </p>
        </div>

        <!-- Feature 3 -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Bảo mật</h3>
            <p class="text-gray-600">
                Phiên đăng nhập được lưu trong database với mã hóa, bảo vệ CSRF và cookie bảo mật
            </p>
        </div>
    </div>

    <!-- Connected Apps -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Ứng dụng được kết nối</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Client A -->
            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-blue-500 transition">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Hệ thống giám sát bằng sáng chế</h3>
                        <p class="text-sm text-gray-500">patent-monitor.balocco-local.info</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">
                    Hệ thống giám sát và quản lý bằng sáng chế
                </p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    SSO đã kích hoạt
                </span>
            </div>

            <!-- Client B -->
            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-blue-500 transition">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Hệ thống quản lý sách</h3>
                        <p class="text-sm text-gray-500">bookcase.balocco-local.info</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">
                    Hệ thống quản lý sách và thư viện
                </p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    SSO đã kích hoạt
                </span>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg shadow-md p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Cách thức hoạt động của chia sẻ phiên đăng nhập</h2>
        
        <div class="grid md:grid-cols-4 gap-6">
            <!-- Step 1 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                    <span class="text-2xl font-bold text-blue-600">1</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Đăng nhập một lần</h3>
                <p class="text-sm text-gray-600">
                    Đăng nhập tại Server Auth hoặc bất kỳ ứng dụng client nào
                </p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                    <span class="text-2xl font-bold text-green-600">2</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Lưu phiên đăng nhập</h3>
                <p class="text-sm text-gray-600">
                    Phiên đăng nhập được lưu trong database chung
                </p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                    <span class="text-2xl font-bold text-purple-600">3</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Chia sẻ Cookie</h3>
                <p class="text-sm text-gray-600">
                    Cookie được chia sẻ đến tất cả *.balocco-local.info
                </p>
            </div>

            <!-- Step 4 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                    <span class="text-2xl font-bold text-orange-600">4</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Tự động đăng nhập</h3>
                <p class="text-sm text-gray-600">
                    Tất cả ứng dụng đều thấy bạn đã đăng nhập!
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics (if authenticated) -->
    @auth
    <div class="grid md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tổng số người dùng</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Phiên đăng nhập hoạt động</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tổng số lần đăng nhập</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_logins'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management (Admin Only) -->
    @if(Auth::check() && Auth::user()->role === 'admin')
    <div class="bg-white rounded-lg shadow-md p-8 mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">👥 Quản lý người dùng</h2>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm người dùng mới
            </a>
        </div>

        @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($user->role === 'admin') bg-red-100 text-red-800 
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($user->role ?? 'member') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $isOnline = DB::table('sessions')
                                    ->where('user_id', $user->id)
                                    ->where('last_activity', '>', now()->subMinutes(5)->timestamp)
                                    ->exists();
                            @endphp
                            
                            @if($isOnline)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                    Trực tuyến
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                                    Ngoại tuyến
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($user->role === 'member')
                                <div class="flex space-x-2">
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('users.delete', $user) }}" 
                                          class="inline" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa user này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Được bảo vệ</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Hiển thị 
                    <span class="font-medium">{{ $users->firstItem() }}</span>
                    đến 
                    <span class="font-medium">{{ $users->lastItem() }}</span>
                    trong tổng số 
                    <span class="font-medium">{{ $users->total() }}</span>
                    người dùng
                </div>
                
                <div class="flex items-center space-x-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-12 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p>Không tìm thấy người dùng nào. <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Tạo người dùng đầu tiên</a></p>
        </div>
        @endif
    </div>
    @endif

    <!-- Recent Login Activity -->
    @if($stats['recent_logins']->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Hoạt động đăng nhập gần đây</h2>
        
        <div class="space-y-3">
            @foreach($stats['recent_logins'] as $log)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-blue-600 font-bold">{{ strtoupper(substr($log->user_name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $log->user_name }}</p>
                        <p class="text-xs text-gray-500">{{ $log->email }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">{{ $log->login_at->diffForHumans() }}</p>
                    <p class="text-xs text-gray-400">{{ $log->ip_address }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endauth

    <!-- Info Boxes -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Setup Guide -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📖 Hướng dẫn thiết lập</h3>
            <p class="text-gray-600 mb-4">
                Để tích hợp SSO vào các ứng dụng client (Patent Monitor & Bookcase):
            </p>
            <ol class="list-decimal list-inside text-sm text-gray-600 space-y-2">
                <li>Config <code class="bg-gray-100 px-1 rounded">SESSION_DOMAIN=.balocco-local.info</code></li>
                <li>Run <code class="bg-gray-100 px-1 rounded">php artisan session:table && migrate</code></li>
                <li>Create middleware <code class="bg-gray-100 px-1 rounded">CheckSharedSession</code></li>
                <li>Test login flow</li>
            </ol>
            <div class="mt-4">
                <a href="{{ route('readme') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Đọc hướng dẫn đầy đủ →
                </a>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">⚙️ Thông tin hệ thống</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Phiên bản Laravel:</span>
                    <span class="font-medium">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phiên bản PHP:</span>
                    <span class="font-medium">{{ PHP_VERSION }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Driver phiên:</span>
                    <span class="font-medium">{{ config('session.driver') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Miền phiên:</span>
                    <span class="font-medium">{{ config('session.domain') ?: '(not set)' }}</span>
                </div>
                @auth
                <div class="flex justify-between">
                    <span class="text-gray-600">ID phiên của bạn:</span>
                    <span class="font-medium text-xs">{{ substr(session()->getId(), 0, 20) }}...</span>
                </div>
                @endauth
            </div>
        </div>
    </div>


</div>
@endsection
