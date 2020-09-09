<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/v1/member/register', 'Api\v1\UserController@register');

Route::post('/v1/member/login', 'Api\v1\UserController@login');

Route::middleware('auth:api')->get('/v1/member/logout', 'Api\v1\UserController@logout');

Route::post('/v1/member/forgot', 'Api\v1\UserController@send_password_reset_code');

Route::post('/v1/member/reset', 'Api\v1\UserController@verify_reset_code');
