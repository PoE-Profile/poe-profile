<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LadderCharacter;
use Symfony\Component\DomCrawler\Crawler;

class PoeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:update {--leagues} {--league_table} {--tree}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '--leagues : Create string with the new leagues and cache it';

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
        if($this->option('leagues')){
            $client = new \GuzzleHttp\Client(['http_errors' => false]);
            $response = $client->request(
                'GET',
                'http://api.pathofexile.com/leagues?type=main', 
                [
                    'headers' => [
                        'User-Agent' => \App\PoeApi::$userAgent,
                    ]
                ]
            );
            $response_data = json_decode($response->getBody(), true);
            $current_leagues = $this->leaguesStringify($response_data);
            \Cache::forever('current_leagues', $current_leagues);
            $this->info('Current leagues are cached: ' . \Cache::get('current_leagues'));
            return;
        }

        if ($this->option('league_table')) {
            $this->addLeagues();
            return;
        }

        if ($this->option('tree')) {
            $this->update_tree_data();
            return;
        }
    }

    public function leaguesStringify($leagues) {
        $leagues_string = '';
        $standard_hardcore = ['standard', 'hardcore', 'ssf standard', 'ssf hardcore'];
        foreach ($leagues as $league) {
            if (in_array(strtolower($league['id']), $standard_hardcore)) {
                continue;
            }
            $leagues_string .= strtolower($league['id']) . ', ';
        }
        return $leagues_string = substr($leagues_string, 0, -2);
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
                'rules'     => $this->get_league_rules($league['id'], $league['rules']),
                'indexed' => true
            ]);
        }
        $main_leagues_string = substr($main_leagues_string, 0, -2);

        $old_leagues = LadderCharacter::groupBy('league')->get()->pluck('league')->toArray();
        foreach ($old_leagues as $id) {
            if (\Str::contains($main_leagues_string, $id)) {
                continue;
            }
            \App\League::firstOrCreate([
                'name' => strtolower($id),
                'type' => 'public',
                'rules' => $this->get_league_rules($id),
                'indexed' => true
            ]);
        }
    }

    //helper methods for updating league table
    public function get_league_rules($name, $rules = []) {
        $temp_rules = '';
        if (count($rules) > 0) {
            foreach ($rules as $rule) {
                $temp_rules .= $rule['description'] . '/';
            }
            return $this->remove_last_char($temp_rules);
        }

        if (\Str::contains($name, 'hardcore') || \Str::contains($name, 'hc')) {
            $temp_rules .= 'A character killed in Hardcore is moved to its parent league./';
        }

        if (\Str::contains($name, 'ssf')) {
            $temp_rules .= 'You may not party in this league./';
        }

        return $this->remove_last_char($temp_rules);
    }

    public function remove_last_char($string) {
        return substr($string, 0, -1) == null ? '' : substr($string, 0, -1);
    }

    public function update_tree_data(){
        $client = new \GuzzleHttp\Client();
        $response = $client->request(
                    'GET',
                    'https://www.pathofexile.com/passive-skill-tree',
                    []
                );
        $c = new Crawler((string)$response->getBody());
        $tree=null;
        $c->filter('script')->each(function ($node) use (&$tree){
            $script=$node->html();
      	    $pattern = "/(?:var passiveSkillTreeData = )(.+);/";
            preg_match($pattern, $script, $matches);
            if(count($matches)>0){
                dump($script);
            }
        });
    }
}
