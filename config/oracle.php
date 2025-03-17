<?php

return [
    'oracle' => [
        'driver' => 'oracle',
        'tns' => env('LDB_TNS', ''),
        'host' => env('LDB_HOST', ''),
        'port' => env('LDB_PORT', '1521'),
        'database' => env('LDB_DATABASE', ''),
        'service_name' => env('LDB_SERVICE_NAME', ''),
        'username' => env('LDB_USERNAME', ''),
        'password' => env('LDB_PASSWORD', ''),
        'charset' => env('LDB_CHARSET', 'AL32UTF8'),
        'prefix' => env('LDB_PREFIX', ''),
        'prefix_schema' => env('LDB_SCHEMA_PREFIX', ''),
        'edition' => env('LDB_EDITION', 'ora$base'),
        'server_version' => env('LDB_SERVER_VERSION', '11g'),
        'load_balance' => env('LDB_LOAD_BALANCE', 'yes'),
        'max_name_len' => env('ORA_MAX_NAME_LEN', 30),
        'dynamic' => [],
        'sessionVars' => [
            'NLS_TIME_FORMAT' => 'HH24:MI:SS',
            'NLS_DATE_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
            'NLS_NUMERIC_CHARACTERS' => '.,',
        ],
    ],
];
