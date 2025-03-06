<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user_identifier;  

    public function __construct($message, $user_identifier)
    {
        $this->message = $message;
        $this->user_identifier = $user_identifier;  
    }

    public function broadcastOn()
    {
        return new Channel('conversation.' . $this->user_identifier);
    }

    public function broadcastAs()
    {
        return 'message.created';
    }
}