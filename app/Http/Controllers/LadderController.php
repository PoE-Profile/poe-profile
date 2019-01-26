<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\League;

class LadderController extends Controller
{
    protected $res_on_page = 50;

    public function index()
    {
        $str_current_leagues=cache('current_leagues', config('app.poe_leagues'));
        $current_leagues = collect(explode(', ', $str_current_leagues));
        $league = League::where('name', $current_leagues[0])->first();
        return view('ladder',compact('league','current_leagues'));
    }

    public function show($name)
    {
        $str_current_leagues=cache('current_leagues', config('app.poe_leagues'));
        $current_leagues = collect(explode(', ', $str_current_leagues));

        $league = League::where('name', $name)->first();

        if(!$league){
            //return view('private_ladder', compact('league'));
            $response = \App\PoeApi::getLadder($name,0);
            if(!$response){
                return redirect()->route('ladders.index');
            }
            $league = \App\League::firstOrCreate([
                'name' => $name,
                'type' => 'private',
                'rules' => '',
                'indexed' => false
            ]);
        }
        return view('ladder', compact('league', 'current_leagues'));
    }


    public function getLadder($name, Request $request)
    {
        $query = \App\LadderCharacter::with('account','account.streamer');
        if ($request->has('search')) {
            $query->whereHas('account', function ($query) use (&$request) {
                    $query->where('name', 'like', '%' . $request->input('search') . '%');
                })
                ->orWhere('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('class')) {
            $query->class($request->input('class'));
        }

        if ($request->has('skill')) {
            $query->skill($request->input('skill'));
        }

        $query->league($name);

        if($request->has('sort')=='Team-Depth') {
            $sort = $request->input('sort')=='Team-Depth' ? 'delve_default' : 'delve_solo';
            $query->orderBy($sort, 'DESC');
        }else{
            $query->where('rank', '>',0);
            $query->orderBy('rank');
        }

        return $query->paginate($this->res_on_page);
    }


    public function getPrivateLadder($name, Request $request){
        //regex : find (PL1873) for private ladders name 'Betraying Pohx (PL1873)'
        //preg_match('/\(PL[0-9]+\)/', 'Betraying Pohx (PL1873)', $matches, PREG_OFFSET_CAPTURE);
        // dd($response);
        $returnResponse['current_page']=1;
        if($request->input('page')){
            $returnResponse['current_page']=$request->input('page');
            $from = ($returnResponse['current_page']-1)*$this->res_on_page;
            $response = \App\PoeApi::getLadder($name,$from,$this->res_on_page);
        }else{
            $response = \App\PoeApi::getLadder($name,0,$this->res_on_page);
        }
        $total = $response['total'];
        $returnResponse['total']=$total;
        $returnResponse['last_page']=(int)ceil($total/$this->res_on_page);


        // dd($returnResponse);
        $returnResponse['data'] = array_map(function($entry) use(&$name){
            $newEntry = \App\LadderCharacter::poeEntryToArray($entry);
            $newEntry['league'] = $name;
            $newEntry['account'] = [
                'id'=>0,
                'name' => $entry['account']['name'],
                'last_character' => "",
                'last_character_info' => null,
            ];
            if (array_key_exists('twitch', $entry['account'])) {
                $newEntry['account']['streamer']['name']=$entry['account']['twitch']['name'];
            }
            // $entry['account']['twitch']['name']
            return $newEntry;
        }, $response['entries']);

        return $returnResponse;
    }

    public function updateCharacterSkill(Request $request){
        $char_id=$request->input('charId');
        $char=\App\LadderCharacter::findOrFail($char_id);
        // dd($char_id);
        $accName = $char->account->name;
        $charName = $char->name;
        $itemsData=\App\PoeApi::getItemsData($accName, $charName);
        if(!$char->account->updateLastCharInfo($itemsData)){
            $char->public=false;
            $char->save();
            return $char;
        }
        $char = $char->fresh()->load('account');
        return $char;
    }

    public function getRaceLadder($name, Request $request){
        $current_leagues = explode(', ', cache('current_leagues', config('app.poe_leagues')));
        $league = \App\League::where('name', $current_leagues[3])->first();
        $race = collect([
            'league' => $league,
        ]);
        return view('race.show', compact('race'));
    }

}
