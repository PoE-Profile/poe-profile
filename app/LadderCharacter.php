<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LadderCharacter extends Model
{
	protected $fillable = [
        'league', 'rank', 'name', 'class', 'level', 'account_id', 'items_most_sockets', 'dead', 'public'
    ];
    protected $casts = [
        'items_most_sockets' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo('App\Account');
    }
}
