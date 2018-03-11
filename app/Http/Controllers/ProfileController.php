<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\PoeApi;
use Illuminate\Http\Request;
use App\Stash;
use App\Item;

class ProfileController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function postProfile(Request $request)
    {
        $acc = $request->input('account');
        return redirect()->route('get.profile',$acc);
    }

    public function getProfile($acc)
    {
        $chars = PoeApi::getCharsData($acc);
        if(!$chars){
            return redirect()->route('home');
        }
        $chars = collect($chars);

        $dbAcc = $this->getDbAcc($acc);
        //select chars check if last_character exist in $chars cache and set as $char
        $char = $chars[0]->name;
        if($chars->contains('name', $dbAcc->last_character)){
            $char = $dbAcc->last_character;
        }

        return view('profile', compact('acc', 'char', 'chars', 'dbAcc'));
    }

    public function getProfileChar($acc, $char)
    {
        $chars = collect(PoeApi::getCharsData($acc));
        if(!$chars){
            return redirect()->route('home');
        }
        $dbAcc = $this->getDbAcc($acc);

        if(!$chars->contains('name', $char)){
            flash('Character with name "'.$char.'" does not exist in account '
                    .$acc.' or is removed.', 'warning');
            $char = $chars[0]->name;
        }
        return view('profile', compact('acc', 'char', 'chars', 'dbAcc'));
    }

    private function getDbAcc($acc){
        $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        if(!$dbAcc){
            $last_character = PoeApi::getLastCharacter($acc);
            \App\Account::create(['name' => $acc, 'last_character' => $last_character]);
            $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        }
        $dbAcc->updateLastChar();
        $dbAcc->updateViews();
        return $dbAcc;
    }

    public function getProfileRanks($acc)
    {
        $rankArchives = \App\LadderCharacter::with('account')
                ->whereHas('account', function ($query) use (&$acc) {
                    $query->where('name', '=', $acc);
                })->get();
        $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        return view('ranks', compact('acc', 'rankArchives', 'dbAcc'));
    }

    public function getStashs($acc)
    {
        $results=[];
        $dbAcc = \App\Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        $currentLeagues = explode(',',config('app.poe_leagues'));
        array_splice($currentLeagues, 2);
        foreach ($currentLeagues as $league) {
            $publicStash = $dbAcc->getPublicStash($league);
            if($publicStash->total>0){
                $results[]=(Object)array(
                    'league'=> $league,
                    'result'=>$publicStash
                );
            }
        }
        return view('public_stash', compact('acc', 'results', 'dbAcc'));
    }

    public function getProfileSnapshots($acc)
    {
        $snapshots = \App\Snapshot::where('original_char', 'like', '%'.$acc.'%')
                                    ->groupBy('original_char')->get();

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
