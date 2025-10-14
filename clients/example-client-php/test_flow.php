<?php
/**
 * Test SSO Flow - Debug script
 * Kiểm tra từng bước của OAuth flow
 */

session_start();

// Configuration
$config = [
    'client_id' => '0199dde2-497c-70ec-8d98-1a4f74f53c6c',
    'client_secret' => 'cJdaBDtUMk99gc73d4MRrrzLDAQubPduS7eX6rHk',
    'redirect_uri' => 'http://localhost:3000/callback.php',
    'sso_server' => 'http://localhost:8000',
];

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>SSO Flow Test</title>";
echo "<script src='https://cdn.tailwindcss.com'></script></head>";
echo "<body class='bg-gray-100 p-8'>";
echo "<div class='max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8'>";
echo "<h1 class='text-3xl font-bold mb-6 text-blue-600'>🔍 SSO Flow Debug Tool</h1>";

// Test 1: Check SSO Server
echo "<div class='mb-6 p-4 border-l-4 border-blue-500 bg-blue-50'>";
echo "<h2 class='text-xl font-bold mb-2'>Test 1: SSO Server Connection</h2>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['sso_server']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpCode == 200) {
    echo "<p class='text-green-600 font-bold'>✓ SSO Server is running at {$config['sso_server']}</p>";
} else {
    echo "<p class='text-red-600 font-bold'>✗ Cannot connect to SSO Server!</p>";
    echo "<p class='text-sm text-red-600'>HTTP Code: $httpCode</p>";
    if ($error) echo "<p class='text-sm text-red-600'>Error: $error</p>";
}
echo "</div>";

// Test 2: Check current session
echo "<div class='mb-6 p-4 border-l-4 border-purple-500 bg-purple-50'>";
echo "<h2 class='text-xl font-bold mb-2'>Test 2: Current Session</h2>";

if (isset($_SESSION['access_token'])) {
    echo "<p class='text-green-600 font-bold'>✓ Access token found in session</p>";
    echo "<p class='text-sm text-gray-600 mt-2'>Token: <code class='bg-gray-200 px-2 py-1 rounded'>" 
        . substr($_SESSION['access_token'], 0, 50) . "...</code></p>";
    
    // Test 3: Validate token với API
    echo "</div>";
    echo "<div class='mb-6 p-4 border-l-4 border-yellow-500 bg-yellow-50'>";
    echo "<h2 class='text-xl font-bold mb-2'>Test 3: Token Validation</h2>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['sso_server'] . '/api/user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['access_token'],
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p class='mb-2'><strong>Request:</strong> GET /api/user</p>";
    echo "<p class='mb-2'><strong>HTTP Code:</strong> $httpCode</p>";
    
    if ($httpCode == 200) {
        echo "<p class='text-green-600 font-bold'>✓ Token is VALID!</p>";
        $userData = json_decode($response, true);
        echo "<pre class='bg-gray-100 p-3 rounded mt-2 text-xs overflow-x-auto'>" 
            . json_encode($userData, JSON_PRETTY_PRINT) . "</pre>";
    } elseif ($httpCode == 401) {
        echo "<p class='text-red-600 font-bold'>✗ Token is INVALID or EXPIRED!</p>";
        echo "<p class='text-sm text-red-600 mt-2'>Response:</p>";
        echo "<pre class='bg-red-100 p-3 rounded mt-2 text-xs overflow-x-auto'>" . htmlspecialchars($response) . "</pre>";
        
        // Decode JWT to see expiration
        $tokenParts = explode('.', $_SESSION['access_token']);
        if (count($tokenParts) == 3) {
            $payload = json_decode(base64_decode(str_pad(strtr($tokenParts[1], '-_', '+/'), strlen($tokenParts[1]) % 4, '=', STR_PAD_RIGHT)), true);
            echo "<p class='text-sm mt-3'><strong>Token Info:</strong></p>";
            echo "<pre class='bg-gray-100 p-3 rounded text-xs overflow-x-auto'>";
            echo "Issued at: " . date('Y-m-d H:i:s', $payload['iat'] ?? 0) . "\n";
            echo "Expires at: " . date('Y-m-d H:i:s', $payload['exp'] ?? 0) . "\n";
            echo "Current time: " . date('Y-m-d H:i:s') . "\n";
            if (isset($payload['exp'])) {
                $remaining = $payload['exp'] - time();
                if ($remaining > 0) {
                    echo "Time remaining: " . round($remaining / 3600, 2) . " hours\n";
                } else {
                    echo "Token EXPIRED " . abs(round($remaining / 3600, 2)) . " hours ago\n";
                }
            }
            echo "</pre>";
        }
    } else {
        echo "<p class='text-orange-600 font-bold'>⚠ Unexpected response!</p>";
        echo "<pre class='bg-gray-100 p-3 rounded mt-2 text-xs overflow-x-auto'>" . htmlspecialchars($response) . "</pre>";
    }
    
} else {
    echo "<p class='text-yellow-600 font-bold'>⚠ No access token in session</p>";
    echo "<p class='text-sm mt-2'>You need to login first.</p>";
}
echo "</div>";

// Test 4: Configuration check
echo "<div class='mb-6 p-4 border-l-4 border-green-500 bg-green-50'>";
echo "<h2 class='text-xl font-bold mb-2'>Test 4: Configuration</h2>";
echo "<table class='w-full text-sm'>";
echo "<tr><td class='font-bold py-1'>Client ID:</td><td class='font-mono'>{$config['client_id']}</td></tr>";
echo "<tr><td class='font-bold py-1'>Client Secret:</td><td class='font-mono'>" . substr($config['client_secret'], 0, 20) . "...</td></tr>";
echo "<tr><td class='font-bold py-1'>Redirect URI:</td><td class='font-mono'>{$config['redirect_uri']}</td></tr>";
echo "<tr><td class='font-bold py-1'>SSO Server:</td><td class='font-mono'>{$config['sso_server']}</td></tr>";
echo "</table>";
echo "</div>";

// Test 5: Session data
echo "<div class='mb-6 p-4 border-l-4 border-gray-500 bg-gray-50'>";
echo "<h2 class='text-xl font-bold mb-2'>Test 5: Full Session Data</h2>";
echo "<pre class='bg-gray-100 p-3 rounded text-xs overflow-x-auto'>" . print_r($_SESSION, true) . "</pre>";
echo "</div>";

// Actions
echo "<div class='mt-8 p-4 bg-gray-50 rounded'>";
echo "<h2 class='text-xl font-bold mb-4'>Actions:</h2>";
echo "<div class='space-x-2'>";
echo "<a href='index.php' class='inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700'>← Back to Home</a>";
echo "<a href='logout.php' class='inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700'>Clear Session</a>";
echo "<a href='test_flow.php' class='inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700'>Refresh</a>";

if (!isset($_SESSION['access_token'])) {
    echo "<a href='login.php' class='inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700'>Login</a>";
}
echo "</div>";
echo "</div>";

echo "</div></body></html>";
?>

