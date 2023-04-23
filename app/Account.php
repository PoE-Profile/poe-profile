<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PoeApi;
use App\Helpers\Items;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Account extends Model
{
    protected $fillable = [
        'name', 'user_id', 'guild', 'challenges_completed', 'poe_avatar_url',
        'last_character', 'last_character_info'
    ];

    protected $casts = [
        'last_character_info' => 'array',
        'characters' => 'array',
        'updated_at'=>'datetime:Y-m-d',
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
        $currentLeagues = explode(', ', \Cache::get('current_leagues'));
        $last_char_league=$this->last_character_info?$this->last_character_info['league']:'';
        if(!in_array(strtolower($last_char_league),$currentLeagues) //if last char legue is not in curent
            || !$this->characters || \Request::has('updateCharacters'))
        {
            $this->updateLastChar();
        }
        $this->views=$this->views+1;
        $this->timestamps = false;
        $this->save();
    }

    public function updateLastChar(){
        if ($this->updated_at->diffInMinutes(now()) > 12){
            $chars = PoeApi::getCharsData($this->name);

            // problem with limits stop
            if ($chars == false) {
                return;
            }

            // if 0 chars acc is private remove info
            if (count($chars) > 0) {

                // set last_character
                $lastChar = collect($chars)->filter(function ($char) {
                    $currentLeague = strtolower(explode(', ', Cache::get('current_leagues'))[0]);
                    return Str::contains(strtolower($char->league), $currentLeague);
                })->sortByDesc('level')->first();

                if (!is_null($this->characters)) {
                    $charactersChanges = collect($this->characters)
                        ->pluck('experience', 'name')
                        ->diff(collect($chars)->pluck('experience', 'name'));

                    if (count($charactersChanges->all()) > 0) {
                        Log::debug($charactersChanges->toJson());
                        $lastChar = collect($chars)->whereIn('name', array_keys($charactersChanges->all()))->first();
                    }
                }

                // if Account dont have characters in the current League
                // if it is new Account and $this->characters isnt set
                // take highest level character
                if (is_null($lastChar)) {
                    $lastChar = collect($chars)->sortByDesc('level')->first();
                }

                if ($lastChar) {
                    $this->characters = $chars;
                    $this->last_character = $lastChar->name;
                    $this->last_character_info = [
                        'league' => $lastChar->league,
                        'name' => $lastChar->name,
                        'class' => $lastChar->class,
                        'level' => $lastChar->level,
                        'items_most_sockets' => $this->last_character_info['items_most_sockets'] ?? null,
                    ];
                }
            }

            $this->touch();
            $this->save();
        }
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

}
