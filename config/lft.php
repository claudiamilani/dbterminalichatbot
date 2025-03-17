<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

return [
    'admin_routes' => [
        'fixed_domain' => ENV('USE_ADMIN_DOMAIN', false), // If true, Admin routes work only under the specified value in domain key
        'domain' => env('ADMIN_DOMAIN', 'lft-app.local'), // Used if 'fixed_domain' key is true
        'prefix' => env('ADMIN_ROUTES_PREFIX', 'admin'), // Used when 'fixed_domain' key is false and public routes are enabled
        'as' => env('ADMIN_ROUTES_ALIAS', 'admin::'),
        'middleware' => ['web'], // Middleware used on all admin application routes
        'auth_middleware' => ['auth:admins'], // Middlewares used on authenticated admin routes
        'login_route' => 'login',
        'home' => 'dashboard',
        'password_reset_route' => 'password.reset'
    ],
    'public_routes' => [
        'enabled' => env('ENABLE_PUBLIC_AREA', false),
        'domain' => env('PUBLIC_DOMAIN', false),
        'prefix' => env('PUBLIC_ROUTES_PREFIX', ''),
        'as' => 'site::',
        'middleware' => ['web'], // Middleware used on all public application routes
        'auth_middleware' => ['auth'], // Middlewares used on authenticated public routes
        'login_route' => 'login',
        'home' => 'index',
        'password_reset_route' => 'password.reset'
    ],
    'api_routes' => [
        'enabled' => true
    ],
    'manuals_path' => env('MANUALS_PATH', 'manuals'),
    'defaults' => [
        'external_roles' => [
            'local' => [],
            'testing' => [],
            'staging' => [],
            'production' => [],
        ],
        'permissions' => [
            'Administrator' => [
                '*'
            ],
        ],
    ],
    'copyright_owner' => env('COPYRIGHT_OWNER','WindTre S.p.A.'),

    'azure_login' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'frontend_redirect' => env('AZURE_FRONTEND_REDIRECT_URI'),
        'tenant_id' => env('AZURE_TENANT_ID'),
    ],
    'bidx_key' => env('BIDX_KEY', ''),
];