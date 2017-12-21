<?php

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

/**
 * Auth Routes
 */

//Route::get('', function (\Illuminate\Http\Request $request) {
//   return $request->session()->get('login_key');
//});

Route::prefix('auth')->group(function () {

    Route::get('captcha', 'Auth\\CaptchaController@index')->name('captcha');

    Route::get('login', 'Auth\\LoginController@index')->name('login');

    Route::post('login/attempt', 'Auth\\LoginController@attempt')->name('login.attempt');

    Route::get('logout', 'Auth\\LoginController@logout')->name('logout');
});

/**
 * APIs Routes
 */

Route::prefix('api')->group(function () {

    Route::any('createRequest', 'APIs\\TicketApiController@create_ticket');

    Route::any('getTicket', 'APIs\\TicketApiController@get_ticket');
});

/**
 * Other Routes
 */

Route::get('/', 'AppController@redirect')
    ->middleware('auth')
    ->name('home_redirecting');

Route::get('{path}', 'AppController@index')
    ->middleware('auth')
    ->name('app');