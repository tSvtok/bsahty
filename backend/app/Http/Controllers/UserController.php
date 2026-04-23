<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        $user->load(['questions', 'friendships']);
        return response()->json(['data' => $user]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'bio' => 'sometimes|nullable|string|max:1000',
            'location' => 'sometimes|nullable|string|max:255',
            'avatar' => 'sometimes|nullable|string|max:2048',
            'sports' => 'sometimes|nullable|array',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json(['data' => $user]);
    }
}
