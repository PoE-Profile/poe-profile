<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PoeApi;
use App\Helpers\Items;

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
        $chars = PoeApi::getCharsData($this->name);
        if(!$chars) return;
        $lastChar = collect($chars)->first(function ($char) {
            return property_exists($char, 'lastActive');
        });
        if(!$lastChar){
            $currentLeague = ucfirst(explode(', ', \Cache::get('current_leagues'))[0]);
            $lastChar = collect($chars)->filter(function ($char) use($currentLeague) {
                return strpos($char->league, $currentLeague) !== false ;
            })->sortByDesc('level')->first();
        }
        if(!$lastChar){
            return;
        }
        $this->last_character = $lastChar->name;
        $this->touch();
        $this->save();
    }


    public function updateLastCharInfo($itemsData=null){
        if($itemsData==null){
            $itemsData=PoeApi::getItemsData($this->name, $this->last_character);
        }
        if (!array_key_exists('items', $itemsData)) {
            return false;
        }
        $items_most_sockets = Items::withMostSockets($itemsData);
        
        if ($this->last_character==$itemsData['character']['name']) {
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
        $ladder_char=$this->ladderChars()
                        ->where('name', $itemsData['character']['name'])->first();
        if($ladder_char){
            $ladder_char->items_most_sockets=$items_most_sockets;
            $ladder_char->public=true;
            $ladder_char->save();
        }
        return true;

    }


    public function getPublicStash($league){
        $client = new \GuzzleHttp\Client();
        try {
            $url='https://www.pathofexile.com/api/trade/search/'.rawurlencode($league);
            $response = $client->post($url, [
                \GuzzleHttp\RequestOptions::JSON => $this->getStashRequestData()
            ]);
            return json_decode((string)$response->getBody());
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return (Object)["total"=>0];
        }
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
