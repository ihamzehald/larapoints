<?php

namespace App\Http\Helpers;

use Illuminate\Support\Str;

trait Generators{
    public function generateOTP($otpLength=6){
        return Str::random($otpLength);
    }
}