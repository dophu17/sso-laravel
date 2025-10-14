<?php
/**
 * Debug Page - Hiển thị thông tin session và config
 */

session_start();

// Configuration
$config = [
    'client_id' => '0199dde2-497c-70ec-8d98-1a4f74f53c6c',
    'client_secret' => 'cJdaBDtUMk99gc73d4MRrrzLDAQubPduS7eX6rHk',
    'redirect_uri' => 'http://localhost:3000/callback.php',
    'sso_server' => 'http://localhost:8000',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug - SSO Client</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">🔍 Debug Information</h1>

            <!-- Configuration -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">⚙️ Configuration</h2>
                <div class="bg-gray-50 rounded p-4 font-mono text-sm">
                    <div class="mb-2"><strong>Client ID:</strong> <?php echo htmlspecialchars($config['client_id']); ?></div>
                    <div class="mb-2"><strong>Client Secret:</strong> <?php echo substr($config['client_secret'], 0, 10) . '...' . substr($config['client_secret'], -5); ?></div>
                    <div class="mb-2"><strong>Redirect URI:</strong> <?php echo htmlspecialchars($config['redirect_uri']); ?></div>
                    <div class="mb-2"><strong>SSO Server:</strong> <?php echo htmlspecialchars($config['sso_server']); ?></div>
                </div>
            </div>

            <!-- Session -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">🔐 Session Data</h2>
                <div class="bg-gray-50 rounded p-4">
                    <?php if (!empty($_SESSION)): ?>
                        <pre class="text-sm overflow-auto"><?php print_r($_SESSION); ?></pre>
                    <?php else: ?>
                        <p class="text-gray-600">Session is empty</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Test API Connection -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">🌐 Test API Connection</h2>
                <?php
                if (isset($_SESSION['access_token'])) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $config['sso_server'] . '/api/user');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $_SESSION['access_token']
                    ]);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    echo '<div class="bg-gray-50 rounded p-4">';
                    echo '<div class="mb-2"><strong>HTTP Code:</strong> ' . $httpCode . '</div>';
                    
                    if ($httpCode === 200) {
                        echo '<div class="text-green-600 font-semibold mb-2">✓ API Connection: OK</div>';
                        echo '<pre class="text-sm overflow-auto">' . htmlspecialchars($response) . '</pre>';
                    } else {
                        echo '<div class="text-red-600 font-semibold mb-2">✗ API Connection: FAILED</div>';
                        echo '<pre class="text-sm overflow-auto">' . htmlspecialchars($response) . '</pre>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="bg-yellow-50 border border-yellow-200 rounded p-4">';
                    echo '<p class="text-yellow-800">No access token in session. Please login first.</p>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <div class="flex gap-3">
                    <a href="index.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        ← Back to Home
                    </a>
                    <a href="?clear=1" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Clear Session & Login Again
                    </a>
                </div>
                
                <?php if (!isset($_SESSION['access_token'])): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <p class="text-sm text-yellow-800">
                            <strong>💡 Tip:</strong> Click <strong>"Clear Session & Login Again"</strong> để bắt đầu test SSO flow
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            // Clear session if requested
            if (isset($_GET['clear'])) {
                session_destroy();
                // Redirect về home để trigger auto-redirect
                echo '<script>window.location.href = "index.php";</script>';
                exit;
            }
            ?>
        </div>

        <!-- Help -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h3 class="font-semibold text-gray-900 mb-2">💡 Common Issues:</h3>
            <ul class="text-sm text-gray-700 space-y-1">
                <li>• <strong>HTTP 401:</strong> Token hết hạn hoặc không hợp lệ → Clear session và login lại</li>
                <li>• <strong>HTTP 404:</strong> SSO Server không chạy → Check php artisan serve</li>
                <li>• <strong>HTTP 500:</strong> Server error → Check SSO Server logs</li>
                <li>• <strong>Invalid client:</strong> Sai Client ID hoặc Secret → Check config</li>
                <li>• <strong>Invalid grant:</strong> Authorization code đã hết hạn → Code chỉ dùng được 1 lần</li>
            </ul>
        </div>
    </div>
</body>
</html>

