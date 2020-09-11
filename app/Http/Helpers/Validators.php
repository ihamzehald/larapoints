<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

trait Validators
{


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

    /**
     * Generic validation error method
     * @param $request
     * @param $rules
     * @return bool|\Illuminate\Support\MessageBag
     */
    public function requestHasErrors($request, $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return false;
    }

}
