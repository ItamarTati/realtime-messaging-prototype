<?php

namespace Tests\Feature;

use App\Jobs\ProcessMessage;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_message(): void
    {
        Queue::fake(); 

        $response = $this->postJson('/api/send-message', [
            'content' => 'Hello, world!',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Message is being processed!',
                     'data' => [
                         'content' => 'Hello, world!',
                         'status' => 'pending',
                     ],
                 ]);

        $this->assertDatabaseHas('messages', [
            'content' => 'Hello, world!',
            'status' => 'pending',
        ]);

        Queue::assertPushed(ProcessMessage::class);
    }

    public function test_send_message_validation_error(): void
    {
        $response = $this->postJson('/api/send-message', [
            'content' => '', 
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['content']);
    }

    public function test_get_message_status(): void
    {
        $message = Message::create([
            'content' => 'Hello, world!',
            'status' => 'pending',
        ]);

        $response = $this->getJson("/api/message-status/{$message->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'pending',
                 ]);
    }

    public function test_get_message_status_for_non_existent_message(): void
    {
        $response = $this->getJson('/api/message-status/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Message not found',
                 ]);
    }

}