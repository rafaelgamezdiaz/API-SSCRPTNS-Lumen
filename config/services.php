<?php

return [
    'clients' => [
        'base_url'  => env('CUSTOMER_SERVICE_BASE_URL'),
        'port'      => env('CUSTOMER_SERVICE_PORT'),
        'secret'    => env('CUSTOMER_SERVICE_SECRET'),
        'prefix'    => env('CUSTOMER_SERVICE_PREFIX')
    ],
    'sales' => [
        'base_url'  => env('INVENTORY_SERVICE_BASE_URL'),
        'port'      => env('INVENTORY_SERVICE_PORT'),
        'secret'    => env('INVENTORY_SERVICE_SECRET'),
        'prefix'    => env('INVENTORY_SERVICE_PREFIX')
    ]
];
