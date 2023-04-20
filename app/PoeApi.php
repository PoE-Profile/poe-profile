<?php
namespace App;

use Illuminate\Support\Facades\Log;

class PoeApi
{
    public static $userAgent = "OAuth poe-profile.info/2.6.0 (contact: https://github.com/PoE-Profile/poe-profile/) StrictMode";

    static public function getCharsData($acc, $realm="pc")
    {
        if(isset($_GET['realm'])){
            $realm = $_GET['realm'];
        }
        $cacheKey=$acc.'/'.$realm;

        if(\Cache::get('limit-characters-api',false)){
            flash('Problem with pathofexile.com api limit. ', 'warning');
            return false;
        }

        //if cache false clear and try again
        if(\Cache::get($cacheKey)===false){
            \Cache::forget($cacheKey);
        }
        
        $time = config('app.poe_cache_time') * 60;
        return \Cache::remember($cacheKey, $time, function () use ($acc,$realm) {
            Log::debug('getting characters from API');
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
                if(self::checkForLimit('characters',$exception)){
                    return false;
                }
                flash('Аccount is private or does not exist. Or pathofexile.com is down for maintenance.', 'warning');
                return false;
            }
            $result = json_decode((string)$response->getBody());
            return $result;
        });
    }

    static public function getTreeData($acc, $char, $realm="pc")
    {
        if(\Cache::get('limit-tree-api',false)){
            flash('Problem with pathofexile.com api limit. ', 'warning');
            return false;
        }

        $key='tree/'.$acc.'/'.$char.'/'.$realm;
        $time = config('app.poe_cache_time') * 60;
        return \Cache::remember($key, $time, function () use ($acc,$char,$realm) {
            Log::debug('getting tree from API');
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
                if(self::checkForLimit('tree',$exception)){
                    return false;
                }
                return [];
           }
            return json_decode((string)$responseTree->getBody(), true);
        });
    }

    static public function getItemsData($acc, $char, $realm="pc", $proxy=false){
        if(\Cache::get('limit-items-api',false)){
            flash('Problem with pathofexile.com api limit. ', 'warning');
            return false;
        }

        //get cahce if no cache get from poe api
        $key='items/'.$acc.'/'.$char.'/'.$realm;
        $time = config('app.poe_cache_time') * 60;

        return \Cache::remember($key, $time, function () use ($acc, $char,$proxy,$realm){
            Log::debug('getting items from API');
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
            }catch (\GuzzleHttp\Exception\ClientException $exception) {
                if(self::checkForLimit('items',$exception)){
                    return false;
                }
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

    public static function checkForLimit($key, \GuzzleHttp\Exception\ClientException $e)
    {
        if($e->getResponse()->getStatusCode()==429){
            $retryAfter =(int)$e->getResponse()->getHeader('Retry-After')[0];
            \Cache::add('limit-'.$key.'-api', true, $retryAfter);
            \Log::critical('Problem with pathofexile.com '.$key.' api limit. Retry-After:'.$retryAfter);
            flash('Problem with pathofexile.com api limit. ', 'warning');
            return true;
        }
        return false;
    }
}
