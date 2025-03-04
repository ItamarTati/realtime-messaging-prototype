<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('status', 'pending')->get();
        return view('admin.messages.index', compact('messages'));
    }
    
    public function complete($id)
    {
        $message = Message::findOrFail($id);
        $message->update(['status' => 'processed']);
        return redirect()->route('admin.messages.index')->with('success', 'Message marked as processed!');
    }
}
