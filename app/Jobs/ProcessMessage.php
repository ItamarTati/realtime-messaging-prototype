<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\MyEvent;

class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; 

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function handle()
    {
        // Simulate processing delay
        sleep(rand(5, 60));

        $this->message->update(['status' => 'processed']);

        event(new MyEvent($this->message));
    }
}