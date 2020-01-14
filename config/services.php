<?php

return [
    'clients' => [
        'base_url'  => env('CLIENTS_SERVICE_BASE_URL'),
        'port'      => env('CLIENTS_SERVICE_PORT'),
        'secret'    => env('CLIENTS_SERVICE_SECRET'),
        'prefix'    => env('CLIENTS_SERVICE_PREFIX')
    ],
    'sales' => [
        'base_url'   => env("SALES_SERVICE_BASE_URL"),
        'port'      => env('SALES_SERVICE_PORT'),
        'secret'    => env('SALES_SERVICE_SECRET'),
        'prefix'    => env('SALES_PREFIX')
    ]
];
