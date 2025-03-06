<?php

use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MyEvent;

Route::post('/send-message', function (Request $request) {
    $request->validate([
        'content' => 'required|string',
        'user_identifier' => 'required|string', 
    ]);

    $message = Message::create([
        'content' => $request->input('content'),
        'status' => 'pending',
        'user_identifier' => $request->input('user_identifier'), 
    ]);

    \App\Jobs\ProcessMessage::dispatch($message);

    return response()->json([
        'message' => 'Message is being processed!',
        'data' => $message,
    ]);
});

Route::get('/messages', function (Request $request) {
    $userIdentifier = $request->query('user_identifier');
    $messages = Message::where('user_identifier', $userIdentifier)->get();

    return response()->json($messages);
});

Route::get('/admin/messages', function () {
    $messages = Message::where('status', 'pending')->get();

    return response()->json($messages);
});

Route::post('/messages/{id}/complete', function ($id) {
    $message = Message::findOrFail($id);
    $message->update(['status' => 'processed']);

    event(new MyEvent($message, $message->user_identifier));

    return response()->json(['message' => 'Message marked as processed!']);
});