<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'User Session',
    'menu_title' => 'User Session',

    'delete' => [
        'title' => 'Terminate User Session',
        'confirm_msg' => 'Confirm terminating User Session?',
        'success' => 'Session was terminated',
        'error' => 'An error occurred. The User Session was not terminated',
    ],
    'purge' => [
        'title_auth' => 'Terminate all the authenticated sessions',
        'confirm_msg_auth' => 'Confirm terminating all authenticated sessions?',
        'title' => 'Terminate all the unauthenticated sessions',
        'confirm_msg' => 'Confirm terminating all unauthenticated sessions?',
        'success' => 'Session was terminated',
        'error' => 'An errorr occurred. The sessions were not terminated',
    ],
    'attributes' => [
        'id' => 'ID',
        'user' => 'User',
        'nameWithMail' => 'Nominative',
        'ip_address' => 'IP address',
        'user_agent' => 'Customer',
        'last_activity' => 'Last activity date',
        'robot' => 'Robot',
        'expiring_in' => 'Expiring in',
        'browser' => 'Browser',
        'os' => 'Operating System',
    ],
    'placeholders' => [

    ],
    'profile' => 'Profile',
    'sections' => [
        'u_sessions' => 'Unauthenticated sessions',
        'a_sessions' => 'Authenticated sessions',
    ],
    'session_driver_error' => "For the sessions managing must be used the custom-database driver",
];