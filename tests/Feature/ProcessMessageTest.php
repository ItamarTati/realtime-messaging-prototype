<?php

namespace Tests\Feature;

use App\Jobs\ProcessMessage;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Events\MyEvent;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProcessMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_message(): void
    {
        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
            'user_identifier' => 'user-123',
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
            'user_identifier' => 'user-123',
        ]);

        ProcessMessage::dispatch($message);

        Queue::assertPushed(ProcessMessage::class, function ($job) use ($message) {
            return $job->message->id === $message->id;
        });
    }

    public function test_event_broadcast_after_message_processed(): void
    {
        Event::fake();

        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
            'user_identifier' => 'user-123',
        ]);

        (new ProcessMessage($message))->handle();

        Event::assertDispatched(MyEvent::class, function ($event) use ($message) {
            return $event->message->id === $message->id &&
                   $event->user_identifier === $message->user_identifier;
        });
    }

    public function test_process_message_job_handles_exceptions(): void
    {
        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
            'user_identifier' => 'user-123',
        ]);

        // Simulate an exception during processing
        $this->mock(Message::class, function ($mock) use ($message) {
            $mock->shouldReceive('update')->andThrow(new \Exception('Test exception'));
        });

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test exception');

        (new ProcessMessage($message))->handle();
    }
}