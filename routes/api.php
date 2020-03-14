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

Route::middleware('auth:api')->get('/user', function (Request $request) {
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

    'middleware' => 'api',
    'prefix' => 'auth/jwt/'

], function ($router) {

    Route::post('login', 'JwtAuthController@login');
    Route::post('logout', 'JwtAuthController@logout');
    Route::post('refresh', 'JwtAuthController@refresh');
    Route::post('me', 'JwtAuthController@me');

});