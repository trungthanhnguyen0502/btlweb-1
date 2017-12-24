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

use Illuminate\Support\Facades\Route;

/**
 * Auth Routes
 */

//Route::get('', function (\Illuminate\Http\Request $request) {
//   return $request->session()->get('login_key');
//});

Route::prefix('auth')->group(function () {

    Route::get('captcha', 'Auth\\CaptchaController@index')->name('captcha');

    Route::get('login', 'Auth\\LoginController@index')
        ->name('login')
        ->middleware('guest');

    Route::post('login/attempt', 'Auth\\LoginController@attempt')
        ->name('login.attempt')
        ->middleware('guest');

    Route::get('logout', 'Auth\\LoginController@logout')
        ->name('logout');

    Route::any('request-password', 'Auth\\ForgotPasswordController@request_password')
        ->name('password.request');

    Route::any('reset-password', 'Auth\\ForgotPasswordController@reset_password')
        ->name('password.reset');
});

/**
 * APIs
 */


Route::group(
    ['prefix' => 'api', 'middleware' => 'auth'],

    function () {

        // Ticket APIs

            // Create ticket
        Route::post('create-ticket', 'APIs\\TicketController@create_ticket')
            ->name('ticket.create');

            // Get ticket by id
        Route::get('get-ticket/{ticket_id}', 'APIs\\TicketController@get_ticket')
            ->where(['ticket_id' => '[0-9]*'])
            ->name('ticket.get');

            // Get ticket by param options
        Route::get('get-tickets', 'APIs\\TicketController@get_tickets')
            ->name('ticket.query');

            // Search tickets by subject
        Route::post('search-ticket', 'APIs\\TicketController@search_ticket')
            ->name('ticket.search');

            // Change read status for a ticket
        Route::post('read-ticket', 'APIs\TicketController@read')
            ->name('ticket.read');

            // Edit relaters
        Route::post('add-relaters', 'APIs\\TicketController@edit_relaters')
            ->name('ticket.add_relaters');

            // Post comment to ticket thread
        Route::post('comment', 'APIs\\TicketThreadController@post_comment')
            ->name('ticket.post_comment');
        // Attachment URL
        Route::get('attachment/{id}/{filename}', 'APIs\\TicketAttachmentController@get_attachment')
            ->name('ticket.attachment');

        // Employee APIs

            // Get current logged-in employee
        Route::get('employee-info', 'APIs\\EmployeeController@get_employee_info')
            ->name('employee.info');

            // Search Employee
        Route::post('search-employee', 'APIs\\EmployeeController@search_employee')
            ->name('employee.search');

        // Team APIs

            // Get list of teams
        Route::get('teams', 'APIs\\TeamController@get_teams');


        // Edit Ticket Routes
        Route::group(
            ['prefix' => 'edit-ticket'],

            function () {

                Route::put('deadline', 'APIs\\EditTicketController@change_deadline');

                Route::put('priority', 'APIs\\EditTicketController@change_priority');

                Route::put('team', 'APIs\\EditTicketController@change_team');

                Route::put('assigned_to', 'APIs\\EditTicketController@assigned_to');
            }
        );
    }
);

Route::group(['prefix' => 'default'], function () {

//    Route::get('profile-picture.{png}', )
});

/**
 * Home Redirecting
 * Redirecting to app
 */

Route::get('/', 'AppController@redirect')
    ->middleware('auth')
    ->name('home');

/**
 * All other routes point to app main page
 */

Route::get('{path}', 'AppController@index')
    ->middleware('auth')
    ->name('app');
