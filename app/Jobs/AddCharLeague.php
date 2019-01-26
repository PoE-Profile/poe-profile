<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Sunra\PhpSimple\HtmlDomParser;

class AddCharLeague implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $items_response;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($response)
    {
        $this->items_response = $response;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $char_league = $this->items_response['character']['league'];
        // $char_league = preg_replace("/ \(([^()]*+|(?R))*\)/", "", $char_league);
        $char_league = preg_replace("/ \(PL[0-9]+\)/", '', $char_league);
        $db_league = \App\League::where('name', '=', $char_league)->get();
        if ($db_league->isEmpty()) {

            $private_league_data = $this->private_league_parser($char_league);

            // \App\League::firstOrCreate([
            //     'name' => strtolower($char_league),
            //     'type' => 'private',
            //     'start_at' => new \Carbon\Carbon($league['startAt']),
            //     'end_at' => $league['endAt'] == null ? null : new \Carbon\Carbon($league['endAt']),
            //     'rules' => $this->get_league_rules($league['id'], $league['rules']),
            //     'indexed' => true
            // ]);
        }
    }

    private function private_league_parser($league) {

        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray([
                        'POESESSID' => ''
                    ], 'pathofexile.com');

        try {
            // $client = new \GuzzleHttp\Client();
            $client = new \GuzzleHttp\Client(['cookies' => true]);
            $response = $client->request(
                'GET',
                'https://www.pathofexile.com/private-leagues/league/'.$league,
                ['cookies' => $cookieJar]
            );

        }catch (\GuzzleHttp\Exception\ClientException $e) {
            dd($e);
            return '';
        }

        $body = $response->getBody()->getContents();
        return;
        dd($body);
        $html = HtmlDomParser::str_get_html($body);
        dd($html);
        // $client = new \GuzzleHttp\Client([
        //     'base_uri' => 'https://www.pathofexile.com',
        //     'cookies' => $cookieJar,
        // ]);
        //
        // $response = $client->post('/login/', [
        //     'form_params' => $login_info,
        // ])->getBody()->getContents();
        // dd($cookieJar);

        $response2 = $client->get('/private-leagues/league/'.$league)->getBody()->getContents();

        dd($response2);
        $dom = HtmlDomParser::str_get_html($response2);
        dd($dom);
    }
}
