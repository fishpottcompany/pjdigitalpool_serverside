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



Route::post('/v1/member/register', 'Api\v1\UserController@register');

Route::post('/v1/member/login', 'Api\v1\UserController@login');

Route::middleware('auth:api')->get('/v1/member/logout', 'Api\v1\UserController@logout');

Route::post('/v1/member/forgot', 'Api\v1\UserController@send_password_reset_code');

Route::post('/v1/member/reset', 'Api\v1\UserController@verify_reset_code');

Route::middleware('auth:api')->post('/v1/admin/audios/add', 'Api\v1\UserController@add_audio');

Route::middleware('auth:api')->get('/v1/admin/audios/list', 'Api\v1\UserController@get_audios');

Route::middleware('auth:api')->post('/v1/admin/videos/add', 'Api\v1\UserController@add_video');

Route::middleware('auth:api')->get('/v1/admin/videos/list', 'Api\v1\UserController@get_videos');