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
        $updateAfter=\Carbon\Carbon::now()->subMinutes(30);
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
            return false;
        }
        $items_most_sockets = [];
        foreach ($itemsData['items'] as $item) {
            if (!array_key_exists('sockets', $item)) {
                continue;
            }
            $grouped = collect($item['sockets'])->groupBy('group');
            $item_most_links=count($grouped[0]);
            $supports = collect($item['socketedItems'])
                        ->where('support',true)
                        ->filter(function ($item) use($item_most_links){
                            return $item['socket'] < $item_most_links;
                        });
            $level=$itemsData['character']['level'];
            $requiredSupports = $level<30 ? 2 : 3 ;
            $requiredSupports = $level<10 ? 1 : $requiredSupports ;

            if ($supports->count()>=$requiredSupports) {
                $items_most_sockets[] = $item;
            }
        }

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
