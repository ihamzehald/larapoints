<?php

namespace App\Http\Helpers;

use Illuminate\Support\Str;

trait Generators
{

    /**
     * @param int $length
     * @return string
     */
    public function generateOTP($length = 6)
    {
        return Str::random($length);
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateOTPVerificationToken($length = 64)
    {
        return Str::random($length);
    }


    /**
     * @param $token
     * @return array
     */
    public function generateAccessTokenDetails($token)
    {
        return  [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth("api_jwt")->factory()->getTTL() * env('JWT_TTL', 60)
        ];
    }
}
