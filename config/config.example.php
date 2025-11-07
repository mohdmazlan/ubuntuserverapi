<?php

return [
    'app' => [
        'name' => 'Ubuntu Server Management API',
        'version' => '1.0.0',
        'debug' => false,
        'timezone' => 'UTC'
    ],
    
    'api' => [
        'prefix' => '/api',
        'version' => 'v1'
    ],
    
    'security' => [
        'api_key_required' => false,
        'allowed_ips' => [],
        'rate_limit' => [
            'enabled' => false,
            'max_requests' => 100,
            'per_minutes' => 1
        ]
    ],
    
    'system' => [
        'max_process_limit' => 100,
        'allowed_commands' => [
            'systemctl',
            'ps',
            'df',
            'du',
            'free',
            'top',
            'uptime',
            'who',
            'ss',
            'ip',
            'ping',
            'lsblk'
        ]
    ]
];
