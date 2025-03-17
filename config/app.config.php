<?php

/**
 * Application Configuration
 */
return [
    'app' => [
        'name' => 'USAP',
        'version' => '1.0.0',
        'debug' => true, // Set to false in production
    ],
    'paths' => [
        'base' => __DIR__ . '/../',
        'public' => __DIR__ . '/../public',
        'uploads' => __DIR__ . '/../public/uploads',
        'temp' => sys_get_temp_dir() . '/usap',
    ],
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'usap',
        'username' => 'usap_user',
        'password' => '', // Set in environment or .env file
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'security' => [
        'session_timeout' => 3600, // 1 hour
    ]
];
