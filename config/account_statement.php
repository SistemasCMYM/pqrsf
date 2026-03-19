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
    'viaticos' => [
    'base_url'      => env('VIATICOS_API_URL'),
    'client_id'     => env('VIATICOS_CLIENT_ID'),
    'client_secret' => env('VIATICOS_CLIENT_SECRET'),
    'scope'         => env('VIATICOS_SCOPE', 'read-anticipos'),
    'username'      => env('VIATICOS_USERNAME'),
    'password'      => env('VIATICOS_PASSWORD'),
    'timeout'       => env('VIATICOS_TIMEOUT', 15),
    'source' => env('ACCOUNT_STATEMENT_SOURCE', 'excel'),
],
];
