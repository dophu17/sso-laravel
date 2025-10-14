<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang xác thực...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Đang xác thực...</h2>
            <p class="text-gray-600 text-sm">Vui lòng chờ trong giây lát</p>
        </div>
    </div>

    <!-- Auto-submit form -->
    <form id="auto-approve-form" method="POST" action="/oauth/authorize">
        @csrf
        
        @if($authToken)
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
        @else
            <input type="hidden" name="state" value="{{ $state }}">
            <input type="hidden" name="client_id" value="{{ $clientId }}">
            <input type="hidden" name="redirect_uri" value="{{ $redirectUri }}">
            <input type="hidden" name="response_type" value="{{ $responseType }}">
            <input type="hidden" name="scope" value="{{ $scope }}">
        @endif
        
        <!-- Auto-approve -->
        <input type="hidden" name="approve" value="1">
    </form>

    <script>
        // Auto-submit form sau khi page load
        window.addEventListener('load', function() {
            // Delay nhỏ để user thấy "Đang xác thực..."
            setTimeout(function() {
                document.getElementById('auto-approve-form').submit();
            }, 500);
        });
    </script>
</body>
</html>

