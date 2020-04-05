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
        $realm=$request->input('realm');

        $b = explode('::', $acc);
        if($b[0] == 'build'){
            return Snapshot::where('hash','=',$b[1])->get()->item_data['items'];
        }

        $dbAcc = \App\Account::where('name', $acc)->first();
        $itemsData=PoeApi::getItemsData($acc, $char, $realm);
        if(!$dbAcc || !array_key_exists('items', $itemsData)){
            return;
        }
        $acc=$dbAcc->name;
        $dbAcc->updateLastCharInfo($itemsData);
        return $itemsData['items'];
    }

    public function getStats(Request $request)
    {
        $acc=$request->input('account');
        $char=$request->input('character');
        $realm=$request->input('realm');

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
        $itemsRes = PoeApi::getItemsData($acc, $char, $realm);
        $tree = PoeApi::getTreeData($acc, $char, $realm);
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

    public function getTwitchChars()
    {
        $streamers = \Cache::remember('OnlineStreamers', 10*60, function () {
            $online = \App\TwitchStreamer::with('account','account.streamer')->where('online', true)
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
                $char['account'] = $streamerItem->account;
                // $char['account']=[
                //     'name'=>$streamerItem->account->name,
                // ];
                // $char['twitch']=$streamerItem;
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
        $realm = $request->input('realm');

        $b = explode('::', $request->input('account'));
        if($b[0] == 'build'){
            $snapshot = Snapshot::where('hash','=',$b[1])->first();
            $itemsData = $snapshot->item_data;
            $treeJson = $snapshot->tree_data;
            $version = $snapshot->poe_version;
        }else{
            $itemsData = PoeApi::getItemsData($acc, $char, $realm);
            $treeJson = PoeApi::getTreeData($acc, $char, $realm);
            $version =  $version = config('app.poe_version');
        }

        $pob = new PobXMLBuilder($itemsData, $treeJson, $version);

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
