<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API V1 Routes
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
    'prefix' => '/auth/jwt/'
], function ($router) {
    Route::post('logout', 'Auth\JWT\JwtAuthController@logout');
    Route::post('refresh', 'Auth\JWT\JwtAuthController@refresh');
    Route::post('login', 'Auth\JWT\JwtAuthController@login');
    Route::post('password/request/reset', 'Auth\JWT\JwtAuthController@sendResetPasswordOTP');
    Route::post('password/otp/verify', 'Auth\JWT\JwtAuthController@verifyOTP');
    Route::post('password/reset', 'Auth\JWT\JwtAuthController@resetPassword');
});

/**
 * User endpoints
 */

Route::group([
    'prefix' => 'user/'
], function ($router) {
    Route::get('me', 'UserController@me');
});



