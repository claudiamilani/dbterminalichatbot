<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    'title' => 'User Accounts',
    'menu_title' => 'Account',
    'create' => [
        'title' => 'Create new account',
        'success' => 'Account created successfully.',
        'error' => 'An error occurred. Account not created.',
    ],
    'edit' => [
        'title' => 'Edit account',
        'success' => 'Account updated successfully.',
        'error' => 'An error occurred. Account not updated.',
    ],
    'delete' => [
        'title' => 'Delete account',
        'confirm_msg' => "Please confirm account deletion.",
        'success' => "Account successfully deleted.",
        'error' => "An error occurred. Account not deleted.",
    ],
    'attributes' => [
        'name' => 'Name',
        'surname' => 'Surname',
        'auth_type_id' => 'Authentication Type',
        'locked' => 'Locked',
        'user' => 'User',
        'pwd_change_required_on' => 'Change Password requested',
        'pwd_change_required_off' => 'No change Password',
        'enabled' => 'Status',
        'enabled_on' => 'Active',
        'enabled_off' => 'Disactive',
        'enabled_from' => 'Enabled from',
        'enabled_to' => 'Enable to',
        'fullname' => 'Full name',
        'email' => 'Email',
        'current_password' => 'Current Password',
        'password' => 'Password',
        'password_check' => 'Password check',
        'roles' => 'Roles',
        'isAdmin' => 'Admin',
        'customer_department_id' => 'Department',
        'login_success_on' => 'Last access',
        'login_success_ipv4' => 'Last IP access',
        'login_failed_ipv4' => 'Last failed IP access',
        'login_failed_on' => 'Last failed access',
        'failed_login_count' => 'Failed accesses',
        'pwd_changed_at' => 'Last password changed at ',
        'browser' => 'Browser' // not found on the IT translation file
    ],
    'placeholders' => [
        'password_change' => 'Required only to change it',
        'password' => 'Password for this account',
        'password_check' => 'Repeat the password',
    ],
    'profile' => 'Profile'

];
