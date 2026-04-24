<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $request)
    {
        $validated = $request->validated();

        $conversation = Conversation::findOrFail($validated['conversation_id']);

        if (!$conversation->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated['user_id'] = $request->user()->id;
        $message = Message::create($validated);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json(['data' => $message], 201);
    }

    public function markAsRead(Message $message)
    {
        $message->update(['is_read' => true]);
        return response()->json(['data' => $message]);
    }

    public function unreadCount(\Illuminate\Http\Request $request)
    {
        $count = Message::whereHas('conversation', function ($query) use ($request) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('users.id', $request->user()->id);
            });
        })
            ->where('user_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
