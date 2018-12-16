<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddCharLeague implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($items_respons)
    {
        //expects respons from character-window/get-items
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //from $items_respons['character'] get league
        //check if in db
        //if not in db add to db
        //start indexing new league
    }
}
