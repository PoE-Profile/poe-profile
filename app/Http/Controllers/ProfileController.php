<?php

namespace App\Http\Controllers;

use App\Item;
use App\PoeApi;
use App\Account;
use App\Snapshot;
use App\Http\Requests;
use Illuminate\Http\Request;


class ProfileController extends Controller
{

    public function postProfile(Request $request)
    {
        $acc = $request->input('account');
        return redirect()->route('profile.acc',
            ['acc' => $acc,'realm'=>$request->input('realm')]
        );
    }

    public function getProfile(Request $request, $acc)
    {
        $account = Account::with(['streamer'])->firstOrCreate(['name' => $acc]);
        $account->updateViews();
        $char = $account->last_character;
        if($account->characters && !collect($account->characters)->contains('name', $char)){
            $char=$account->characters[0]['name'];
        }
        $snapshot = Snapshot::getAccChar($acc,$char);
        if(!$snapshot && !$account->characters){
            return redirect()->back();
        }
        return view('profile', compact('account','char','snapshot'));
    }

    public function getProfileChar(Request $request, $acc, $char)
    {
        $snapshot = Snapshot::getAccChar($acc,$char);
        $account = Account::with(['streamer'])->firstOrCreate(['name' => $acc]);
        $account->updateViews();
        
        if($account->characters && !collect($account->characters)->contains('name', $char) && !$snapshot){
            flash('Character with name "'.$char.'" does not exist in account '
                    .$acc.' or is removed.', 'warning');
            return redirect()->route('profile.acc',
                ['acc' => $acc]
            );
        }

        return view('profile', compact('account','char','snapshot'));
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

    public function getProfileSnapshots(Request $request, $acc)
    {
        $q = \App\Snapshot::query();
        if($request->has('version')){
            $q->where('poe_version',$request->input('version'));
        }
        $q->where('original_char', 'like', '%'.$acc.'%');
        $snapshots = $q->orderBy('created_at', 'DESC')->get()->unique('original_char');
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
