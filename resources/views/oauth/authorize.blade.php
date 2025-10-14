@extends('layouts.app')

@section('title', 'Xác thực OAuth - SSO Server')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Yêu cầu xác thực</h2>

        <div class="mb-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded p-4 mb-4">
                <p class="text-sm text-gray-600 mb-2">Ứng dụng</p>
                <p class="font-bold text-lg">{{ $client->name }}</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                <p class="text-sm text-yellow-800">
                    <strong>{{ $client->name }}</strong> đang yêu cầu quyền truy cập vào tài khoản của bạn.
                </p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Ứng dụng sẽ có quyền:</p>
                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                    <li>Đọc thông tin cơ bản của bạn (tên, email)</li>
                    <li>Truy cập vào tài khoản của bạn</li>
                </ul>
            </div>
        </div>

        <form method="POST" action="/oauth/authorize" class="space-y-3">
            @csrf
            
            @if(isset($authToken))
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
            @else
                <input type="hidden" name="state" value="{{ $request->get('state') }}">
                <input type="hidden" name="client_id" value="{{ $request->get('client_id') }}">
                <input type="hidden" name="redirect_uri" value="{{ $request->get('redirect_uri') }}">
                <input type="hidden" name="response_type" value="{{ $request->get('response_type') }}">
                <input type="hidden" name="scope" value="{{ $request->get('scope') }}">
            @endif

            <button type="submit" name="approve" value="1" 
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Cho phép truy cập
            </button>

            <button type="submit" name="deny" value="1"
                    class="w-full bg-gray-300 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Từ chối
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500">
                Bằng cách cho phép, bạn đồng ý để {{ $client->name }} truy cập thông tin của bạn.
            </p>
        </div>
    </div>
</div>
@endsection

