<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message; 
use App\Jobs\ProcessMessage; 
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);
    
        $message = Message::create([
            'content' => $request->input('content'),
            'status' => 'pending',
        ]);
    
        ProcessMessage::dispatch($message);
    
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
