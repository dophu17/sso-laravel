<?php
/**
 * SSO Login - Redirects to SSO Server for authentication
 */

session_start();

// Configuration
$config = [
    'client_id' => '1c17afc9-6f69-4f42-8a48-7121659977c0',
    'redirect_uri' => 'http://localhost/client-app/callback.php',
    'sso_server' => 'http://localhost:8000',
];

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

