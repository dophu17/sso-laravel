<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SSO Server')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">SSO Server</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('profile') }}" class="text-gray-700 hover:text-blue-600 transition">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ Auth::user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Đăng xuất
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 transition">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 transition">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        @yield('content')
    </main>

    <footer class="bg-white shadow-lg mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600">© {{ date('Y') }} SSO Server - Hệ thống đăng nhập một lần</p>
        </div>
    </footer>
</body>
</html>

