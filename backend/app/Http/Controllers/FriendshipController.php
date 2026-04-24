<?php

namespace App\Http\Controllers;

use App\Enums\FriendshipStatus;
use App\Http\Requests\StoreFriendshipRequest;
use App\Models\Friendship;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $status = $request->input('status', FriendshipStatus::ACCEPTED->value);
        
        // Get friends where status is accepted
        $friendships = Friendship::with(['user', 'friend'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('friend_id', $user->id);
            })
            ->where('status', $status)
            ->get();

        return response()->json(['data' => $friendships]);
    }

    public function store(StoreFriendshipRequest $request)
    {
        $validated = $request->validated();
        $userId = $request->user()->id;
        $friendId = $validated['friend_id'];

        // Prevent duplicate requests
        $existing = Friendship::where(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $friendId)->where('friend_id', $userId);
        })->first();

        if ($existing) {
            return response()->json(['message' => 'Friendship already exists or is pending.'], 400);
        }

        $friendship = Friendship::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendshipStatus::PENDING,
        ]);

        broadcast(new \App\Events\FriendRequestReceived($friendship))->toOthers();

        return response()->json(['data' => $friendship], 201);
    }

    public function update(Request $request, Friendship $friendship)
    {
        $request->validate(['status' => 'required|string']);

        $status = $request->input('status');

        // Only the receiver can accept
        if ($status === FriendshipStatus::ACCEPTED->value && $request->user()->id !== $friendship->friend_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendship->update(['status' => $status]);

        return response()->json(['data' => $friendship]);
    }

    public function destroy(Request $request, Friendship $friendship)
    {
        if ($request->user()->id !== $friendship->user_id && $request->user()->id !== $friendship->friend_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendship->delete();
        return response()->json(null, 204);
    }
}
