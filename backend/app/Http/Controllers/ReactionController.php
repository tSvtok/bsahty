<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReactionRequest;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function store(StoreReactionRequest $request)
    {
        $validated = $request->validated();
        $userId = $request->user()->id;

        // Check if user already reacted to this entity
        $existing = Reaction::where('user_id', $userId)
            ->where('reactable_id', $validated['reactable_id'])
            ->where('reactable_type', $validated['reactable_type'])
            ->first();

        if ($existing) {
            // If they are reacting with the same type, toggle it (delete)
            if ($existing->type->value === $validated['type']) {
                $existing->delete();
                return response()->json(['message' => 'Reaction removed'], 200);
            }
            // Otherwise, update the type
            $existing->update(['type' => $validated['type']]);
            return response()->json(['data' => $existing], 200);
        }

        $validated['user_id'] = $userId;
        $reaction = Reaction::create($validated);

        return response()->json(['data' => $reaction], 201);
    }
}
