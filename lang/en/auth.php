<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'failed_with_attempts' => 'Attempts left :attempts_left',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'remember_me' => 'Remember Me',
    'login_msg' => 'Login to start session.',
    'login' => 'Login',
    'generic_error' => 'An error occurred. Not able to go further.',
    'ldap_unknown_user' => 'Unvalid credentials',
    'invalid_user' => 'Unvalis username',
    'locked' => 'Account blocked.<br>Please contact the Customer Care calling the <b>1928</b>',
    'disabled' => 'Account disabled',
    'pwd_complexity_error' => 'The password does not respect the complexity criteria required',
    'pwd_reset_unlock' => 'Account blocked. We suggest you to reset your password to unluck your account or contatc the staff of ' .env('APP_NAME'),
    'pwd_history_error' => 'You cannot use one of your recent used password. Choose a new password password',
    'insecure_pwd_should_be_changed' => 'The password does not respect the complexity criteria required. You must change password for a more secure one.',



];
