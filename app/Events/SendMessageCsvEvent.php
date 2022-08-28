<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageCsvEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $idShop;
    public $success;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($idShop, $success, $message)
    {
        $this->idShop = $idShop;
        $this->success = $success;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        $string = 'load_message_' . $this->idShop;
        return [$string];
    }


    public function broadcastAs()
    {

        return 'add_message_' . $this->idShop;
    }
}
