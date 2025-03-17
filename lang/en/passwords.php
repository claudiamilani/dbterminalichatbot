<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Passwords must be at least six characters and match the confirmation.',
    'reset' => 'Your password has been reset!',
    'sent' => 'We have e-mailed your password reset link!',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that e-mail address.",
    'reset_msg' => "Provide the email address associated with your account and choose a new password. ",
    'new_password' => "New password",
    'confirm_new_password' => "Confirm new password",
    'reset_password' => "Forgot password",
    'reset_password_msg' => "Provide the email address associated with your account. We'll send you a mail with instructions.",
    'recover_page_title' => "Password recovery",
    'reset_page_title' => "Reset password",
    'must_reset_password' => "Must reset password for logging in.",
    'change' => [
        'success' => 'Password was modified',
        'error' => 'Password not modified',
        'generic_error' => 'An error occurred. Password not modified',
        'wrong_password' => 'The entered password  is not valid',
        'mail_sent' => 'Your recover password email is sent'
    ],
    'resets' => [
        'generic_error' => 'Service temporarily unavailable',
        'invalid_token' => 'Token expired or not valid',
        'invalid_user' => 'The user doe not match with the requested Token',
    ]
];
