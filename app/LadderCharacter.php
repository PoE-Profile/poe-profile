<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LadderCharacter extends Model
{
	protected $fillable = [
        'league', 'rank', 'name', 'class', 'level', 'account_id', 'items_most_sockets',
        'dead', 'public', 'unique_id', 'online', 'experience', 'stats'
    ];
    protected $casts = [
        'items_most_sockets' => 'array',
        'dead' => 'boolean',
        'online' => 'boolean',
        'public' => 'boolean',
    ];

    protected $appends = ['levelProgress'];

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

    public function getLevelProgressAttribute() {
        $exp_table = \Storage::disk('public_folder')->get('/jsons/expirience_table.json');
        $exp_table = json_decode($exp_table, true);
        $id = $this->level - 1;
        $current_level = $exp_table['levels'][$id];
        //check if level 100 return 100%
        if ($current_level['total'] === 0) {
            return 100;
        }
        $xp_gained = $this->experience - $current_level['startAt'];
        $percentage = ($xp_gained / $current_level['total']) * 100;

        return (int) round($percentage);
    }

}
