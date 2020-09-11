<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during JWT authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'password_reset_request_otp_mail_title' => 'Your OTP code',
    'password_reset_request_otp_title' => 'Your OTP code',
    'password_reset_request_otp_body' => ':otp is your OTP to reset your password.',
    "success" => [
        "register" => "User registered successfully",
        "login" => "User logged in successfully",
        "logout" => "User logged out successfully",
        "refresh" => "JWT token refresh successfully",
        "otp_email" => "OTP email sent successfully",
        "otp_verification" => "OTP verified successfully",
        "reset_password" => "Password reset successfully"
    ],
    "error" => [
        "wrong_credentials_msg" => "The provided credentials don't match our records",
        "wrong_credentials" => "The provided credentials don't match our records",
        "otp_email" => "Oops, something went wrong while trying to send your OTP",
        "otp_expired" =>  "This OTP expired",
        "otp_invalid" =>  "This OTP invalid",
        "reset_password" => "Something went wrong while trying to reset your password"

    ]

];
