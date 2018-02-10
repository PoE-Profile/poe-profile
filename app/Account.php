<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sunra\PhpSimple\HtmlDomParser;

class Account extends Model
{
    protected $fillable = [
        'name', 'guild', 'challenges_completed', 'poe_avatar_url', 'last_character', 'last_character_info'
    ];


    protected $casts = [
        'last_character_info' => 'array',
    ];

    public function streamer()
    {
        return $this->hasOne('App\TwitchStreamer');
    }

    public function ladderChars()
    {
        return $this->hasMany('App\LadderCharacter');
    }

    protected function getNewAccInfo($acc){
        // $html = HtmlDomParser::file_get_html('https://www.pathofexile.com/account/view-profile/'. $acc .'/characters');
        //problem with HtmlDomParser::file_get_html with php 7.1 
        //get with Guzzle and then str_get_html
        $client = new \GuzzleHttp\Client([
              'base_uri' => 'https://www.pathofexile.com/',
              'timeout'  => 5.0,
            ]);
        $response = $client->request('GET', '/account/view-profile/'. $acc .'/characters');
        $body = $response->getBody()->getContents();
        $html = HtmlDomParser::str_get_html($body);
        

        //filter for accoutn name
        $name = $html->find('.container-content h2',0);
        if($name){
            $name = explode('-',$name->plaintext)[0];
        }
        $name=trim($name);
        $last_character = $this->getLastCharFrom($html);

        return array(
            'name' => $name,
            'guild' => '',
            'poe_avatar_url' => '',
            'challenges_completed' => 0,
            'last_character' => $last_character
        );
    }

    public function updateViews(){
        if(\Schema::hasColumn('accounts', 'views')){
            $this->views=$this->views+1;
            $this->timestamps = false;
            $this->save();
        }
    }

    public function updateLastChar(){
        // stop if acc last_character updated in last 60 min
        $updateAfter=\Carbon\Carbon::now()->subMinutes(60);
        if($updateAfter<$this->updated_at){
            return;
        }
        $last_character = $this->getLastCharFrom(null);
        $this->last_character=$last_character;
        $this->touch();
        $this->save();
    }

    private function getLastCharFrom($html){
        if($html==null){
            // $html = HtmlDomParser::file_get_html('https://www.pathofexile.com/account/view-profile/'. $this->name .'/characters');
            //problem with HtmlDomParser::file_get_html with php 7.1 
            //get with Guzzle and then str_get_html

            $client = new \GuzzleHttp\Client([
              'base_uri' => 'https://www.pathofexile.com/',
              'timeout'  => 5.0,
            ]);
            $response = $client->request('GET', '/account/view-profile/'. $this->name .'/characters');
            $body = $response->getBody()->getContents();
            $html = HtmlDomParser::str_get_html($body);
        }
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

    public function updateLastCharInfo($itemsRequestData){
        if($itemsRequestData==null){
            $itemsRequestData=$this->getItemsFor($this->last_character);
        }
        if (!array_key_exists('items', $itemsRequestData)) {
            var_dump('private acc');
            return;
        }
        $items_most_sockets = [];
        foreach ($itemsRequestData['items'] as $item) {
            if (!array_key_exists('sockets', $item)) {
                continue;
            }
            if (count($item['sockets']) >= 5) {
                $items_most_sockets[] = $item;
            }
        }
        // \Log::info("update last_character_info name->".$this->last_character.":");
        // \Log::info($itemsRequestData['character']);
        $lastChar=[
            'league'=>$itemsRequestData['character']['league'],
            'name'=>$itemsRequestData['character']['name'],
            'class'=>$itemsRequestData['character']['class'],
            'level'=>$itemsRequestData['character']['level'],
            'items_most_sockets'=>$items_most_sockets,
        ];
        $this->last_character_info=$lastChar;
        $this->save();
    }

    public function getItemsFor($char){
        //get cahce if no cache get from poe api
        $acc=$this->name;
        $key='items/'.$acc.'/'.$char;
        $items_response = \Cache::remember($key, 5, function () use ($acc,$char){
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
                $response = $e->getResponse();
                // $responseBodyAsString = $response->getBody()->getContents();
            }
            $response = json_decode((string)$response->getBody(), true);
            return $response;
        });

        return $items_response;
    }

}
