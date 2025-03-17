<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stderr'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/lft-app.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/lft-app.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
        ],
        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'admin_gui_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/admin_gui.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'auth_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/auth.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'mail_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mail.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'legacy_import_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/legacy_import.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'RTMP_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/rtmp.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'ingestion_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/ingestion.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'transpose_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/transpose.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'aws-sm_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/aws_sm.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],
        'rtmp_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/rtmp.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'ignore_exceptions' => false,
        ],


        'admin_gui' => [
            'driver' => 'stack',
            'channels' => ['stderr','admin_gui_daily'],
            'ignore_exceptions' => false,
        ],
        'auth' => [
            'driver' => 'stack',
            'level' => env('LOG_LEVEL', 'debug'),
            'channels' => ['stderr','auth_daily'],
            'ignore_exceptions' => false,
        ],
        'mail' => [
            'driver' => 'stack',
            'level' => env('LOG_LEVEL', 'debug'),
            'channels' => ['stderr','mail_daily'],
            'ignore_exceptions' => false,
        ],
        'legacy_import' => [
            'driver' => 'stack',
            'level' => env('LOG_LEVEL', 'debug'),
            'channels' => ['stderr','legacy_import_daily'],
            'ignore_exceptions' => false,
        ],
        'RTMP' => [
            'driver' => 'stack',
            'level' => env('LOG_LEVEL', 'debug'),
            'channels' => ['stderr','rtmp_daily'],
            'ignore_exceptions' => false,
        ],
        'ingestion' => [
            'driver' => 'stack',
            'level' => env('LOG_LEVEL', 'debug'),
            'channels' => ['stderr','ingestion_daily'],
            'ignore_exceptions' => false,
        ],
        'transpose' => [
            'driver' => 'stack',
            'level' => env('LOG_LEVEL', 'debug'),
            'channels' => ['stderr','transpose_daily'],
            'ignore_exceptions' => false,
        ],
        'aws-sm' => [
            'driver' => 'stack',
            'level' => env('LOG_LEVEL', 'debug'),
            'channels' => ['stderr','aws-sm_daily'],
            'ignore_exceptions' => false,
        ],
    ],

];
