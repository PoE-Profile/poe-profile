<?php

namespace App\Console\Commands;

use App\Account;
use App\TwitchStreamer;
use App\LadderCharacter;
use App\PoeApi;

use Illuminate\Console\Command;

class PoeLadder extends Command
{
    protected $signature = 'poe:ladder {--select} {--total=} {--update} {--debug} {--delve}';
    protected $description = 'Take all characters from specific League';
    private $totalIndexedRanks = 3000;
    private $take=200;
    private $selectedLeague="";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */

    public function handle()
    {
        $this->info("Start Indexing:");

        if(\Cache::get('current_leagues')==null){
            $this->call('poe:update', ['--leagues' => true]);
        }

        if($this->option('select')){
            $this->selectLeague();
        }

        $currentLeagues = explode(', ', \Cache::get('current_leagues'));
        foreach ($currentLeagues as $league) {
            if(strlen($this->selectedLeague)>0 && $this->selectedLeague!=$league){
                continue;
            }
            $this->info("");
            $this->info("Index ".$league);

            $pages = floor($this->totalIndexedRanks/$this->take)+1;
            $bar = $this->output->createProgressBar($pages);
            for ($i=0; $i <= $pages; $i++) {
                $offset = $i*$this->take;
                $delve = $this->option('delve');
                if(!$delve){
                    $response = PoeApi::getLadder($league,$offset);
                }else{
                    $response = PoeApi::getLadder($league,$offset,$this->take,$delve);
                }
                $this->index($league,$response);
                $bar->advance();
            }
            $bar->finish();
        }
        //clear above total to stop repeats on last rank
        $this->clearAboveTotal();
        $this->info("Finish");
    }

    public function index($league, $response){

        $chars = $this->mapEntries($response['entries'],$league);
        foreach ($chars as $char) {
            $ladderCharacter = LadderCharacter::where('unique_id', '=', $char['unique_id'])
                                                ->where('league', '=', $league)->first();
            if (!$ladderCharacter) {
                //not in db add new
                $ladderCharacter = LadderCharacter::create($char);
            }else{
                //else update
                if($this->option('delve')){
                    //update only delve
                    $ladderCharacter->delve_default=$char['delve_default'];
                    $ladderCharacter->delve_solo=$char['delve_solo'];
                    $ladderCharacter->save();
                    continue;
                }
                $ladderCharacter->updateLadderInfo($char);
            }

            if($this->option('update')){
                \App\Jobs\UpdateLadderStatus::dispatch($ladderCharacter->id);
            }
        }

    }

    public function mapEntries($entries, $league){
        return array_map(function($entry) use(&$league){
            $newEntry = LadderCharacter::poeEntryToArray($entry);
            $newEntry['league'] = $league;
            if($this->option('delve')){
                $newEntry['rank']=0;
            }
            $twitch = null;
            if (array_key_exists('twitch', $entry['account'])) {
                $twitch = $entry['account']['twitch']['name'];
            }
            $acc = $this->charAcc($entry['account']['name'],$twitch);
            $newEntry['account_id'] = $acc->id;
            return $newEntry;
        }, $entries);
    }

    public function charAcc($accName,$charTwitch=null){
        $acc=Account::where('name', '=', $accName)->first();
        //if no acc create new
        if (!$acc) {
            $acc = Account::create(['name' => $accName]);
        }
        //if has twitch add to db
        if ($charTwitch != null) {
            if (TwitchStreamer::where('name', '=', $charTwitch)->first() === null) {
                $newStreamer = TwitchStreamer::create([
                    'name' => $charTwitch,
                    'account_id' => $acc->id
                ]);
            }
        }
        return $acc;
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

}
