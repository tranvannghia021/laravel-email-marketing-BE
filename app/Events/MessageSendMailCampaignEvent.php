<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSendMailCampaignEvent  implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $idShop;
    public $id;
    public $total;
    public $sended;
    public $failed;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($idShop, $id, $total, $sended, $failed)
    {
        $this->idShop = $idShop;
        $this->id = $id;
        $this->total = $total;
        $this->sended = $sended;
        $this->failed = $failed;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $string = 'load_campaign_' . $this->idShop;
        return [$string];
    }


    public function broadcastAs()
    {
        return 'add_campaign_' . $this->idShop;
    }
}
