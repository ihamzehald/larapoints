<?php

namespace App\Http\Helpers;

use Illuminate\Support\Str;

trait Generators{

    public function generateOTP($length=6){
        return Str::random($length);
    }

    public function generateOTPVerificationToken($length=64){
        return Str::random($length);
    }

}