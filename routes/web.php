<?php
use Illuminate\Support\Facades\Http;

// \DB::listen(function($sql) {
//     var_dump($sql->sql);
// });

Route::group(['middleware' => 'web'], function () {
    //Main Page
    Route::get('/',function () {
        $str_current_leagues=cache('current_leagues', config('app.poe_leagues'));
        $current_leagues = collect(explode(', ', $str_current_leagues));
        return view('index',compact('current_leagues'));
    })->name('home');

    Route::get('/ladders/', 'LadderController@index')->name('ladders.index');
    Route::get('/ladders/{name}', 'LadderController@show')->name('ladders.show');
    Route::get('/api/ladders/{name}', 'LadderController@getLadder')
        ->name('api.ladders');
    Route::get('/api/private-ladders/{name}', 'LadderController@getPrivateLadder')
        ->name('api.ladders.private');
    Route::post('/api/ladders/update-skill/', 'LadderController@updateCharacterSkill')
        ->name('api.ladders.skill');
    Route::get('/race/{name}', 'LadderController@getRaceLadder')->name('race');


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
    Route::get('/builds', 'ProfileController@indexBuild')->name('builds');
    Route::get('/build/{hash}', 'ProfileController@showBuild')->name('build.show');

    //profile routes
    Route::get('/profile', 'ProfileController@getProfile')->name('profile');
    Route::post('/profile', 'ProfileController@postProfile')->name('profile.post');
    Route::get('/profile/{acc}/ranks', 'ProfileController@getProfileRanks')->name('profile.ranks');
    Route::get('/profile/{acc}/snapshots', 'ProfileController@getProfileSnapshots')->name('profile.snapshots');
    Route::get('/profile/{acc}/stashes', 'ProfileController@getStashs')->name('profile.stashes');
    Route::get('/profile/{acc}', 'ProfileController@getProfile')->name('profile.acc');
    Route::get('/profile/{acc}/{char}', 'ProfileController@getProfileChar')->name('profile.char');

    // routes for passive-skill-tree
    Route::get('/passive-skill-tree/{any}','SkillTreeController@showSkillTree')
            ->name('profile.tree');
    Route::get('/character-window/get-passive-skills','SkillTreeController@getPassiveSkills')
            ->name('profile.tree.passives');

    // routes for atlas-skill-tree
    Route::get('/atlas-skill-tree/{any}','SkillTreeController@showAtlasTree')
            ->name('profile.atlas');

    Route::get('/skill-img/{name}', function ($name) {
        return null;
        $prefaced = ["Anomalous ", "Divergent ", "Phantasmal "];
        $name = str_replace($prefaced, '', $name);
        $name=str_replace(" ","_",$name);
        $name=str_replace("'","",$name);
        $file_path=storage_path('app/skills/').$name.'.png';
        if(file_exists($file_path)){
            return Image::make($file_path)->response();
        }
        $response = Http::get('https://poedb.tw/us/'.$name);
        $html = $response->body();
        // https://web.poecdn.com/image/Art/2DArt/SkillIcons/DoomBlastSkill.png
        $pattern = '/https:\/\/web.poecdn.com\/image\/Art\/2DArt\/SkillIcons.*\.png/';
        preg_match ($pattern, $html, $matches);
        if(count($matches)>0){
            $img_url=$matches[0];
            // dd($img_url);
            $img = \Image::make($img_url)->encode('png', 100);
            if(!file_exists(storage_path('app/skills/'))){
                mkdir(storage_path('app/skills/'));
            }
            $img->save($file_path);
            return $img->response();
        }
        return null;
    });

});
