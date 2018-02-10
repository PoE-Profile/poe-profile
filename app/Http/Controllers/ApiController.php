<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\PoB\PobXMLBuilder;

class ApiController extends CacheController
{
    public function getItems(Request $request)
    {
        if (!$request->has('account') && !$request->has('character')) {
            return;
        }
        $acc=$request->input('account');
        $char=$request->input('character');
        //getItemsCache() is from parant class CacheController
        return $this->getItemsCache($acc, $char);
    }

    public function getStats(Request $request)
    {
        if (!$request->has('account') && !$request->has('character')) {
            return;
        }
        $acc=$request->input('account');
        $char=$request->input('character');
        //getItemsCache() is from parant class CacheController
        return $this->getStatsCache($acc, $char);
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
        //->where('public', '=', 1) -> removed for now
        if ($request->has('classFilter') && $request->has('skillFilter')) {
            $respond = \App\LadderCharacter::with('account')
                                ->where('items_most_sockets', 'like', "%typeLine\":\"".$request->input('skillFilter')."\"%")
                                ->where('class', '=', $request->input('classFilter'))
                                ->where('league', '=', $request->input('leagueFilter'))->orderBy('rank', 'asc')
                                ->paginate();
            return $respond;
        }
        if ($request->has('classFilter')) {
            $respond = \App\LadderCharacter::with('account')
                ->where('class', '=', $request->input('classFilter'))
                ->where('league', '=', $request->input('leagueFilter'))->orderBy('rank', 'asc')
                ->paginate();
            return $respond;
        }


        if ($request->has('skillFilter')) {
            $respond = \App\LadderCharacter::with('account')
                        ->where('items_most_sockets', 'like', "%typeLine\":\"".$request->input('skillFilter')."\"%")
                        ->where('league', '=', $request->input('leagueFilter'))->orderBy('rank', 'asc')
                        ->paginate();
            return $respond;
        }

        if ($request->has('leagueFilter')) {
            $respond = \App\LadderCharacter::with('account')->where('league', '=', $request->input('leagueFilter'))
                        ->orderBy('rank', 'asc')->paginate();
            return $respond;
        }

        $respond = \App\LadderCharacter::with('account')->where('league', '=', 'Harbinger')->orderBy('rank', 'asc')->paginate();
        return $respond;
    }

    public function getTwitchChars()
    {
        $online = \App\TwitchStreamer::with('account')->where('online', true)->orderBy('viewers', 'desc')->get();
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
    }
    public function getXMLtest(Request $request, $acc, $char)
    {
        // $acc = $request->input('account');
        // $char = $request->input('char');

        $itemsData = $this->getItemsCache($acc, $char, true);
        $treeJson = $this->getTreeCache($acc, $char);
        $pob = new PobXMLBuilder($itemsData, $treeJson);
        

        // show XML ---->
        // Header('Content-type: text/xml');
        // print($pob->getXML());
        // die();
        return $pob->encodedXML();
    }

    public function getXML(Request $request)
    {
        $acc = $request->input('account');
        $char = $request->input('char');

        $itemsData = $this->getItemsCache($acc, $char, true);
        $treeJson = $this->getTreeCache($acc, $char);
        $pob = new PobXMLBuilder($itemsData, $treeJson);
        

        // show XML ---->
        // Header('Content-type: text/xml');
        // print($pob->getXML());
        // die();
        return $pob->encodedXML();
    }
}
