<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Events\MyEvent; // Import the event

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('status', 'pending')->get();
        return view('admin.messages.index', compact('messages'));
    }
    
    public function complete($id)
    {
        // Find the message
        $message = Message::findOrFail($id);
        
        // Update the message status to 'processed'
        $message->update(['status' => 'processed']);
        
        // Broadcast the event after marking as processed
        broadcast(new MyEvent($message->content, $message->user_identifier));
        
        // Redirect back with success message
        return redirect()->route('admin.messages.index')->with('success', 'Message marked as processed!');
    }
}