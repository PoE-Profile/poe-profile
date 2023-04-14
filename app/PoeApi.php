<?php
namespace App;

class PoeApi
{
    public static $userAgent = "OAuth poe-profile.info/2.6.0 (contact: https://github.com/PoE-Profile/poe-profile/) StrictMode";

    static public function getCharsData($acc, $realm="pc")
    {
        if(isset($_GET['realm'])){
            $realm = $_GET['realm'];
        }
        // $time = config('app.poe_cache_time') * 60;
        $time = 1440 * 60; //problem with limit make cach
        $cacheKey=$acc.'/'.$realm;
        return \Cache::remember($cacheKey, $time, function () use ($acc,$realm) {
            $client = new \GuzzleHttp\Client(['cookies' => true]);
            try {
                $response = $client->request(
                    'POST',
                    'https://www.pathofexile.com/character-window/get-characters',
                    [
                        'headers' => [
                            'User-Agent' => self::$userAgent,
                        ],
                        'form_params' => [
                            'accountName' => $acc,
                            'realm' => $realm
                        ]
                    ]
                );
            } catch (\GuzzleHttp\Exception\ClientException $exception) {
                // dd('ClientException');
                // flash('pathofexile.com is currently down for maintenance. Please try again later. ', 'warning');
                flash('Аccount is private or does not exist. ', 'warning');
                return false;
            }
            $result = json_decode((string)$response->getBody());
            //if result false no data for acc stop here
            if ($result==false) {
                flash('Аccount is private or does not exist. ', 'warning');
                return false;
            }

            return $result;
        });
    }

    static public function getTreeData($acc, $char, $realm="pc")
    {
        $key='tree/'.$acc.'/'.$char.'/'.$realm;
        $time = config('app.poe_cache_time') * 60;
        return \Cache::remember($key, $time, function () use ($acc,$char,$realm) {
            $client = new \GuzzleHttp\Client(['cookies' => true]);
            try {
            $responseTree = $client->request(
                        'GET',
                        'https://www.pathofexile.com/character-window/get-passive-skills',
                        [
                            'headers' => [
                                'User-Agent' => self::$userAgent,
                            ],
                            'query' => [
                                'accountName' => $acc,
                                'character' => $char,
                                'realm' => $realm
                            ]
                        ]
                    );
            }catch (\GuzzleHttp\Exception\ClientException $exception) {
               return [];
           }
            return json_decode((string)$responseTree->getBody(), true);
        });
    }

    static public function getItemsData($acc, $char, $realm="pc", $proxy=false){
        //get cahce if no cache get from poe api
        $key='items/'.$acc.'/'.$char.'/'.$realm;
        $time = config('app.poe_cache_time') * 60;

        return \Cache::remember($key, $time, function () use ($acc, $char,$proxy,$realm){
            $client = new \GuzzleHttp\Client(['cookies' => true]);
            //make Requests to PathOfExile website to retrieve Character Items
            $page_url='https://www.pathofexile.com/character-window/get-items';
            if($proxy){
                $page_url = config('app.poe_proxy').$page_url;
            }
            try {
                $response = $client->request(
                    'POST',
                    $page_url, [
                        'headers' => [
                            'User-Agent' => self::$userAgent,
                        ],
                        'form_params' => [
                            'accountName' => $acc,
                            'character' => $char,
                            'realm' => $realm
                        ]
                    ]
                );
            }catch (\GuzzleHttp\Exception\ClientException $e) {
                //$response = $e->getResponse();
                return [];
            }



            $response = json_decode((string)$response->getBody(), true);
            return $response;
        });
    }

    static public function getLadder($id, $offset, $limit=200, $delve=false, $proxy=false, $realm="pc"){
        $base_url = 'https://www.pathofexile.com/api/ladders';
        $parms = '?offset='.$offset.'&limit='.$limit.'&id='.$id.'&type=league&realm='.$realm;
        if($delve){
            $parms = $parms.'&sort=depth';
        }
        $page_url = $base_url.$parms;

        if($proxy){
            $page_url = config('app.poe_proxy').$page_url;
        }

        $client = new \GuzzleHttp\Client(['http_errors' => false,'cookies' => true]);
        try {
            //$response = $client->get($page_url);
            $response = $client->request(
                'GET',
                $page_url, 
                [
                    'headers' => [
                        'User-Agent' => self::$userAgent,
                    ],
                ]
            );
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            //$response = $e->getResponse();
            return [];
        }

        if($response->getStatusCode()==404){
            flash('404 Ladder "'.$id.'" Not Found. ', 'warning');
            return false;
        }

        return json_decode($response->getBody(), true);
    }
}
