<?php
namespace App;

use Sunra\PhpSimple\HtmlDomParser;

class PoeApi
{
    static public function getCharsData($acc)
    {
        return \Cache::remember($acc, config('app.poe_cache_time'), function () use ($acc) {
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

            return $result;
        });
    }

    static public function getTreeData($acc, $char)
    {
        $key='tree/'.$acc.'/'.$char;
        $time = config('app.poe_cache_time');
        return \Cache::remember($key, $time, function () use ($acc,$char) {
            $client = new \GuzzleHttp\Client();
            try {
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
            }catch (\GuzzleHttp\Exception\ClientException $exception) {
               return [];
           }
            return json_decode((string)$responseTree->getBody(), true);
        });
    }

    static public function getItemsData($acc, $char){
        //get cahce if no cache get from poe api
        $key='items/'.$acc.'/'.$char;
        $time = config('app.poe_cache_time');
        return \Cache::remember($key, $time, function () use ($acc, $char){
            $client = new \GuzzleHttp\Client();
            //make Requests to PathOfExile website to retrieve Character Items
            try {
                $response = $client->request(
                    'POST',
                    'https://www.pathofexile.com/character-window/get-items', [
                    'form_params' => [
                        'accountName' => $acc,
                        'character' => $char
                    ]
                ]);
            }catch (\GuzzleHttp\Exception\ClientException $e) {
                //$response = $e->getResponse();
                return [];
            }

            

            $response = json_decode((string)$response->getBody(), true);
            \App\Jobs\AddCharLeague::dispatch($response);
            return $response;
        });
    }

    static public function getLastCharacter($acc){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'GET',
                'https://www.pathofexile.com/account/view-profile/'. $acc .'/characters'
            );
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return '';
        }

        $body = $response->getBody()->getContents();
        $html = HtmlDomParser::str_get_html($body);
        $temp = $html->find('script',3)->outertext;
        $test = explode('new C(',$temp);
        $last_character='';

        if(count($test)>1){
            $temp=$test[1];
            $temp = explode(');',$temp)[0];
            $last_character=json_decode($temp)->name;
        }
        return $last_character;

    }

    static public function getLadder($id, $offset, $limit=200, $delve=false){
        $base_url = 'https://www.pathofexile.com/api/ladders';
        $parms = '?offset='.$offset.'&limit='.$limit.'&id='.$id.'&type=league';
        if($delve){
            $parms = '?offset='.$offset.'&limit='.$limit.'&id='.$id.'&type=league&sort=depth';
        }
        $page_url = $base_url.$parms;

        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        try {
            $response = $client->get($page_url);
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            //$response = $e->getResponse();
            dd($e->getResponse());
            return [];
        }

        return json_decode($response->getBody(), true);
    }
}
