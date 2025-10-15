@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Welcome Card -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-gray-600">{{ config('app.name') }} Dashboard</p>
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

        <!-- User Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">User Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-sm font-medium text-gray-500">User ID</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $user->id }}</p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-sm font-medium text-gray-500">Name</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $user->name }}</p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-sm font-medium text-gray-500">Role</label>
                    <p class="mt-1 text-lg text-gray-900">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                            {{ $user->role ?? 'User' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Session Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
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

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
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
                    <strong>Session ID:</strong> {{ session()->getId() }}
                </div>
                <div class="bg-white rounded p-3 mb-2">
                    <strong>Auth Check:</strong> {{ Auth::check() ? '✅ Authenticated' : '❌ Not authenticated' }}
                </div>
                <div class="bg-white rounded p-3 mb-2">
                    <strong>Session Driver:</strong> {{ config('session.driver') }}
                </div>
                <div class="bg-white rounded p-3 mb-2">
                    <strong>Session Domain:</strong> {{ config('session.domain') ?: '(empty)' }}
                </div>
                <div class="bg-white rounded p-3">
                    <strong>Session Cookie:</strong> {{ config('session.cookie') }}
                </div>
            </div>
        </details>
        @endif

    </div>
</div>
@endsection

