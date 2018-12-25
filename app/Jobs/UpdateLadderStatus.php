<?php

namespace App\Jobs;

use App\Account;
use App\TwitchStreamer;
use App\LadderCharacter;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class UpdateLadderStatus extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $char_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($char_id)
    {
        $this->char_id = $char_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ladderChar = LadderCharacter::find($this->char_id);
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        // $responseChars = $client->request(
        //     'POST',
        //     'https://www.pathofexile.com/character-window/get-items',[
        //     'form_params' => [
        //         'accountName' => $ladderChar->account->name,
        //         'character' => $ladderChar->name
        //     ]
        // ]);

        //Beacouse raid limit on Path of Exile API we are making the request from separate ip/server (proxy)
        $responseChars = $client->request(
            'GET',
            'http://jump.boxbg.net/kas7er/getItemJson.php',[
            'query' => [
                'accountName' => $ladderChar->account->name,
                'character' => $ladderChar->name
            ]
        ]);

        $statuscode = $responseChars->getStatusCode();
        $responseChars = json_decode((string)$responseChars->getBody(), true);
        // if (403 === $statuscode) {
        if (array_key_exists('error', $responseChars)) {
            $ladderChar->public = 0;
        } else {
            $ladderChar->public = 1;
            $items = [];
            if (array_key_exists('items', $responseChars)) {
                foreach ($responseChars['items'] as $item) {
                    if ($item['inventoryId'] == 'Weapon2' || !array_key_exists('socketedItems', $item)) {
                        continue;
                    }
                    if (count($item['socketedItems']) >= 5) {
                        $items[] = $item;
                    }
                }
                if (count($items) > 0) {
                    $ladderChar->items_most_sockets = $items;
                } else {
                    $ladderChar->items_most_sockets = null;
                }
            }
        }

        $ladderChar->save();
        //add timeout for ratelimit
        sleep(1);
    }

    public function tags()
    {
        return ['UpdateLadderStatus/', 'char_id:' . $this->char_id];
    }

}
