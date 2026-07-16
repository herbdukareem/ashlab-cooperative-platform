<?php

return [
    'name' => env('PLATFORM_NAME', 'Ashlab Cooperative Platform'),
    'tenant_header' => env('TENANT_HEADER', 'X-Cooperative-ID'),
    'audit_retention_days' => (int) env('AUDIT_RETENTION_DAYS', 2555),
    'identifier_hash_key' => env('IDENTIFIER_HASH_KEY') ?: env('APP_KEY'),
    'pagination' => ['default' => 25, 'maximum' => 100],
];
