<?php

// use DB;
use App\ApiPages;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use GuzzleHttp\Client;

// \DB::listen(function($sql) {
//     var_dump($sql->sql);
// });

Route::group(['middleware' => 'web'], function () {

    Route::get('/ladders', ['as' => 'ladders', 'uses' => function () {
        return view('ladder');
    }]);

    Route::get('/twitch', ['as' => 'twitch', 'uses' => function () {
        return view('twitch');
    }]);
    
    Route::get('/favorites', ['as' => 'favorites', 'uses' => function () {
        return view('favorites');
    }]);

    Route::get('/passive-skill-tree/{any}', function () {
        return view('tree');
    });

    Route::get('/character-window/get-passive-skills', function () {
        $client = new \GuzzleHttp\Client();
        $dbAcc = \App\Account::where('name', $_GET['accountName'])->first();
        $acc=$dbAcc->name;

        $responseThree = $client->request(
            'GET',
            'https://www.pathofexile.com/character-window/get-passive-skills',
            [
            'query' => [
                'accountName' => $acc,
                'character' => $_GET['character']
            ]
        ]
        );

        return json_decode((string)$responseThree->getBody(), true);
    });

    Route::get('/about', function () {
        return view('about');
    });

    Route::get('/update_notes', function () {
        return view('update_notes');
    });

    Route::get('/profile_tutorial', function () {
        return view('profile_tutorial');
    });

    //Main Page
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
    Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@home']);
    
    Route::get('/builds/{id}/{name}', ['as' => 'show.builds', 'uses' => 'HomeController@showBuild']);
    Route::get('/builds', ['as' => 'index.builds', 'uses' => 'HomeController@indexBuild']);

    Route::get('/profile', ['as' => 'view.profile', 'uses' => 'HomeController@profileDefault']);
    Route::post('/profile', ['as' => 'view.post.profile', 'uses' => 'HomeController@postProfile']);
    Route::post('/profile/set', ['as' => 'set.profile', 'uses' => 'HomeController@postSetProfile']);
    Route::get('/profile/{acc}/ranks', ['as' => 'profile.ranks', 'uses' => 'HomeController@getProfileRanks']);
    Route::get('/profile/{acc}', ['as' => 'get.profile', 'uses' => 'HomeController@getProfile']);
    Route::get('/profile/{acc}/{char}', 'HomeController@profile');

});

