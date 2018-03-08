<?php

// \DB::listen(function($sql) {
//     var_dump($sql->sql);
// });

Route::group(['middleware' => 'web'], function () {
    //Main Page
    Route::get('/', ['as' => 'home', 'uses' => function () {
        return view('index');
    }]);
    Route::get('/ladders', ['as' => 'ladders', 'uses' => function () {
        return view('ladder');
    }]);

    Route::get('/twitch', ['as' => 'twitch', 'uses' => function () {
        return view('twitch');
    }]);

    Route::get('/favorites', ['as' => 'favorites', 'uses' => function () {
        return view('favorites');
    }]);

    Route::get('/about', function () {
        return view('about');
    });

    Route::get('/update_notes', function () {
        return view('update_notes');
    });

    Route::get('/profile_tutorial', function () {
        return view('profile_tutorial');
    });

    Route::get('/build_tutorial', function () {
        return view('build_tutorial');
    });

    // saved Builds/Snapshots
    Route::get('/builds', ['as' => 'index.builds', 'uses' => 'ProfileController@indexBuild']);
    Route::get('/build/{hash}', ['as' => 'show.build', 'uses' => 'ProfileController@showBuild']);

    //profile routes
    Route::get('/profile', ['as' => 'view.profile', 'uses' => 'ProfileController@profileDefault']);
    Route::post('/profile', ['as' => 'view.post.profile', 'uses' => 'ProfileController@postProfile']);
    Route::get('/profile/{acc}/ranks', ['as' => 'profile.ranks', 'uses' => 'ProfileController@getProfileRanks']);
    Route::get('/profile/{acc}/snapshots', ['as' => 'profile.snapshots', 'uses' => 'ProfileController@getProfileSnapshots']);
    Route::get('/profile/{acc}', ['as' => 'get.profile', 'uses' => 'ProfileController@getProfile']);
    Route::get('/profile/{acc}/{char}', ['as' => 'get.profile.char', 'uses' => 'ProfileController@getProfileChar']);

    // routes for passive-skill-tree
    Route::get('/passive-skill-tree/{any}',
            ['as' => 'profile.tree', 'uses' => 'SkillTreeController@showSkillTree']);
    Route::get('/character-window/get-passive-skills',
            ['as' => 'profile.tree.passives', 'uses' => 'SkillTreeController@getPassiveSkills']);

});
