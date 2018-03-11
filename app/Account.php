<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sunra\PhpSimple\HtmlDomParser;
use App\PoeApi;

class Account extends Model
{
    protected $fillable = [
        'name', 'user_id', 'guild', 'challenges_completed', 'poe_avatar_url',
        'last_character', 'last_character_info'
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
        $last_character = PoeApi::getLastCharacter($this->name);
        $this->last_character = $last_character;
        $this->touch();
        $this->save();
    }


    public function updateLastCharInfo($itemsData=null){
        if($itemsData==null){
            $itemsData=PoeApi::getItemsData($this->name, $this->last_character);
        }
        if (!array_key_exists('items', $itemsData)) {
            var_dump('private acc');
            return;
        }
        $items_most_sockets = [];
        foreach ($itemsData['items'] as $item) {
            if (!array_key_exists('sockets', $item)) {
                continue;
            }
            if (count($item['sockets']) >= 5) {
                $items_most_sockets[] = $item;
            }
        }
        $lastChar=[
            'league'=>$itemsData['character']['league'],
            'name'=>$itemsData['character']['name'],
            'class'=>$itemsData['character']['class'],
            'level'=>$itemsData['character']['level'],
            'items_most_sockets'=>$items_most_sockets,
        ];

        $this->last_character_info=$lastChar;
        $this->save();
    }


    public function getPublicStash($league){
        $key='stash/'.$this->name.'/'.$league;
        $requestData = $this->getStashRequestData();
        $response = \Cache::remember($key, config('app.poe_cache_time')+10,
            function () use ($league){
                $client = new \GuzzleHttp\Client();
                try {
                    $url='https://www.pathofexile.com/api/trade/search/'.rawurlencode($league).'/';
                    $response = $client->post($url, [
                        \GuzzleHttp\RequestOptions::JSON => $this->getStashRequestData()
                    ]);
                    return json_decode((string)$response->getBody());
                }catch (\GuzzleHttp\Exception\ClientException $e) {
                    return (Object)["total"=>0];
                }
            });
        return $response;
    }

    private function getStashRequestData(){
        return [
        	"query"=>[
        		"status"=> [
        	      "option" => "any"
        	    ],
        	    "stats" => [
        	      [
        	        "type" => "and",
        	        "filters" => []
        	      ]
        	    ],
        	    "filters" => [
        	      "trade_filters"=> [
        	        "disabled"=> false,
        	        "filters"=> [
        		          	"account"=> [
        		            	"input"=> $this->name
        		          	]
        	        	]
        	      	]
        	    ]
        	],
        	"sort"=>[
        		"price"=>"asc"
        	]
        ];
    }

}
