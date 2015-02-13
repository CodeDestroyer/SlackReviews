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

Event::listen('illuminate.query', function($query)
{
	Log::info($query);
});
Route::get('/', function()
{
	Slack::send('Die!');
	Slack::to('@pat')->send('am i real?');
	Log::info("test");
	return View::make('hello');
});


Route::group(['prefix' => 'reviews'], function () {
	Route::get('request', 'ReviewController@requestReview');
	Route::get('complete', 'ReviewController@completeReview');
	Route::get('claim', 'ReviewController@claimReview');
	Route::get('list','ReviewController@listReviewsToUser');
	Route::get('drop','ReviewController@dropReview');
});

Route::group(['prefix' => 'deploy'], function () {
	Route::get('request', 'DeploymentController@addDeployment');
	Route::get('stage', 'DeploymentController@stageDeployment');
	Route::get('deploy', 'DeploymentController@deployDeployment');
	Route::get('validate', 'DeploymentController@validateDeployment');
	Route::get('stagingValidate', 'DeploymentController@validateStaging');
	Route::get('list', 'DeploymentController@listDeployments');


;
});