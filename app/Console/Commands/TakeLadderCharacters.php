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
    protected $signature = 'poe:ladder {--select} {--total=} {--update} {--debug}';

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
        if($this->option('select')){
            $this->selectLeague();
        }
        $this->buildUrls();
        $this->info("Start Indexing:");
        $bar = $this->output->createProgressBar(count($this->laddersPages));
        foreach ($this->laddersPages as $page) {
            $this->indexCharsFrom($page);
            $bar->advance();
        }
        $bar->finish();
        //clear above total and afetr to remove to stop repeats on last rank
        $this->clearAboveTotal();

        $this->info("Finish");
    }

    public function buildUrls(){
        $currentLeagues = explode(',',config('app.poe_leagues'));
        foreach ($currentLeagues as $league) {
            if(strlen($this->selectedLeague)>0 && $this->selectedLeague!=$league){
                continue;
            }
            //+ 1 page to get above total and afetr to remove to stop repeats on last rank
            $pages=floor($this->totalIndexedRanks/$this->take)+1;
            for ($i=0; $i < $pages; $i++) {
                $offset = $i*$this->take;
                $base='http://api.pathofexile.com/leagues/';
                $url = $base.$league.'?ladder=1&ladderOffset='.$offset.'&ladderLimit='.$this->take;
                $this->laddersPages[] = (Object)array(
                                            'url' => $url,
                                            'league' => $league,
                                        );

            }
            if($this->option('debug')){
                dump($pages);
            }
        }
    }

    public function indexCharsFrom($page){
        if($this->option('debug')){
            $this->info("start request");
        }
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->get($page->url);
        $response = json_decode($response->getBody(), true);
        $chars = array_map(function($entry) use(&$page){
            $twitch = null;
            if (array_key_exists('twitch', $entry['account'])) {
                $twitch = $entry['account']['twitch']['name'];
            }
            return [
                'league' => strtolower($page->league),
                'rank' => $entry['rank'],
                'dead' => $entry['dead'],
                'name' => $entry['account']['name'],
                'twitch' => $twitch,
                'charName' => $entry['character']['name'],
                'class' => $entry['character']['class'],
                'level' => $entry['character']['level']
            ];
        }, $response['ladder']['entries']);

        foreach ($chars as $char) {
            $this->indexChar($char);
        }
    }

    public function indexChar($char){
        $ladderCharacter = LadderCharacter::where('name', '=', $char['charName'])->first();
        if ($ladderCharacter !== null) {
            //update char
            //$this->info($char['rank'].' character '. $char['charName']. ' updated!');
            $ladderCharacter->timestamps = false;
            $ladderCharacter->rank = $char['rank'];
            $ladderCharacter->level = $char['level'];
            $ladderCharacter->dead = $char['dead'];
            $ladderCharacter->class = $char['class'];
            $ladderCharacter->save();
            $ladderCharacter->timestamps = true;
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
                'account_id' => $acc->id,
                'dead' => $char['dead'],
                'public' => true
            ]);
        }

        if($this->option('update')){
            // dd($ladderCharacter->id);
            $job = (new \App\Jobs\UpdateLadderStatus($ladderCharacter->id));
            dispatch($job);
            //add timeout for ratelimit
            sleep(1);
        }
    }

    public function clearAboveTotal(){
        \DB::table('ladder_characters')->where('rank', '>', $this->totalIndexedRanks)->delete();
    }

    public function selectLeague(){
        $currentLeagues = explode(',',config('app.poe_leagues'));
        $index=1;
        foreach ($currentLeagues as $league) {
            $this->info($index.".".$league);
            $index++;
        }
        $index= $this->ask('What league ?(int):');
        $this->selectedLeague=$currentLeagues[$index-1];
    }
}
