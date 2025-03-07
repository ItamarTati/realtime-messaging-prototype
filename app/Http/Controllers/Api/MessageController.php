<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Events\MyEvent; // Import the event
use App\Jobs\ProcessMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'user_identifier' => 'required|string',
        ]);
    
        // Check if a message with the same user_identifier exists and is already broadcasted
        $existingMessage = Message::where('user_identifier', $request->input('user_identifier'))
            ->where('broadcasted', true)
            ->first();
    
        if ($existingMessage) {
            return response()->json(['message' => 'This message has already been broadcasted.'], 409);
        }
    
        // Create the message
        $message = Message::create([
            'content' => $request->input('content'),
            'status' => 'pending',
            'user_identifier' => $request->input('user_identifier'),
            'broadcasted' => false, // Ensure it starts as false
        ]);
    
        // Dispatch the job to process the message
        ProcessMessage::dispatch($message);
    
        // Broadcast the event
        broadcast(new MyEvent($message->content, $message->user_identifier));
    
        // Mark as broadcasted
        $message->update(['broadcasted' => true]);
    
        return response()->json([
            'message' => 'Message is being processed!',
            'data' => $message,
        ], 201);
    }

    public function getMessageStatus($id)
    {
        $message = Message::find($id);
    
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }
    
        return response()->json(['status' => $message->status]);
    }
}