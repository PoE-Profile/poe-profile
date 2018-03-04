<?php

use Illuminate\Http\Request;


Route::group(['middleware' => 'api'], function () {
	Route::post('char/items', 'ApiController@getItems');
	Route::post('char/stats', 'ApiController@getStats');
	Route::get('ladderData', 'ApiController@getLadder');
	Route::get('favorites/chars', 'ApiController@getFavsChars');
	Route::get('twitch', 'ApiController@getTwitchChars');
	Route::post('getPoBCode', 'ApiController@getXML');
	Route::post('/build/save', 'ApiController@saveBuild');
	Route::get('/build/{hash}', 'ApiController@getBuild');

	// Route::get('/getBuild', function() {
	// 	$build = \App\Build::find(3);
	// 	return $build;
	// });
});
