<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	Slack::send('Die!');
	Slack::to('@pat')->send('am i real?');
	Log::info("test");
	return View::make('hello');
});


Route::group(['prefix' => 'reviews'], function () {
	Route::get('test', 'ReviewController@testThis');

});