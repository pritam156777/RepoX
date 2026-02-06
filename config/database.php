<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */

    // FORCE MYSQL – never allow sqlite in production
    'default' => 'mysql',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [

        /*
        |----------------------------------------------------------------------
        | SQLite (DISABLED)
        |----------------------------------------------------------------------
        | Kept only to avoid framework errors, but unusable
        */

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => null,
        ],

        /*
        |----------------------------------------------------------------------
        | MySQL (PRIMARY & ONLY DB)
        |----------------------------------------------------------------------
        */

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => [
        'table' => 'migrations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis (OPTIONAL – SAFE DEFAULTS)
    |--------------------------------------------------------------------------
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => Str::slug(env('APP_NAME', 'laravel')).'-database-',
        ],

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
