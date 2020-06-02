<?php

namespace App\Http\Helpers;

class Constants
{

    /**
     * OTP statuses
     */

    const RESET_PASSWORD_OTP_CREATED = 1;
    const RESET_PASSWORD_OTP_ACTIVATED = 2;
    const RESET_PASSWORD_OTP_EXPIRED = 3;

    /**
     * OTP lifetime
     * In hours
     */

    const OTP_LIFETIME = 1;

    /**
     * OTP verification statuses
     */

    const RESET_PASSWORD_OTP_VERIFICATION_CREATED = 1;
    const RESET_PASSWORD_OTP_VERIFICATION_ACTIVATED = 2;
    const RESET_PASSWORD_OTP_VERIFICATION_EXPIRED = 3;

    /**
     * OTP verification token lifetime
     * In hours
     */

    const OTP_VERIFICATION_TOKEN_LIFETIME = 1;


    /**
     * HTTP statuses
     */

    const HTTP_SUCCESS = 200;
    const HTTP_ERROR = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
}
