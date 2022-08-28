<?php

namespace App\Listeners;

use App\Events\LoginEvent;
use App\Services\WeHookService;


class RegisterWeHookListen
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(LoginEvent $event)
    {
        (new WeHookService)->register($event->shop);
    }
}
