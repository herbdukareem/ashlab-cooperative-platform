<?php

return [
    'mode' => env('INTEGRATION_MODE', 'sandbox'),
    'allow_live' => (bool) env('ALLOW_LIVE_FINANCIAL_INTEGRATIONS', false),
    'transfer' => [
        'driver' => env('TRANSFER_GATEWAY', 'sandbox'),
        'webhook_secret' => env('TRANSFER_WEBHOOK_SECRET'),
        'timeout_seconds' => (int) env('TRANSFER_TIMEOUT_SECONDS', 15),
    ],
    'identity' => [
        'driver' => env('IDENTITY_GATEWAY', 'sandbox'),
        'timeout_seconds' => (int) env('IDENTITY_TIMEOUT_SECONDS', 15),
    ],
];
