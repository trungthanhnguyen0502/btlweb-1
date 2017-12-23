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

    Route::get('login', 'Auth\\LoginController@index')->name('login')->middleware('guest');

    Route::post('login/attempt', 'Auth\\LoginController@attempt')->name('login.attempt')->middleware('guest');

    Route::get('logout', 'Auth\\LoginController@logout')->name('logout');

    Route::any('request-password', 'Auth\\ForgotPasswordController@request_password')->name('password.request');

    Route::any('reset-password', 'Auth\\ForgotPasswordController@reset_password')->name('password.reset');
});

/**
 * APIs Routes
 */


Route::group(
    ['prefix' => 'api', 'middleware' => 'auth'],

    function () {

        Route::post('create-ticket', 'APIs\\TicketController@create_ticket');
        Route::get('get-tickets', 'APIs\\TicketController@get_tickets');
        Route::get('count-tickets', 'APIs\\TicketController@count_tickets');

        // Post comment to ticket thread
        Route::post('comment', 'APIs\\TicketController@comment');
        // Attachment URL
        Route::get('attachment/{id}/{filename}', 'APIs\\TicketAttachmentController@get_attachment');

        // Employee APIs

        // Get current logged-in employee
        Route::get('employee-info', 'APIs\\EmployeeController@get_employee_info');
        // Search Employee
        Route::post('search-employee', 'APIs\\EmployeeController@search_employee');
    }
);

/**
 * Test Routes
 */

Route::get('create-request', 'TicketController@create_ticket');


/**
 * Home Route
 *
 * Redirecting to app
 */

Route::get('/', 'AppController@redirect')
    ->middleware('auth')
    ->name('home');

/**
 * All other routes point to app
 */

Route::get('{path}', 'AppController@index')
    ->middleware('auth')
    ->name('app');