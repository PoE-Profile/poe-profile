<?php

// \DB::listen(function($sql) {
//     var_dump($sql->sql);
// });

Route::group(['middleware' => 'web'], function () {
    //Main Page
    Route::get('/',function () {
        return view('index');
    })->name('home');

    Route::get('/ladders/', 'LadderController@index')->name('ladders.index');
    Route::get('/ladders/{name}', 'LadderController@show')->name('ladders.show');
    Route::get('/api/ladders/{name}', 'LadderController@getLadder')
        ->name('api.ladders');
    Route::get('/api/private-ladders/{name}', 'LadderController@getPrivateLadder')
        ->name('api.ladders.private');

    Route::get('/twitch', function () {
        return view('twitch');
    })->name('twitch');

    Route::get('/favorites', function () {
        return view('favorites');
    })->name('favorites');

    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/update_notes', function () {
        return view('changelog');
    })->name('changelog');

    Route::get('/tutorial/profile', function () {
        return view('profile_tutorial');
    })->name('tutorial.profile');

    Route::get('/tutorial/build', function () {
        return view('build_tutorial');
    })->name('tutorial.build');

    // saved Builds/Snapshots
    Route::get('/builds', 'ProfileController@indexBuild')->name('index.builds');
    Route::get('/build/{hash}', 'ProfileController@showBuild')->name('show.build');

    //profile routes
    Route::get('/profile', 'ProfileController@profileDefault')->name('profile');
    Route::post('/profile', 'ProfileController@postProfile')->name('profile.post');
    Route::get('/profile/{acc}/ranks', 'ProfileController@getProfileRanks')->name('profile.ranks');
    Route::get('/profile/{acc}/snapshots', 'ProfileController@getProfileSnapshots')->name('profile.snapshots');
    Route::get('/profile/{acc}/stashes', 'ProfileController@getStashs')->name('profile.stashes');
    Route::get('/profile/{acc}', 'ProfileController@getProfile')->name('profile.acc');
    Route::get('/profile/{acc}/{char}', 'ProfileController@getProfileChar')->name('profile.acc.char');

    // routes for passive-skill-tree
    Route::get('/passive-skill-tree/{any}','SkillTreeController@showSkillTree')
            ->name('profile.tree');
    Route::get('/character-window/get-passive-skills','SkillTreeController@getPassiveSkills')
            ->name('profile.tree.passives');

});
