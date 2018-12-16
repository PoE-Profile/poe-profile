<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PoeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:update {--leagues}';

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
            $response = $client->get('http://api.pathofexile.com/leagues?type=main');
            $response_data = json_decode($response->getBody(), true);
            $current_leagues = $this->leaguesStringify($response_data);
            \Cache::forever('current_leagues', $current_leagues);
            $this->info('Current leagues are cached: ' . \Cache::get('current_leagues'));
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
}
