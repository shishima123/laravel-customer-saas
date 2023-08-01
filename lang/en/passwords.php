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

    'reset' => 'Your password has been reset!',
    'sent' => 'We have emailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",
    'disable' => "Your account has been disabled",
    'email_reset_pass' => [
        'subject' => 'Reset Password Notification',
        'reset_pass' => 'Reset password',
        'line_1' => "You are receiving this email because we received a password reset request for your account.",
        'line_2' => "This password reset link will expire in :count minutes.",
        'line_3' => "If you did not request a password reset, no further action is required."
    ]

];
