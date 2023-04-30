<?php

namespace App\Events;

use App\Snapshot;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SnapshotCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $snapshot;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Snapshot $snapshot)
    {
        $this->snapshot=$snapshot;
        sleep(3);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        
        return new Channel('update-snapshot');//.'.str_replace('/','.',$this->snapshot->original_char));
    }

    public function broadcastWith()
    {
        return [
            'original_char' => $this->snapshot->original_char,
            'snapshot' => $this->snapshot
        ];
    }
}
