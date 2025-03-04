<?php

use App\Http\Controllers\Api\MessageController;

Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('api.send-message');
Route::get('/message-status/{id}', [MessageController::class, 'getMessageStatus'])->name('api.message-status');