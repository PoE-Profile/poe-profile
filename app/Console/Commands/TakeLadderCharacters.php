<?php

namespace App\Console\Commands;

use App\Account;
use App\TwitchStreamer;
use App\LadderCharacter;

use Illuminate\Console\Command;

class TakeLadderCharacters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * get:characters --limit=5 --pages=10 --queue
     */
    protected $signature = 'poe:ladder {--select} {--total=} {--update} {--debug} {--fill_league_table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take all characters from specific League';

    private $laddersPages = [];
    private $totalIndexedRanks = 3000;
    private $take=200;
    private $selectedLeague="";
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        if(\Cache::get('current_leagues')==null){
            $this->call('poe:update', ['--leagues' => true]);
        }

        if ($this->option('fill_league_table')) {
            $this->addLeagues();
            return;
        }
        if($this->option('select')){
            $this->selectLeague();
        }
        $this->buildUrls();
        $this->info("Start Indexing:");
        $bar = $this->output->createProgressBar(count($this->laddersPages));
        foreach ($this->laddersPages as $page) {
            $this->indexCharsFrom($page->url, $page->league);
            // $this->indexCharsFrom($page->url_with_depth, $page->league);
            $bar->advance();
        }
        $bar->finish();
        //clear above total and afetr to remove to stop repeats on last rank
        $this->clearAboveTotal();

        $this->info("Finish");
    }

    public function buildUrls(){
        $currentLeagues = explode(', ', \Cache::get('current_leagues'));
        foreach ($currentLeagues as $league) {
            if(strlen($this->selectedLeague)>0 && $this->selectedLeague!=$league){
                continue;
            }
            //+ 1 page to get above total and afetr to remove to stop repeats on last rank
            $pages=floor($this->totalIndexedRanks/$this->take)+1;
            for ($i=0; $i < $pages; $i++) {
                $offset = $i*$this->take;
                $base_url = 'https://www.pathofexile.com/api/ladders?offset=';
                $url = $base_url.$offset.'&limit='.$this->take.'&id='.$league.'&type=league';
                $url_with_depth = $base_url.$offset.'&limit='.$this->take.'&id='.$league.'&type=league&sort=depth';
                $this->laddersPages[] = (Object)array(
                                            'url' => $url,
                                            'url_with_depth' => $url_with_depth,
                                            'league' => $league,
                                        );

            }
            if($this->option('debug')){
                dump($pages);
            }
        }
    }

    public function indexCharsFrom($page_url, $page_league){
        if($this->option('debug')){
            $this->info("start request");
        }
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->get($page_url);
        $response = json_decode($response->getBody(), true);

        $chars = array_map(function($entry) use(&$page_league){
            $twitch = null;
            $delve = null;
            if (array_key_exists('twitch', $entry['account'])) {
                $twitch = $entry['account']['twitch']['name'];
            }

            $delve['default'] = 0;
            $delve['solo'] = 0;
            if (array_key_exists('depth', $entry['character'])) {
                $delve['default'] = $entry['character']['depth']['default'];
                $delve['solo'] = $entry['character']['depth']['solo'];
            }
            return [
                'league' => strtolower($page_league),
                'rank' => $entry['rank'],
                'dead' => $entry['dead'],
                'name' => $entry['account']['name'],
                'twitch' => $twitch,
                'charName' => $entry['character']['name'],
                'class' => $entry['character']['class'],
                'level' => $entry['character']['level'],
                'unique_id' => $entry['character']['id'],
                'delve_default' => $delve['default'],
                'delve_solo' => $delve['solo'],
                'experience' => $entry['character']['experience'],
                'online' => $entry['online'],
            ];
        }, $response['entries']);

        foreach ($chars as $char) {
            $this->indexChar($char);
        }
    }

    public function indexChar($char){
        $ladderCharacter = LadderCharacter::where('name', '=', $char['charName'])
                            ->where('league', '=', $char['league'])->first();
        if ($ladderCharacter !== null) {
            //update char
            //$this->info($char['rank'].' character '. $char['charName']. ' updated!');
            $ladderCharacter->rank = $char['rank'];
            $ladderCharacter->level = $char['level'];
            $ladderCharacter->dead = $char['dead'];
            $ladderCharacter->unique_id = $char['unique_id'];
            $ladderCharacter->experience = $char['experience'];
            $ladderCharacter->online = $char['online'];
            $ladderCharacter->delve_default = 0;
            $ladderCharacter->delve_solo = 0;
            $ladderCharacter->save();
        } else {
            //add new
            // $this->info($char['rank'].' character '. $char['charName']. ' added!');
            $acc=Account::where('name', '=', $char['name'])->first();

            //if no acc create new
            if (!$acc) {
                $acc = Account::create(['name' => $char['name']]);
            }
            //if has twitch add to db
            if ($char['twitch'] != null) {
                if (TwitchStreamer::where('name', '=', $char['twitch'])->first() === null) {
                    $newStreamer = TwitchStreamer::create([
                        'name' => $char['twitch'],
                        'account_id' => $acc->id
                    ]);
                }
            }
            $ladderCharacter = LadderCharacter::create([
                'rank' => $char['rank'],
                'league' => $char['league'],
                'name' => $char['charName'],
                'class' => $char['class'],
                'level' => $char['level'],
                'unique_id' => $char['unique_id'],
                'experience' => $char['experience'],
                'online' => $char['online'],
                'delve_default' => 0,
                'delve_solo' => 0,
                'account_id' => $acc->id,
                'dead' => $char['dead'],
                'public' => true
            ]);
        }

        if($this->option('update')){
            \App\Jobs\UpdateLadderStatus::dispatch($ladderCharacter->id);
        }
    }

    public function clearAboveTotal(){
        \DB::table('ladder_characters')->where('rank', '>', $this->totalIndexedRanks)->delete();
    }

    public function selectLeague(){
        $currentLeagues = explode(', ', \Cache::get('current_leagues'));
        $index=1;
        foreach ($currentLeagues as $league) {
            $this->info($index.".".$league);
            $index++;
        }
        $index= $this->ask('What league ?(int):');
        $this->selectedLeague=$currentLeagues[$index-1];
    }

    public function remove_last_char($string) {
        return substr($string, 0, -1) == null ? '' : substr($string, 0, -1);
    }

    public function addLeagues() {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->get('http://api.pathofexile.com/leagues?type=main');
        $main_leagues = json_decode($response->getBody(), true);
        $main_leagues_string = '';
        foreach ($main_leagues as $league) {
            $main_leagues_string .= strtolower($league['id']) . ', ';
            \App\League::firstOrCreate([
                'name'      => strtolower($league['id']),
                'type'      => 'public',
                'start_at'  => new \Carbon\Carbon($league['startAt']),
                'end_at'    => $league['endAt'] == null ? null : new \Carbon\Carbon($league['endAt']),
                'rules'     => $this->get_league_rules($league['id'], $league['rules'])
            ]);
        }
        $main_leagues_string = substr($main_leagues_string, 0, -2);

        $old_leagues = LadderCharacter::groupBy('league')->get()->pluck('league')->toArray();
        foreach ($old_leagues as $id) {
            if (str_contains($main_leagues_string, $id)) {
                continue;
            }
            \App\League::firstOrCreate([
                'name' => strtolower($id),
                'type' => 'public',
                'rules' => $this->get_league_rules($id)
            ]);
        }
    }

    public function get_league_rules($name, $rules = []) {
        $temp_rules = '';
        if (count($rules) > 0) {
            foreach ($rules as $rule) {
                $temp_rules .= $rule['description'] . '/';
            }
            return $this->remove_last_char($temp_rules);
        }

        if (str_contains($name, 'hardcore') || str_contains($name, 'hc')) {
            $temp_rules .= 'A character killed in Hardcore is moved to its parent league./';
        }

        if (str_contains($name, 'ssf')) {
            $temp_rules .= 'You may not party in this league./';
        }

        return $this->remove_last_char($temp_rules);
    }

}
