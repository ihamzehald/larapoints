<?php

namespace App\Http\Helpers;

use Illuminate\Support\Str;

trait Generators{

    /**
     * @param int $length
     * @return string
     */
    public function generateOTP($length=6){
        return Str::random($length);
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateOTPVerificationToken($length=64){
        return Str::random($length);
    }

}