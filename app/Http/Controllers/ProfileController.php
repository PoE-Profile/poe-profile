<?php

namespace App\Http\Controllers;

use App\Item;
use App\Stash;
use App\PoeApi;
use App\Account;
use App\Snapshot;
use App\Http\Requests;
use Illuminate\Http\Request;


class ProfileController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function postProfile(Request $request)
    {
        // dd($request->input('realm'));
        $acc = $request->input('account');
        return redirect()->route('profile.acc',
            ['acc' => $acc,'realm'=>$request->input('realm')]
        );
    }

    public function getProfile(Request $request, $acc)
    {
        $chars = PoeApi::getCharsData($acc);
        if(!$chars){
            return redirect()->back();
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

    public function getProfileChar(Request $request, $acc, $char)
    {
        $chars = PoeApi::getCharsData($acc);
        if(!$chars){
            return redirect()->back();
        }
        $chars = collect($chars);
        $dbAcc = $this->getDbAcc($acc);
        if(!$chars->contains('name', $char)){
            flash('Character with name "'.$char.'" does not exist in account '
                    .$acc.' or is removed.', 'warning');
            $char = $chars[0]->name;
        }
        $build = Snapshot::getAccChar($acc,$char);
        if($request->has('race')){
            return view('race.profile', compact('acc', 'char', 'chars', 'dbAcc'));
        }
        return view('profile', compact('acc', 'char', 'chars', 'dbAcc', 'build'));
    }

    private function getDbAcc($acc){
        $dbAcc = Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        if(!$dbAcc){
            $dbAcc = Account::create(['name' => $acc]);
            $dbAcc = Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
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
        $dbAcc = Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        return view('ranks', compact('acc', 'rankArchives', 'dbAcc'));
    }

    public function getStashs($acc)
    {
        $results=[];
        $dbAcc = Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
        $currentLeagues = explode(', ', \Cache::get('current_leagues', config('app.poe_leagues')));
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
                            ->orderBy('created_at', 'DESC')->get()->unique('original_char');
        $snapshots = $snapshots->map(function($item){
            return $item->toLadderChar();
        });
        $dbAcc = Account::with(['ladderChars', 'streamer'])->where('name', $acc)->first();
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
