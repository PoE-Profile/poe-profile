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
            case 'update-token':
                $this->info("Request token from Twitch api");
                $this->update_token();
                break;

            case 'update':
                $this->info("start update twtich table");
                $this->update_info();
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
    private function update_token()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'POST',
            'https://id.twitch.tv/oauth2/token?client_id=gi3es6sr9cmscw4aww6lbt309dyj8e&client_secret=g68pe92grxt37fayigrl1w6xcl6807&grant_type=client_credentials'
        );

        \Storage::put('access_token.json', (string) $response->getBody());
    }

    public function get_token()
    {
        $data = json_decode(\Storage::get('access_token.json'));
        return 'Bearer '. $data->access_token;
    }

    public function update_info()
    {
        \DB::statement("UPDATE `twitch_streamers` set online=0 ,viewers=0");

        $client = new \GuzzleHttp\Client();
        $token = $this->get_token();
        $response = $client->request(
            'GET',
            'https://api.twitch.tv/helix/streams?game_id=29307&first=50',
            [
                'headers' => [
                    'Client-ID' => 'gi3es6sr9cmscw4aww6lbt309dyj8e',
                    'Authorization' =>  $token,
                    'User-Agent' => 'testing/1.0',
                    'Accept'     => 'application/json',
                ]
            ]
        );
        
        $data = json_decode((string) $response->getBody())->data;

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
