<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\League;

class LadderController extends Controller
{
    public function index()
    {
        $current_leagues = explode(', ', cache('current_leagues', config('app.poe_leagues')));
        $league = League::where('name', $current_leagues[0])->first();
        return view('ladder',compact('league','current_leagues'));
    }

    public function show($name)
    {
        $current_leagues = explode(', ', cache('current_leagues', config('app.poe_leagues')));
        $league = League::where('name', $name)->first();
        if(!$league){
            //return view('private_ladder', compact('league'));
            $league = \App\League::firstOrCreate([
                'name' => strtolower($name),
                'type' => 'public',
                'rules' => '',
                'indexed' => false
            ]);
        }
        return view('ladder', compact('league', 'current_leagues'));
    }


    public function getLadder($name, Request $request)
    {
        $query = \App\LadderCharacter::with('account');
        $take = 30;
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

        $query->league($name)->where('rank', '>',0);

        if($request->has('sort')=='Team-Depth') {
            $sort = $request->input('sort')=='Team-Depth' ? 'delve_default' : 'delve_solo';
            $query->orderBy($sort, 'DESC');
        }else{
            $query->orderBy('rank');
        }

        return $query->paginate($take);
    }


    public function getPrivateLadder($name, Request $request){
        //regex : find (PL1873) for private ladders name 'Betraying Pohx (PL1873)'
        //preg_match('/\(PL[0-9]+\)/', 'Betraying Pohx (PL1873)', $matches, PREG_OFFSET_CAPTURE);

        // $base_url = 'https://www.pathofexile.com/api/ladders?offset=0';
        // $page_url = $base_url.'&limit=100&id='.$name.'&type=league';
        // $client = new \GuzzleHttp\Client(['http_errors' => false]);
        // $response = $client->get($page_url);
        $response = $response = \App\PoeApi::getLadder($name,0);
        $data = array_map(function($entry) use(&$name){
            $newEntry = \App\LadderCharacter::poeEntryToArray($entry);
            $newEntry['league'] = $name;
            $newEntry['account'] = [
                'id'=>0,
                'name' => $entry['account']['name'],
                'last_character' => "",
                'last_character_info' => null,
            ];
            return $newEntry;
        }, $response['entries']);
        return ['data' => $data];
    }

}
