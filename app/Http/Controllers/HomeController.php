<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Stash;
use App\Item;

class HomeController extends CacheController
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->cookie('default-acc')){
            //return redirect()->route('view.profile');
        }
        return view('index');
    }

    public function home()
    {
        return view('index');
    }

    public function profileDefault(Request $request)
    {
        $char = '';
        $acc = $request->cookie('default-acc');
        if(!$acc){
            flash('No main profile .', 'warning');
            return redirect()->route('home');
        }

        return redirect()->route('get.profile',$acc);
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

    //set default-acc for profile
    public function postSetProfile(Request $request){
        //session(['default-acc' => $request->input('account')]);
        $acc=$request->input('account');
        flash('Аccount '.$acc.' is set as your main profil next time you load '.config('app.url').'/profile is going to load '.$acc, 'success');

        return redirect()->route('get.profile',$acc)
            ->withCookie(cookie()->forever('default-acc', $acc, null, null, false, false));
        //return redirect()->route('view.profile');
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

        return view('profile', compact('acc', 'char', 'chars', 'dbAcc'));
    }

    public function profile($acc, $char)
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
        // json_encode($value [, $options, $depth])
        return view('profile', compact('acc', 'char', 'chars', 'dbAcc'));
    }

    public function getStashes($acc){
        //return view(temp_stashes', compact('acc','stashes'));
        $stashes=Stash::where('accountName',  $acc )->with('items')->get();
        return view('stashes', compact('acc','stashes'));
    }

}
