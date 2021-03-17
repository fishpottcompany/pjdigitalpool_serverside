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

Route::post('/v1/member/guest', 'Api\v1\UserController@login_guest_user');

Route::post('/v1/member/loginn', 'Api\v1\UserController@register');

Route::middleware('auth:api')->get('/v1/member/logout', 'Api\v1\UserController@logout');

Route::post('/v1/member/forgot', 'Api\v1\UserController@send_password_reset_code');

Route::post('/v1/member/reset', 'Api\v1\UserController@verify_reset_code');

Route::middleware('auth:api')->post('/v1/admin/audios/add', 'Api\v1\UserController@add_audio');

Route::middleware('auth:api')->get('/v1/admin/audios/list', 'Api\v1\UserController@get_audios');

Route::middleware('auth:api')->post('/v1/admin/audios/remove', 'Api\v1\UserController@delete_audio');

Route::middleware('auth:api')->post('/v1/admin/videos/add', 'Api\v1\UserController@add_video');

Route::middleware('auth:api')->get('/v1/admin/videos/list', 'Api\v1\UserController@get_videos');

Route::middleware('auth:api')->post('/v1/admin/videos/remove', 'Api\v1\UserController@delete_video');

Route::middleware('auth:api')->post('/v1/admin/favorites/list', 'Api\v1\UserController@get_favorites');

Route::middleware('auth:api')->post('/v1/admin/messages/add', 'Api\v1\UserController@add_message');

Route::middleware('auth:api')->get('/v1/admin/prequests/list', 'Api\v1\UserController@get_prayer_requests');

Route::middleware('auth:api')->get('/v1/admin/feedbacks/list', 'Api\v1\UserController@get_feedbacks');

Route::middleware('auth:api')->get('/v1/admin/testimonies/list', 'Api\v1\UserController@get_testimonies');

Route::middleware('auth:api')->get('/v1/admin/users/list', 'Api\v1\UserController@get_users');

Route::middleware('auth:api')->post('/v1/admin/articles/add', 'Api\v1\UserController@add_article');

Route::middleware('auth:api')->post('/v1/admin/articles/remove', 'Api\v1\UserController@delete_article');

Route::middleware('auth:api')->get('/v1/admin/articles/list', 'Api\v1\UserController@get_articles');

Route::middleware('auth:api')->post('/v1/admin/today/notice/add', 'Api\v1\UserController@update_notice');

Route::middleware('auth:api')->get('/v1/admin/today/dasboard', 'Api\v1\UserController@get_dashboard');

Route::middleware('auth:api')->post('/v1/admin/payment/idmaker', 'Api\v1\UserController@get_transaction_id');

Route::middleware('auth:api')->get('/v1/admin/payment/update', 'Api\v1\UserController@update_transaction');

Route::middleware('auth:api')->get('/v1/admin/payment/list', 'Api\v1\UserController@get_payments');

Route::middleware('auth:api')->post('/v1/admin/notifications/send', 'Api\v1\UserController@send_notification');