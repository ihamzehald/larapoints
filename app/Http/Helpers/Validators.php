<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Carbon\Carbon;

trait Validators
{

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function isEmailValid(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

    /**
     * @param $otp
     * @param $lifetime
     * @return bool
     */
    public function isOTPValid($otp, $lifetime)
    {
        return $this->isExpired($otp->created_at, $lifetime);
    }

    /**
     * @param $token
     * @param $lifetime
     * @return bool
     */
    public function isOTPVerificationTokenValid($token, $lifetime)
    {
        return $this->isExpired($token->created_at, $lifetime);
    }

    /**
     * @param $date
     * @param $lifetime
     * @return bool
     */
    public function isExpired($date, $lifetime)
    {
        return Carbon::parse($date)->addHours($lifetime)->gte(Carbon::now())
            ? true
            : false;
    }
}
