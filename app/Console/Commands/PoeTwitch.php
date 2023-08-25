<?php

namespace App\Console\Commands;

use App\TwitchStreamer;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Account;
use App\Snapshot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                $dbAcc = Account::where('name', $poeAcc)->first();
                if ($dbAcc) {
                    $newStreamer = TwitchStreamer::create([
                        'name' => $streamerAcc,
                        'account_id' => $dbAcc->id
                    ]);
                } else {
                    $this->error("Account does not exists or private!");
                }
                break;

            default:
                $this->error("No valid operation given");
                break;
        }
    }
    private function update_token()
    {
        $client = new Client();
        $response = $client->request(
            'POST',
            'https://id.twitch.tv/oauth2/token',
            [
                'query' => [
                    'client_id' => env('TWITCH_CLIENT_ID'),
                    'client_secret' => env('TWITCH_CLIENT_SECRET'),
                    'grant_type' => 'client_credentials'
                ]
            ]
        );

        Storage::put('access_token.json', (string) $response->getBody());
    }

    public function get_token()
    {
        $data = json_decode(Storage::get('access_token.json'));
        return 'Bearer '. $data->access_token;
    }

    public function update_info()
    {
        DB::statement("UPDATE `twitch_streamers` set online=0 ,viewers=0");

        $client = new Client();
        $token = $this->get_token();
        $response = $client->request(
            'GET',
            'https://api.twitch.tv/helix/streams?game_id=29307&first=20',
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
        // get current streamers from DB sorted Ascending
        $streamers = TwitchStreamer::whereIn('name', collect($data)->pluck('user_name'))
            ->orderBy('updated_at', 'asc')
            ->get();

        foreach ($streamers as $dbStreamer) {
            $this->info($dbStreamer->name);
            $stream = collect($data)
                ->filter(fn($s) => strtolower($s->user_name) === strtolower($dbStreamer->name))
                ->first();

            $thumbnail = str_replace('{width}', 240, $stream->thumbnail_url);
            $thumbnail = str_replace('{height}', 135, $thumbnail);

            $dbStreamer->viewers = $stream->viewer_count;
            $dbStreamer->status = $stream->title;
            $dbStreamer->img_preview = $thumbnail;
            $dbStreamer->channel_id = $stream->user_id;
            $dbStreamer->online = true;
            $dbStreamer->last_online = now();
            $dbStreamer->save();

            $dbStreamer->account->updateLastChar();

            if ($dbStreamer->isForSnapshot()) {
                $acc = $dbStreamer->account->name;
                $char = $dbStreamer->account->last_character;
                $dbStreamer->account->updateLastCharInfo();
                Snapshot::create($acc, $char);
            }
        }

        Cache::forget('OnlineStreamers');
    }
}
