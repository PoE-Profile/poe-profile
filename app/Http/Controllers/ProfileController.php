<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Stash;
use App\Item;

class ProfileController extends CacheController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function postProfile(Request $request)
    {
        $acc = $request->input('account');

        //getCharsCache() is from parant class CacheController
        $chars = $this->getCharsCache($acc);
        if(!$chars){
            // flash('Аccount is private or does not exist. ', 'warning');
            return redirect()->route('home');
        }
        return redirect()->route('get.profile',$acc);
    }

    public function getProfile($acc)
    {
        //getCharsCache() is from parant class CacheController
        $chars = $this->getCharsCache($acc);

        if(!$chars){
            return redirect()->route('home');
        }

        $char = $chars[0]->name;
        $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        //if acc try to get last char and update views
        if($dbAcc){
            //check if last_character exist in $chars cache and set as $char
            $last=$dbAcc->last_character;
            $tempCheck=array_filter($chars, function($c) use($last){
                return $c->name==$last;
            });
            if(count($tempCheck)>0){
                $char=$last;
            }

            $dbAcc->updateViews();
        }

        $chars = collect($chars);
        return view('profile', compact('acc', 'char', 'chars', 'dbAcc'));
    }

    public function getProfileChar($acc, $char)
    {
        //getCharsCache() is from parant class CacheController
        $chars = $this->getCharsCache($acc);
        if(!$chars){
            flash('Аccount is private or does not exist. ', 'warning');
            return redirect()->route('home');
        }
        $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        if($dbAcc){
            $dbAcc->updateViews();
        }

        $tempCheck=array_filter($chars, function($c) use($char){
            return $c->name==$char;
        });
        if(count($tempCheck)==0){
            flash('Character with name "'.$char.'" does not exist in account '.$acc.' or is removed.', 'warning');
            return redirect()->route('get.profile',$acc);
        }
        $chars = collect($chars);
        return view('profile', compact('acc', 'char', 'chars', 'dbAcc'));
    }

    public function getProfileRanks($acc)
    {
        $rankArchives = \App\LadderCharacter::with('account')->whereHas('account', function ($query) use (&$acc) {
            $query->where('name', '=', $acc);
        })->get();
        $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        return view('ranks', compact('acc', 'rankArchives', 'dbAcc'));
    }

    public function getProfileSnapshots($acc)
    {
        $snapshots = \App\Snapshot::where('original_char', 'like', '%'.$acc.'%')->groupBy('original_char')->get();

        $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        return view('snapshots', compact('acc', 'snapshots', 'dbAcc'));
    }

    public function indexBuild()
    {
        $acc = '';
        $build = null;
        $loadBuild = "true";
        return view('profile', compact('acc', 'build', 'loadBuild'));
    }

    public function showBuild($hash)
    {
        $build = \App\Snapshot::where('hash', '=', $hash)->first();
        $loadBuild = "true";
        $acc = 'build::'.$hash;
        if ($build == null) {
            flash("404 Build snapshot ".$hash." Not Found")->warning()->important();
            return view('profile', compact('acc', 'build', 'loadBuild'));
        }
        return view('profile', compact('acc', 'build', 'loadBuild'));
    }
}
