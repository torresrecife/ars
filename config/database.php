<?php

use Illuminate\Support\Str;

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', dirname(__DIR__) . '/database/database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', env('DB_MYSQL_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', env('DB_MYSQL_PORT', '3306')),
            'database' => env('DB_DATABASE', env('DB_MYSQL_NAME', 'forge')),
            'username' => env('DB_USERNAME', env('DB_MYSQL_USER', 'forge')),
            'password' => env('DB_PASSWORD', env('DB_MYSQL_PASS', '')),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', env('DB_MYSQL_CHARSET', 'utf8mb4')),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('SQLSRV_DATABASE_URL', env('DATABASE_URL')),
            'host' => env('SQLSRV_DB_HOST', env('DB_SQLSRV_HOST', 'localhost')),
            'port' => env('SQLSRV_DB_PORT', env('DB_SQLSRV_PORT', '1433')),
            'database' => env('SQLSRV_DB_DATABASE', env('DB_SQLSRV_NAME', 'forge')),
            'username' => env('SQLSRV_DB_USERNAME', env('DB_SQLSRV_USER', 'forge')),
            'password' => env('SQLSRV_DB_PASSWORD', env('DB_SQLSRV_PASS', '')),
            'charset' => env('SQLSRV_DB_CHARSET', env('DB_SQLSRV_CHARSET', 'utf8')),
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    'migrations' => 'migrations',

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'ars'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],
];
