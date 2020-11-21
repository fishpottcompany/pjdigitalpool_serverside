<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('web/login');
});

Route::get('/admin', function () {
    return view('web/login');
});

Route::get('/admin/login', function () {
    return view('web/login');
});


/*
|--------------------------------------------------------------------------
| AUDIOS
|--------------------------------------------------------------------------
|
*/
Route::get('/admin/audios/add', function () {
    return view('web/audios/add');
});

Route::get('/admin/audios/delete', function () {
    return view('web/audios/delete');
});


//Route::post('/upload-file', [UserController::class, 'add_audio'])->name('add_audio');
Route::post('/v1/admin/audios/add', 'Api\v1\UserController@add_audio')->name('add_audio');

/*
|--------------------------------------------------------------------------
| VIDEOS
|--------------------------------------------------------------------------
|
*/
Route::get('/admin/videos/add', function () {
    return view('web/videos/add');
});

Route::get('/admin/videos/delete', function () {
    return view('web/videos/delete');
});

/*
|--------------------------------------------------------------------------
| USERS
|--------------------------------------------------------------------------
|
*/
Route::get('/admin/users/list', function () {
    return view('web/users/list');
});

/*
|--------------------------------------------------------------------------
| MESSAGES
|--------------------------------------------------------------------------
|
*/
Route::get('/admin/prequests/list', function () {
    return view('web/prequests/list');
});
Route::get('/admin/testimonies/list', function () {
    return view('web/testimonies/list');
});
Route::get('/admin/feedbacks/list', function () {
    return view('web/feedbacks/list');
});

/*
|--------------------------------------------------------------------------
| AUDIOS
|--------------------------------------------------------------------------
|
*/
Route::get('/admin/articles/add', function () {
    return view('web/articles/add');
});

Route::get('/admin/articles/delete', function () {
    return view('web/articles/delete');
});


/*
|--------------------------------------------------------------------------
| TODAY
|--------------------------------------------------------------------------
|
*/
Route::get('/admin/today/notice', function () {
    return view('web/today/notice');
});

