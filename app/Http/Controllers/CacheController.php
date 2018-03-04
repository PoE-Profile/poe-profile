<?php

namespace App\Http\Controllers;

use App\Parse_mods\Stats_Manager;
use App\Parse_mods\Base_Stats;
use App\Parse_mods\CharacterTreePoints;
use App\Account;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\PoB\Tree;

class CacheController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //get chars for acc from cache if no get from poe site
    protected function getCharsCache($acc)
    {
        $chars = \Cache::remember($acc, config('app.poe_cache_time'), function () use ($acc) {
            $client = new \GuzzleHttp\Client();
            try {
                $response = $client->request(
                    'POST',
                    'https://www.pathofexile.com/character-window/get-characters',
                    [
                    'form_params' => [
                        'accountName' => $acc
                    ]
                ]
                );
            } catch (\GuzzleHttp\Exception\ClientException $exception) {
                // dd('ClientException');
                flash('pathofexile.com is currently down for maintenance. Please try again later. ', 'warning');
                return false;
            }
            $result = json_decode((string)$response->getBody());
            //if result false no data for acc stop here
            if ($result==false) {
                flash('Аccount is private or does not exist. ', 'warning');
                return false;
            }

            $dbAcc = \App\Account::where('name', $acc)->first();
            if (!$dbAcc) {
                $newAcc=\App\Account::getNewAccInfo($acc);
                \App\Account::create($newAcc);
            } else {
                $dbAcc->updateLastChar();
            }

            return $result;
        });
        return $chars;
    }

    //get items for acc/char from cache if no get from poe site
    protected function getItemsCache($acc, $char, $response=false)
    {
        //get acc from db to get real name
        $dbAcc = \App\Account::where('name', $acc)->first();
        $items_response = $dbAcc->getItemsFor($char);
        if ($response) {
            return $items_response;
        }
        return $items_response['items'];
    }

    protected function getStatsCache($acc, $char)
    {
        //get acc from db to get real name
        $dbAcc = \App\Account::where('name', $acc)->first();
        $acc=$dbAcc->name;

        $itemsRes = $this->getItemsCache($acc, $char, true);
        $key='stats/'.$acc.'/'.$char;
        if (isset($_GET['offHand'])) {
            $key='stats/'.$acc.'/'.$char.'/offHand';
        }
        // dd(config('app.poe_cache_time'));
        $stats = \Cache::remember($key, config('app.poe_cache_time'), function () use ($acc,$dbAcc,$char,$itemsRes) {
            if ($dbAcc->last_character==$itemsRes['character']['name']) {
                $dbAcc->updateLastCharInfo($itemsRes);
            }
            $stManager = new Stats_Manager($itemsRes, $this->getTreeCache($acc, $char));
            return $stManager->getStats();
        });

        return $stats;
    }

    public function getTreeCache($acc, $char)
    {
        $key='tree/'.$acc.'/'.$char;

        return \Cache::remember($key, config('app.poe_cache_time'), function () use ($acc,$char) {
            $client = new \GuzzleHttp\Client();
            $responseTree = $client->request(
                        'GET',
                        'https://www.pathofexile.com/character-window/get-passive-skills',
                        [
                            'query' => [
                                'accountName' => $acc,
                                'character' => $char
                            ]
                        ]
                    );

            return json_decode((string)$responseTree->getBody(), true);
        });
    }


}
