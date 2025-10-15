<?php

return [
    'server' => env('SSO_SERVER', 'https://auth.balocco-local.info'),
    'callback_url' => env('SSO_CALLBACK_URL', env('APP_URL') . '/sso/callback'),
    'session_key' => 'sso_user',
    'timeout' => 10,
    'allowed_domains' => [
        'auth.balocco-local.info',
    ],
];