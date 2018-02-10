<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
