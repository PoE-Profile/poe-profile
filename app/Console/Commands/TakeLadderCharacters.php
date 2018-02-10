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
    protected $signature = 'poe:ladder {--league=} {--total=} {--update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take all characters from specific League';

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
        $currentLeagues = [
            'Harbinger',
            'Hardcore Harbinger',
            'Standard',
            'Hardcore',
            'SSF Harbinger',
            'SSF Harbinger HC',
            'SSF Standard',
            'SSF Hardcore'
        ];
        $currentLeagues = explode(',',config('app.poe_leagues'));

        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $league = $this->option('league');
        $total = $this->option('total');

        for ($i=0; $i < round($total/200); $i++) {
            $offset = $i*200;
            $url='http://api.pathofexile.com/leagues/'.$league.'?ladder=1&ladderOffset='.$offset.'&ladderLimit=200';
            // dd($url);
            $this->info('Next api request '. $url. '!');
            $response = $client->get($url);
            $response = json_decode($response->getBody(), true);
            // dd($response);

            // if (empty($response['ladder'])) {
            //     continue;
            // }

            //take all accounts
            $this->info('start array_map response!');
            $accounts = array_map(function($entry) use(&$league){
                $twitch = null;
                if (array_key_exists('twitch', $entry['account'])) {
                    $twitch = $entry['account']['twitch']['name'];
                }
                return [
                    'league' => strtolower($league),
                    'rank' => $entry['rank'],
                    'dead' => $entry['dead'],
                    'name' => $entry['account']['name'],
                    'twitch' => $twitch,
                    'charName' => $entry['character']['name'],
                    'class' => $entry['character']['class'],
                    'level' => $entry['character']['level']
                ];
            }, $response['ladder']['entries']);

            $this->info('end array_map response!');

            foreach ($accounts as $acc) {
                // dd($acc);
                
                $ladderCharacter = LadderCharacter::where('name', '=', $acc['charName'])->first();
                if ($ladderCharacter !== null) {
                    $this->info($acc['rank'].' character '. $acc['charName']. ' updated!');
                    $ladderCharacter->rank = $acc['rank'];
                    $ladderCharacter->level = $acc['level'];
                    $ladderCharacter->dead = $acc['dead'];
                    $ladderCharacter->class = $acc['class'];
                    $ladderCharacter->save();
                } else {
                    $this->info($acc['rank'].' character '. $acc['charName']. ' added!');

                    $accID = null;
                    if (Account::where('name', '=', $acc['name'])->count() > 0) {
                        $accID = Account::where('name', '=', $acc['name'])->first()->id;
                    } else {
                        $newAcc = Account::create(['name' => $acc['name']]);
                        $accID = $newAcc->id;
                    }

                    if ($acc['twitch'] != null) {
                        if (TwitchStreamer::where('name', '=', $acc['twitch'])->first() === null) {
                            $newStreamer = TwitchStreamer::create([
                                'name' => $acc['twitch'],
                                'account_id' => $accID
                            ]);
                        }
                    }
                    $ladderCharacter = LadderCharacter::create([
                        'rank' => $acc['rank'],
                        'league' => $acc['league'],
                        'name' => $acc['charName'],
                        'class' => $acc['class'],
                        'level' => $acc['level'],
                        'account_id' => $accID,
                        'dead' => $acc['dead'],
                    ]);
                }

                if($this->option('update')){
                    // dd($ladderCharacter->id);
                    $job = (new \App\Jobs\UpdateLadderStatus($ladderCharacter->id));
                    dispatch($job);
                }

                
            }
            //add timeout after evry FOR cicle
            sleep(3);
        }
    }
}
