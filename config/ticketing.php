<?php

return [
    'use_package_routes' => true,
    'admin_routes' => [
        'prefix' => 'admin/ticketing',
        'middleware' => ['web','auth:admins'],
    ],
    'public_routes' => [
        'prefix' => 'support',
        'middleware' => ['web','auth'],
    ],
    'file_upload' => [
        'allowed_extensions' => ['csv', 'image', 'doc', 'txt'],
        'allowed_mime_types' => [
            'csv' => [
                'application/csv',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
                'text/anytext',
                'text/comma-separated-values'
            ],
            'image' => [
                'image/jpeg',
                'image/jpeg',
                'image/png',
                'image/bmp',
                'image/gif',
                'image/tiff',
                'application/postscript',
                'image/svg+xml',
            ],
            'doc' => [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/pdf'
            ],
            'txt' => [
                'text/plain'
            ]

        ]
    ],

    'routes' => [
        'backend' => env('TICKETING_ADMIN_BASE_URI',"admin.lft-app.local:8081"),
        'frontend' => env('TICKETING_FRONTEND_BASE_URI',"lft-app.local:8081")
    ],

    'schedule' => env('TICKETING_SCHEDULE', false),

    'custom_forms' => [
        'user' => [
            'namespace' => 'App\\Auth\\',
            'label' => 'Account',
            'model' => 'user',
            'filters' => [
                'filter1' => [
                    'label' => 'Enabled',
                    'method' => 'scopeEnabled',
                    'call' => 'enabled'
                ],
                'filter2' => [
                    'label' => 'Not Expired',
                    'method' => 'scopeNotExpired',
                    'call' => 'notExpired'
                ]
            ],
            'key' => 'id',
            'value' => 'fullname'
        ]

]
];