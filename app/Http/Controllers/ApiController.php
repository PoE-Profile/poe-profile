<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\PoB\PobXMLBuilder;
use App\PoeApi;
use App\Snapshot;
use App\Parse_mods\Stats_Manager;

class ApiController extends Controller
{
    public function getItems(Request $request)
    {
        $acc=$request->input('account');
        $char=$request->input('character');

        $b = explode('::', $acc);
        if($b[0] == 'build'){
            return Snapshot::where('hash','=',$b[1])->get()->item_data['items'];
        }

        $dbAcc = \App\Account::where('name', $acc)->first();
        if(!$dbAcc){
            return;
        }
        $itemsData=PoeApi::getItemsData($acc, $char);
        $acc=$dbAcc->name;
        $dbAcc->updateLastCharInfo($itemsData);
        return $itemsData['items'];
    }

    public function getStats(Request $request)
    {
        $acc=$request->input('account');
        $char=$request->input('character');

        $b = explode('::', $acc);
        if ($b[0] == 'build') {
            $build=Snapshot::where('hash','=',$b[1])->first();
            if(!$build){
                return [];
            }
            return $build->getStats();
        }

        //get acc from db to get real name
        $dbAcc = \App\Account::where('name', $acc)->first();
        $acc = $dbAcc->name;
        $itemsRes = PoeApi::getItemsData($acc, $char);
        $tree = PoeApi::getTreeData($acc, $char);
        $stManager = new Stats_Manager($itemsRes, $tree, isset($_GET['offHand']));
        return $stManager->getStats();
    }

    public function getFavsChars()
    {
        $favs=collect(explode(',', $_GET['accs']));
        $accs=\App\Account::with('streamer')->whereIn('name', $favs)->get();
        // $accs=\App\Account::with('streamer')->where('name','kas7erpoe', false)->get();
        //dd($accs->toJson());
        if (count($favs)==0) {
            return [];
        }
        // dd($favs);
        $newFavs = $favs->map(function ($favItem, $key) use ($accs) {
            $char=[
                'league'=>'',
                'name'=>'',
                'class'=>'',
                'level'=>'',
                'items_most_sockets'=>'',
                'account'=>[
                    'name'=>$favItem,
                ]
            ];

            //$item = $accs->where('name', $favItem, false)->first();
            $item = $accs->filter(function ($item) use ($favItem) {
                return strtolower($item['name']) == strtolower($favItem);
            })->first();

            if ($item) {
                if ($item->last_character_info) {
                    $char=$item->last_character_info;
                    $char['account']=$item;
                    // $char['twitch']=$item->streamer;
                }
            }
            return $char;
        });

        return $newFavs;
    }

    public function getLadder(Request $request)
    {
        $take = 30;
        if ($request->has('searchFilter')) {
            $respond = \App\LadderCharacter::with('account')
                ->where('league', '=', $request->input('leagueFilter'))
                ->whereHas('account', function ($query) use (&$request) {
                    $query->where('name', 'like', '%'.$request->input ('searchFilter').'%');
                })
                ->orWhere('name', 'like', '%'.$request->input ('searchFilter').'%')
                ->paginate($take);

            return $respond;
        }

        if ($request->has('classFilter') && $request->has('skillFilter')) {
            $respond = \App\LadderCharacter::with('account')
                                ->where('items_most_sockets', 'like', "%typeLine\":\"".$request->input('skillFilter')."\"%")
                                ->where('class', '=', $request->input('classFilter'))
                                ->where('league', '=', $request->input('leagueFilter'))->orderBy('rank', 'asc')
                                ->paginate($take);
            return $respond;
        }
        if ($request->has('classFilter')) {
            $respond = \App\LadderCharacter::with('account')
                ->where('class', '=', $request->input('classFilter'))
                ->where('league', '=', $request->input('leagueFilter'))->orderBy('rank', 'asc')
                ->paginate($take);
            return $respond;
        }


        if ($request->has('skillFilter')) {
            $respond = \App\LadderCharacter::with('account')
                        ->where('items_most_sockets', 'like', "%typeLine\":\"".$request->input('skillFilter')."\"%")
                        ->where('league', '=', $request->input('leagueFilter'))->orderBy('rank', 'asc')
                        ->paginate($take);
            return $respond;
        }

        if ($request->has('leagueFilter')) {
            $respond = \App\LadderCharacter::with('account')->where('league', '=', $request->input('leagueFilter'))
                        ->orderBy('rank', 'asc')->paginate($take);
            return $respond;
        }

        $currentLeagues = explode(',', env('POE_LEAGUES'));
        $respond = \App\LadderCharacter::with('account')->where('league', '=', $currentLeagues[0])->orderBy('rank', 'asc')->paginate($take);
        return $respond;
    }

    public function getTwitchChars()
    {
        $streamers = \Cache::remember('OnlineStreamers', 15, function () {
            $online = \App\TwitchStreamer::with('account')->where('online', true)
                                ->orderBy('viewers', 'desc')->get();
            $online = $online->map(function ($streamerItem, $key) {
                $char=[];
                if ($streamerItem->account->last_character_info) {
                    $char=$streamerItem->account->last_character_info;
                } else {
                    $char=[
                        'league'=>'',
                        'name'=>'',
                        'class'=>'',
                        'level'=>'',
                        'items_most_sockets'=>'',
                    ];
                }
                $char['account']=[
                    'name'=>$streamerItem->account->name,
                ];
                $char['twitch']=$streamerItem;
                return $char;
            });
            return $online;
        });

        return $streamers;
    }

    public function getXML(Request $request)
    {
        $acc = $request->input('account');
        $char = $request->input('char');

        $b = explode('::', $request->input('account'));
        if($b[0] == 'build'){
            $snapshot = Snapshot::where('hash','=',$b[1])->first();
            $itemsData = $snapshot->item_data;
            $treeJson = $snapshot->tree_data;
        }else{
            $itemsData = PoeApi::getItemsData($acc, $char);
            $treeJson = PoeApi::getTreeData($acc, $char);
        }

        $pob = new PobXMLBuilder($itemsData, $treeJson);

        // show XML ---->
        // Header('Content-type: text/xml');
        // print($pob->getXML());
        // die();
        return $pob->encodedXML();
    }

    public function getSnapshots($acc, $char)
    {
        $original_char = $original_char = $acc .'/'. $char;
        $snapshots = Snapshot::where('original_char', '=', $original_char)
                                ->orderBy('created_at', 'desc')->take(25)->get();
        return $snapshots;
    }

    public function getBuild($hash)
    {
        $build = Snapshot::where('hash', '=', $hash)->first();
        return $build;
    }

    public function saveBuild(Request $request)
    {
        $acc=$request->input('account');
        $char=$request->input('char');
        $snapshot = Snapshot::create($acc, $char);

        $favStore = $snapshot->item_data['character'];
        $favStore['league'] = 'localBuild';
        $favStore['buildId'] = $snapshot->hash;
        return $favStore;
    }
}
