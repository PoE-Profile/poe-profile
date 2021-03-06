<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'name', 'type', 'rules', 'mods', 'request_invite', 'crowdfunded', 
        'max_players', 'players', 'start_at', 'end_at', 'indexed'
    ];
    protected $casts = [
        'crowdfunded' => 'boolean',
        'request_invite' => 'boolean',
        'indexed' => 'boolean',
    ];
    // public $timestamps = false;
}
