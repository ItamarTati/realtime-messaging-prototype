<?php

namespace Tests\Feature;

use App\Jobs\ProcessMessage;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Events\MyEvent;
use Illuminate\Support\Facades\Event;

class ProcessMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_message(): void
    {
        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
        ]);

        (new ProcessMessage($message))->handle();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'processed',
        ]);
    }

    public function test_process_message_job_dispatched(): void
    {
        Queue::fake();

        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
        ]);

        Queue::assertPushed(ProcessMessage::class, function ($job) use ($message) {
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('message');
            $property->setAccessible(true);
            $jobMessage = $property->getValue($job);

            return $jobMessage->id === $message->id;
        });
    }

    public function test_event_broadcast_after_message_processed(): void
    {
        Event::fake();

        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
        ]);

        (new ProcessMessage($message))->handle();

        Event::assertDispatched(MyEvent::class, function ($event) use ($message) {
            return $event->message->id === $message->id;
        });
    }
}