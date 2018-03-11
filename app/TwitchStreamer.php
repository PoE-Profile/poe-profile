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
        if(count($snapshots)==0){
            return true;
        }
        $last_level = intval($snapshots->last()->original_level);
        $curent_level = intval($this->account->last_character_info);
        $diff=$curent_level-$last_level;
        if($curent_level<60 && $diff>=10){
            return true;
        }else if($curent_level>60 && $curent_level<90 && $diff>=5 ){
            return true;
        }else if($diff>=1 && $curent_level>=90){
            return true;
        }

        return false;
    }
}
