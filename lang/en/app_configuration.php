<?php

return [

    /*
    |--------------------------------------------------------------------------
    | App Configuration Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'title' => 'Configuration',
    'menu_title' => 'App Configuration',
    'edit' => [
        'title' => 'Modify Configuration',
        'success' => 'App Configuration was modified',
        'error' => 'An error occurred. App Configuration was not modified',
        'file_overwrite_warning' => 'Uploading a file will destroy the previus one on save',
        'passwords_reset_warning' => 'Modifying this parameter will alter the number of historic password saved for all the Users',
    ],
    'attributes' => [
        'wl_object' => 'Title of WL',
        'wl_body' => 'Text of WL',
        'pwdr_mail_obj_c' => 'Title Customer mail',
        'pwdr_mail_body_c' => 'Text Customer mail',
        'pwdr_mail_obj_u' => 'Title of User mail',
        'pwdr_mail_body_u' => 'Text of User mail ',
        'object' => 'Title',
        'body' => 'Text',
        'manual_file_name' => 'Customer Manual file namef',
        'manual_file_path' => 'Customer Manual File',
        'file_exists' => 'File uploaded',
        'file_404' => 'File not found',
        'max_failed_login_attempts' => 'N° of maximum successive failed access attempt',
        'failed_login_reset_interval' => 'Reset Timer for failed attempt (days)',
        'pwd_reset_unlocks_account' => 'Password Reset will unlock the account',
        'pwd_min_length' => 'Minimum password lenght',
        'pwd_regexp' => 'RegExp password complexity',
        'pwd_complexity_err_msg' => 'Complexity error message',
        'pwd_history' => 'N° historic password stored',
        'pwd_expires_in' => 'Password expires in (days)',
        'pwd_never_expires' => 'Password never expires',
    ],
    'fieldset' => [
        'manual' => 'Customer Manual',
        'wl' => 'Welcome Letter',
        'password_recovery_customer' => 'Recovery customers password Mail',
        'password_recovery_user' => 'Recovery Users password Mail',
        'security' => 'Account and Security'
    ],
    'placeholders_hint' => [
        'wl' => '',
        'pwdr_c' => 'It ss possible to use the placeholder %RESET_URL% in the body of the mail for generate the URL with the token for the password recovery',
        'pwdr_u' => 'It ss possible to use the placeholder %RESET_URL% in the body of the mail for generate the URL with the token for the password recovery'
    ]

];
