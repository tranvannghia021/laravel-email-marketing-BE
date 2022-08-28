<?php

namespace App\Providers;

use App\Events\LoginEvent;
use App\Events\MessageSendMailCampaignEvent;
use App\Listeners\GetAllCusListen;
use App\Listeners\RegisterWeHookListen;
use App\Listeners\SaveShopListen;
use Illuminate\Auth\Events\Registered;
use function Illuminate\Events\queueable;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LoginEvent::class =>[
            RegisterWeHookListen::class,
            GetAllCusListen::class,
        ],
       
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        

    }
}
