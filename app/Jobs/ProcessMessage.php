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
        \Log::info('Starting ProcessMessage job', ['message_id' => $this->message->id]);
    
        try {
            // Simulate processing delay
            sleep(rand(10, 20));
    
            $this->message->update(['status' => 'processed']);
    
            \Log::info('Message processed', ['message_id' => $this->message->id]);
    
            \Log::info('Dispatching MyEvent', ['message_id' => $this->message->id]);
            
            event(new MyEvent($this->message, $this->message->user_identifier));
        
        } catch (\Exception $e) {
        
            \Log::error('Error in ProcessMessage job', [
                'message_id' => $this->message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}