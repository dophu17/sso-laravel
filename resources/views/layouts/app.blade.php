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
                <div class="flex items-center">
                    @auth
                        <span class="text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Đăng xuất</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 mr-4">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">Đăng ký</a>
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
            <p class="text-center text-gray-600">© {{ date('Y') }} SSO Server - Single Sign-On System</p>
        </div>
    </footer>
</body>
</html>

