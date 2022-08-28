<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $shop;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
