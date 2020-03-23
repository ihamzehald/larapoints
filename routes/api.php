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

Route::middleware('auth:api_token')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Token auth endpoints
 */

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('logout', 'Auth\LoginController@logout');
});

Route::post('register', "Auth\RegisterController@register");
Route::post('login', 'Auth\LoginController@login');

/**
 * JWT auth endpoints
 */

Route::group([
    'prefix' => 'auth/jwt/'
], function ($router) {
    Route::post('logout', 'Auth\JWT\JwtAuthController@logout');
    Route::post('refresh', 'Auth\JWT\JwtAuthController@refresh');
    Route::post('me', 'Auth\JWT\JwtAuthController@me');
    Route::post('login', 'Auth\JWT\JwtAuthController@login');
    Route::post('password/request/reset', 'Auth\JWT\JwtAuthController@sendResetPasswordOTP');
});



/**
 * Forgot password routes
 */

//Route::group([
//
//    'middleware' => 'api',
//
//], function ($router) {
//
//    /**
//     * Web app flow
//     * These routes follow the web flow as the following:
//     * 1- User send reset password request to his email
//     * 2- User receive a reset password link to his inbox then click on reset password button
//     * 3- User get redirected to a reset password web portal
//     * 4- User enter his new password and confirm password in the web form and submit the form
//     * 5- If the password reset successfully the user wil be loggedin to the application
//     * as default laravel reset password behaviour
//     *
//     */
//
//    /**
//     * Mobile flow:
//     * 1- user request reset password token.
//     * 2- a token sent to user inbox.
//     * 3- user use this token to send verify request
//     * 4- A verify request returns a tmp access token
//     * 5- the user use this access token to reset his password
//     */
//    Route::post('auth/request-reset-password', 'Auth\ApiForgotPasswordController@sendResetLinkEmail');
////    Route::post('logout', 'JwtAuthController@logout');
////    Route::post('refresh', 'JwtAuthController@refresh');
////    Route::post('me', 'JwtAuthController@me');
//
//});

//$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');