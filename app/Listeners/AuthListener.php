<?php

namespace App\Listeners;

use App\Events\RegisteredEvent;
use App\Mail\Auth\CheckRegistration;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;

class AuthListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handleUserRegistered(RegisteredEvent $event): void
    {

        Mail::to($event->user->email)->queue(new CheckRegistration($event->user,$event->str));

    }


    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            RegisteredEvent::class => 'handleUserRegistered',

        ];
    }

    /**
     * Handle the event.
     */

}
