<?php

return [
    'source' => env('ACCOUNT_STATEMENT_SOURCE', 'excel'),
    'api' => [
        'base_url' => env('ACCOUNT_STATEMENT_API_BASE_URL'),
        'token' => env('ACCOUNT_STATEMENT_API_TOKEN'),
        'timeout' => env('ACCOUNT_STATEMENT_API_TIMEOUT', 15),
        'username' => env('ACCOUNT_STATEMENT_API_USERNAME'),
        'password' => env('ACCOUNT_STATEMENT_API_PASSWORD'),
    ],
];
