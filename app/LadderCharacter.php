<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LadderCharacter extends Model
{
	protected $fillable = [
        'league', 'rank', 'name', 'class', 'level', 'account_id', 'items_most_sockets',
        'dead', 'public', 'unique_id', 'online', 'experience', 'stats', 'delve_solo', 'delve_default'
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

        return $query->league($request->input('leagueFilter'))
                        ->where('rank', '>',0)->paginate($take);
    }

    public function getLevelProgressAttribute() {
        $exp_table = \File::get(app_path('/Helpers/expirience_table.json'));
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

    //map poe api entry to our db struct;
    static public function poeEntryToArray($entry){
        $delve = ['default'=>0, 'solo'=>0];
        if (array_key_exists('depth', $entry['character'])) {
            $delve['default'] = $entry['character']['depth']['default'];
            $delve['solo'] = $entry['character']['depth']['solo'];
        }

        return [
            'rank' => $entry['rank'],
            'dead' => $entry['dead'],
            'name' => $entry['character']['name'],
            'class' => $entry['character']['class'],
            'level' => $entry['character']['level'],
            'unique_id' => $entry['character']['id'],
            'delve_default' => $delve['default'],
            'delve_solo' => $delve['solo'],
            'experience' => $entry['character']['experience'],
            'online' => $entry['online'],
            'public' => true
        ];
    }

    public function updateLadderInfo($char_info){
        $this->updateStats($char_info);
        $this->rank = $char_info['rank'];
        $this->level = $char_info['level'];
        $this->dead = $char_info['dead'];
        $this->unique_id = $char_info['unique_id'];
        $this->experience = $char_info['experience'];
        $this->online = $char_info['online'];
        $this->delve_default=$char_info['delve_default'];
        $this->delve_solo=$char_info['delve_solo'];
        $this->save();
    }

    public function updateStats($char_info){
        if ($this->online) {
            $last_update = \Carbon\Carbon::parse($this->updated_at);
            $diff = now()->diffInMinutes($last_update);
            $exp = (($char_info['experience'] - $this->experience) / $diff) * 60;
            if ($diff >= 5 && $diff <= 60) {
                $this->stats = json_encode([
                    'xph' => $this->number_format_short($exp),
                ]);

                // dump('diff in minutes from last update: ' . $diff);
                // dump('exp grinded from last update: ' . ($char_info['experience'] - $this->experience));
                // dump('exp per hour: ' . $this->number_format_short($exp));
            }
            
        }
    }

    function number_format_short($n, $precision = 1)
    {
        if ($n < 900) {
        // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
        // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
        // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
        // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
        // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
        return $n_format . $suffix;
    }
}
