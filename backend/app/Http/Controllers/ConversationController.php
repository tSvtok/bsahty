<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $conversations = $user->conversations()
            ->with(['users', 'lastMessage'])
            ->get()
            ->map(function ($convo) use ($user) {
                $otherUser = $convo->users->where('id', '!=', $user->id)->first();
                $convo->other_user = $otherUser;
                return $convo;
            });

        return response()->json(['data' => $conversations]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;
        $userIds[] = $request->user()->id;
        $userIds = array_unique($userIds);
        sort($userIds);

        // For 1-on-1, check if exists
        if (count($userIds) === 2) {
            $existing = Conversation::has('users', 2)
                ->whereHas('users', function ($q) use ($userIds) {
                    $q->whereIn('user_id', $userIds);
                }, '=', 2)->first();

            if ($existing) {
                return response()->json($existing->load('users'));
            }
        }

        $conversation = Conversation::create();
        $conversation->users()->attach($userIds);

        return response()->json($conversation->load('users'), 201);
    }

    public function show(Request $request, Conversation $conversation)
    {
        if (!$conversation->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->load(['messages', 'users']);
        
        $otherUser = $conversation->users->where('id', '!=', $request->user()->id)->first();
        
        $data = $conversation->toArray();
        $data['other_user'] = $otherUser;

        return response()->json(['data' => $data]);
    }
}
