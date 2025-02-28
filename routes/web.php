<?php
use App\Http\Controllers\LadderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillTreeController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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

    Route::get('/ladders/', [LadderController::class, 'index'])->name('ladders.index');
    Route::get('/ladders/{name}', [LadderController::class, 'show'])->name('ladders.show');
    Route::get('/api/ladders/{name}', [LadderController::class, 'getLadder'])
        ->name('api.ladders');
    Route::get('/api/private-ladders/{name}', [LadderController::class, 'getPrivateLadder'])
        ->name('api.ladders.private');
    Route::post('/api/ladders/update-skill/', [LadderController::class, 'updateCharacterSkill'])
        ->name('api.ladders.skill');
    Route::get('/race/{name}', [LadderController::class, 'getRaceLadder'])->name('race');

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

    // saved Builds/Snapshots
    Route::get('/builds', [ProfileController::class, 'indexBuild'])->name('builds');
    Route::get('/build/{hash}', [ProfileController::class, 'showBuild'])->name('build.show');

    //profile routes
    Route::get('/profile', [ProfileController::class, 'getProfile'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'postProfile'])->name('profile.post');
    Route::get('/profile/{acc}/ranks', [ProfileController::class, 'getProfileRanks'])->name('profile.ranks');
    Route::get('/profile/{acc}/snapshots', [ProfileController::class, 'getProfileSnapshots'])->name('profile.snapshots');
    Route::get('/profile/{acc}', [ProfileController::class, 'getProfile'])->name('profile.acc');
    Route::get('/profile/{acc}/{char}', [ProfileController::class, 'getProfileChar'])->name('profile.char');

    // routes for passive-skill-tree
    Route::get('/passive-skill-tree/{any}', [SkillTreeController::class, 'showSkillTree'])
            ->name('profile.tree');
    Route::get('/passive-skill-tree/alternate/{any}', [SkillTreeController::class, 'showSkillTreeAlternate'])
            ->name('profile.tree.alternate');
            
    Route::get('/character-window/get-passive-skills', [SkillTreeController::class, 'getPassiveSkills'])
            ->name('profile.tree.passives');

    // routes for atlas-skill-tree
    Route::get('/atlas-skill-tree/{any}', [SkillTreeController::class, 'showAtlasTree'])
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
