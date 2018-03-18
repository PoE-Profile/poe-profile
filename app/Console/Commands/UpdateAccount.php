<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LadderCharacter;
use Sunra\PhpSimple\HtmlDomParser;

class UpdateAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:update-ladder-info {--limit=0} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $currentLeagues = explode(',',config('app.poe_leagues'));
        foreach ($currentLeagues as $league) {
            $this->updateLeague($league);
        }
    }

    private function updateLeague($league){
        $this->info("updateLeague ".$league);
        $limit = $this->option('limit') ? $this->option('limit') : 100;
        $charsToUpdate=LadderCharacter::where('league', '=', $league)
                        // ->where('updated_at', '<', \Carbon\Carbon::now()->toDateString())
                        ->where('public', true)
                        ->orderBy('rank')->take($limit)->get();
        // dd($accToUpdate->toArray());
        foreach ($charsToUpdate as $char) {
            $this->info("update ".$char->rank." ".$char->name);
            $job = (new \App\Jobs\UpdateLadderStatus($char->id));
            dispatch($job);
            usleep(700000);
        }
    }

}
