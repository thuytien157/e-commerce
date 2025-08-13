<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // * cho tất cả path
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Cho phép mọi domain (hoặc ghi rõ domain)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];


