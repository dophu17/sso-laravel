<?php
/**
 * Example Client Application - Web A
 * Demonstrates SSO integration with Laravel Passport OAuth2 Server
 */

session_start();

// Configuration
$config = [
    'client_id' => '1c17afc9-6f69-4f42-8a48-7121659977c0', // Get this from SSO Server
    'client_secret' => 'bakH3OwfDwwigTrIbszsPfJG6BiXFUncdoyYy88L', // Get this from SSO Server
    'redirect_uri' => 'http://localhost/client-app/callback.php',
    'sso_server' => 'http://localhost:8000',
];

// Helper function to make HTTP requests
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
    }
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

// Check for login status messages
$loginSuccess = isset($_GET['login']) && $_GET['login'] === 'success';
$loginFailed = isset($_GET['login']) && $_GET['login'] === 'failed';
$errorMessage = $_GET['error'] ?? null;

// Check if user is logged in
if (isset($_SESSION['access_token'])) {
    // User is logged in, get user info
    $response = makeRequest(
        $config['sso_server'] . '/api/user',
        'GET',
        null,
        ['Authorization: Bearer ' . $_SESSION['access_token']]
    );
    
    if ($response['code'] === 200) {
        $user = $response['body']['data'];
    } else {
        // Token expired or invalid
        unset($_SESSION['access_token']);
        header('Location: index.php?login=failed&error=token_expired');
        exit;
    }
} else {
    // Chưa login - tự động redirect đến SSO Server
    // Chỉ redirect nếu không phải đang có message (để tránh loop)
    if (!$loginSuccess && !$loginFailed) {
        // Generate random state for CSRF protection
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        
        // Build authorization URL
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
        ];
        
        $authUrl = $config['sso_server'] . '/oauth/authorize?' . http_build_query($params);
        
        // Redirect to SSO Server
        header('Location: ' . $authUrl);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web A - SSO Client Example</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-blue-600">Web A - Example Client</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <?php if (isset($user)): ?>
                        <span class="text-gray-700 mr-4">Xin chào, <?php echo htmlspecialchars($user['name']); ?></span>
                        <a href="logout.php" class="text-red-600 hover:text-red-800">Đăng xuất</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Đăng nhập với SSO
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <?php if ($loginSuccess): ?>
                <!-- Success Message -->
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>✓ Đăng nhập thành công!</strong> Bạn đã được xác thực qua SSO Server.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($loginFailed): ?>
                <!-- Error Message -->
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <strong>✗ Đăng nhập thất bại!</strong> 
                                <?php 
                                    switch ($errorMessage) {
                                        case 'token_expired':
                                            echo 'Token đã hết hạn, vui lòng đăng nhập lại.';
                                            break;
                                        case 'access_denied':
                                            echo 'Bạn đã từ chối quyền truy cập từ SSO Server.';
                                            break;
                                        case 'invalid_state':
                                            echo 'CSRF validation failed. Vui lòng thử lại.';
                                            break;
                                        case 'no_code':
                                            echo 'Không nhận được authorization code từ SSO Server.';
                                            break;
                                        case 'token_request_failed':
                                            echo 'Không thể lấy access token từ SSO Server.';
                                            break;
                                        case 'no_access_token':
                                            echo 'SSO Server không trả về access token.';
                                            break;
                                        case 'invalid_grant':
                                            echo 'Authorization code không hợp lệ hoặc đã hết hạn. Vui lòng thử lại.';
                                            break;
                                        case 'invalid_client':
                                            echo 'Client ID hoặc Client Secret không đúng. Vui lòng kiểm tra cấu hình.';
                                            break;
                                        case 'unauthorized_client':
                                            echo 'Client không được phép sử dụng authorization code flow.';
                                            break;
                                        default:
                                            echo 'Đã có lỗi xảy ra trong quá trình xác thực. (' . htmlspecialchars($errorMessage) . ')';
                                    }
                                ?>
                            </p>
                            <div class="mt-3">
                                <a href="debug.php" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    → Xem thông tin debug chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($user)): ?>
                <!-- Logged in view -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Đăng nhập thành công!</h2>
                            <p class="text-gray-600">Bạn đã đăng nhập qua SSO Server</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin người dùng:</h3>
                        <dl class="space-y-2">
                            <div class="flex">
                                <dt class="font-semibold text-gray-700 w-32">ID:</dt>
                                <dd class="text-gray-600"><?php echo htmlspecialchars($user['id']); ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="font-semibold text-gray-700 w-32">Tên:</dt>
                                <dd class="text-gray-600"><?php echo htmlspecialchars($user['name']); ?></dd>
                            </div>
                            <div class="flex">
                                <dt class="font-semibold text-gray-700 w-32">Email:</dt>
                                <dd class="text-gray-600"><?php echo htmlspecialchars($user['email']); ?></dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded">
                        <p class="text-sm text-green-800">
                            <strong>✓ SSO hoạt động!</strong> Bạn có thể truy cập Web B và Web C mà không cần đăng nhập lại.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Not logged in - chỉ hiển thị nếu có error message -->
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Login thất bại</h2>
                    <p class="text-gray-600 mb-6">
                        Đã có lỗi xảy ra trong quá trình đăng nhập.
                    </p>

                    <a href="logout.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Thử lại
                    </a>
                    
                    <div class="mt-4">
                        <a href="debug.php" class="text-sm text-gray-600 hover:text-gray-800 underline">
                            Xem thông tin debug
                        </a>
                    </div>

                    <div class="mt-6 text-left bg-gray-50 rounded p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">SSO Flow:</h3>
                        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                            <li>Tự động redirect đến SSO Server (Web D)</li>
                            <li>Bạn đăng nhập trên SSO Server</li>
                            <li>Sau khi approve, redirect về đây</li>
                            <li>Hiển thị thông báo thành công</li>
                        </ol>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-white shadow-lg mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-gray-600">Web A - SSO Client Example</p>
        </div>
    </footer>
</body>
</html>

