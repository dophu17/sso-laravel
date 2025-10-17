<?php

return [
    'server' => env('SSO_SERVER', 'https://auth.your-domain.com'),
    'callback_url' => env('SSO_CALLBACK_URL', env('APP_URL') . '/sso/callback'),
    'session_key' => env('SESSION_COOKIE', 'your_session_name'),
    'timeout' => 10,
    'allowed_domains' => [
        'auth.your-domain.com',
    ],
];