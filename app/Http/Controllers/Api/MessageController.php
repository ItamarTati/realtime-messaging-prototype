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
    
        // Create the message
        $message = Message::create([
            'content' => $request->input('content'),
            'status' => 'pending',
            'user_identifier' => $request->input('user_identifier'),
        ]);
    
        // Dispatch the job to process the message
        ProcessMessage::dispatch($message);
    
        // Broadcast the event
        broadcast(event: new MyEvent($message, $message->user_identifier));
    
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