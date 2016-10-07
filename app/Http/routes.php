<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', function () {
    return view('home');
});

Route::group(array('prefix' => 'api/v1'), function()
{
    Route::get('lists/', [
        'middleware' => 'web',
        'uses' => 'MailchimpController@index'
    ]);

    Route::post('lists/', [
        'middleware' => 'web',
        'uses' => 'MailchimpController@store'
    ]);

    Route::post('lists/{list_id?}', [
        'middleware' => 'web',
        'uses' => 'MailchimpController@addListMember'
    ]);

    Route::put('lists/{list_id?}/members/{subscriber_hash?}', [
        'middleware' => 'web',
        'uses' => 'MailchimpController@updateListMember'
    ]);

    Route::resource('mailchimp', 'MailchimpController');
});