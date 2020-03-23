<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;

trait Validators{

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function isEmailValid(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

}