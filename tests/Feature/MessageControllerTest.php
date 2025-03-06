<?php

namespace Tests\Feature;

use App\Jobs\ProcessMessage;
use App\Models\Message;
use App\Events\MyEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_message(): void
    {
        Queue::fake();
        Event::fake();

        $response = $this->postJson('/api/send-message', [
            'content' => 'Hello, world!',
            'user_identifier' => 'user-123',
        ]);

        dd($response->json(), $response->status()); // Debug the response

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Message is being processed!',
                     'data' => [
                         'content' => 'Hello, world!',
                         'status' => 'pending',
                         'user_identifier' => 'user-123',
                     ],
                 ]);

        $this->assertDatabaseHas('messages', [
            'content' => 'Hello, world!',
            'status' => 'pending',
            'user_identifier' => 'user-123',
        ]);

        Queue::assertPushed(ProcessMessage::class);
        Event::assertDispatched(MyEvent::class);
    }

    public function test_send_message_validation_error(): void
    {
        $response = $this->postJson('/api/send-message', [
            'content' => '', // Invalid: content is required
            'user_identifier' => '', // Invalid: user_identifier is required
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['content', 'user_identifier']);
    }

    public function test_get_message_status(): void
    {
        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
            'user_identifier' => 'user-123',
        ]);

        $response = $this->getJson("/api/message-status/{$message->id}");
        dd($response->json(), $response->status()); // Debug the response

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'pending',
                 ]);
    }

    public function test_get_message_status_for_non_existent_message(): void
    {
        $response = $this->getJson('/api/message-status/999');
        dd($response->json(), $response->status()); // Debug the response

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Message not found',
                 ]);
    }

    public function test_process_message_job(): void
    {
        Event::fake();

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

        Event::assertDispatched(MyEvent::class, function ($event) use ($message) {
            return $event->message->id === $message->id &&
                   $event->user_identifier === $message->user_identifier;
        });
    }

    public function test_process_message_job_dispatched(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/send-message', [
            'content' => 'Hello, world!',
            'user_identifier' => 'user-123',
        ]);

        Queue::assertPushed(ProcessMessage::class, function ($job) use ($response) {
            $message = $response->json('data');
            dd($job, $message); // Debug the job and response
            return $job->getMessage()->id === $message['id'];
        });
    }
}