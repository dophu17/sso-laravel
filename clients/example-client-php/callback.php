<?php
/**
 * OAuth Callback - Handles the authorization code and exchanges it for an access token
 */

session_start();

// Configuration
$config = [
    'client_id' => '1c17afc9-6f69-4f42-8a48-7121659977c0',
    'client_secret' => 'bakH3OwfDwwigTrIbszsPfJG6BiXFUncdoyYy88L',
    'redirect_uri' => 'http://localhost/client-app/callback.php',
    'sso_server' => 'http://localhost:8000',
];

// Check for errors from SSO Server
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
    // Redirect về home với thông báo lỗi
    header('Location: index.php?login=failed&error=' . $error);
    exit;
}

// Verify state to prevent CSRF
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    header('Location: index.php?login=failed&error=invalid_state');
    exit;
}

// Get authorization code
if (!isset($_GET['code'])) {
    header('Location: index.php?login=failed&error=no_code');
    exit;
}

$code = $_GET['code'];

// Exchange authorization code for access token
$tokenUrl = $config['sso_server'] . '/oauth/token';
$tokenData = [
    'grant_type' => 'authorization_code',
    'client_id' => $config['client_id'],
    'client_secret' => $config['client_secret'],
    'redirect_uri' => $config['redirect_uri'],
    'code' => $code,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Debug: Log response (comment out in production)
// error_log("Token Response: HTTP $httpCode - " . $response);

if ($httpCode !== 200) {
    // Log error for debugging
    error_log("Failed to get token. HTTP: $httpCode, Response: $response");
    
    // Check specific error
    $errorData = json_decode($response, true);
    $errorType = $errorData['error'] ?? 'token_request_failed';
    
    header('Location: index.php?login=failed&error=' . $errorType);
    exit;
}

$tokenResponse = json_decode($response, true);

if (!isset($tokenResponse['access_token'])) {
    error_log("No access token in response: " . $response);
    header('Location: index.php?login=failed&error=no_access_token');
    exit;
}

// Store access token in session
$_SESSION['access_token'] = $tokenResponse['access_token'];
$_SESSION['refresh_token'] = $tokenResponse['refresh_token'] ?? null;

// Clear oauth state
unset($_SESSION['oauth_state']);

// Redirect to home page with success message
header('Location: index.php?login=success');
exit;

