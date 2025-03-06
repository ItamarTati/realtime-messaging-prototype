<?php

namespace App\Listeners;

use App\Events\MyEvent;
use Illuminate\Support\Facades\Log;

class MyEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\MyEvent  $event
     * @return void
     */
    public function handle(MyEvent $event)
    {  
        // Log the event
        \Log::info('MyEvent received', ['message_id' => $event->message->id]);
    }
}
