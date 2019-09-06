<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Account;
use App\Snapshot;

class PoeTwitch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poe:twitch {operation}';
    public $twitchChannels = [
        'RaizQT' => 'raizqt',
        'nugiyen' => 'nugiyen',
        'chistor_' => 'chistor_',
        'DismantleTime' => 'dismantletime',
        'norseman21' => 'norse_tv',
        'Walter_Cronkite' => 'morikiopa',
        'Aducat' => 'sirkultan',
        'tarke' => 'tarkecat',
        'Ghazzy' => 'ghazzy',
        'Karvarousku' => 'karvarouskugaming',
        'sefearion' => 'sefearionpvp',
        'Blattos' => 'blatty',
        'Empyrianwarpgate' => 'empyriangaming',
        'Morsexier' => 'MorsRageNG',
        'Anafobia' => 'anafobia',
        'Dsfarblarwaggle' => 'waggle',
        'mathil' => 'mathil1',
        'AlkaizerX' => 'alkaizerx',
        'etup' => 'etup',
        'Zizaran' => 'zizaran',
        'fandabear1' => 'cutedog_',
        'poizntv' => 'poizntv',
        'FleepQc' => 'fleepqc',
        'LiftingNerdBro' => 'lifting_',
        'yojimoji' => 'itsyoji',
        'pohx' => 'pohx',
        'blasting_cap' => 'blasting_cap',
        'ZiggyD' => 'ziggydlive',
        'Helman' => 'helmannn',
        'Angryafrican' => 'angryaa',
        'HegemonyTV' => 'hegemonytv',
        'Rexitus' => 'rexitus',
        'xtreme1330' => 'xxtremetv',
        'DeMiGodking102' => 'demigodkinglol',
        'Shinrha' => 'shinrha',
        'Krippers' => 'nl_kripp',
        'DCLara' => 'dclara1',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating twitch streamers table';

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

        // take acounts
        switch ($this->argument('operation')) {
            case 'seeds':
                $this->info("start seeding twtich table");
                $this->seedTwitchTable();
                break;

            case 'update':
                $this->info("start update twtich table");
                $this->updateTwitchInfo();
                break;
            case 'add':
                $poeAcc = $this->ask('Path of Exile profile');
                $streamerAcc = $this->ask('Streamer channel name');
                $dbAcc = \App\Account::where('name', $poeAcc)->first();
                $newStreamer = \App\TwitchStreamer::create([
                    'name' => $streamerAcc,
                    'account_id' => $dbAcc->id
                ]);
                break;

            default:
                # code...
                $this->error("No valid operation given");
                break;
        }
    }
    private function seedTwitchTable()
    {
        foreach ($this->twitchChannels as $key => $value) {
            $dbAcc = \App\Account::where('name', $key)->first();
            if (!$dbAcc) {
                $dbAcc = \App\Account::create(['name' => $key]);
                $this->info('add acc:' . $key);
            } else {
                $this->info('update acc:' . $key);
            }
            $dbAcc->updateLastChar();
            $dbAcc->updateLastCharInfo();

            if (!$dbAcc->streamer) {
                $this->info('add twitch:' . $value);
                $newStreamer = \App\TwitchStreamer::create([
                    'name' => $value,
                    'account_id' => $dbAcc->id
                ]);
            }
        }
    }

    public function updateTwitchInfo()
    {
        \DB::statement("UPDATE `twitch_streamers` set online=0 ,viewers=0");
        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'GET',
            'https://api.twitch.tv/helix/streams?game_id=29307&first=50',
            [
                'headers' => [
                    'Client-ID' => 'gi3es6sr9cmscw4aww6lbt309dyj8e',
                    'User-Agent' => 'testing/1.0',
                    'Accept'     => 'application/json',
                ]
            ]
        );
        $data = json_decode((string) $response->getBody())->data;
        // dd($data);
        foreach ($data as $stream) {

            $this->info($stream->user_name);
            $dbStreamer = \App\TwitchStreamer::where('name', $stream->user_name)->first();
            // if in DB update info
            if ($dbStreamer) {
                $thumbnail = str_replace('{width}', 240, $stream->thumbnail_url);
                $thumbnail = str_replace('{height}', 135, $thumbnail);

                $dbStreamer->viewers = $stream->viewer_count;
                $dbStreamer->status = $stream->title;
                $dbStreamer->img_preview = $thumbnail;
                $dbStreamer->channel_id = $stream->user_id;
                $dbStreamer->online = true;
                $dbStreamer->last_online = \Carbon\Carbon::now();
                $dbStreamer->save();

                $dbStreamer->account->updateLastChar();
                $dbStreamer->account->updateLastCharInfo();
                if ($dbStreamer->isForSnapshot()) {
                    $acc = $dbStreamer->account->name;
                    $char = $dbStreamer->account->last_character;
                    Snapshot::create($acc, $char);
                }
            }
        }
        \Cache::forget('OnlineStreamers');
    }
}
