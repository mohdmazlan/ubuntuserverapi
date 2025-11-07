# Configuration

Copy `config.example.php` to `config.php` and update the settings for your environment.

## Example Configuration

```php
<?php

return [
    'app' => [
        'name' => 'Ubuntu Server Management API',
        'version' => '1.0.0',
        'debug' => false,  // Set to false in production
        'timezone' => 'UTC'
    ],
    
    'api' => [
        'prefix' => '/api',
        'version' => 'v1'
    ],
    
    'security' => [
        'api_key_required' => true,  // Enable in production
        'api_key' => 'your-secret-api-key-here',  // Change this!
        'allowed_ips' => [],  // Restrict by IP if needed
        'rate_limit' => [
            'enabled' => true,
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
```

## Security Notes

- **Never commit** `config.php` with real credentials to version control
- Change the default API key in production
- Enable authentication (`api_key_required`) in production
- Set `debug` to `false` in production
- Configure `allowed_ips` if you need IP whitelisting
