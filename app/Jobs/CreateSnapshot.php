<?php

namespace App\Jobs;

use App\Events\SnapshotCreated;
use App\PoeApi;
use App\Snapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateSnapshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $account;

    public $character;

    /**
     * Create a new job instance.
     *
     * @param string $account account
     * @param string $character character
     *
     * @return void
     */
    public function __construct($account, $character)
    {
        $this->account = $account;
        $this->character = $character;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $itemsData = PoeApi::getItemsData($this->account, $this->character);
        $treeData = PoeApi::getTreeData($this->account, $this->character);

        if (!array_key_exists('items', $itemsData)) {
            return;
        }

        $itemsNoFlasks = array_filter($itemsData['items'], function ($item){
            return $item['inventoryId']!="Flask";
        });

        $itemsNoFlasks=json_encode($itemsNoFlasks);
        $hash = md5(json_encode($treeData).'/'.$itemsNoFlasks);

        $snapshot = Snapshot::firstOrNew(['hash' => $hash]);
        $snapshot->item_data = $itemsData;
        $snapshot->tree_data = $treeData;
        $snapshot->original_char = $this->account .'/'. $this->character;
        $snapshot->poe_version = config('app.poe_version');
        $snapshot->original_level = $itemsData['character']['level'];
        $snapshot->save();
        event(new SnapshotCreated($snapshot));
        // SnapshotCreated::dispatch($snapshot);
    }
}
