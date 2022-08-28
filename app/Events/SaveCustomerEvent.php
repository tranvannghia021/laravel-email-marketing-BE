<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaveCustomerEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $idShop;
    public $total;
    public $sended;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($idShop, $total, $sended)
    {
        $this->idShop = $idShop;
        $this->total = $total;
        $this->sended = $sended;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $string = 'load_customer_' . $this->idShop;
        return [$string];
    }


    public function broadcastAs()
    {

        return 'add_customer_' . $this->idShop;
    }
}
