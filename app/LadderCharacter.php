<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LadderCharacter extends Model
{
	protected $fillable = [
        'league', 'rank', 'name', 'class', 'level', 'account_id', 'items_most_sockets',
        'dead', 'public', 'online', 'experience', 'stats'
    ];
    protected $casts = [
        'items_most_sockets' => 'array',
        'dead' => 'boolean',
        'online' => 'boolean',
        'public' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo('App\Account');
    }

    public function scopeLeague($query, $league)
    {
        return $query->where('league', '=', $league)->orderBy('rank', 'asc');
    }

    public function scopeSkill($query, $skill)
    {
        return $query->where('items_most_sockets', 'like', "%typeLine\":\"" . $skill . "\"%");
    }

    public function scopeClass($query, $class)
    {
        return $query->where('class', '=', $class);
    }

    public function scopeFilter($query, $request)
    {
        $take = 30;
        if ($request->has('searchFilter')) {
            $query->whereHas('account', function ($query) use (&$request) {
                    $query->where('name', 'like', '%' . $request->input('searchFilter') . '%');
                })
                ->orWhere('name', 'like', '%' . $request->input('searchFilter') . '%');
        }

        if ($request->has('classFilter')) {
            $query->class($request->input('classFilter'));
        }

        if ($request->has('skillFilter')) {
            $query->skill($request->input('skillFilter'));
        }

        return $query->league($request->input('leagueFilter'))->paginate($take);
    }

}
