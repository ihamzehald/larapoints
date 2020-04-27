<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**
 * JWT auth endpoints
 */

Route::group([
    'prefix' => 'user/auth/jwt/'
], function ($router) {
    Route::post('logout', 'Auth\JWT\JwtAuthController@logout');
    Route::post('refresh', 'Auth\JWT\JwtAuthController@refresh');
    Route::get('me', 'Auth\JWT\JwtAuthController@me');
    Route::post('login', 'Auth\JWT\JwtAuthController@login');
    Route::post('password/request/reset', 'Auth\JWT\JwtAuthController@sendResetPasswordOTP');
    Route::post('password/otp/verify', 'Auth\JWT\JwtAuthController@verifyOTP');
    Route::post('password/reset', 'Auth\JWT\JwtAuthController@resetPassword');
});



