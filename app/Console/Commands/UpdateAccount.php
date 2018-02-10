<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Account;
use Sunra\PhpSimple\HtmlDomParser;

class UpdateAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:update-acc {--limit=0} ';

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
        $limit = $this->option('limit') ? $this->option('limit') : 100;
        $accToUpdate=Account::where('last_character_info', '=', '')
                        ->where('updated_at', '<', \Carbon\Carbon::now()->subMinutes(90)->toDateTimeString())
                        ->orderBy('views', 'desc')->take($limit)->get();
        //dd($accToUpdate->toArray());
        foreach ($accToUpdate as $acc) {
            $this->info("update ".$acc->name);
            $acc->updateLastChar();
            $this->comment("update updateLastCharInfo");
            $acc->updateLastCharInfo(null);
        }
    }

}
