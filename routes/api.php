<?php

use App\Http\Controllers\ApiController;

Route::group(['middleware' => 'api'], function () {
	Route::post('char/items', [ApiController::class, 'getItems']);
	Route::post('char/stats', [ApiController::class, 'getStats']);
	Route::get('favorites/chars', [ApiController::class, 'getFavsChars']);
	Route::get('twitch', [ApiController::class, 'getTwitchChars']);
	Route::get('pob_code', [ApiController::class, 'getXML']);
	Route::post('/build/save', [ApiController::class, 'saveBuild']);
	Route::get('/build/{hash}', [ApiController::class, 'getBuild']);
	Route::get('/snapshots/{acc}/{char}', [ApiController::class, 'getSnapshots']);
});
