<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Snapshot;

class TwitchStreamer extends Model
{
	protected $fillable = [
        'name', 'viewers', 'fallowers', 'status', 'account_id',
    ];
    protected $casts = [
        'online' => 'boolean',
    ];
    public function account()
    {
        return $this->belongsTo('App\Account');
    }

    public function isForSnapshot(){
        $original_char = $this->account->name .'/'. $this->account->last_character;
        $snapshots = Snapshot::where('original_char', '=', $original_char)->get();
        if(!$this->account->last_character_info)
            return true;
        $curent_level = intval($this->account->last_character_info['level']);
        if($curent_level<10){
            return false;
        }

        if(count($snapshots)==0 && $curent_level>10){
            return true;
        }

        $last_level = 0;
        if($snapshots->last()){
            $last_level = intval($snapshots->last()->original_level);
        }
        
        $diff=$curent_level-$last_level;
        if($curent_level>20 && $curent_level<80 && $diff>=10){
            return true;
        }else if($curent_level>80 && $curent_level<90 && $diff>=5 ){
            return true;
        }else if($diff>=1 && $curent_level>=90){
            return true;
        }

        return false;
    }
}
