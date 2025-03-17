<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AWS & Secrets Details
    |--------------------------------------------------------------------------
    |
    |
    */

    'region' => env('AWS_REGION','eu-south-1'),
    'enable_secrets_manager' => env('ENABLE_SECRETS_MANAGER', false),
    'secrets_list' => env('AWS_SECRETS_LIST', 'vas-secret-db-mdm-dbterminali,vas-secret-db-legacy-dbterminali,vas-secret-cache-mdm-dbterminali,vas-secret-endpoint-mdm-dbterminali-rtmp,vas-secret-sftp-legacy-dbterminali,vas-secret-endpoint-mdm-dbterminali-samlvas'),

    /*
    |--------------------------------------------------------------------------
    | Tag used to return list of Secrets
    |--------------------------------------------------------------------------
    |
    | All the secrets with the `dev` tag will be loaded into environment variables.
    |
    */

    'retrieve_secrets_list' => env('AWS_RETRIEVE_SECRETS_LIST', false),
    'filter_secrets' => env('AWS_FILTER_SECRETS', false),
    'tag_name' => env('AWS_SECRETS_TAG_NAME', 'stage'),
    'tag_value' => env('AWS_SECRETS_TAG_VALUE', 'dev'),

    /*
    |--------------------------------------------------------------------------
    | Environments where this service provider is enabled
    |--------------------------------------------------------------------------
    |
    | List the environment names in an array where this package should be enabled,
    | it will be compared against env('APP_ENV') set in the app.yaml file.
    | Should be a comma separated list of values. E.g. production,staging
    |
    */

    'enabled_environments' => env('AWS_SECRETS_ENABLED_ENV', 'production,staging,local'),

    /*
    |--------------------------------------------------------------------------
    | Variables that require overwriting the config
    |--------------------------------------------------------------------------
    |
    | Some (not all) variables are set into the config, as such updating the env() will not overwrite
    | the config cached values. The variables below will overwrite the config.
    |
    | Example:
    | .env
    | VARIABLES_CONFIG_KEYS=APP_KEY:app.key,OTHER_KEY:app.other_key
    |
    */

    'variables_config' => env('AWS_SECRETS_VARIABLES_CONFIGS',[
        'database.connections.oracle.username' => 'LDB_USERNAME',
        'database.connections.oracle.password' => 'LDB_PASSWORD',
        'database.redis.default.password' => 'REDIS_PASSWORD',
        'database.redis.cache.password' => 'REDIS_PASSWORD',
        'database.redis.horizon.password' => 'REDIS_PASSWORD',
        'database.connections.pgsql.username' => 'DB_USERNAME',
        'database.connections.pgsql.password' => 'DB_PASSWORD',
        'dbt.configuration_rtmp.session_user' => 'CONFIGURATION_SESSION_RTMP_USER',
        'dbt.configuration_rtmp.session_password' => 'CONFIGURATION_SESSION_RTMP_PASSWORD',
        'dbt.configuration_rtmp.request_user' => 'CONFIGURATION_REQUEST_RTMP_USER',
        'dbt.configuration_rtmp.request_password' => 'CONFIGURATION_REQUEST_RTMP_PASSWORD',
        'filesystems.disks.remote.username' => 'REMOTE_SFTP_USERNAME',
        'filesystems.disks.remote.password' => 'REMOTE_SFTP_PASSWORD',
        'dbt.samlvas.token' => 'SAMLVAS_TOKEN',
    ]),

    /*
    |--------------------------------------------------------------------------
    | Cache Enabled
    |--------------------------------------------------------------------------
    |
    | Boolean if you would like to enable cache. Datastore requests can add an additional 100-250ms
    | of latency to each request. It is recommended to use caching to significantly reduce this latency.
    |
    */

    'cache_enabled' => env('AWS_SECRETS_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Cache Expiry
    |--------------------------------------------------------------------------
    |
    | The length of time that the Cache should be enabled for in minutes. 30-60 minutes is recommended.
    |
    */

    'cache_expiry' => env('AWS_SECRETS_CACHE_EXPIRY', 30),

    /*
    |--------------------------------------------------------------------------
    | Cache Store
    |--------------------------------------------------------------------------
    |
    | Define the cache store that you wish to use (this must be configured in your config.cache file).
    | Note: you can only use a store that does not require credentials to access it. As such file is suggested.
    |
    */

    'cache_store' => env('AWS_SECRETS_CACHE_STORE', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Key rotation
    |--------------------------------------------------------------------------
    |
    | If key rotation is enabled, force retrieving config if NextRotationDate is in the past
    |
    */

    'key_rotation' => env('AWS_SECRETS_KEY_ROTATION', false),

    /*
    |--------------------------------------------------------------------------
    | Debugging
    |--------------------------------------------------------------------------
    |
    | Enable debugging, latency introduced by this package on bootstrapping is calculated and logged
    | to the system log (viewable in stackdriver).
    |
    */

    'debug' => env('APP_DEBUG', false),

];